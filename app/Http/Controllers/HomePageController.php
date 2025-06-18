<?php

namespace App\Http\Controllers;

use App\Models\HomePageSetting;
use App\Models\HomeFeature;
use App\Models\HomeFeaturedUnit;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Services\GeneratorService;

class HomePageController extends Controller
{
    public function index()
    {
        $homePage = HomePageSetting::first();
        $features = HomeFeature::active()->ordered()->get();
        $featuredUnits = HomeFeaturedUnit::active()->ordered()->withUnitProject()->get();
        $availableUnits = Unit::active()->with('project')->ordered()->get();
        
        return view('pages.home-page.index', compact('homePage', 'features', 'featuredUnits', 'availableUnits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'banner_type' => 'required|in:image,video',
            'banner_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'banner_video' => 'nullable|mimes:mp4,mov,avi|max:51200', // 50MB
            'banner_alt_text' => 'nullable|string|max:255',
            'hero_title' => 'required|string|max:255',
            'hero_description' => 'required|string',
            'about_section_title' => 'nullable|string|max:255',
            'about_title' => 'required|string|max:255',
            'about_description' => 'required|string',
            'about_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'about_image_alt_text' => 'nullable|string|max:255',
            'about_link_text' => 'nullable|string|max:100',
            'about_link_url' => 'nullable|url|max:255',
            'features_section_title' => 'nullable|string|max:255',
            'features_title' => 'required|string|max:255',
            'features_description' => 'nullable|string',
            'features_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'features_image_alt_text' => 'nullable|string|max:255',
            'features_link_text' => 'nullable|string|max:100',
            'features_link_url' => 'nullable|url|max:255',
            'location_section_title' => 'nullable|string|max:255',
            'location_title' => 'required|string|max:255',
            'location_description' => 'required|string',
            'location_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'location_image_alt_text' => 'nullable|string|max:255',
            'location_link_text' => 'nullable|string|max:100',
            'location_link_url' => 'nullable|url|max:255',
        ], [
            'banner_type.required' => 'Banner type is required',
            'banner_image.image' => 'Banner must be an image file',
            'banner_image.max' => 'Banner image size cannot exceed 10MB',
            'banner_video.mimes' => 'Banner video must be mp4, mov, or avi format',
            'banner_video.max' => 'Banner video size cannot exceed 50MB',
            'hero_title.required' => 'Hero title is required',
            'hero_description.required' => 'Hero description is required',
            'about_section_title.required' => 'About section title is required',
            'about_title.required' => 'About section title is required',
            'about_description.required' => 'About section description is required',
            'about_image.required' => 'About section image is required',
            'about_image.image' => 'About image must be an image file',
            'about_image.max' => 'About image size cannot exceed 5MB',
            'features_title.required' => 'Features section title is required',
            'features_image.required' => 'Features section image is required',
            'features_image.image' => 'Features image must be an image file',
            'features_image.max' => 'Features image size cannot exceed 5MB',
            'location_title.required' => 'Location section title is required',
            'location_description.required' => 'Location section description is required',
            'location_image.required' => 'Location section image is required',
            'location_image.image' => 'Location image must be an image file',
            'location_image.max' => 'Location image size cannot exceed 5MB',
        ]);

        try {
            // Upload banner media
            $bannerImagePath = null;
            $bannerVideoPath = null;
            
            if ($request->banner_type === 'image' && $request->hasFile('banner_image')) {
                $bannerImagePath = ImageService::uploadAndCompress(
                    $request->file('banner_image'),
                    'home-page/banner',
                    85,
                    1920
                );
            } elseif ($request->banner_type === 'video' && $request->hasFile('banner_video')) {
                $bannerVideoPath = $request->file('banner_video')->store('home-page/banner', 'public');
            }

            // Upload other images
            $aboutImagePath = ImageService::uploadAndCompress(
                $request->file('about_image'),
                'home-page/about',
                85,
                1200
            );

            $featuresImagePath = ImageService::uploadAndCompress(
                $request->file('features_image'),
                'home-page/features',
                85,
                1200
            );

            $locationImagePath = ImageService::uploadAndCompress(
                $request->file('location_image'),
                'home-page/location',
                85,
                1200
            );

            HomePageSetting::create([
                'banner_type' => $request->banner_type,
                'banner_image_path' => $bannerImagePath,
                'banner_video_path' => $bannerVideoPath,
                'banner_alt_text' => $request->banner_alt_text,
                'hero_title' => $request->hero_title,
                'hero_description' => $request->hero_description,
                'about_section_title' => $request->about_section_title?: 'About The Development',
                'about_title' => $request->about_title,
                'about_description' => $request->about_description,
                'about_image_path' => $aboutImagePath,
                'about_image_alt_text' => $request->about_image_alt_text,
                'about_link_text' => $request->about_link_text ?: 'Discover More',
                'about_link_url' => $request->about_link_url,
                'features_section_title' => $request->features_section_title ?: 'Exclusive Features',
                'features_title' => $request->features_title,
                'features_description' => $request->features_description,
                'features_image_path' => $featuresImagePath,
                'features_image_alt_text' => $request->features_image_alt_text,
                'features_link_text' => $request->features_link_text ?: 'Learn More',
                'features_link_url' => $request->features_link_url,
                'location_section_title' => $request->location_section_title ?: 'Location & Accessibility',
                'location_title' => $request->location_title,
                'location_description' => $request->location_description,
                'location_image_path' => $locationImagePath,
                'location_image_alt_text' => $request->location_image_alt_text,
                'location_link_text' => $request->location_link_text ?: 'Get Direction',
                'location_link_url' => $request->location_link_url,
                'is_active' => $request->has('status')
            ]);

            return redirect()->route('home-page.index')
                           ->with('success', 'Home page settings created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to create home page settings: ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $homePage = HomePageSetting::first();
        
        if (!$homePage) {
            return redirect()->route('home-page.index')
                           ->with('error', 'Home page settings not found');
        }

        $request->validate([
            'banner_type' => 'required|in:image,video',
            'banner_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'banner_video' => 'nullable|mimes:mp4,mov,avi|max:51200',
            'banner_alt_text' => 'nullable|string|max:255',
            'hero_title' => 'required|string|max:255',
            'hero_description' => 'required|string',
            'about_section_title' => 'nullable|string|max:255',
            'about_title' => 'required|string|max:255',
            'about_description' => 'required|string',
            'about_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'about_image_alt_text' => 'nullable|string|max:255',
            'about_link_text' => 'nullable|string|max:100',
            'about_link_url' => 'nullable|url|max:255',
            'features_section_title' => 'nullable|string|max:255',
            'features_title' => 'required|string|max:255',
            'features_description' => 'nullable|string',
            'features_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'features_image_alt_text' => 'nullable|string|max:255',
            'features_link_text' => 'nullable|string|max:100',
            'features_link_url' => 'nullable|url|max:255',
            'location_section_title' => 'nullable|string|max:255',
            'location_title' => 'required|string|max:255',
            'location_description' => 'required|string',
            'location_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'location_image_alt_text' => 'nullable|string|max:255',
            'location_link_text' => 'nullable|string|max:100',
            'location_link_url' => 'nullable|url|max:255',
        ]);

        try {
            // Handle banner media update
            $bannerImagePath = $homePage->banner_image_path;
            $bannerVideoPath = $homePage->banner_video_path;
            
            if ($request->banner_type === 'image') {
                // If switching to image or updating image
                if ($request->hasFile('banner_image')) {
                    // Delete old video if exists
                    if ($bannerVideoPath) {
                        ImageService::deleteFile($bannerVideoPath);
                        $bannerVideoPath = null;
                    }
                    // Update image
                    $bannerImagePath = ImageService::updateImage(
                        $request->file('banner_image'),
                        $bannerImagePath,
                        'home-page/banner',
                        85,
                        1920
                    );
                }
            } elseif ($request->banner_type === 'video') {
                // If switching to video or updating video
                if ($request->hasFile('banner_video')) {
                    // Delete old image if exists
                    if ($bannerImagePath) {
                        ImageService::deleteFile($bannerImagePath);
                        $bannerImagePath = null;
                    }
                    // Delete old video if exists
                    if ($bannerVideoPath) {
                        ImageService::deleteFile($bannerVideoPath);
                    }
                    // Upload new video
                    $bannerVideoPath = $request->file('banner_video')->store('home-page/banner', 'public');
                }
            }

            // Update other images
            $aboutImagePath = ImageService::updateImage(
                $request->file('about_image'),
                $homePage->about_image_path,
                'home-page/about',
                85,
                1200
            );

            $featuresImagePath = ImageService::updateImage(
                $request->file('features_image'),
                $homePage->features_image_path,
                'home-page/features',
                85,
                1200
            );

            $locationImagePath = ImageService::updateImage(
                $request->file('location_image'),
                $homePage->location_image_path,
                'home-page/location',
                85,
                1200
            );

            $homePage->update([
                'banner_type' => $request->banner_type,
                'banner_image_path' => $bannerImagePath,
                'banner_video_path' => $bannerVideoPath,
                'banner_alt_text' => $request->banner_alt_text,
                'hero_title' => $request->hero_title,
                'hero_description' => $request->hero_description,
                'about_section_title' => $request->about_section_title?: 'About The Development',
                'about_title' => $request->about_title,
                'about_description' => $request->about_description,
                'about_image_path' => $aboutImagePath,
                'about_image_alt_text' => $request->about_image_alt_text,
                'about_link_text' => $request->about_link_text ?: 'Discover More',
                'about_link_url' => $request->about_link_url,
                'features_section_title' => $request->features_section_title ?: 'Exclusive Features',
                'features_title' => $request->features_title,
                'features_description' => $request->features_description,
                'features_image_path' => $featuresImagePath,
                'features_image_alt_text' => $request->features_image_alt_text,
                'features_link_text' => $request->features_link_text ?: 'Learn More',
                'features_link_url' => $request->features_link_url,
                'location_section_title' => $request->location_section_title ?: 'Location & Accessibility',
                'location_title' => $request->location_title,
                'location_description' => $request->location_description,
                'location_image_path' => $locationImagePath,
                'location_image_alt_text' => $request->location_image_alt_text,
                'location_link_text' => $request->location_link_text ?: 'Get Direction',
                'location_link_url' => $request->location_link_url,
                'is_active' => $request->has('status')
            ]);

            return redirect()->route('home-page.index')
                           ->with('success', 'Home page settings updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to update home page settings: ' . $e->getMessage());
        }
    }

    public function destroy()
    {
        $homePage = HomePageSetting::first();
        
        if (!$homePage) {
            return redirect()->route('home-page.index')
                           ->with('error', 'Home page settings not found');
        }

        try {
            // Delete all associated images and videos
            ImageService::deleteFiles([
                $homePage->banner_image_path,
                $homePage->banner_video_path,
                $homePage->about_image_path,
                $homePage->features_image_path,
                $homePage->location_image_path
            ]);

            $homePage->delete();

            return redirect()->route('home-page.index')
                           ->with('success', 'Home page settings deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('home-page.index')
                           ->with('error', 'Failed to delete home page settings: ' . $e->getMessage());
        }
    }
}