<?php

namespace App\Http\Controllers;

use App\Models\Accessibility;
use App\Models\AccessibilityPage;
use App\Models\AccessibilityPageBanner;
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
        
        // Get accessibility page settings
        $accessibilityPage = AccessibilityPage::with(['bannerImages' => function($query) {
            $query->ordered();
        }])->first();

        return view('pages.accessibility.index', compact('accessibilities', 'accessibilityPage'));
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

    public function updatePage(Request $request)
    {
        $accessibilityPage = AccessibilityPage::first();

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'banner_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'banner_alt_texts.*' => 'nullable|string|max:255',
            'banner_captions.*' => 'nullable|string|max:500',
            'existing_banner_alt_texts.*' => 'nullable|string|max:255',
            'existing_banner_captions.*' => 'nullable|string|max:500',
        ], [
            'title.required' => 'Title is required',
            'title.max' => 'Title cannot exceed 255 characters',
            'description.required' => 'Description is required',
            'meta_title.max' => 'Meta title cannot exceed 255 characters',
            'meta_description.max' => 'Meta description cannot exceed 500 characters',
            'meta_keywords.max' => 'Meta keywords cannot exceed 255 characters',
            'banner_images.*.image' => 'Banner file must be an image',
            'banner_images.*.max' => 'Banner image size cannot exceed 5MB',
        ]);

        try {
            if (!$accessibilityPage) {
                // Create new accessibility page
                $accessibilityPage = AccessibilityPage::create([
                    'title' => $request->title,
                    'description' => $request->description,
                    'meta_title' => $request->meta_title,
                    'meta_description' => $request->meta_description,
                    'meta_keywords' => $request->meta_keywords,
                ]);

                $message = 'Accessibility page created successfully';
            } else {
                // Update existing accessibility page
                $accessibilityPage->update([
                    'title' => $request->title,
                    'description' => $request->description,
                    'meta_title' => $request->meta_title,
                    'meta_description' => $request->meta_description,
                    'meta_keywords' => $request->meta_keywords,
                ]);

                // Handle existing banner images update
                $this->updateBannerImages($accessibilityPage, $request);

                $message = 'Accessibility page updated successfully';
            }

            // Upload new banner images (for both create and update)
            if ($request->hasFile('banner_images')) {
                $this->uploadBannerImages($accessibilityPage, $request);
            }

            return redirect()->route('accessibilities.index')
                           ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to save accessibility page: ' . $e->getMessage());
        }
    }

    public function reorderBanners(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:accessibility_page_banners,id',
            'orders.*.order' => 'required|integer|min:1'
        ]);

        try {
            foreach ($request->orders as $orderData) {
                AccessibilityPageBanner::where('id', $orderData['id'])
                          ->update(['order' => $orderData['order']]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function uploadBannerImages(AccessibilityPage $accessibilityPage, Request $request)
    {
        $bannerImages = $request->file('banner_images');
        $altTexts = $request->input('banner_alt_texts', []);
        $captions = $request->input('banner_captions', []);
        
        $maxOrder = $accessibilityPage->bannerImages()->max('order') ?? 0;

        foreach ($bannerImages as $index => $file) {
            if ($file && $file->isValid()) {
                $imagePath = ImageService::uploadAndCompress(
                    $file,
                    'accessibility/banners',
                    85,
                    1920
                );

                AccessibilityPageBanner::create([
                    'accessibility_page_id' => $accessibilityPage->id,
                    'image_path' => $imagePath,
                    'alt_text' => $altTexts[$index] ?? null,
                    'caption' => $captions[$index] ?? null,
                    'order' => $maxOrder + $index + 1
                ]);
            }
        }
    }

    private function updateBannerImages(AccessibilityPage $accessibilityPage, Request $request)
    {
        $existingAltTexts = $request->input('existing_banner_alt_texts', []);
        $existingCaptions = $request->input('existing_banner_captions', []);
        $deleteImages = $request->input('delete_banner_images', []);

        // Delete selected images
        if (!empty($deleteImages)) {
            $imagesToDelete = AccessibilityPageBanner::whereIn('id', $deleteImages)->get();
            foreach ($imagesToDelete as $image) {
                ImageService::deleteFile($image->image_path);
                $image->delete();
            }
        }

        // Update existing images
        foreach ($existingAltTexts as $imageId => $altText) {
            $image = AccessibilityPageBanner::find($imageId);
            if ($image && $image->accessibility_page_id == $accessibilityPage->id) {
                $image->update([
                    'alt_text' => $altText,
                    'caption' => $existingCaptions[$imageId] ?? $image->caption
                ]);
            }
        }
    }
}