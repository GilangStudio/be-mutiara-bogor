<?php

namespace App\Http\Controllers;

use App\Models\HomeFeaturedUnit;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Services\GeneratorService;

class HomeFeaturedUnitController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id|unique:home_featured_units,unit_id',
        ], [
            'unit_id.required' => 'Unit is required',
            'unit_id.exists' => 'Selected unit does not exist',
            'unit_id.unique' => 'This unit is already featured on home page',
        ]);

        try {
            $order = GeneratorService::generateOrder(new HomeFeaturedUnit());

            HomeFeaturedUnit::create([
                'unit_id' => $request->unit_id,
                'order' => $order,
                'is_active' => $request->has('status')
            ]);

            return redirect()->route('home-page.index')
                           ->with('success', 'Featured unit added successfully');

        } catch (\Exception $e) {
            return redirect()->route('home-page.index')
                           ->with('error', 'Failed to add featured unit: ' . $e->getMessage());
        }
    }

    public function update(Request $request, HomeFeaturedUnit $featuredUnit)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id|unique:home_featured_units,unit_id,' . $featuredUnit->id,
        ], [
            'unit_id.required' => 'Unit is required',
            'unit_id.exists' => 'Selected unit does not exist',
            'unit_id.unique' => 'This unit is already featured on home page',
        ]);

        try {
            $featuredUnit->update([
                'unit_id' => $request->unit_id,
                'is_active' => $request->has('status')
            ]);

            return redirect()->route('home-page.index')
                           ->with('success', 'Featured unit updated successfully');

        } catch (\Exception $e) {
            return redirect()->route('home-page.index')
                           ->with('error', 'Failed to update featured unit: ' . $e->getMessage());
        }
    }

    public function destroy(HomeFeaturedUnit $featuredUnit)
    {
        try {
            $featuredUnit->delete();
            
            // Reorder after delete
            GeneratorService::reorderAfterDelete(new HomeFeaturedUnit());

            return redirect()->route('home-page.index')
                           ->with('success', 'Featured unit removed successfully');

        } catch (\Exception $e) {
            return redirect()->route('home-page.index')
                           ->with('error', 'Failed to remove featured unit: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:home_featured_units,id',
            'orders.*.order' => 'required|integer|min:1'
        ]);

        try {
            foreach ($request->orders as $orderData) {
                HomeFeaturedUnit::where('id', $orderData['id'])
                                ->update(['order' => $orderData['order']]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}