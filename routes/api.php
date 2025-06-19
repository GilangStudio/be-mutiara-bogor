<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LandingPageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Routes for landing page API endpoints.
| All routes are public (no authentication required).
|
*/

// Landing Page API Routes - Public Access
Route::prefix('v1')->group(function () {
    
    // Company Profile
    Route::get('/company-profile', [LandingPageController::class, 'getCompanyProfile']);
    
    // Home Page
    Route::get('/home-page', [LandingPageController::class, 'getHomePage']);

    // Concept Page
    Route::get('/accessibility-page', [LandingPageController::class, 'getAccessibilityPage']);
    
    // Concept Page
    Route::get('/concept-page', [LandingPageController::class, 'getConceptPage']);
    
    // Development/Projects
    Route::prefix('development')->group(function () {
        // Project Categories
        Route::get('/categories', [LandingPageController::class, 'getProjectCategories']);
        
        // Projects
        Route::get('/projects', [LandingPageController::class, 'getProjects']);
        Route::get('/projects/{slug}', [LandingPageController::class, 'getProjectDetail']);
        
        // Units
        Route::get('/projects/{projectSlug}/units/{unitSlug}', [LandingPageController::class, 'getUnitDetail']);
    });
    
    // News
    Route::prefix('news')->group(function () {
        Route::get('/', [LandingPageController::class, 'getNews']);
        Route::get('/{slug}', [LandingPageController::class, 'getNewsDetail']);
    });
    
    // Accessibilities
    Route::get('/accessibilities', [LandingPageController::class, 'getAccessibilities']);
    
    // FAQs
    Route::get('/faqs', [LandingPageController::class, 'getFaqs']);
    
    // Contact Form - Only POST method for submitting
    Route::post('/contact', [LandingPageController::class, 'submitContact']);
    
});