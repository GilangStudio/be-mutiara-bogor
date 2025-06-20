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
    public function index()
    {
        $leads = Lead::with(['platform', 'historyLead.sales'])
                    ->orderBy('id', 'desc')
                    ->get();

        return view('pages.crm.leads.index', compact('leads'));
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