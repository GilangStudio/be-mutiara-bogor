<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\FacilityImage;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Models\ProjectCategory;
use App\Services\GeneratorService;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('category')->withCount('units')->ordered()->get();
        return view('pages.projects.index', compact('projects'));
    }

    public function create()
    {
        $categories = ProjectCategory::active()->ordered()->get();
        return view('pages.projects.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:project_categories,id',
            'name' => 'required|string|max:255|unique:projects,name',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'main_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240', // 10MB
            'banner_type' => 'required|in:image,video',
            'banner_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'banner_video' => 'nullable|mimes:mp4,mov,avi|max:204800', // 200MB untuk video
            'logo_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // 2MB
            'siteplan_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'gallery_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'gallery_alt_texts.*' => 'nullable|string|max:255',
            'gallery_captions.*' => 'nullable|string|max:500',
            'facility_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'facility_titles.*' => 'nullable|string|max:255',
            'facility_descriptions.*' => 'nullable|string|max:500',
            'facility_alt_texts.*' => 'nullable|string|max:255',
        ], [
            'category_id.required' => 'Category is required',
            'category_id.exists' => 'Invalid category selected',
            'name.required' => 'Project name is required',
            'name.unique' => 'Project name already exists',
            'name.max' => 'Project name cannot exceed 255 characters',
            'short_description.max' => 'Short description cannot exceed 500 characters',
            'meta_title.max' => 'Meta title cannot exceed 255 characters',
            'meta_description.max' => 'Meta description cannot exceed 500 characters',
            'meta_keywords.max' => 'Meta keywords cannot exceed 255 characters',
            'main_image.required' => 'Main image is required',
            'main_image.image' => 'File must be an image',
            'main_image.max' => 'Main image size cannot exceed 10MB',
            'banner_type.required' => 'Banner type is required',
            'banner_type.in' => 'Banner type must be image or video',
            'banner_image.image' => 'Banner file must be an image',
            'banner_image.max' => 'Banner image size cannot exceed 10MB',
            'banner_video.mimes' => 'Banner video must be mp4, mov, or avi format',
            'banner_video.max' => 'Banner video size cannot exceed 50MB',
            'logo_image.image' => 'File must be an image',
            'logo_image.max' => 'Logo size cannot exceed 2MB',
            'siteplan_image.image' => 'File must be an image',
            'siteplan_image.max' => 'Siteplan size cannot exceed 10MB',
            'gallery_images.*.image' => 'Gallery files must be images',
            'gallery_images.*.max' => 'Gallery image size cannot exceed 10MB',
            'facility_images.*.image' => 'Facility files must be images',
            'facility_images.*.max' => 'Facility image size cannot exceed 10MB',
        ]);

        try {
            // Generate slug dan order
            $slug = GeneratorService::generateSlug(new Project(), $request->name);
            $order = GeneratorService::generateOrder(new Project());

            // Upload gambar dengan kompresi
            $mainImagePath = ImageService::uploadAndCompress(
                $request->file('main_image'), 
                'projects/main', 
                85, 
                1200
            );

            $bannerPath = null;
            $bannerVideoPath = null;
            
            if ($request->banner_type === 'image' && $request->hasFile('banner_image')) {
                $bannerPath = ImageService::uploadAndCompress(
                    $request->file('banner_image'), 
                    'projects/banner', 
                    85, 
                    1920
                );
            } elseif ($request->banner_type === 'video' && $request->hasFile('banner_video')) {
                $bannerVideoPath = $request->file('banner_video')->store('projects/banner', 'public');
            }

            $logoPath = null;
            if ($request->hasFile('logo_image')) {
                $logoPath = ImageService::uploadAndCompress(
                    $request->file('logo_image'), 
                    'projects/logo', 
                    90, 
                    400
                );
            }

            $siteplanPath = null;
            if ($request->hasFile('siteplan_image')) {
                $siteplanPath = ImageService::uploadAndCompress(
                    $request->file('siteplan_image'), 
                    'projects/siteplan', 
                    85, 
                    1920
                );
            }

            // Simpan project
            $project = Project::create([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'slug' => $slug,
                'short_description' => $request->short_description,
                'description' => $request->description,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
                'main_image_path' => $mainImagePath,
                'main_image_path' => $mainImagePath,
                'banner_type' => $request->banner_type,
                'banner_path' => $bannerPath,
                'banner_video_path' => $bannerVideoPath,
                'logo_path' => $logoPath,
                'siteplan_image_path' => $siteplanPath,
                'order' => $order,
                'is_active' => $request->has('status')
            ]);

            // Upload gallery images
            if ($request->hasFile('gallery_images')) {
                $this->uploadGalleryImages($project, $request);
            }

            // Upload facility images
            if ($request->hasFile('facility_images')) {
                $this->uploadFacilityImages($project, $request);
            }

            return redirect()->route('development.project.index')
                           ->with('success', 'Project created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to create project: ' . $e->getMessage());
        }
    }

    public function show(Project $project)
    {
        $project->load([
            'category', 
            'images' => function($query) {
                $query->ordered(); 
            },
            'facilityImages' => function($query) {
                $query->ordered();
            }
        ]);
    
        return view('pages.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $categories = ProjectCategory::active()->ordered()->get();
        $project->load([
            'images' => function($query) {
                $query->ordered(); 
            },
            'facilityImages' => function($query) {
                $query->ordered();
            }
        ]);
        return view('pages.projects.edit', compact('project', 'categories'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'category_id' => 'required|exists:project_categories,id',
            'name' => 'required|string|max:255|unique:projects,name,' . $project->id,
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'main_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'banner_type' => 'required|in:image,video',
            'banner_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'banner_video' => 'nullable|mimes:mp4,mov,avi|max:204800',
            'logo_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'siteplan_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'gallery_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'gallery_alt_texts.*' => 'nullable|string|max:255',
            'gallery_captions.*' => 'nullable|string|max:500',
            'existing_gallery_alt_texts.*' => 'nullable|string|max:255',
            'existing_gallery_captions.*' => 'nullable|string|max:500',
            'facility_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'facility_titles.*' => 'nullable|string|max:255',
            'facility_descriptions.*' => 'nullable|string|max:500',
            'facility_alt_texts.*' => 'nullable|string|max:255',
            'existing_facility_titles.*' => 'nullable|string|max:255',
            'existing_facility_descriptions.*' => 'nullable|string|max:500',
            'existing_facility_alt_texts.*' => 'nullable|string|max:255',
        ], [
            'category_id.required' => 'Category is required',
            'category_id.exists' => 'Invalid category selected',
            'name.required' => 'Project name is required',
            'name.unique' => 'Project name already exists',
            'name.max' => 'Project name cannot exceed 255 characters',
            'short_description.max' => 'Short description cannot exceed 500 characters',
            'meta_title.max' => 'Meta title cannot exceed 255 characters',
            'meta_description.max' => 'Meta description cannot exceed 500 characters',
            'meta_keywords.max' => 'Meta keywords cannot exceed 255 characters',
            'main_image.image' => 'File must be an image',
            'main_image.max' => 'Main image size cannot exceed 10MB',
            'banner_image.image' => 'File must be an image',
            'banner_image.max' => 'Banner image size cannot exceed 10MB',
            'banner_type.required' => 'Banner type is required',
            'banner_type.in' => 'Banner type must be image or video',
            'banner_video.mimes' => 'Banner video must be mp4, mov, or avi format',
            'banner_video.max' => 'Banner video size cannot exceed 50MB',
            'logo_image.image' => 'File must be an image',
            'logo_image.max' => 'Logo size cannot exceed 2MB',
            'siteplan_image.image' => 'File must be an image',
            'siteplan_image.max' => 'Siteplan size cannot exceed 10MB',
            'gallery_images.*.image' => 'Gallery files must be images',
            'gallery_images.*.max' => 'Gallery image size cannot exceed 10MB',
            'facility_images.*.image' => 'Facility files must be images',
            'facility_images.*.max' => 'Facility image size cannot exceed 10MB',
        ]);

        try {
            // Generate slug jika nama berubah
            $slug = GeneratorService::generateSlug(new Project(), $request->name, $project->id);

            // Update gambar jika ada file baru
            $mainImagePath = ImageService::updateImage(
                $request->file('main_image'),
                $project->main_image_path,
                'projects/main',
                85,
                1200
            );

            $bannerPath = $project->banner_path;
            $bannerVideoPath = $project->banner_video_path;
            
            if ($request->banner_type === 'image') {
                // Jika switch ke image atau update image
                if ($request->hasFile('banner_image')) {
                    // Delete old video jika ada
                    if ($bannerVideoPath) {
                        ImageService::deleteFile($bannerVideoPath);
                        $bannerVideoPath = null;
                    }
                    // Update image
                    $bannerPath = ImageService::updateImage(
                        $request->file('banner_image'),
                        $bannerPath,
                        'projects/banner',
                        85,
                        1920
                    );
                }
            } elseif ($request->banner_type === 'video') {
                // Jika switch ke video atau update video
                if ($request->hasFile('banner_video')) {
                    // Delete old image jika ada
                    if ($bannerPath) {
                        ImageService::deleteFile($bannerPath);
                        $bannerPath = null;
                    }
                    // Delete old video jika ada
                    if ($bannerVideoPath) {
                        ImageService::deleteFile($bannerVideoPath);
                    }
                    // Upload new video
                    $bannerVideoPath = $request->file('banner_video')->store('projects/banner', 'public');
                }
            }

            $logoPath = ImageService::updateImage(
                $request->file('logo_image'),
                $project->logo_path,
                'projects/logo',
                90,
                400
            );

            $siteplanPath = ImageService::updateImage(
                $request->file('siteplan_image'),
                $project->siteplan_image_path,
                'projects/siteplan',
                85,
                1920
            );

            // Update project
            $project->update([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'slug' => $slug,
                'short_description' => $request->short_description,
                'description' => $request->description,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
                'main_image_path' => $mainImagePath,
                'banner_type' => $request->banner_type,
                'banner_path' => $bannerPath,
                'banner_video_path' => $bannerVideoPath,
                'logo_path' => $logoPath,
                'siteplan_image_path' => $siteplanPath,
                'is_active' => $request->has('status')
            ]);

            // Handle gallery images update
            $this->updateGalleryImages($project, $request);

            // Upload new gallery images
            if ($request->hasFile('gallery_images')) {
                $this->uploadGalleryImages($project, $request);
            }

            // Handle facility images update
            $this->updateFacilityImages($project, $request);

            // Upload new facility images
            if ($request->hasFile('facility_images')) {
                $this->uploadFacilityImages($project, $request);
            }

            return redirect()->back()
                           ->with('success', 'Project updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to update project: ' . $e->getMessage());
        }
    }

    public function destroy(Project $project)
    {
        try {
            // Hapus semua gambar
            ImageService::deleteFiles([
                $project->main_image_path,
                $project->banner_path,
                $project->banner_video_path,
                $project->logo_path,
                $project->siteplan_image_path
            ]);

            // Hapus project
            $project->delete();
            
            // Reorder setelah delete
            GeneratorService::reorderAfterDelete(new Project());

            return redirect()->route('development.project.index')
                           ->with('success', 'Project deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('development.project.index')
                           ->with('error', 'Failed to delete project: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:projects,id',
            'orders.*.order' => 'required|integer|min:1'
        ]);

        try {
            foreach ($request->orders as $orderData) {
                Project::where('id', $orderData['id'])
                       ->update(['order' => $orderData['order']]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Reorder gallery images
     */
    public function reorderGallery(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:project_images,id',
            'orders.*.order' => 'required|integer|min:1'
        ]);

        try {
            foreach ($request->orders as $orderData) {
                ProjectImage::where('id', $orderData['id'])
                        ->update(['order' => $orderData['order']]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Reorder facility images
     */
    public function reorderFacility(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:facility_images,id',
            'orders.*.order' => 'required|integer|min:1'
        ]);

        try {
            foreach ($request->orders as $orderData) {
                FacilityImage::where('id', $orderData['id'])
                        ->update(['order' => $orderData['order']]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Upload gallery images
     */
    private function uploadGalleryImages(Project $project, Request $request)
    {
        $galleryImages = $request->file('gallery_images');
        $altTexts = $request->input('gallery_alt_texts', []);
        $captions = $request->input('gallery_captions', []);
        
        // Get current max order
        $maxOrder = $project->images()->max('order') ?? 0;

        foreach ($galleryImages as $index => $file) {
            if ($file && $file->isValid()) {
                $imagePath = ImageService::uploadAndCompress(
                    $file,
                    'projects/gallery',
                    85,
                    1200
                );

                ProjectImage::create([
                    'project_id' => $project->id,
                    'image_path' => $imagePath,
                    'alt_text' => $altTexts[$index] ?? null,
                    'caption' => $captions[$index] ?? null,
                    'order' => $maxOrder + $index + 1
                ]);
            }
        }
    }

    /**
     * Upload facility images
     */
    private function uploadFacilityImages(Project $project, Request $request)
    {
        $facilityImages = $request->file('facility_images');
        $titles = $request->input('facility_titles', []);
        $descriptions = $request->input('facility_descriptions', []);
        $altTexts = $request->input('facility_alt_texts', []);
        
        // Get current max order
        $maxOrder = $project->facilityImages()->max('order') ?? 0;

        foreach ($facilityImages as $index => $file) {
            if ($file && $file->isValid()) {
                $imagePath = ImageService::uploadAndCompress(
                    $file,
                    'projects/facilities',
                    85,
                    1200
                );

                FacilityImage::create([
                    'project_id' => $project->id,
                    'title' => $titles[$index] ?? 'Facility',
                    'description' => $descriptions[$index] ?? null,
                    'image_path' => $imagePath,
                    'alt_text' => $altTexts[$index] ?? null,
                    'order' => $maxOrder + $index + 1
                ]);
            }
        }
    }

    /**
     * Update existing gallery images
     */
    private function updateGalleryImages(Project $project, Request $request)
    {
        $existingAltTexts = $request->input('existing_gallery_alt_texts', []);
        $existingCaptions = $request->input('existing_gallery_captions', []);
        $deleteImages = $request->input('delete_gallery_images', []);

        // Delete selected images
        if (!empty($deleteImages)) {
            $imagesToDelete = ProjectImage::whereIn('id', $deleteImages)->get();
            foreach ($imagesToDelete as $image) {
                ImageService::deleteFile($image->image_path);
                $image->delete();
            }
        }

        // Update existing images alt text and captions
        foreach ($existingAltTexts as $imageId => $altText) {
            $image = ProjectImage::find($imageId);
            if ($image && $image->project_id == $project->id) {
                $image->update([
                    'alt_text' => $altText,
                    'caption' => $existingCaptions[$imageId] ?? $image->caption
                ]);
            }
        }
    }

    /**
     * Update existing facility images
     */
    private function updateFacilityImages(Project $project, Request $request)
    {
        $existingTitles = $request->input('existing_facility_titles', []);
        $existingDescriptions = $request->input('existing_facility_descriptions', []);
        $existingAltTexts = $request->input('existing_facility_alt_texts', []);
        $deleteFacilityImages = $request->input('delete_facility_images', []);

        // Delete selected facility images
        if (!empty($deleteFacilityImages)) {
            $imagesToDelete = FacilityImage::whereIn('id', $deleteFacilityImages)->get();
            foreach ($imagesToDelete as $image) {
                ImageService::deleteFile($image->image_path);
                $image->delete();
            }
        }

        // Update existing facility images
        foreach ($existingTitles as $imageId => $title) {
            $image = FacilityImage::find($imageId);
            if ($image && $image->project_id == $project->id) {
                $image->update([
                    'title' => $title,
                    'description' => $existingDescriptions[$imageId] ?? $image->description,
                    'alt_text' => $existingAltTexts[$imageId] ?? $image->alt_text
                ]);
            }
        }
    }
}