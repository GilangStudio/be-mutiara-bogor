<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FaqsController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\ConceptPageController;
use App\Http\Controllers\HomeFeatureController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\ProjectCategoryController;
use App\Http\Controllers\HomeFeaturedUnitController;
use App\Http\Controllers\ConceptPageSectionController;
use App\Http\Controllers\ContactMessageController; // Add this import

//route group guest
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginProcess']);
});

// Public Contact Form Route (dapat diakses tanpa login)
Route::get('/contact', [\App\Http\Controllers\ContactFormController::class, 'index'])->name('contact.form');
Route::post('/contact', [\App\Http\Controllers\ContactFormController::class, 'store'])->name('contact.store');

//route group auth
Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('pages.dashboard.index');
    })->name('dashboard');

    // Home Page Management Routes
    Route::prefix('home-page')->name('home-page.')->group(function () {
        Route::get('/', [HomePageController::class, 'index'])->name('index');
        Route::post('/', [HomePageController::class, 'store'])->name('store');
        Route::put('/', [HomePageController::class, 'update'])->name('update');
        Route::delete('/', [HomePageController::class, 'destroy'])->name('destroy');
        
        // Home Features Routes
        Route::prefix('features')->name('features.')->group(function () {
            Route::post('/', [HomeFeatureController::class, 'store'])->name('store');
            Route::put('/{feature}', [HomeFeatureController::class, 'update'])->name('update');
            Route::delete('/{feature}', [HomeFeatureController::class, 'destroy'])->name('destroy');
            Route::post('/reorder', [HomeFeatureController::class, 'reorder'])->name('reorder');
        });
        
        // Home Featured Units Routes
        Route::prefix('featured-units')->name('featured-units.')->group(function () {
            Route::post('/', [HomeFeaturedUnitController::class, 'store'])->name('store');
            Route::put('/{featuredUnit}', [HomeFeaturedUnitController::class, 'update'])->name('update');
            Route::delete('/{featuredUnit}', [HomeFeaturedUnitController::class, 'destroy'])->name('destroy');
            Route::post('/reorder', [HomeFeaturedUnitController::class, 'reorder'])->name('reorder');
        });
    });

    
    // Development Category Routes
    Route::prefix('development')->name('development.')->group(function () {
        Route::prefix('category')->name('category.')->group(function () {
            Route::get('/', [ProjectCategoryController::class, 'index'])->name('index');
            Route::post('/', [ProjectCategoryController::class, 'store'])->name('store');
            Route::put('/{category}', [ProjectCategoryController::class, 'update'])->name('update');
            Route::delete('/{category}', [ProjectCategoryController::class, 'destroy'])->name('destroy');
            Route::post('/reorder', [ProjectCategoryController::class, 'reorder'])->name('reorder');
        });
    });

    // Project Routes
    Route::prefix('development')->name('development.')->group(function () {
        Route::prefix('project')->name('project.')->group(function () {
            Route::get('/', [ProjectController::class, 'index'])->name('index');
            Route::get('/create', [ProjectController::class, 'create'])->name('create');
            Route::post('/', [ProjectController::class, 'store'])->name('store');
            Route::get('/{project}', [ProjectController::class, 'show'])->name('show');
            Route::get('/{project}/edit', [ProjectController::class, 'edit'])->name('edit');
            Route::put('/{project}', [ProjectController::class, 'update'])->name('update');
            Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('destroy');
            Route::post('/reorder', [ProjectController::class, 'reorder'])->name('reorder');
            Route::post('/gallery/reorder', [ProjectController::class, 'reorderGallery'])->name('gallery.reorder');
            Route::post('/facility/reorder', [ProjectController::class, 'reorderFacility'])->name('facility.reorder');
        });
    });

    // Unit Routes
    Route::prefix('development')->name('development.')->group(function () {
        Route::prefix('project/{project}/unit')->name('unit.')->group(function () {
            Route::get('/', [UnitController::class, 'index'])->name('index');
            Route::get('/create', [UnitController::class, 'create'])->name('create');
            Route::post('/', [UnitController::class, 'store'])->name('store');
            Route::get('/{unit}/edit', [UnitController::class, 'edit'])->name('edit');
            Route::put('/{unit}', [UnitController::class, 'update'])->name('update');
            Route::delete('/{unit}', [UnitController::class, 'destroy'])->name('destroy');
            Route::post('/reorder', [UnitController::class, 'reorder'])->name('reorder');
            Route::post('/gallery/reorder', [UnitController::class, 'reorderGallery'])->name('gallery.reorder');
        });
    });

    // News Routes
    Route::prefix('news')->name('news.')->group(function () {
        Route::get('/', [NewsController::class, 'index'])->name('index');
        Route::get('/create', [NewsController::class, 'create'])->name('create');
        Route::post('/', [NewsController::class, 'store'])->name('store');
        Route::get('/{news}/edit', [NewsController::class, 'edit'])->name('edit');
        Route::put('/{news}', [NewsController::class, 'update'])->name('update');
        Route::delete('/{news}', [NewsController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('accessibility')->name('facilities.')->group(function () {
        Route::get('/', [FacilityController::class, 'index'])->name('index');
        Route::get('/create', [FacilityController::class, 'create'])->name('create');
        Route::post('/', [FacilityController::class, 'store'])->name('store');
        Route::get('/{facility}/edit', [FacilityController::class, 'edit'])->name('edit');
        Route::put('/{facility}', [FacilityController::class, 'update'])->name('update');
        Route::delete('/{facility}', [FacilityController::class, 'destroy'])->name('destroy');
        Route::post('/reorder', [FacilityController::class, 'reorder'])->name('reorder');
    });

    // FAQ Routes
    Route::prefix('faqs')->name('faqs.')->group(function () {
        Route::get('/', [FaqsController::class, 'index'])->name('index');
        Route::post('/', [FaqsController::class, 'store'])->name('store');
        Route::put('/{faq}', [FaqsController::class, 'update'])->name('update');
        Route::delete('/{faq}', [FaqsController::class, 'destroy'])->name('destroy');
        Route::post('/reorder', [FaqsController::class, 'reorder'])->name('reorder');
    });

    // Contact Messages Routes
    Route::prefix('contact-messages')->name('contact-messages.')->group(function () {
        Route::get('/', [ContactMessageController::class, 'index'])->name('index');
        Route::get('/{contactMessage}', [ContactMessageController::class, 'show'])->name('show');
        Route::patch('/{contactMessage}/status', [ContactMessageController::class, 'updateStatus'])->name('update-status');
        Route::post('/{contactMessage}/reply', [ContactMessageController::class, 'reply'])->name('reply');
        Route::delete('/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-action', [ContactMessageController::class, 'bulkAction'])->name('bulk-action');
        Route::post('/mark-all-read', [ContactMessageController::class, 'markAllAsRead'])->name('mark-all-read');
    });

    // Company Profile Routes
    Route::prefix('company-profile')->name('company-profile.')->group(function () {
        Route::get('/', [CompanyProfileController::class, 'index'])->name('index');
        Route::post('/', [CompanyProfileController::class, 'store'])->name('store');
        Route::put('/{profile}', [CompanyProfileController::class, 'update'])->name('update');
        
        // Social Media Routes
        Route::prefix('social-media')->name('social-media.')->group(function () {
            Route::post('/', [CompanyProfileController::class, 'storeSocialMedia'])->name('store');
            Route::put('/{socialMedia}', [CompanyProfileController::class, 'updateSocialMedia'])->name('update');
            Route::delete('/{socialMedia}', [CompanyProfileController::class, 'destroySocialMedia'])->name('destroy');
            Route::post('/reorder', [CompanyProfileController::class, 'reorderSocialMedia'])->name('reorder');
        });
    });

    // Concept Routes
    Route::prefix('concept')->name('concept.')->group(function () {
        Route::get('/', [ConceptPageController::class, 'index'])->name('index');
        Route::post('/', [ConceptPageController::class, 'store'])->name('store');
        Route::put('/', [ConceptPageController::class, 'update'])->name('update');
        Route::delete('/', [ConceptPageController::class, 'destroy'])->name('destroy');
        
        // Section Routes
        Route::prefix('sections')->name('sections.')->group(function () {
            Route::post('/', [ConceptPageSectionController::class, 'store'])->name('store');
            Route::put('/{section}', [ConceptPageSectionController::class, 'update'])->name('update');
            Route::delete('/{section}', [ConceptPageSectionController::class, 'destroy'])->name('destroy');
            Route::post('/reorder', [ConceptPageSectionController::class, 'reorder'])->name('reorder');
        });
    });

    // Settings Routes
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [App\Http\Controllers\SettingsController::class, 'index'])->name('index');
        Route::put('/profile', [App\Http\Controllers\SettingsController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [App\Http\Controllers\SettingsController::class, 'updatePassword'])->name('password.update');
    });

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});