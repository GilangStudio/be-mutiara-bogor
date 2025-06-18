<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Project;
use App\Models\UnitGallery;
use App\Models\UnitSpecification;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Services\GeneratorService;

class UnitController extends Controller
{
    public function index(Project $project)
    {
        $units = Unit::where('project_id', $project->id)
                    ->with('project')
                    ->ordered()
                    ->get();
                    
        return view('pages.units.index', compact('units', 'project'));
    }

    public function create(Project $project)
    {
        return view('pages.units.create', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'carports' => 'nullable|integer|min:0',
            'land_area' => 'nullable|string|max:50',
            'building_area' => 'nullable|string|max:50',
            'main_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'banner_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'floor_plan_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'gallery_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'gallery_alt_texts.*' => 'nullable|string|max:255',
            'gallery_captions.*' => 'nullable|string|max:500',
            'spec_names.*' => 'nullable|string|max:255',
            'spec_values.*' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Unit name is required',
            'name.max' => 'Unit name cannot exceed 255 characters',
            'short_description.max' => 'Short description cannot exceed 500 characters',
            'meta_title.max' => 'Meta title maksimal 255 karakter',
            'meta_description.max' => 'Meta description maksimal 500 karakter',
            'meta_keywords.max' => 'Meta keywords maksimal 255 karakter',
            'bedrooms.integer' => 'Bedrooms must be a number',
            'bathrooms.integer' => 'Bathrooms must be a number',
            'carports.integer' => 'Carports must be a number',
            'main_image.required' => 'Main image is required',
            'main_image.image' => 'File must be an image',
            'main_image.max' => 'Main image size cannot exceed 5MB',
            'banner_image.required' => 'Banner image is required',
            'banner_image.image' => 'File must be an image',
            'banner_image.max' => 'Banner image size cannot exceed 5MB',
            'floor_plan_image.image' => 'File must be an image',
            'floor_plan_image.max' => 'Floor plan image size cannot exceed 5MB',
            'gallery_images.*.image' => 'Gallery files must be images',
            'gallery_images.*.max' => 'Gallery image size cannot exceed 5MB',
        ]);

