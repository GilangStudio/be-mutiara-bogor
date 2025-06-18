<?php

namespace App\Http\Controllers;

use App\Models\HomeFeature;
use Illuminate\Http\Request;
use App\Services\GeneratorService;

class HomeFeatureController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'icon' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ], [
            'icon.required' => 'Icon is required',
            'title.required' => 'Feature title is required',
            'title.max' => 'Feature title cannot exceed 255 characters',
            'description.required' => 'Feature description is required',
        ]);

        try {
            $order = GeneratorService::generateOrder(new HomeFeature());

            HomeFeature::create([
                'icon' => $request->icon,
                'title' => $request->title,
                'description' => $request->description,
                'order' => $order,
                'is_active' => $request->has('status')
            ]);

            return redirect()->route('home-page.index')
                        ->with('success', 'Feature added successfully');
        } catch (\Exception $e) {
            return redirect()->route('home-page.index')
                        ->with('error', 'Failed to add feature: ' . $e->getMessage());
        }
    }
        
    public function reorder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:home_features,id',
            'orders.*.order' => 'required|integer|min:1'
        ]);

        try {
            foreach ($request->orders as $orderData) {
                HomeFeature::where('id', $orderData['id'])
                          ->update(['order' => $orderData['order']]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, HomeFeature $feature)
    {
        $request->validate([
            'icon' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ], [
            'icon.required' => 'Icon is required',
            'title.required' => 'Feature title is required',
            'title.max' => 'Feature title cannot exceed 255 characters',
            'description.required' => 'Feature description is required',
        ]);

        try {
            $feature->update([
                'icon' => $request->icon,
                'title' => $request->title,
                'description' => $request->description,
                'is_active' => $request->has('status')
            ]);

            return redirect()->route('home-page.index')
                           ->with('success', 'Feature updated successfully');

        } catch (\Exception $e) {
            return redirect()->route('home-page.index')
                           ->with('error', 'Failed to update feature: ' . $e->getMessage());
        }
    }

    public function destroy(HomeFeature $feature)
    {
        try {
            $feature->delete();
            
            // Reorder after delete
            GeneratorService::reorderAfterDelete(new HomeFeature());

            return redirect()->route('home-page.index')
                           ->with('success', 'Feature deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('home-page.index')
                           ->with('error', 'Failed to delete feature: ' . $e->getMessage());
        }
    }
}