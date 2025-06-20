<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use Illuminate\Http\Request;

class PlatformController extends Controller
{
    public function index()
    {
        $platforms = Platform::orderBy('id', 'desc')->get();
        return view('pages.crm.platform.index', compact('platforms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'platform_name' => 'required|string|max:255|unique:platforms,platform_name',
        ], [
            'platform_name.required' => 'Platform name is required',
            'platform_name.max' => 'Platform name cannot exceed 255 characters',
            'platform_name.unique' => 'Platform name already exists'
        ]);

        try {
            Platform::create([
                'platform_name' => $request->platform_name
            ]);

            return redirect()->route('crm.platform.index')
                           ->with('success', 'Platform created successfully');

        } catch (\Exception $e) {
            return redirect()->route('crm.platform.index')
                           ->with('error', 'Failed to create platform: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Platform $platform)
    {
        $request->validate([
            'platform_name' => 'required|string|max:255|unique:platforms,platform_name,' . $platform->id,
        ], [
            'platform_name.required' => 'Platform name is required',
            'platform_name.max' => 'Platform name cannot exceed 255 characters',
            'platform_name.unique' => 'Platform name already exists'
        ]);

        try {
            $platform->update([
                'platform_name' => $request->platform_name
            ]);

            return redirect()->route('crm.platform.index')
                           ->with('success', 'Platform updated successfully');

        } catch (\Exception $e) {
            return redirect()->route('crm.platform.index')
                           ->with('error', 'Failed to update platform: ' . $e->getMessage());
        }
    }

    public function destroy(Platform $platform)
    {
        try {
            // Check if platform is being used by leads
            if ($platform->leads()->count() > 0) {
                return redirect()->route('crm.platform.index')
                               ->with('error', 'Platform cannot be deleted because it is still being used by leads');
            }

            $platform->delete();

            return redirect()->route('crm.platform.index')
                           ->with('success', 'Platform deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('crm.platform.index')
                           ->with('error', 'Failed to delete platform: ' . $e->getMessage());
        }
    }
}