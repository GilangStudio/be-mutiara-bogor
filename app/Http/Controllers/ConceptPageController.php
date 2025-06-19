<?php

namespace App\Http\Controllers;

use App\Models\ConceptPage;
use App\Models\ConceptPageSection;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Services\GeneratorService;

class ConceptPageController extends Controller
{
    public function index()
    {
        $conceptPage = ConceptPage::first();
        $sections = ConceptPageSection::ordered()->get();
        
        return view('pages.concept.index', compact('conceptPage', 'sections'));
    }

    public function store(Request $request)
    {
        // Check if concept page already exists
        $existingPage = ConceptPage::first();
        if ($existingPage) {
            return redirect()->route('concept.index')
                           ->with('error', 'Concept page already exists. You can only have one concept page.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'banner_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'banner_alt_text' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ], [
            'title.required' => 'Title is required',
            'title.max' => 'Title cannot exceed 255 characters',
            'description.required' => 'Description is required',
            'banner_image.required' => 'Banner image is required',
            'banner_image.image' => 'Banner must be an image file',
            'banner_image.max' => 'Banner image size cannot exceed 5MB',
            'banner_alt_text.max' => 'Banner alt text cannot exceed 255 characters',
            'meta_title.max' => 'Meta title cannot exceed 255 characters',
            'meta_description.max' => 'Meta description cannot exceed 500 characters',
            'meta_keywords.max' => 'Meta keywords cannot exceed 255 characters',
        ]);

        try {
            // Upload banner image
            $bannerPath = ImageService::uploadAndCompress(
                $request->file('banner_image'),
                'concept-page/banner',
                85,
                1920
            );

            ConceptPage::create([
                'title' => $request->title,
                'description' => $request->description,
                'banner_image_path' => $bannerPath,
                'banner_alt_text' => $request->banner_alt_text,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords
            ]);

            return redirect()->route('concept.index')
                           ->with('success', 'Concept page created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to create concept page: ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $conceptPage = ConceptPage::first();
        
        if (!$conceptPage) {
            return redirect()->route('concept.index')
                           ->with('error', 'Concept page not found');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'banner_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'banner_alt_text' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ], [
            'title.required' => 'Title is required',
            'title.max' => 'Title cannot exceed 255 characters',
            'meta_description.max' => 'Meta description cannot exceed 500 characters',
            'description.required' => 'Description is required',
            'banner_image.image' => 'Banner must be an image file',
            'banner_image.max' => 'Banner image size cannot exceed 5MB',
            'banner_alt_text.max' => 'Banner alt text cannot exceed 255 characters',
           'meta_title.max' => 'Meta title cannot exceed 255 characters',
           'meta_description.max' => 'Meta description cannot exceed 500 characters',
           'meta_keywords.max' => 'Meta keywords cannot exceed 255 characters',
        ]);

        try {
            // Update banner image if new file provided
            $bannerPath = ImageService::updateImage(
                $request->file('banner_image'),
                $conceptPage->banner_image_path,
                'concept-page/banner',
                85,
                1920
            );

            $conceptPage->update([
                'title' => $request->title,
                'description' => $request->description,
                'banner_image_path' => $bannerPath,
                'banner_alt_text' => $request->banner_alt_text,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords
            ]);

            return redirect()->route('concept.index')
                           ->with('success', 'Concept page updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to update concept page: ' . $e->getMessage());
        }
    }

    public function destroy()
    {
        $conceptPage = ConceptPage::first();
        
        if (!$conceptPage) {
            return redirect()->route('concept.index')
                           ->with('error', 'Concept page not found');
        }

        try {
            // Delete banner image
            ImageService::deleteFile($conceptPage->banner_image_path);

            $conceptPage->delete();

            return redirect()->route('concept.index')
                           ->with('success', 'Concept page deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('concept.index')
                           ->with('error', 'Failed to delete concept page: ' . $e->getMessage());
        }
    }
}