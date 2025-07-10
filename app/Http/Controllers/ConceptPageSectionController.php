<?php

namespace App\Http\Controllers;

use App\Models\ConceptPageSection;
use Illuminate\Http\Request;
use App\Services\ImageService;

class ConceptPageSectionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'required|string',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
            'image_alt_text' => 'nullable|string|max:255',
            'layout_type' => 'required|in:image_left,image_right',
        ], [
            'content.required' => 'Content is required',
            'image.required' => 'Image is required',
            'image.image' => 'File must be an image',
            'image.max' => 'Image size cannot exceed 10MB',
            'layout_type.required' => 'Layout type is required',
            'layout_type.in' => 'Layout type must be either image_left or image_right',
        ]);

        try {
            // Generate order
            $order = ConceptPageSection::max('order') ?? 0;

            // Upload image
            $imagePath = ImageService::uploadAndCompress(
                $request->file('image'),
                'concept-page/sections',
                85,
                1200
            );

            ConceptPageSection::create([
                'title' => $request->title,
                'content' => $request->content,
                'image_path' => $imagePath,
                'image_alt_text' => $request->image_alt_text,
                'layout_type' => $request->layout_type,
                'order' => $order + 1,
                'is_active' => $request->has('status')
            ]);

            return redirect()->route('concept.index')
                           ->with('success', 'Section added successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to add section: ' . $e->getMessage());
        }
    }

    public function update(Request $request, ConceptPageSection $section)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'image_alt_text' => 'nullable|string|max:255',
            'layout_type' => 'required|in:image_left,image_right',
        ], [
            'content.required' => 'Content is required',
            'image.image' => 'File must be an image',
            'image.max' => 'Image size cannot exceed 10MB',
            'layout_type.required' => 'Layout type is required',
            'layout_type.in' => 'Layout type must be either image_left or image_right',
        ]);

        try {
            // Update image if new file provided
            $imagePath = ImageService::updateImage(
                $request->file('image'),
                $section->image_path,
                'concept-page/sections',
                85,
                1200
            );

            $section->update([
                'title' => $request->title,
                'content' => $request->content,
                'image_path' => $imagePath,
                'image_alt_text' => $request->image_alt_text,
                'layout_type' => $request->layout_type,
                'is_active' => $request->has('status')
            ]);

            return redirect()->route('concept.index')
                           ->with('success', 'Section updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to update section: ' . $e->getMessage());
        }
    }

    public function destroy(ConceptPageSection $section)
    {
        try {
            // Delete image
            ImageService::deleteFile($section->image_path);

            $section->delete();
            
            // Reorder sections
            $this->reorderSections();

            return redirect()->route('concept.index')
                           ->with('success', 'Section deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('concept.index')
                           ->with('error', 'Failed to delete section: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:concept_page_sections,id',
            'orders.*.order' => 'required|integer|min:1'
        ]);

        try {
            foreach ($request->orders as $orderData) {
                ConceptPageSection::where('id', $orderData['id'])
                                 ->update(['order' => $orderData['order']]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function reorderSections()
    {
        $sections = ConceptPageSection::orderBy('order')->get();
        
        foreach ($sections as $index => $section) {
            $section->update(['order' => $index + 1]);
        }
    }
}