        try {
            // Generate slug and order
            $slug = GeneratorService::generateSlug(new Unit(), $request->name);
            $order = Unit::where('project_id', $project->id)->max('order') ?? 0;

            // Upload images
            $mainImagePath = ImageService::uploadAndCompress(
                $request->file('main_image'),
                'units/main',
                85,
                1200
            );

            $bannerPath = ImageService::uploadAndCompress(
                $request->file('banner_image'),
                'units/banner',
                85,
                1920
            );

            $floorPlanPath = null;
            if ($request->hasFile('floor_plan_image')) {
                $floorPlanPath = ImageService::uploadAndCompress(
                    $request->file('floor_plan_image'),
                    'units/floor-plan',
                    85,
                    1200
                );
            }

            // Create unit
            $unit = Unit::create([
                'project_id' => $project->id,
                'name' => $request->name,
                'slug' => $slug,
                'short_description' => $request->short_description,
                'description' => $request->description,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
                'bedrooms' => $request->bedrooms,
                'bathrooms' => $request->bathrooms,
                'carports' => $request->carports,
                'land_area' => $request->land_area,
                'building_area' => $request->building_area,
                'main_image_path' => $mainImagePath,
                'banner_path' => $bannerPath,
                'floor_plan_image_path' => $floorPlanPath,
                'order' => $order + 1,
                'is_active' => $request->has('status')
            ]);

            // Upload gallery images
            if ($request->hasFile('gallery_images')) {
                $this->uploadGalleryImages($unit, $request);
            }

            // Save specifications
            if ($request->has('spec_names')) {
                $this->saveSpecifications($unit, $request);
            }

            return redirect()->route('development.unit.index', $project)
                           ->with('success', 'Unit created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to create unit: ' . $e->getMessage());
        }
    }

    public function edit(Project $project, Unit $unit)
    {
        $unit->load(['galleries' => function($query) {
            $query->ordered();
        }, 'specifications']);
        
        return view('pages.units.edit', compact('project', 'unit'));
    }

    public function update(Request $request, Project $project, Unit $unit)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'carports' => 'nullable|integer|min:0',
            'land_area' => 'nullable|string|max:50',
            'building_area' => 'nullable|string|max:50',
            'main_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'banner_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'floor_plan_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'gallery_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'gallery_alt_texts.*' => 'nullable|string|max:255',
            'gallery_captions.*' => 'nullable|string|max:500',
            'existing_gallery_alt_texts.*' => 'nullable|string|max:255',
            'existing_gallery_captions.*' => 'nullable|string|max:500',
            'spec_names.*' => 'nullable|string|max:255',
            'spec_values.*' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Unit name is required',
            'name.max' => 'Unit name cannot exceed 255 characters',
            'short_description.max' => 'Short description cannot exceed 500 characters',
            'meta_title.max' => 'Meta title cannot exceed 255 characters',
            'meta_description.max' => 'Meta description cannot exceed 500 characters',
            'meta_keywords.max' => 'Meta keywords cannot exceed 255 characters',
            'bedrooms.integer' => 'Bedrooms must be a number',
            'bathrooms.integer' => 'Bathrooms must be a number',
            'carports.integer' => 'Carports must be a number',
            'main_image.image' => 'File must be an image',
            'main_image.max' => 'Main image size cannot exceed 5MB',
            'banner_image.image' => 'File must be an image',
            'banner_image.max' => 'Banner image size cannot exceed 5MB',
            'floor_plan_image.image' => 'File must be an image',
            'floor_plan_image.max' => 'Floor plan image size cannot exceed 5MB',
            'gallery_images.*.image' => 'Gallery files must be images',
            'gallery_images.*.max' => 'Gallery image size cannot exceed 5MB',
        ]);

        try {
            // Generate slug if name changed
            $slug = GeneratorService::generateSlug(new Unit(), $request->name, $unit->id);

            // Update images
            $mainImagePath = ImageService::updateImage(
                $request->file('main_image'),
                $unit->main_image_path,
                'units/main',
                85,
                1200
            );

            $bannerPath = ImageService::updateImage(
                $request->file('banner_image'),
                $unit->banner_path,
                'units/banner',
                85,
                1920
            );

            $floorPlanPath = ImageService::updateImage(
                $request->file('floor_plan_image'),
                $unit->floor_plan_image_path,
                'units/floor-plan',
                85,
                1200
            );

            // Update unit
            $unit->update([
                'name' => $request->name,
                'slug' => $slug,
                'short_description' => $request->short_description,
                'description' => $request->description,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
                'bedrooms' => $request->bedrooms,
                'bathrooms' => $request->bathrooms,
                'carports' => $request->carports,
                'land_area' => $request->land_area,
                'building_area' => $request->building_area,
                'main_image_path' => $mainImagePath,
                'banner_path' => $bannerPath,
                'floor_plan_image_path' => $floorPlanPath,
                'is_active' => $request->has('status')
            ]);

            // Handle gallery images update
            $this->updateGalleryImages($unit, $request);

            // Upload new gallery images
            if ($request->hasFile('gallery_images')) {
                $this->uploadGalleryImages($unit, $request);
            }

            // Update specifications
            $this->updateSpecifications($unit, $request);

            // return redirect()->route('development.unit.index', $project)
            //                ->with('success', 'Unit updated successfully');

            return redirect()->back()
                           ->with('success', 'Unit updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to update unit: ' . $e->getMessage());
        }
    }

    public function destroy(Project $project, Unit $unit)
    {
        try {
            // Delete images
            ImageService::deleteFiles([
                $unit->main_image_path,
                $unit->banner_path,
                $unit->floor_plan_image_path
            ]);

            // Delete unit (cascade delete will handle galleries and specifications)
            $unit->delete();

            // Reorder remaining units
            $this->reorderUnits($project);

            return redirect()->route('development.unit.index', $project)
                           ->with('success', 'Unit deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('development.unit.index', $project)
                           ->with('error', 'Failed to delete unit: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request, Project $project)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:units,id',
            'orders.*.order' => 'required|integer|min:1'
        ]);

        try {
            foreach ($request->orders as $orderData) {
                Unit::where('id', $orderData['id'])
                    ->where('project_id', $project->id)
                    ->update(['order' => $orderData['order']]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function reorderGallery(Request $request, Project $project)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:unit_galleries,id',
            'orders.*.order' => 'required|integer|min:1'
        ]);

        try {
            foreach ($request->orders as $orderData) {
                UnitGallery::where('id', $orderData['id'])
                          ->update(['order' => $orderData['order']]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function uploadGalleryImages(Unit $unit, Request $request)
    {
        $galleryImages = $request->file('gallery_images');
        $altTexts = $request->input('gallery_alt_texts', []);
        $captions = $request->input('gallery_captions', []);
        
        $maxOrder = $unit->galleries()->max('order') ?? 0;

        foreach ($galleryImages as $index => $file) {
            if ($file && $file->isValid()) {
                $imagePath = ImageService::uploadAndCompress(
                    $file,
                    'units/gallery',
                    85,
                    1200
                );

                UnitGallery::create([
                    'unit_id' => $unit->id,
                    'image_path' => $imagePath,
                    'alt_text' => $altTexts[$index] ?? null,
                    'caption' => $captions[$index] ?? null,
                    'order' => $maxOrder + $index + 1
                ]);
            }
        }
    }

    private function updateGalleryImages(Unit $unit, Request $request)
    {
        $existingAltTexts = $request->input('existing_gallery_alt_texts', []);
        $existingCaptions = $request->input('existing_gallery_captions', []);
        $deleteImages = $request->input('delete_gallery_images', []);

        // Delete selected images
        if (!empty($deleteImages)) {
            $imagesToDelete = UnitGallery::whereIn('id', $deleteImages)->get();
            foreach ($imagesToDelete as $image) {
                ImageService::deleteFile($image->image_path);
                $image->delete();
            }
        }

        // Update existing images
        foreach ($existingAltTexts as $imageId => $altText) {
            $image = UnitGallery::find($imageId);
            if ($image && $image->unit_id == $unit->id) {
                $image->update([
                    'alt_text' => $altText,
                    'caption' => $existingCaptions[$imageId] ?? $image->caption
                ]);
            }
        }
    }

    private function saveSpecifications(Unit $unit, Request $request)
    {
        $specNames = $request->input('spec_names', []);
        $specValues = $request->input('spec_values', []);

        foreach ($specNames as $index => $name) {
            if (!empty($name) && !empty($specValues[$index])) {
                UnitSpecification::create([
                    'unit_id' => $unit->id,
                    'name' => $name,
                    'value' => $specValues[$index]
                ]);
            }
        }
    }

    private function updateSpecifications(Unit $unit, Request $request)
    {
        // Delete old specifications
        $unit->specifications()->delete();

        // Save new specifications
        $this->saveSpecifications($unit, $request);
    }

    private function reorderUnits(Project $project)
    {
        $units = Unit::where('project_id', $project->id)->orderBy('order')->get();
        
        foreach ($units as $index => $unit) {
            $unit->update(['order' => $index + 1]);
        }
    }
}