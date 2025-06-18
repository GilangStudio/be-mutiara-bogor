<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use App\Models\SocialMedia;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Services\GeneratorService;

class CompanyProfileController extends Controller
{
    public function index()
    {
        $profile = CompanyProfile::first();
        $socialMedias = SocialMedia::ordered()->get();
        
        return view('pages.company-profile.index', compact('profile', 'socialMedias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pt_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'address' => 'required|string',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'map_embed' => 'required|string',
            'logo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'pt_name.required' => 'PT Name is required',
            'company_name.required' => 'Company Name is required',
            'address.required' => 'Address is required',
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email address',
            'phone.required' => 'Phone is required',
            'map_embed.required' => 'Map embed code is required',
            'logo.required' => 'Logo is required',
            'logo.image' => 'Logo must be an image',
            'logo.max' => 'Logo size cannot exceed 2MB',
        ]);

        try {
            // Upload logo
            $logoPath = ImageService::uploadAndCompress(
                $request->file('logo'),
                'company/logo',
                90,
                400
            );

            CompanyProfile::create([
                'pt_name' => $request->pt_name,
                'company_name' => $request->company_name,
                'address' => $request->address,
                'email' => $request->email,
                'phone' => $request->phone,
                'map_embed' => $request->map_embed,
                'logo_path' => $logoPath,
            ]);

            return redirect()->route('company-profile.index')
                           ->with('success', 'Company profile created successfully');

        } catch (\Exception $e) {
            return redirect()->route('company-profile.index')
                           ->with('error', 'Failed to create company profile: ' . $e->getMessage());
        }
    }

    public function update(Request $request, CompanyProfile $profile)
    {
        $request->validate([
            'pt_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'address' => 'required|string',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'map_embed' => 'required|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'pt_name.required' => 'PT Name is required',
            'company_name.required' => 'Company Name is required',
            'address.required' => 'Address is required',
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email address',
            'phone.required' => 'Phone is required',
            'map_embed.required' => 'Map embed code is required',
            'logo.image' => 'Logo must be an image',
            'logo.max' => 'Logo size cannot exceed 2MB',
        ]);

        try {
            // Update logo if new file uploaded
            $logoPath = ImageService::updateImage(
                $request->file('logo'),
                $profile->logo_path,
                'company/logo',
                90,
                400
            );

            $profile->update([
                'pt_name' => $request->pt_name,
                'company_name' => $request->company_name,
                'address' => $request->address,
                'email' => $request->email,
                'phone' => $request->phone,
                'map_embed' => $request->map_embed,
                'logo_path' => $logoPath,
            ]);

            return redirect()->route('company-profile.index')
                           ->with('success', 'Company profile updated successfully');

        } catch (\Exception $e) {
            return redirect()->route('company-profile.index')
                           ->with('error', 'Failed to update company profile: ' . $e->getMessage());
        }
    }

    public function storeSocialMedia(Request $request)
    {
        $request->validate([
            'platform' => 'required|string|max:50',
            'url' => 'required|url|max:255',
        ], [
            'platform.required' => 'Platform is required',
            'url.required' => 'URL is required',
            'url.url' => 'Please enter a valid URL',
        ]);

        try {
            $order = GeneratorService::generateOrder(new SocialMedia());

            SocialMedia::create([
                'platform' => $request->platform,
                'url' => $request->url,
                'order' => $order,
                'is_active' => $request->has('status')
            ]);

            return redirect()->route('company-profile.index')
                           ->with('success', 'Social media added successfully');

        } catch (\Exception $e) {
            return redirect()->route('company-profile.index')
                           ->with('error', 'Failed to add social media: ' . $e->getMessage());
        }
    }

    public function updateSocialMedia(Request $request, SocialMedia $socialMedia)
    {
        $request->validate([
            'platform' => 'required|string|max:50',
            'url' => 'required|url|max:255',
        ], [
            'platform.required' => 'Platform is required',
            'url.required' => 'URL is required',
            'url.url' => 'Please enter a valid URL',
        ]);

        try {
            $socialMedia->update([
                'platform' => $request->platform,
                'url' => $request->url,
                'is_active' => $request->has('status')
            ]);

            return redirect()->route('company-profile.index')
                           ->with('success', 'Social media updated successfully');

        } catch (\Exception $e) {
            return redirect()->route('company-profile.index')
                           ->with('error', 'Failed to update social media: ' . $e->getMessage());
        }
    }

    public function destroySocialMedia(SocialMedia $socialMedia)
    {
        try {
            $socialMedia->delete();
            
            // Reorder setelah delete
            GeneratorService::reorderAfterDelete(new SocialMedia());

            return redirect()->route('company-profile.index')
                           ->with('success', 'Social media deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('company-profile.index')
                           ->with('error', 'Failed to delete social media: ' . $e->getMessage());
        }
    }

    public function reorderSocialMedia(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:social_media,id',
            'orders.*.order' => 'required|integer|min:1'
        ]);

        try {
            foreach ($request->orders as $orderData) {
                SocialMedia::where('id', $orderData['id'])
                          ->update(['order' => $orderData['order']]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}