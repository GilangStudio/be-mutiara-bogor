<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LeadsController;
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

Route::post('/login', [AuthController::class, 'login']);

Route::post('/send_otp', [AuthController::class, 'send_otp']);
Route::post('/verify_otp', [AuthController::class, 'verify_otp']);

Route::post('/fcm_token', [AuthController::class, 'update_token']);
Route::post('/leads/submit', [LeadsController::class, 'store']);

// Route::group(['middleware' => 'auth:sanctum'], function () {
Route::group(['name' => 'auth:api'], function () {
    Route::post('/update_password', [AuthController::class, 'update_password']);
    Route::post('/reset_password', [AuthController::class, 'reset_password']);

    Route::get('/get_statistics', [LeadsController::class, 'get_statistics']);
    Route::post('/toggle_favorite', [LeadsController::class, 'toggle_favorite']);
    Route::put('/update_lead/{id}', [LeadsController::class, 'update_lead']);
    Route::post('/change_status/{id}', [LeadsController::class, 'change_status']);
    Route::get('/get_leads', [LeadsController::class, 'get_leads']);
    Route::get('/leads/{id}', [LeadsController::class, 'get_leads_detail']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/user/update', [AuthController::class, 'update']);
    Route::post('/update_token', [AuthController::class, 'update_token']);
});