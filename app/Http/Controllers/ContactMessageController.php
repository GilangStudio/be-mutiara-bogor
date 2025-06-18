<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Log;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('email', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('phone', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('message', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('to', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Date filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // If requesting all IDs for global selection
        if ($request->ajax() && $request->filled('get_all_ids')) {
            $allIds = $query->pluck('id')->toArray();
            return response()->json([
                'all_ids' => $allIds,
                'total_count' => count($allIds)
            ]);
        }
        
        // Order by latest
        $messages = $query->orderBy('created_at', 'desc')
                         ->paginate(20)
                         ->appends($request->query());
        
        return view('pages.contact-messages.index', compact('messages'));
    }

    public function show(ContactMessage $contactMessage)
    {
        // Mark as read when viewed
        if ($contactMessage->status === 'unread') {
            $contactMessage->update(['status' => 'read']);
        }
        
        return view('pages.contact-messages.show', compact('contactMessage'));
    }

    public function updateStatus(Request $request, ContactMessage $contactMessage)
    {
        $request->validate([
            'status' => 'required|in:unread,read,replied',
        ]);

        try {
            $contactMessage->update([
                'status' => $request->status
            ]);

            return redirect()->back()
                           ->with('success', 'Message status updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to update message status: ' . $e->getMessage());
        }
    }

    public function reply(Request $request, ContactMessage $contactMessage)
    {
        $request->validate([
            'reply' => 'required|string',
        ], [
            'reply.required' => 'Reply message is required',
        ]);

        try {
            $contactMessage->update([
                'reply' => $request->reply,
                'status' => 'replied',
                'replied_at' => now()
            ]);

            return redirect()->back()
                           ->with('success', 'Reply saved successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to save reply: ' . $e->getMessage());
        }
    }

    public function destroy(ContactMessage $contactMessage)
    {
        try {
            $contactMessage->delete();

            return redirect()->route('contact-messages.index')
                           ->with('success', 'Contact message deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('contact-messages.index')
                           ->with('error', 'Failed to delete contact message: ' . $e->getMessage());
        }
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:mark_read,mark_unread,mark_replied,delete',
        ]);

        try {
            $action = $request->action;
            $messageIds = [];

            // Check if this is a global selection (all pages)
            if ($request->has('select_all_pages') && $request->select_all_pages === 'true') {
                // Build query with current filters
                $query = ContactMessage::query();
                
                // Apply same filters as index method
                if ($request->filled('search')) {
                    $searchTerm = $request->search;
                    $query->where(function($q) use ($searchTerm) {
                        $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                          ->orWhere('email', 'LIKE', '%' . $searchTerm . '%')
                          ->orWhere('phone', 'LIKE', '%' . $searchTerm . '%')
                          ->orWhere('message', 'LIKE', '%' . $searchTerm . '%')
                          ->orWhere('to', 'LIKE', '%' . $searchTerm . '%');
                    });
                }
                
                if ($request->filled('status')) {
                    $query->where('status', $request->status);
                }
                
                if ($request->filled('date_from')) {
                    $query->whereDate('created_at', '>=', $request->date_from);
                }
                
                if ($request->filled('date_to')) {
                    $query->whereDate('created_at', '<=', $request->date_to);
                }
                
                // Get all matching IDs
                $messageIds = $query->pluck('id')->toArray();
                $count = count($messageIds);
                
                // For performance, limit bulk operations to reasonable numbers
                if ($count > 1000) {
                    return redirect()->route('contact-messages.index')
                                   ->with('error', "Bulk action limited to 1000 items. Please use filters to narrow down your selection. Current selection: {$count} items.");
                }
            } else {
                // Regular selection with individual IDs
                $request->validate([
                    'message_ids' => 'required|array|min:1',
                    'message_ids.*' => 'exists:contact_messages,id'
                ]);
                
                $messageIds = $request->message_ids;
                $count = count($messageIds);
            }

            if (empty($messageIds)) {
                return redirect()->route('contact-messages.index')
                               ->with('error', 'No messages selected for bulk action.');
            }

            // Perform bulk action
            switch ($action) {
                case 'mark_read':
                    ContactMessage::whereIn('id', $messageIds)->update(['status' => 'read']);
                    $message = "{$count} message(s) marked as read successfully";
                    break;
                
                case 'mark_unread':
                    ContactMessage::whereIn('id', $messageIds)->update(['status' => 'unread']);
                    $message = "{$count} message(s) marked as unread successfully";
                    break;
                
                case 'mark_replied':
                    ContactMessage::whereIn('id', $messageIds)->update(['status' => 'replied']);
                    $message = "{$count} message(s) marked as replied successfully";
                    break;
                
                case 'delete':
                    ContactMessage::whereIn('id', $messageIds)->delete();
                    $message = "{$count} message(s) deleted successfully";
                    break;
            }

            return redirect()->route('contact-messages.index')
                           ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Bulk action failed: ' . $e->getMessage());
            
            return redirect()->route('contact-messages.index')
                           ->with('error', 'Bulk action failed: ' . $e->getMessage());
        }
    }

    public function markAllAsRead()
    {
        try {
            ContactMessage::where('status', 'unread')->update(['status' => 'read']);

            return redirect()->route('contact-messages.index')
                           ->with('success', 'All messages marked as read successfully');

        } catch (\Exception $e) {
            return redirect()->route('contact-messages.index')
                           ->with('error', 'Failed to mark all messages as read: ' . $e->getMessage());
        }
    }
}