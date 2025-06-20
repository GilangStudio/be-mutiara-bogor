<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SalesController extends Controller
{
    public function index()
    {
        $sales = Sales::with(['user', 'historyLeads' => function($query) {
            $query->selectRaw('sales_id, COUNT(*) as total_leads')
                  ->groupBy('sales_id');
        }])->orderBy('is_active', 'desc')
          ->orderBy('order', 'asc')
          ->get();

        return view('pages.crm.sales.index', compact('sales'));
    }

    public function create()
    {
        $maxOrder = Sales::max('order') + 1;
        return view('pages.crm.sales.create', compact('maxOrder'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|min:10|max:15|regex:/^([0-9\s\-\+\(\)]*)$/|unique:sales,phone',
            'email' => 'required|email|max:255|unique:sales,email',
        ], [
            'name.required' => 'Name is required',
            'name.max' => 'Name cannot exceed 255 characters',
            'phone.required' => 'Phone number is required',
            'phone.min' => 'Phone number minimum 10 digits',
            'phone.max' => 'Phone number maximum 15 characters',
            'phone.regex' => 'Invalid phone number format',
            'phone.unique' => 'Phone number already exists',
            'email.required' => 'Email is required',
            'email.email' => 'Invalid email format',
            'email.unique' => 'Email already exists'
        ]);

        try {
            // Generate username from name (remove spaces, lowercase)
            $username = strtolower(str_replace(' ', '', $request->name));
            $baseUsername = $username;
            $counter = 1;
            
            // Check if username exists and make it unique
            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . $counter;
                $counter++;
            }

            // Generate password from last 6 digits of phone
            $cleanPhone = preg_replace('/[^0-9]/', '', $request->phone);
            $password = substr($cleanPhone, -6);

            // Create user account
            $user = User::create([
                'name' => $request->name,
                'username' => $username,
                'email' => $request->email,
                'password' => Hash::make($password),
                'role' => 'sales',
                'is_active' => true
            ]);

            // Get next order number
            $order = Sales::max('order') + 1;

            // Create sales record
            Sales::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'order' => $order,
                'is_active' => true,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id()
            ]);

            return redirect()->route('crm.sales.index')
                           ->with('success', "Sales created successfully. Username: {$username}, Password: {$password}");

        } catch (\Exception $e) {
            return redirect()->route('crm.sales.create')
                           ->withInput()
                           ->with('error', 'Failed to create sales: ' . $e->getMessage());
        }
    }

    public function edit(Sales $sales)
    {
        return view('pages.crm.sales.edit', compact('sales'));
    }

    public function update(Request $request, Sales $sales)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|min:10|max:15|regex:/^([0-9\s\-\+\(\)]*)$/|unique:sales,phone,' . $sales->id,
            'email' => 'required|email|max:255|unique:sales,email,' . $sales->id,
            'username' => 'required|string|max:255|unique:users,username,' . $sales->user_id,
            'password' => 'nullable|string|min:6',
        ], [
            'name.required' => 'Name is required',
            'name.max' => 'Name cannot exceed 255 characters',
            'phone.required' => 'Phone number is required',
            'phone.min' => 'Phone number minimum 10 digits',
            'phone.max' => 'Phone number maximum 15 characters',
            'phone.regex' => 'Invalid phone number format',
            'phone.unique' => 'Phone number already exists',
            'email.required' => 'Email is required',
            'email.email' => 'Invalid email format',
            'email.unique' => 'Email already exists',
            'username.required' => 'Username is required',
            'username.unique' => 'Username already exists',
            'password.min' => 'Password minimum 6 characters'
        ]);

        try {
            // Update user data
            $userData = [
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $sales->user->update($userData);

            // Update sales data
            $sales->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'updated_by' => Auth::id()
            ]);

            $message = 'Sales updated successfully';
            if ($request->filled('password')) {
                $message .= ' with new password';
            }

            return redirect()->route('crm.sales.edit', $sales)
                           ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('crm.sales.edit', $sales)
                           ->withInput()
                           ->with('error', 'Failed to update sales: ' . $e->getMessage());
        }
    }

    public function destroy(Sales $sales)
    {
        try {
            // Check if sales has leads with status NEW
            $hasNewLeads = $sales->historyLeads()
                                ->whereHas('lead', function($query) {
                                    $query->where('status', 'NEW');
                                })->count();

            if ($hasNewLeads > 0) {
                return redirect()->route('crm.sales.index')
                               ->with('error', 'Sales cannot be deactivated because they still have leads with NEW status');
            }

            // Reorder other sales
            Sales::where('order', '>', $sales->order)
                 ->where('is_active', true)
                 ->decrement('order');

            // Deactivate sales
            $sales->update([
                'is_active' => false,
                'order' => null,
                'updated_by' => Auth::id()
            ]);

            // Deactivate user account
            $sales->user->update(['is_active' => false]);

            return redirect()->route('crm.sales.index')
                           ->with('success', 'Sales deactivated successfully');

        } catch (\Exception $e) {
            return redirect()->route('crm.sales.index')
                           ->with('error', 'Failed to deactivate sales: ' . $e->getMessage());
        }
    }

    public function activate(Sales $sales)
    {
        try {
            $maxOrder = Sales::where('is_active', true)->max('order') + 1;

            $sales->update([
                'is_active' => true,
                'order' => $maxOrder,
                'updated_by' => Auth::id()
            ]);

            $sales->user->update(['is_active' => true]);

            return redirect()->route('crm.sales.index')
                           ->with('success', 'Sales activated successfully');

        } catch (\Exception $e) {
            return redirect()->route('crm.sales.index')
                           ->with('error', 'Failed to activate sales: ' . $e->getMessage());
        }
    }
}