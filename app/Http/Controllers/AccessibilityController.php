<?php

namespace App\Http\Controllers;

use App\Models\Accessibility;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Services\GeneratorService;

class AccessibilityController extends Controller
{
    public function index()
    {
        $accessibilities = Accessibility::orderBy('order', 'asc')
                            ->orderBy('created_at', 'desc')
                            ->get();
        
        return view('pages.accessibility.index', compact('accessibilities'));
    }

    public function create()
    {
        return view('pages.accessibility.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120', // 5MB
        ], [
            'name.required' => 'Accessibility name is required',
            'name.max' => 'Accessibility name cannot exceed 255 characters',
            'image.required' => 'Accessibility image is required',
            'image.image' => 'File must be an image',
            'image.max' => 'Image size cannot exceed 5MB',
        ]);

        try {
            // Generate order
            $order = GeneratorService::generateOrder(new Accessibility());

            // Upload image
            $imagePath = ImageService::uploadAndCompress(
                $request->file('image'), 
                'accessibility', 
                85, 
                1200
            );

            Accessibility::create([
                'name' => $request->name,
                'description' => $request->description,
                'image_path' => $imagePath,
                'order' => $order,
                'is_active' => $request->has('status'),
            ]);

            return redirect()->route('accessibilities.index')
                           ->with('success', 'Accessibility created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to create accessibility: ' . $e->getMessage());
        }
    }

    public function edit(Accessibility $accessibility)
    {
        return view('pages.accessibility.edit', compact('accessibility'));
    }

    public function update(Request $request, Accessibility $accessibility)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'name.required' => 'Accessibility name is required',
            'name.max' => 'Accessibility name cannot exceed 255 characters',
            'image.image' => 'File must be an image',
            'image.max' => 'Image size cannot exceed 5MB',
        ]);

        try {
            // Update image if new file provided
            $imagePath = ImageService::updateImage(
                $request->file('image'),
                $accessibility->image_path,
                'accessibility',
                85,
                1200
            );

            $accessibility->update([
                'name' => $request->name,
                'description' => $request->description,
                'image_path' => $imagePath,
                'is_active' => $request->has('status'),
            ]);

            return redirect()->back()
                           ->with('success', 'Accessibility updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to update accessibility: ' . $e->getMessage());
        }
    }

    public function destroy(Accessibility $accessibility)
    {
        try {
            // Delete image if exists
            if ($accessibility->image_path) {
                ImageService::deleteFile($accessibility->image_path);
            }

            $accessibility->delete();
            
            // Reorder after delete
            GeneratorService::reorderAfterDelete(new Accessibility());

            return redirect()->route('accessibilities.index')
                           ->with('success', 'Accessibility deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('accessibilities.index')
                           ->with('error', 'Failed to delete accessibility: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:accessibilities,id',
            'orders.*.order' => 'required|integer|min:1'
        ]);

        try {
            foreach ($request->orders as $orderData) {
                Accessibility::where('id', $orderData['id'])
                       ->update(['order' => $orderData['order']]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}