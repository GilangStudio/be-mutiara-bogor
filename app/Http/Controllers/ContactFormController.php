<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class ContactFormController extends Controller
{
    /**
     * Show the contact form page
     */
    public function index()
    {
        return view('contact-form');
    }

    /**
     * Store contact message from public form
     */
    public function store(Request $request)
    {
        // Rate limiting to prevent spam
        $key = 'contact-form:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return redirect()->back()
                           ->with('error', "Too many attempts. Please try again in {$seconds} seconds.")
                           ->withInput();
        }

        // Validation
        $request->validate([
            'name' => 'required|string|max:255|min:2',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'to' => 'nullable|string|max:100',
            'message' => 'required|string|max:1000|min:10',
        ], [
            'name.required' => 'Name is required',
            'name.min' => 'Name must be at least 2 characters',
            'name.max' => 'Name cannot exceed 255 characters',
            'email.required' => 'Email address is required',
            'email.email' => 'Please enter a valid email address',
            'email.max' => 'Email cannot exceed 255 characters',
            'phone.max' => 'Phone number cannot exceed 20 characters',
            'to.max' => 'Department selection is invalid',
            'message.required' => 'Message is required',
            'message.min' => 'Message must be at least 10 characters',
            'message.max' => 'Message cannot exceed 1000 characters',
        ]);

        try {
            // Sanitize input data
            $data = [
                'name' => strip_tags(trim($request->name)),
                'email' => strtolower(trim($request->email)),
                'phone' => $this->formatPhoneNumber($request->phone),
                'to' => $request->to ?: null,
                'message' => strip_tags(trim($request->message)),
                'status' => 'unread'
            ];

            // Create contact message
            ContactMessage::create($data);

            // Hit rate limiter
            RateLimiter::hit($key, 300); // 5 minutes decay

            // Send notification (optional - you can implement email notifications here)
            // $this->sendNotification($data);

            return redirect()->back()
                           ->with('success', 'Thank you for your message! We will get back to you soon.')
                           ->withInput(['name' => '', 'email' => '', 'phone' => '', 'to' => '', 'message' => '']);

        } catch (\Exception $e) {
            \Log::error('Contact form submission failed: ' . $e->getMessage());
            
            return redirect()->back()
                           ->with('error', 'Sorry, there was an error sending your message. Please try again.')
                           ->withInput();
        }
    }

    /**
     * Format phone number to Indonesian standard
     */
    private function formatPhoneNumber($phone)
    {
        if (!$phone) {
            return null;
        }

        // Remove all non-numeric characters except +
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Handle Indonesian phone number formatting
        if (str_starts_with($phone, '0')) {
            $phone = '+62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '62') && !str_starts_with($phone, '+62')) {
            $phone = '+' . $phone;
        } elseif (!str_starts_with($phone, '+') && strlen($phone) >= 10) {
            $phone = '+62' . $phone;
        }

        return $phone;
    }

    /**
     * Send notification to admin (optional implementation)
     */
    private function sendNotification($data)
    {
        // You can implement email notification to admin here
        // Mail::to('admin@example.com')->send(new ContactMessageNotification($data));
        
        // Or send to Slack, Discord, etc.
        // Notification::route('slack', config('services.slack.webhook'))
        //     ->notify(new NewContactMessage($data));
    }

    /**
     * Get contact statistics for admin
     */
    public function getStats()
    {
        if (!auth()->check()) {
            abort(403);
        }

        return response()->json([
            'unread_count' => ContactMessage::unread()->count(),
            'today_count' => ContactMessage::today()->count(),
            'this_week_count' => ContactMessage::thisWeek()->count(),
            'this_month_count' => ContactMessage::thisMonth()->count(),
        ]);
    }
}