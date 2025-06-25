<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Sales;
use App\Enum\LeadStatus;
use App\Models\Platform;
use App\Models\HistoryLeads;
use Illuminate\Http\Request;
use App\Models\HistoryMoveLeads;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::with(['platform', 'historyLead.sales']);
        
        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('phone', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('email', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('message', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('note', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('path_referral', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Platform filter
        if ($request->filled('platform_id')) {
            $query->where('platform_id', $request->platform_id);
        }
        
        // Sales filter
        if ($request->filled('sales_id')) {
            $query->whereHas('historyLead', function($q) use ($request) {
                $q->where('sales_id', $request->sales_id);
            });
        }
        
        // Assignment type filter
        if ($request->filled('assignment_type')) {
            $assignmentType = $request->assignment_type === 'auto';
            $query->whereHas('historyLead', function($q) use ($assignmentType) {
                $q->where('is_automatic', $assignmentType);
            });
        }
        
        // Date range filter
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
        $leads = $query->orderBy('created_at', 'desc')
                      ->paginate(20)
                      ->appends($request->query());
        
        // Get filter options
        $platforms = Platform::orderBy('platform_name')->get();
        $sales = Sales::where('is_active', true)->orderBy('order')->get();
        $statusOptions = LeadStatus::cases();
        
        return view('pages.crm.leads.index', compact('leads', 'platforms', 'sales', 'statusOptions'));
    }

    public function create()
    {
        $platforms = Platform::orderBy('platform_name')->get();
        $sales = Sales::where('is_active', true)
                     ->orderBy('order')
                     ->get();

        return view('pages.crm.leads.create', compact('platforms', 'sales'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|min:10|max:15|regex:/^([0-9\s\-\+\(\)]*)$/|unique:leads,phone',
            'email' => 'nullable|email|max:255|unique:leads,email',
            'message' => 'nullable|string|max:1000',
            'note' => 'nullable|string|max:500',
            'platform_id' => 'required|exists:platforms,id',
            'sales_assignment' => 'required|in:auto,manual',
            'sales_id' => 'required_if:sales_assignment,manual|nullable|exists:sales,id',
            'path_referral' => 'nullable|string|max:255'
        ], [
            'name.required' => 'Name is required',
            'name.max' => 'Name cannot exceed 255 characters',
            'phone.required' => 'Phone number is required',
            'phone.min' => 'Phone number minimum 10 digits',
            'phone.max' => 'Phone number maximum 15 characters',
            'phone.regex' => 'Invalid phone number format',
            'phone.unique' => 'Phone number already registered',
            'email.email' => 'Invalid email format',
            'email.unique' => 'Email already registered',
            'message.max' => 'Message cannot exceed 1000 characters',
            'note.max' => 'Note cannot exceed 500 characters',
            'platform_id.required' => 'Platform is required',
            'platform_id.exists' => 'Invalid platform',
            'sales_assignment.required' => 'Assignment type is required',
            'sales_id.required_if' => 'Sales is required for manual assignment',
            'sales_id.exists' => 'Invalid sales',
            'path_referral.max' => 'Path referral cannot exceed 255 characters'
        ]);

        try {
            // Determine sales assignment
            if ($request->sales_assignment === 'auto') {
                $salesId = $this->getNextSalesId();
            } else {
                $salesId = $request->sales_id;
                
                // Validate if sales is active
                if (!Sales::where('id', $salesId)->where('is_active', true)->exists()) {
                    return redirect()->back()
                                   ->withInput()
                                   ->with('error', 'Selected sales is not active');
                }
            }

            // Create lead
            $lead = Lead::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'message' => $request->message,
                'status' => LeadStatus::NEW->value,
                'note' => $request->note,
                'platform_id' => $request->platform_id,
                'path_referral' => $request->path_referral,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id()
            ]);

            // Create history lead record
            HistoryLeads::create([
                'leads_id' => $lead->id,
                'sales_id' => $salesId,
                'is_automatic' => $request->sales_assignment === 'auto',
                'is_favorited' => false
            ]);

            // TODO: Send notification to sales (implement notification system)

            return redirect()->route('crm.leads.index')
                           ->with('success', 'Lead created successfully and assigned to sales');

        } catch (\Exception $e) {
            return redirect()->route('crm.leads.create')
                           ->withInput()
                           ->with('error', 'Failed to create lead: ' . $e->getMessage());
        }
    }

    public function edit(Lead $lead)
    {
        $lead->load(['platform', 'historyLead.sales']);
        $platforms = Platform::orderBy('platform_name')->get();
        $sales = Sales::where('is_active', true)->orderBy('order')->get();
        $statusOptions = LeadStatus::cases();
        $historyMoveLeads = HistoryMoveLeads::with(['fromSales', 'toSales'])
                                          ->where('leads_id', $lead->id)
                                          ->orderBy('created_at', 'desc')
                                          ->get();

        return view('pages.crm.leads.edit', compact('lead', 'platforms', 'sales', 'statusOptions', 'historyMoveLeads'));
    }

    public function update(Request $request, Lead $lead)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|min:10|max:15|regex:/^([0-9\s\-\+\(\)]*)$/|unique:leads,phone,' . $lead->id,
            'email' => 'nullable|email|max:255|unique:leads,email,' . $lead->id,
            'message' => 'nullable|string|max:1000',
            'note' => 'nullable|string|max:500',
            'platform_id' => 'required|exists:platforms,id',
            'sales_id' => 'required|exists:sales,id',
            'path_referral' => 'nullable|string|max:255'
        ]);

        try {
            $lead->load('historyLead');
            $currentSalesId = $lead->historyLead->sales_id;

            // Check if sales changed and lead status is NEW
            if ($request->sales_id != $currentSalesId && $lead->status == LeadStatus::NEW->value) {
                // Validate new sales is active
                if (!Sales::where('id', $request->sales_id)->where('is_active', true)->exists()) {
                    return redirect()->back()
                                   ->withInput()
                                   ->with('error', 'Selected sales is not active');
                }

                // Update history lead
                $lead->historyLead->update([
                    'sales_id' => $request->sales_id,
                    'is_automatic' => false,
                    'is_favorited' => false
                ]);

                // Create move history
                HistoryMoveLeads::create([
                    'leads_id' => $lead->id,
                    'from_sales_id' => $currentSalesId,
                    'to_sales_id' => $request->sales_id
                ]);

                // TODO: Send notification to new sales
            }

            // Update lead data
            $lead->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'message' => $request->message,
                'note' => $request->note,
                'platform_id' => $request->platform_id,
                'path_referral' => $request->path_referral,
                'updated_by' => Auth::id()
            ]);

            return redirect()->route('crm.leads.edit', $lead)
                           ->with('success', 'Lead updated successfully');

        } catch (\Exception $e) {
            return redirect()->route('crm.leads.edit', $lead)
                           ->withInput()
                           ->with('error', 'Failed to update lead: ' . $e->getMessage());
        }
    }

    public function destroy(Lead $lead)
    {
        try {
            $lead->delete();

            return redirect()->route('crm.leads.index')
                           ->with('success', 'Lead deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('crm.leads.index')
                           ->with('error', 'Failed to delete lead: ' . $e->getMessage());
        }
    }

    public function changeStatus(Request $request, Lead $lead)
    {
        $request->validate([
            'status' => ['required', 'in:' . implode(',', array_column(LeadStatus::cases(), 'value'))]
        ], [
            'status.required' => 'Status is required',
            'status.in' => 'Invalid status'
        ]);

        try {
            $lead->update([
                'status' => $request->status,
                'updated_by' => Auth::id()
            ]);

            return redirect()->route('crm.leads.edit', $lead)
                           ->with('success', 'Lead status updated successfully');

        } catch (\Exception $e) {
            return redirect()->route('crm.leads.edit', $lead)
                           ->with('error', 'Failed to update lead status: ' . $e->getMessage());
        }
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:update_status,assign_sales,delete',
            'status' => 'required_if:action,update_status|in:' . implode(',', array_column(LeadStatus::cases(), 'value')),
            // 'sales_id' => 'required_if:action,assign_sales|exists:sales,id',
        ],[
            'action.required' => 'Action is required',
            'action.in' => 'Invalid action',
            'status.required_if' => 'Status is required for update status action',
            'status.in' => 'Invalid status',
            // 'sales_id.required_if' => 'Sales is required for assign sales action',
            // 'sales_id.exists' => 'Invalid sales'
        ]);

        try {
            $action = $request->action;
            $leadIds = [];

            // Check if this is a global selection (all pages)
            if ($request->has('select_all_pages') && $request->select_all_pages === 'true') {
                // Build query with current filters
                $query = Lead::query();
                
                // Apply same filters as index method
                if ($request->filled('search')) {
                    $searchTerm = $request->search;
                    $query->where(function($q) use ($searchTerm) {
                        $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                          ->orWhere('phone', 'LIKE', '%' . $searchTerm . '%')
                          ->orWhere('email', 'LIKE', '%' . $searchTerm . '%')
                          ->orWhere('message', 'LIKE', '%' . $searchTerm . '%')
                          ->orWhere('note', 'LIKE', '%' . $searchTerm . '%')
                          ->orWhere('path_referral', 'LIKE', '%' . $searchTerm . '%');
                    });
                }
                
                if ($request->filled('status')) {
                    $query->where('status', $request->status);
                }
                
                if ($request->filled('platform_id')) {
                    $query->where('platform_id', $request->platform_id);
                }
                
                if ($request->filled('sales_id')) {
                    $query->whereHas('historyLead', function($q) use ($request) {
                        $q->where('sales_id', $request->sales_id);
                    });
                }
                
                if ($request->filled('assignment_type')) {
                    $assignmentType = $request->assignment_type === 'auto';
                    $query->whereHas('historyLead', function($q) use ($assignmentType) {
                        $q->where('is_automatic', $assignmentType);
                    });
                }
                
                if ($request->filled('date_from')) {
                    $query->whereDate('created_at', '>=', $request->date_from);
                }
                
                if ($request->filled('date_to')) {
                    $query->whereDate('created_at', '<=', $request->date_to);
                }
                
                // Get all matching IDs
                $leadIds = $query->pluck('id')->toArray();
                $count = count($leadIds);
                
                // For performance, limit bulk operations to reasonable numbers
                if ($count > 1000) {
                    return redirect()->route('crm.leads.index')
                                   ->with('error', "Bulk action limited to 1000 items. Please use filters to narrow down your selection. Current selection: {$count} items.");
                }
            } else {
                // Regular selection with individual IDs
                $request->validate([
                    'lead_ids' => 'required|array|min:1',
                    'lead_ids.*' => 'exists:leads,id'
                ]);
                
                $leadIds = $request->lead_ids;
                $count = count($leadIds);
            }

            if (empty($leadIds)) {
                return redirect()->route('crm.leads.index')
                               ->with('error', 'No leads selected for bulk action.');
            }

            // Perform bulk action
            switch ($action) {
                case 'update_status':
                    Lead::whereIn('id', $leadIds)->update([
                        'status' => $request->status,
                        'updated_by' => Auth::id()
                    ]);
                    $message = "{$count} lead(s) status updated successfully";
                    break;
                
                // case 'assign_sales':
                //     // Only for NEW status leads
                //     $newLeads = Lead::whereIn('id', $leadIds)
                //                    ->where('status', LeadStatus::NEW->value)
                //                    ->get();
                    
                //     foreach ($newLeads as $lead) {
                //         $currentSalesId = $lead->historyLead->sales_id;
                        
                //         if ($currentSalesId != $request->sales_id) {
                //             // Update history lead
                //             $lead->historyLead->update([
                //                 'sales_id' => $request->sales_id,
                //                 'is_automatic' => false
                //             ]);
                            
                //             // Create move history
                //             HistoryMoveLeads::create([
                //                 'leads_id' => $lead->id,
                //                 'from_sales_id' => $currentSalesId,
                //                 'to_sales_id' => $request->sales_id
                //             ]);
                //         }
                //     }
                    
                //     $message = "{$newLeads->count()} lead(s) reassigned successfully";
                //     break;
                
                case 'delete':
                    Lead::whereIn('id', $leadIds)->delete();
                    $message = "{$count} lead(s) deleted successfully";
                    break;
            }

            return redirect()->route('crm.leads.index')
                           ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('crm.leads.index')
                           ->with('error', 'Bulk action failed: ' . $e->getMessage());
        }
    }

    /**
     * Get next sales ID for automatic assignment
     */
    private function getNextSalesId()
    {
        $maxOrder = Sales::where('is_active', true)->max('order');
        
        if (!$maxOrder) {
            throw new \Exception('No active sales available');
        }

        $lastAutoAssignment = HistoryLeads::with('sales')
                                        ->where('is_automatic', true)
                                        ->orderBy('id', 'desc')
                                        ->first();

        if (!$lastAutoAssignment) {
            // First time assignment, get sales with order 1
            return Sales::where('is_active', true)
                       ->where('order', 1)
                       ->firstOrFail()
                       ->id;
        }

        $nextOrder = $lastAutoAssignment->sales->order + 1;

        if ($nextOrder > $maxOrder) {
            // Reset to first sales
            $nextOrder = 1;
        }

        return Sales::where('is_active', true)
                   ->where('order', $nextOrder)
                   ->firstOrFail()
                   ->id;
    }
}