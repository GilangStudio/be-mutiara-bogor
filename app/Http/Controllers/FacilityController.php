<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Services\GeneratorService;

class FacilityController extends Controller
{
    public function index()
    {
        $facilities = Facility::orderBy('order', 'asc')
                            ->orderBy('created_at', 'desc')
                            ->get();
        
        return view('pages.facilities.index', compact('facilities'));
    }

    public function create()
    {
        return view('pages.facilities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120', // 5MB
        ], [
            'name.required' => 'Facility name is required',
            'name.max' => 'Facility name cannot exceed 255 characters',
            'image.required' => 'Facility image is required',
            'image.image' => 'File must be an image',
            'image.max' => 'Image size cannot exceed 5MB',
        ]);

        try {
            // Generate order
            $order = GeneratorService::generateOrder(new Facility());

            // Upload image
            $imagePath = ImageService::uploadAndCompress(
                $request->file('image'), 
                'facilities', 
                85, 
                1200
            );

            Facility::create([
                'name' => $request->name,
                'description' => $request->description,
                'image_path' => $imagePath,
                'order' => $order,
                'is_active' => $request->has('status'),
            ]);

            return redirect()->route('facilities.index')
                           ->with('success', 'Facility created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to create facility: ' . $e->getMessage());
        }
    }

    public function edit(Facility $facility)
    {
        return view('pages.facilities.edit', compact('facility'));
    }

    public function update(Request $request, Facility $facility)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'name.required' => 'Facility name is required',
            'name.max' => 'Facility name cannot exceed 255 characters',
            'image.image' => 'File must be an image',
            'image.max' => 'Image size cannot exceed 5MB',
        ]);

        try {
            // Update image if new file provided
            $imagePath = ImageService::updateImage(
                $request->file('image'),
                $facility->image_path,
                'facilities',
                85,
                1200
            );

            $facility->update([
                'name' => $request->name,
                'description' => $request->description,
                'image_path' => $imagePath,
                'is_active' => $request->has('status'),
            ]);

            return redirect()->back()
                           ->with('success', 'Facility updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to update facility: ' . $e->getMessage());
        }
    }

    public function destroy(Facility $facility)
    {
        try {
            // Delete image if exists
            if ($facility->image_path) {
                ImageService::deleteFile($facility->image_path);
            }

            $facility->delete();
            
            // Reorder after delete
            GeneratorService::reorderAfterDelete(new Facility());

            return redirect()->route('facilities.index')
                           ->with('success', 'Facility deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('facilities.index')
                           ->with('error', 'Failed to delete facility: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:facilities,id',
            'orders.*.order' => 'required|integer|min:1'
        ]);

        try {
            foreach ($request->orders as $orderData) {
                Facility::where('id', $orderData['id'])
                       ->update(['order' => $orderData['order']]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}