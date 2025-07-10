@extends('layouts.main')

@section('title', 'Home Page Settings')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">
<style>
    .form-control:focus {
        border-color: #0054a6;
        box-shadow: 0 0 0 0.2rem rgba(0, 84, 166, 0.25);
    }
    
    .card-header h3 {
        margin-bottom: 0;
    }
    
    .form-hint {
        color: #6c757d;
        font-size: 0.75rem;
    }
    
    .loading {
        pointer-events: none;
        opacity: 0.6;
    }

    /* Feature Styles */
    .feature-item {
        transition: all 0.3s ease;
        position: relative;
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
    }

    .feature-item:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transform: translateY(-1px);
    }

    /* Reorder Styles */
    .feature-reorder-mode .feature-item {
        border: 2px dashed #0054a6;
        background: #f8f9fa;
    }

    .feature-reorder-mode .dropdown {
        pointer-events: none;
        opacity: 0.5;
    }

    .sortable-feature-ghost {
        opacity: 0.5;
        background: #f8f9fa;
    }

    .sortable-feature-chosen {
        background: #e3f2fd;
        transform: rotate(2deg);
    }

    .sortable-feature-drag {
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        transform: rotate(3deg);
    }

    .cursor-move {
        cursor: move;
    }

    .image-preview-card {
        border: 2px dashed #ddd;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .image-preview-card:hover {
        border-color: #0054a6;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    #feature-reorder-instruction {
        animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Featured Units Styles */
    .unit-item {
        transition: all 0.3s ease;
        position: relative;
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
    }

    .unit-item:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transform: translateY(-1px);
    }

    .unit-reorder-mode .unit-item {
        border: 2px dashed #28a745;
        background: #f8f9fa;
    }

    .unit-reorder-mode .dropdown {
        pointer-events: none;
        opacity: 0.5;
    }

    .sortable-unit-ghost {
        opacity: 0.5;
        background: #f8f9fa;
    }

    .sortable-unit-chosen {
        background: #d4edda;
        transform: rotate(2deg);
    }

    .sortable-unit-drag {
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        transform: rotate(3deg);
    }

    #unit-reorder-instruction {
        animation: slideDown 0.3s ease-out;
    }

    .banner-preview-section {
        min-height: 200px;
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .section-preview {
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
        padding: 1rem;
        background: #fff;
    }

    .preview-image {
        height: 120px;
        width: 100%;
        object-fit: cover;
        border-radius: 0.375rem;
    }
</style>

<style>
    /* Featured Unit Image Selection Styles */
    .form-imagecheck {
        position: relative;
        transition: all 0.3s ease;
    }

    .form-imagecheck:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .form-imagecheck-figure {
        position: relative;
        border-radius: 0.5rem;
        overflow: hidden;
        display: block;
        border: 3px solid transparent;
        transition: all 0.3s ease;
    }

    .form-imagecheck-input:checked + .form-imagecheck-figure {
        border-color: #0054a6;
        box-shadow: 0 0 0 0.2rem rgba(0, 84, 166, 0.25);
    }

    .form-imagecheck-input:checked + .form-imagecheck-figure .selection-indicator {
        opacity: 1 !important;
    }

    .form-imagecheck-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        transition: all 0.3s ease;
    }

    .form-imagecheck:hover .form-imagecheck-image {
        transform: scale(1.05);
    }

    .form-imagecheck-input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    /* Unit Info Overlay */
    .form-imagecheck-figure .position-absolute.bottom-0 {
        transition: all 0.3s ease;
    }

    .form-imagecheck:hover .position-absolute.bottom-0 {
        background: rgba(0, 0, 0, 0.9) !important;
    }

    /* Selection Indicator Animation */
    .selection-indicator {
        transition: all 0.3s ease;
        transform: scale(0.8);
    }

    .form-imagecheck-input:checked + .form-imagecheck-figure .selection-indicator {
        transform: scale(1);
    }

    /* Modal Enhancements */
    .modal-xl .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }

    /* Button Loading State */
    .btn .btn-content,
    .btn .btn-loading {
        transition: opacity 0.3s ease;
    }

    .btn.loading .btn-content {
        opacity: 0;
    }

    .btn.loading .btn-loading {
        opacity: 1;
    }

    /* Grid Responsive */
    #unit-selection-grid .col-md-6:nth-child(2n+1),
    #edit-unit-selection-grid .col-md-6:nth-child(2n+1) {
        clear: left;
    }

    @media (min-width: 992px) {
        #unit-selection-grid .col-lg-4:nth-child(3n+1),
        #edit-unit-selection-grid .col-lg-4:nth-child(3n+1) {
            clear: left;
        }
        
        #unit-selection-grid .col-md-6:nth-child(2n+1),
        #edit-unit-selection-grid .col-md-6:nth-child(2n+1) {
            clear: none;
        }
    }

    /* Already Featured Badge */
    .badge.bg-success {
        z-index: 10;
    }
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h2 class="page-title">Home Page Settings</h2>
    @if($homePage)
    <div class="btn-list">
        <a href="#" class="btn btn-outline-info" target="_blank">
            <i class="ti ti-eye me-1"></i> Preview
        </a>
        <button type="button" class="btn btn-outline-danger delete-btn"
                data-id="{{ $homePage->id }}"
                data-name="Home Page Settings"
                data-url="{{ route('home-page.destroy') }}">
            <i class="ti ti-trash me-1"></i> Reset All
        </button>
    </div>
    @endif
</div>
@endsection

@section('content')
{{-- Alert Messages --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible" role="alert">
    <div class="d-flex">
        <div>
            <i class="ti ti-check icon alert-icon me-2"></i>
        </div>
        <div>{{ session('success') }}</div>
    </div>
    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible" role="alert">
    <div class="d-flex">
        <div>
            <i class="ti ti-exclamation-circle icon alert-icon me-2"></i>
        </div>
        <div>{{ session('error') }}</div>
    </div>
    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
</div>
@endif

{{-- Quick Navigation --}}
<div class="row g-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body py-3">
                <div class="d-flex flex-wrap gap-2">
                    <a href="#banner-section" class="btn btn-outline-primary btn-sm">
                        <i class="ti ti-photo me-1"></i> Banner
                    </a>
                    <a href="#hero-section" class="btn btn-outline-primary btn-sm">
                        <i class="ti ti-typography me-1"></i> Hero
                    </a>
                    <a href="#brochure-section" class="btn btn-outline-primary btn-sm">
                        <i class="ti ti-file-text me-1"></i> Brochure
                    </a>
                    <a href="#about-section" class="btn btn-outline-primary btn-sm">
                        <i class="ti ti-info-circle me-1"></i> About
                    </a>
                    <a href="#features-section" class="btn btn-outline-primary btn-sm">
                        <i class="ti ti-star me-1"></i> Features
                    </a>
                    <a href="#location-section" class="btn btn-outline-primary btn-sm">
                        <i class="ti ti-map-pin me-1"></i> Location
                    </a>
                    <a href="#featured-units" class="btn btn-outline-primary btn-sm">
                        <i class="ti ti-home me-1"></i> Featured Units
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Home Page Settings Form --}}
<div class="row g-3">
    <div class="col-12">
        <form action="{{ $homePage ? route('home-page.update') : route('home-page.store') }}" method="POST" enctype="multipart/form-data" id="home-page-form">
            @csrf
            @if($homePage)
                @method('PUT')
            @endif
            
            {{-- Banner Section --}}
            <div class="card" id="banner-section">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-photo me-2"></i>
                        Banner Section
                    </h3>
                    <div class="card-actions">
                        <span class="badge bg-red-lt">Required</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Current Banner Preview --}}
                        @if($homePage && ($homePage->banner_image_url || $homePage->banner_video_url))
                        <div class="col-12 mb-4">
                            <label class="form-label text-secondary">Current Banner</label>
                            <div class="banner-preview-section">
                                @if($homePage->banner_type === 'video' && $homePage->banner_video_url)
                                <video controls class="w-100" style="max-height: 300px;">
                                    <source src="{{ $homePage->banner_video_url }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                                @else
                                <img src="{{ $homePage->banner_image_url }}" class="w-100" style="max-height: 300px; object-fit: cover;">
                                @endif
                            </div>
                        </div>
                        @endif

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Banner Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('banner_type') is-invalid @enderror" 
                                        name="banner_type" required id="banner-type-select">
                                    <option value="image" {{ old('banner_type', $homePage->banner_type ?? 'image') === 'image' ? 'selected' : '' }}>Image</option>
                                    <option value="video" {{ old('banner_type', $homePage->banner_type ?? '') === 'video' ? 'selected' : '' }}>Video</option>
                                </select>
                                @error('banner_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Banner Alt Text</label>
                                <input type="text" class="form-control @error('banner_alt_text') is-invalid @enderror" 
                                       name="banner_alt_text" value="{{ old('banner_alt_text', $homePage->banner_alt_text ?? '') }}" 
                                       placeholder="Describe banner for accessibility">
                                @error('banner_alt_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Helps screen readers describe the banner content.</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3" id="banner-image-section">
                                <label class="form-label">
                                    Banner Image 
                                    @if(!$homePage || !$homePage->banner_image_url)
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <input type="file" class="form-control @error('banner_image') is-invalid @enderror" 
                                       name="banner_image" accept="image/*" id="banner-image">
                                @error('banner_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    @if($homePage && $homePage->banner_image_url)
                                        Leave empty to keep current banner image.
                                    @endif
                                    Recommended: 1920x1080px, Max: 10MB. Supported: JPG, PNG, WebP
                                </small>
                                <div class="mt-2" id="banner-image-preview"></div>
                            </div>
                            <div class="mb-3" id="banner-video-section" style="display: none;">
                                <label class="form-label">
                                    Banner Video 
                                    @if(!$homePage || !$homePage->banner_video_url)
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <input type="file" class="form-control @error('banner_video') is-invalid @enderror" 
                                       name="banner_video" accept="video/*" id="banner-video">
                                @error('banner_video')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    @if($homePage && $homePage->banner_video_url)
                                        Leave empty to keep current banner video.
                                    @endif
                                    Supported: MP4, MOV, AVI. Max: 200MB
                                </small>
                                <div class="mt-2" id="banner-video-preview"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Hero Section --}}
            <div class="card mt-3" id="hero-section">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-typography me-2"></i>
                        Hero Section
                    </h3>
                    <div class="card-actions">
                        <span class="badge bg-red-lt">Required</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Hero Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('hero_title') is-invalid @enderror" 
                                       name="hero_title" value="{{ old('hero_title', $homePage->hero_title ?? '') }}" required
                                       placeholder="Enter compelling hero title">
                                @error('hero_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Main heading that appears over the banner. Keep it concise and impactful.</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Hero Description <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('hero_description') is-invalid @enderror" 
                                          name="hero_description" rows="3" required 
                                          placeholder="Enter hero description">{{ old('hero_description', $homePage->hero_description ?? '') }}</textarea>
                                @error('hero_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Subtitle that appears below the hero title. Briefly describe your development.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SEO Meta --}}
            @include('components.seo-meta-form', ['data' => $homePage, 'type' => is_null($homePage) ? 'create' : 'edit'])

            {{-- Brochure Section --}}
            <div class="card mt-3" id="brochure-section">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-file-text me-2"></i>
                        Brochure Section
                    </h3>
                    <div class="card-actions">
                        <span class="badge bg-blue-lt">Optional</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Current Brochure Preview --}}
                        @if($homePage && $homePage->brochure_file_url)
                        <div class="col-12 mb-4">
                            <label class="form-label text-secondary">Current Brochure</label>
                            <div class="section-preview">
                                <div class="d-flex align-items-center p-3 border rounded">
                                    <div class="me-3">
                                        <i class="ti ti-file-text text-red" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Project Brochure</h6>
                                        <small class="text-secondary">PDF File</small>
                                    </div>
                                    <div>
                                        <a href="{{ $homePage->brochure_file_url }}" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                            <i class="ti ti-eye me-1"></i> View
                                        </a>
                                        <a href="{{ $homePage->brochure_file_url }}" download class="btn btn-sm btn-outline-success">
                                            <i class="ti ti-download me-1"></i> Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Upload Brochure PDF</label>
                                <input type="file" class="form-control @error('brochure_file') is-invalid @enderror" 
                                    name="brochure_file" accept=".pdf" id="brochure-file">
                                @error('brochure_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    @if($homePage && $homePage->brochure_file_url)
                                        {{-- Kosongkan untuk tetap menggunakan brochure saat ini. --}}
                                        Leave empty to keep current brochure
                                    @endif
                                    Upload a PDF brochure for the project. Maximum size: 20MB. Supported format: PDF
                                </small>
                                <div class="mt-2" id="brochure-file-preview"></div>
                            </div>
                            
                            {{-- Brochure Description --}}
                            {{-- <div class="mb-3">
                                <div class="alert alert-info">
                                    <div class="d-flex">
                                        <div class="me-2">
                                            <i class="ti ti-info-circle"></i>
                                        </div>
                                        <div>
                                            <h4 class="alert-title">Tips Brochure</h4>
                                            <div class="text-secondary">
                                                Brochure akan ditampilkan sebagai download link di halaman home. 
                                                Pastikan brochure berisi informasi lengkap tentang proyek seperti:
                                                <ul class="mt-2 mb-0">
                                                    <li>Denah dan spesifikasi unit</li>
                                                    <li>Fasilitas dan lokasi</li>
                                                    <li>Harga dan skema pembayaran</li>
                                                    <li>Kontak dan informasi pengembang</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                        
                        {{-- <div class="col-lg-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="ti ti-file-text text-muted mb-3" style="font-size: 4rem;"></i>
                                    <h5 class="card-title">Brochure Preview</h5>
                                    <p class="text-secondary small">
                                        Upload file PDF untuk preview brochure proyek Anda. 
                                        Pengunjung dapat mengunduh brochure dari halaman home.
                                    </p>
                                    @if($homePage && $homePage->brochure_file_url)
                                    <div class="mt-3">
                                        <span class="badge bg-success-lt">
                                            <i class="ti ti-check me-1"></i>
                                            Brochure tersedia
                                        </span>
                                    </div>
                                    @else
                                    <div class="mt-3">
                                        <span class="badge bg-warning-lt">
                                            <i class="ti ti-alert-triangle me-1"></i>
                                            Belum ada brochure
                                        </span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>

            {{-- About Development Section --}}
            <div class="card mt-3" id="about-section">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-info-circle me-2"></i>
                        About Development Section
                    </h3>
                    <div class="card-actions">
                        <span class="badge bg-red-lt">Required</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">About Development Title</label>
                                        <input type="text" class="form-control @error('about_section_title') is-invalid @enderror" 
                                            name="about_section_title" value="{{ old('about_section_title', $homePage->about_section_title ?? '') }}" 
                                            placeholder="About development section title">
                                        @error('about_section_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-hint">Small text that appears above the main about title.</small>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">About Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('about_title') is-invalid @enderror" 
                                            name="about_title" value="{{ old('about_title', $homePage->about_title ?? '') }}" required
                                            placeholder="Enter about section title">
                                        @error('about_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">About Description <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('about_description') is-invalid @enderror" 
                                                name="about_description" rows="6" required 
                                                placeholder="Describe your development in detail">{{ old('about_description', $homePage->about_description ?? '') }}</textarea>
                                        @error('about_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-hint">Detailed description about your development project and its unique selling points.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Link Text</label>
                                        <input type="text" class="form-control @error('about_link_text') is-invalid @enderror" 
                                            name="about_link_text" value="{{ old('about_link_text', $homePage->about_link_text ?? 'Discover More') }}" 
                                            placeholder="Link button text">
                                        @error('about_link_text')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Link URL</label>
                                        <input type="url" class="form-control @error('about_link_url') is-invalid @enderror" 
                                            name="about_link_url" value="{{ old('about_link_url', $homePage->about_link_url ?? '') }}" 
                                            placeholder="https://example.com">
                                        @error('about_link_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-hint">Optional. Link to more detailed information.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            {{-- Current About Image --}}
                            @if($homePage && $homePage->about_image_url)
                            <div class="mb-3">
                                <label class="form-label text-secondary">Current About Image</label>
                                <div class="section-preview">
                                    <img src="{{ $homePage->about_image_url }}" class="preview-image">
                                    <div class="mt-2 d-flex justify-content-between align-items-center">
                                        <small class="text-secondary fw-medium">About Image</small>
                                        <a href="{{ $homePage->about_image_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="ti ti-external-link"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label">
                                    About Image 
                                    @if(!$homePage || !$homePage->about_image_url)
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <input type="file" class="form-control @error('about_image') is-invalid @enderror" 
                                    name="about_image" accept="image/*" 
                                    {{ !$homePage || !$homePage->about_image_url ? 'required' : '' }} 
                                    id="about-image">
                                @error('about_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    @if($homePage && $homePage->about_image_url)
                                        Leave empty to keep current image.
                                    @endif
                                    Recommended: 1200x800px, Max: 10MB
                                </small>
                                <div class="mt-2" id="about-image-preview"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">About Image Alt Text</label>
                                <input type="text" class="form-control @error('about_image_alt_text') is-invalid @enderror" 
                                    name="about_image_alt_text" value="{{ old('about_image_alt_text', $homePage->about_image_alt_text ?? '') }}" 
                                    placeholder="Describe image for accessibility">
                                @error('about_image_alt_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Features Section --}}
            <div class="card mt-3" id="features-section">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-star me-2"></i>
                        Features Section
                    </h3>
                    <div class="card-actions">
                        <span class="badge bg-red-lt">Required</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Features Section Title</label>
                                        <input type="text" class="form-control @error('features_section_title') is-invalid @enderror" 
                                               name="features_section_title" value="{{ old('features_section_title', $homePage->features_section_title ?? 'Exclusive Features') }}" 
                                               placeholder="Features section title">
                                        @error('features_section_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-hint">Small text that appears above the main features title.</small>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Features Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('features_title') is-invalid @enderror" 
                                               name="features_title" value="{{ old('features_title', $homePage->features_title ?? '') }}" required
                                               placeholder="Enter features section title">
                                        @error('features_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Features Description</label>
                                        <textarea class="form-control @error('features_description') is-invalid @enderror" 
                                                  name="features_description" rows="4" 
                                                  placeholder="Describe the features section">{{ old('features_description', $homePage->features_description ?? '') }}</textarea>
                                        @error('features_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-hint">Optional. Brief description about the features.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Link Text</label>
                                        <input type="text" class="form-control @error('features_link_text') is-invalid @enderror" 
                                               name="features_link_text" value="{{ old('features_link_text', $homePage->features_link_text ?? 'Learn More') }}" 
                                               placeholder="Link button text">
                                        @error('features_link_text')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Link URL</label>
                                        <input type="url" class="form-control @error('features_link_url') is-invalid @enderror" 
                                               name="features_link_url" value="{{ old('features_link_url', $homePage->features_link_url ?? '') }}" 
                                               placeholder="https://example.com">
                                        @error('features_link_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            {{-- Current Features Image --}}
                            @if($homePage && $homePage->features_image_url)
                            <div class="mb-3">
                                <label class="form-label text-secondary">Current Features Image</label>
                                <div class="section-preview">
                                    <img src="{{ $homePage->features_image_url }}" class="preview-image">
                                    <div class="mt-2 d-flex justify-content-between align-items-center">
                                        <small class="text-secondary fw-medium">Features Image</small>
                                        <a href="{{ $homePage->features_image_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="ti ti-external-link"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label">
                                    Features Image 
                                    @if(!$homePage || !$homePage->features_image_url)
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <input type="file" class="form-control @error('features_image') is-invalid @enderror" 
                                       name="features_image" accept="image/*" 
                                       {{ !$homePage || !$homePage->features_image_url ? 'required' : '' }} 
                                       id="features-image">
                                @error('features_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    @if($homePage && $homePage->features_image_url)
                                        Leave empty to keep current image.
                                    @endif
                                    Recommended: 1200x800px, Max: 10MB
                                </small>
                                <div class="mt-2" id="features-image-preview"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Features Image Alt Text</label>
                                <input type="text" class="form-control @error('features_image_alt_text') is-invalid @enderror" 
                                       name="features_image_alt_text" value="{{ old('features_image_alt_text', $homePage->features_image_alt_text ?? '') }}" 
                                       placeholder="Describe image for accessibility">
                                @error('features_image_alt_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Location Section --}}
            <div class="card mt-3" id="location-section">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-map-pin me-2"></i>
                        Location & Accessibility Section
                    </h3>
                    <div class="card-actions">
                        <span class="badge bg-red-lt">Required</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Location Section Title</label>
                                        <input type="text" class="form-control @error('location_section_title') is-invalid @enderror" 
                                               name="location_section_title" value="{{ old('location_section_title', $homePage->location_section_title ?? 'Location & Accessibility') }}" 
                                               placeholder="Location section title">
                                        @error('location_section_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Location Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('location_title') is-invalid @enderror" 
                                               name="location_title" value="{{ old('location_title', $homePage->location_title ?? '') }}" required
                                               placeholder="Enter location title">
                                        @error('location_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Location Description <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('location_description') is-invalid @enderror" 
                                                  name="location_description" rows="6" required 
                                                  placeholder="Describe the location advantages">{{ old('location_description', $homePage->location_description ?? '') }}</textarea>
                                        @error('location_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-hint">Highlight location benefits, accessibility, and nearby facilities.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Link Text</label>
                                        <input type="text" class="form-control @error('location_link_text') is-invalid @enderror" 
                                               name="location_link_text" value="{{ old('location_link_text', $homePage->location_link_text ?? 'Get Direction') }}" 
                                               placeholder="Link button text">
                                        @error('location_link_text')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Link URL</label>
                                        <input type="url" class="form-control @error('location_link_url') is-invalid @enderror" 
                                               name="location_link_url" value="{{ old('location_link_url', $homePage->location_link_url ?? '') }}" 
                                               placeholder="https://maps.google.com/...">
                                        @error('location_link_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-hint">Link to Google Maps or directions.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            {{-- Current Location Image --}}
                            @if($homePage && $homePage->location_image_url)
                            <div class="mb-3">
                                <label class="form-label text-secondary">Current Location Image</label>
                                <div class="section-preview">
                                    <img src="{{ $homePage->location_image_url }}" class="preview-image">
                                    <div class="mt-2 d-flex justify-content-between align-items-center">
                                        <small class="text-secondary fw-medium">Location Image</small>
                                        <a href="{{ $homePage->location_image_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="ti ti-external-link"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label">
                                    Location Image 
                                    @if(!$homePage || !$homePage->location_image_url)
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <input type="file" class="form-control @error('location_image') is-invalid @enderror" 
                                       name="location_image" accept="image/*" 
                                       {{ !$homePage || !$homePage->location_image_url ? 'required' : '' }} 
                                       id="location-image">
                                @error('location_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    @if($homePage && $homePage->location_image_url)
                                        Leave empty to keep current image.
                                    @endif
                                    Recommended: 1200x800px, Max: 10MB
                                </small>
                                <div class="mt-2" id="location-image-preview"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Location Image Alt Text</label>
                                <input type="text" class="form-control @error('location_image_alt_text') is-invalid @enderror" 
                                       name="location_image_alt_text" value="{{ old('location_image_alt_text', $homePage->location_image_alt_text ?? '') }}" 
                                       placeholder="Describe image for accessibility">
                                @error('location_image_alt_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Settings --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-settings me-2"></i>
                        Settings
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-0">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" value="1" 
                                   {{ old('status', $homePage->is_active ?? true) ? 'checked' : '' }}>
                            <span class="form-check-label">Active</span>
                        </label>
                        <small class="form-hint">Enable home page to be displayed on the website.</small>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        @if($homePage)
                        <small class="text-secondary">
                            <i class="ti ti-clock me-1"></i>
                            Last saved: {{ $homePage->updated_at->format('d M Y, H:i') }}
                        </small>
                        @else
                        <div></div>
                        @endif
                        <button type="submit" class="btn btn-primary" id="submit-btn">
                            <i class="ti ti-device-floppy me-1"></i> 
                            {{ $homePage ? 'Update' : 'Create' }} Home Page
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Features Management --}}
<div class="row g-3 mt-4" id="features-management">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-star me-2"></i>
                    Home Features Management
                </h3>
                <div class="card-actions">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-blue-lt">{{ $features->count() }} Features</span>
                        @if($features->count() > 0)
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="toggle-feature-reorder">
                            <i class="ti ti-arrows-sort me-1"></i> 
                            <span id="feature-reorder-text">Reorder</span>
                        </button>
                        @endif
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#add-feature">
                            <i class="ti ti-plus me-1"></i> Add Feature
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($features->count() > 0)
                    <div class="row g-3" id="sortable-features">
                        @foreach($features as $feature)
                        <div class="col-md-6 col-lg-4 sortable-feature-item" data-id="{{ $feature->id }}">
                            <div class="feature-item p-3">
                                {{-- Reorder Handle --}}
                                <div class="feature-reorder-handle position-absolute top-0 start-0 m-2 cursor-move bg-secondary text-white rounded p-1" style="display: none; z-index: 10;">
                                    <i class="ti ti-grip-vertical"></i>
                                </div>

                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="{{ $feature->icon }} me-2 text-primary fs-4"></i>
                                            <h5 class="mb-0">{{ $feature->title }}</h5>
                                        </div>
                                        <p class="text-secondary small mb-2">{{ $feature->description }}</p>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span class="badge bg-{{ $feature->is_active ? 'green' : 'red' }}-lt">
                                                {{ $feature->status_text }}
                                            </span>
                                            <small class="text-secondary">Order: {{ $feature->order }}</small>
                                        </div>
                                    </div>
                                    <div class="dropdown ms-2">
                                        <button class="btn btn-sm btn-ghost-secondary" data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <button type="button" class="dropdown-item" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#edit-feature"
                                                    data-id="{{ $feature->id }}"
                                                    data-icon="{{ $feature->icon }}"
                                                    data-title="{{ $feature->title }}"
                                                    data-description="{{ $feature->description }}"
                                                    data-status="{{ $feature->is_active }}">
                                                <i class="ti ti-edit me-1"></i> Edit
                                            </button>
                                            <button type="button" class="dropdown-item text-danger delete-feature-btn"
                                                    data-id="{{ $feature->id }}"
                                                    data-name="{{ $feature->title }}"
                                                    data-url="{{ route('home-page.features.destroy', $feature) }}">
                                                <i class="ti ti-trash me-1"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="empty">
                            <div class="empty-icon">
                                <i class="ti ti-star icon icon-lg text-secondary"></i>
                            </div>
                            <p class="empty-title h5">No Features Yet</p>
                            <p class="empty-subtitle text-secondary">
                                Add features to showcase unique selling points on your home page.<br>
                                Features will appear in the "Exclusive Features" section.
                            </p>
                            <div class="empty-action">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-feature">
                                    <i class="ti ti-plus me-1"></i> Add First Feature
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Featured Units Management --}}
<div class="row g-3 mt-3" id="featured-units">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-home me-2"></i>
                    Featured Units Management
                </h3>
                <div class="card-actions">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-green-lt">{{ $featuredUnits->count() }} Units</span>
                        @if($featuredUnits->count() > 0)
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="toggle-unit-reorder">
                            <i class="ti ti-arrows-sort me-1"></i> 
                            <span id="unit-reorder-text">Reorder</span>
                        </button>
                        @endif
                        @if($availableUnits->count() > 0)
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#add-featured-unit">
                            <i class="ti ti-plus me-1"></i> Add Featured Unit
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($featuredUnits->count() > 0)
                    <div class="row g-3" id="sortable-units">
                        @foreach($featuredUnits as $featuredUnit)
                        <div class="col-md-6 col-lg-4 sortable-unit-item" data-id="{{ $featuredUnit->id }}">
                            <div class="unit-item">
                                {{-- Reorder Handle --}}
                                <div class="unit-reorder-handle position-absolute top-0 start-0 m-2 cursor-move bg-secondary text-white rounded p-1" style="display: none; z-index: 10;">
                                    <i class="ti ti-grip-vertical"></i>
                                </div>

                                <div class="card h-100">
                                    @if($featuredUnit->unit->main_image_url)
                                    <img src="{{ $featuredUnit->unit->main_image_url }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                                    @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="ti ti-photo text-secondary" style="font-size: 3rem;"></i>
                                    </div>
                                    @endif
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $featuredUnit->unit->name }}</h5>
                                        <p class="text-secondary small mb-2">
                                            <i class="ti ti-building me-1"></i>{{ $featuredUnit->unit->project->name }}
                                        </p>
                                        @if($featuredUnit->unit->short_description)
                                        <p class="card-text text-secondary small">{{ Str::limit($featuredUnit->unit->short_description, 80) }}</p>
                                        @endif
                                        
                                        {{-- Unit Details --}}
                                        <div class="row g-2 mb-3">
                                            @if($featuredUnit->unit->bedrooms)
                                            <div class="col-4">
                                                <small class="text-secondary">
                                                    <i class="ti ti-bed me-1"></i>{{ $featuredUnit->unit->bedrooms }} BR
                                                </small>
                                            </div>
                                            @endif
                                            @if($featuredUnit->unit->bathrooms)
                                            <div class="col-4">
                                                <small class="text-secondary">
                                                    <i class="ti ti-bath me-1"></i>{{ $featuredUnit->unit->bathrooms }} BA
                                                </small>
                                            </div>
                                            @endif
                                            @if($featuredUnit->unit->carports)
                                            <div class="col-4">
                                                <small class="text-secondary">
                                                    <i class="ti ti-car me-1"></i>{{ $featuredUnit->unit->carports }} CP
                                                </small>
                                            </div>
                                            @endif
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-{{ $featuredUnit->is_active ? 'green' : 'red' }}-lt">
                                                {{ $featuredUnit->status_text }}
                                            </span>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-ghost-secondary" data-bs-toggle="dropdown">
                                                    <i class="ti ti-dots-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a href="{{ route('development.unit.edit', [$featuredUnit->unit->project, $featuredUnit->unit]) }}" class="dropdown-item">
                                                        <i class="ti ti-external-link me-1"></i> View Unit
                                                    </a>
                                                    <button type="button" class="dropdown-item" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#edit-featured-unit"
                                                            data-id="{{ $featuredUnit->id }}"
                                                            data-unit-id="{{ $featuredUnit->unit_id }}"
                                                            data-status="{{ $featuredUnit->is_active }}">
                                                        <i class="ti ti-edit me-1"></i> Edit
                                                    </button>
                                                    <button type="button" class="dropdown-item text-danger delete-unit-btn"
                                                            data-id="{{ $featuredUnit->id }}"
                                                            data-name="{{ $featuredUnit->unit->name }}"
                                                            data-url="{{ route('home-page.featured-units.destroy', $featuredUnit) }}">
                                                        <i class="ti ti-trash me-1"></i> Remove
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <small class="text-secondary d-block mt-2">Order: {{ $featuredUnit->order }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="empty">
                            <div class="empty-icon">
                                <i class="ti ti-home icon icon-lg text-secondary"></i>
                            </div>
                            <p class="empty-title h5">No Featured Units Yet</p>
                            <p class="empty-subtitle text-secondary">
                                Select units to feature on your home page showcase.<br>
                                @if($availableUnits->count() == 0)
                                You need to create units first before featuring them.
                                @else
                                Featured units will be displayed prominently on the home page.
                                @endif
                            </p>
                            <div class="empty-action">
                                @if($availableUnits->count() > 0)
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-featured-unit">
                                    <i class="ti ti-plus me-1"></i> Add First Featured Unit
                                </button>
                                @else
                                <a href="{{ route('development.project.index') }}" class="btn btn-primary">
                                    <i class="ti ti-plus me-1"></i> Create Units First
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal Add Feature --}}
<div class="modal modal-blur fade" id="add-feature" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form class="modal-content" action="{{ route('home-page.features.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add New Feature</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Feature Icon <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" id="add-icon-preview">
                                    <i class="bi bi-question-circle"></i>
                                </span>
                                <select class="form-select" name="icon" required id="add-feature-icon">
                                    <option value="">Select Icon</option>
                                    @foreach(\App\Models\HomeFeature::getAvailableIcons() as $icon => $label)
                                    <option value="{{ $icon }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <small class="form-hint">Choose an icon that represents your feature.</small>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Feature Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" required 
                                   placeholder="Enter feature title" maxlength="255">
                            <small class="form-hint">Keep it concise and descriptive.</small>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Feature Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="description" rows="4" required 
                                      placeholder="Describe this feature in detail"></textarea>
                            <small class="form-hint">Explain the benefit this feature provides to residents.</small>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" value="1" checked>
                            <span class="form-check-label">Active</span>
                        </label>
                        <small class="form-hint">Enable this feature to be displayed on the home page.</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary ms-auto">
                    <i class="ti ti-plus me-1"></i> Add Feature
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Feature --}}
<div class="modal modal-blur fade" id="edit-feature" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form class="modal-content" id="edit-feature-form" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Feature</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Feature Icon <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" id="edit-icon-preview">
                                    <i class="bi bi-question-circle"></i>
                                </span>
                                <select class="form-select" id="edit-feature-icon" name="icon" required>
                                    <option value="">Select Icon</option>
                                    @foreach(\App\Models\HomeFeature::getAvailableIcons() as $icon => $label)
                                    <option value="{{ $icon }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Feature Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit-feature-title" name="title" required 
                                   placeholder="Enter feature title" maxlength="255">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Feature Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit-feature-description" name="description" rows="4" required 
                                      placeholder="Describe this feature in detail"></textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit-feature-status" name="status" value="1">
                            <span class="form-check-label">Active</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary ms-auto">
                    Update Feature
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Add Featured Unit with Image Selection --}}
@if($availableUnits->count() > 0)
<div class="modal modal-blur fade" id="add-featured-unit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <form class="modal-content" action="{{ route('home-page.featured-units.store') }}" method="POST" id="add-featured-unit-form">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add Featured Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Select Unit <span class="text-danger">*</span></label>
                            <small class="form-hint d-block mb-3">Choose one unit to feature on the home page by clicking on the image.</small>
                            
                            {{-- Check if there are available units --}}
                            @php
                                $nonFeaturedUnits = $availableUnits->filter(function($unit) use ($featuredUnits) {
                                    return !$featuredUnits->contains('unit_id', $unit->id);
                                });
                            @endphp
                            
                            @if($nonFeaturedUnits->count() > 0)
                                <div class="row g-3" id="unit-selection-grid">
                                    @foreach($nonFeaturedUnits as $unit)
                                    <div class="col-md-6 col-lg-4">
                                        <label class="form-imagecheck mb-2 w-100">
                                            <input name="unit_id" type="radio" value="{{ $unit->id }}" class="form-imagecheck-input" required>
                                            <span class="form-imagecheck-figure">
                                                @if($unit->main_image_url)
                                                <img src="{{ $unit->main_image_url }}" alt="{{ $unit->name }}" class="form-imagecheck-image">
                                                @else
                                                <div class="form-imagecheck-image d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                                                    <i class="ti ti-photo text-muted" style="font-size: 3rem;"></i>
                                                </div>
                                                @endif
                                                
                                                {{-- Unit Info Overlay --}}
                                                <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75 text-white p-2">
                                                    <h6 class="mb-1 text-white">{{ $unit->name }}</h6>
                                                    <small class="text-white-50">{{ $unit->project->name }}</small>
                                                    
                                                    {{-- Unit Details --}}
                                                    <div class="row g-1 mt-1">
                                                        @if($unit->bedrooms)
                                                        <div class="col-4">
                                                            <small class="text-white-75">
                                                                <i class="ti ti-bed me-1"></i>{{ $unit->bedrooms }} BR
                                                            </small>
                                                        </div>
                                                        @endif
                                                        @if($unit->bathrooms)
                                                        <div class="col-4">
                                                            <small class="text-white-75">
                                                                <i class="ti ti-bath me-1"></i>{{ $unit->bathrooms }} BA
                                                            </small>
                                                        </div>
                                                        @endif
                                                        @if($unit->carports)
                                                        <div class="col-4">
                                                            <small class="text-white-75">
                                                                <i class="ti ti-car me-1"></i>{{ $unit->carports }}
                                                            </small>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                {{-- Selection Indicator --}}
                                                <div class="position-absolute top-0 end-0 m-2">
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center selection-indicator" style="width: 24px; height: 24px; opacity: 0;">
                                                        <i class="ti ti-check" style="font-size: 14px;"></i>
                                                    </div>
                                                </div>
                                            </span>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <div class="empty">
                                        <div class="empty-icon">
                                            <i class="ti ti-home icon icon-lg text-secondary" style="font-size: 3rem;"></i>
                                        </div>
                                        <p class="empty-title h6">No Available Units</p>
                                        <p class="empty-subtitle text-secondary">
                                            All units are already featured or no units exist yet.
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    @if($nonFeaturedUnits->count() > 0)
                    <div class="col-12">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" value="1" checked>
                            <span class="form-check-label">Active</span>
                        </label>
                        <small class="form-hint">Enable this unit to be displayed in the featured units section.</small>
                    </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                @if($nonFeaturedUnits->count() > 0)
                <button type="submit" class="btn btn-primary ms-auto" id="add-featured-unit-submit">
                    <span class="btn-content">
                        <i class="ti ti-plus me-1"></i> Add Featured Unit
                    </span>
                    <span class="btn-loading d-none">
                        <span class="spinner-border spinner-border-sm me-2"></span>
                        Adding...
                    </span>
                </button>
                @endif
            </div>
        </form>
    </div>
</div>
@endif

{{-- Modal Edit Featured Unit with Image Selection --}}
<div class="modal modal-blur fade" id="edit-featured-unit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <form class="modal-content" id="edit-featured-unit-form" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Featured Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Select Unit <span class="text-danger">*</span></label>
                            <small class="form-hint d-block mb-3">Choose one unit to feature on the home page by clicking on the image.</small>
                            
                            <div class="row g-3" id="edit-unit-selection-grid">
                                @foreach($availableUnits as $unit)
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-imagecheck mb-2 w-100">
                                        <input name="unit_id" type="radio" value="{{ $unit->id }}" class="form-imagecheck-input" required>
                                        <span class="form-imagecheck-figure">
                                            @if($unit->main_image_url)
                                            <img src="{{ $unit->main_image_url }}" alt="{{ $unit->name }}" class="form-imagecheck-image">
                                            @else
                                            <div class="form-imagecheck-image d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                                                <i class="ti ti-photo text-muted" style="font-size: 3rem;"></i>
                                            </div>
                                            @endif
                                            
                                            {{-- Unit Info Overlay --}}
                                            <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75 text-white p-2">
                                                <h6 class="mb-1 text-white">{{ $unit->name }}</h6>
                                                <small class="text-white-50">{{ $unit->project->name }}</small>
                                                
                                                {{-- Unit Details --}}
                                                <div class="row g-1 mt-1">
                                                    @if($unit->bedrooms)
                                                    <div class="col-4">
                                                        <small class="text-white-75">
                                                            <i class="ti ti-bed me-1"></i>{{ $unit->bedrooms }} BR
                                                        </small>
                                                    </div>
                                                    @endif
                                                    @if($unit->bathrooms)
                                                    <div class="col-4">
                                                        <small class="text-white-75">
                                                            <i class="ti ti-bath me-1"></i>{{ $unit->bathrooms }} BA
                                                        </small>
                                                    </div>
                                                    @endif
                                                    @if($unit->carports)
                                                    <div class="col-4">
                                                        <small class="text-white-75">
                                                            <i class="ti ti-car me-1"></i>{{ $unit->carports }}
                                                        </small>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            {{-- Selection Indicator --}}
                                            <div class="position-absolute top-0 end-0 m-2">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center selection-indicator" style="width: 24px; height: 24px; opacity: 0;">
                                                    <i class="ti ti-check" style="font-size: 14px;"></i>
                                                </div>
                                            </div>
                                            
                                            {{-- Already Featured Badge --}}
                                            @if($featuredUnits->contains('unit_id', $unit->id))
                                            <div class="position-absolute top-0 start-0 m-2">
                                                <span class="badge bg-success text-white">Currently Featured</span>
                                            </div>
                                            @endif
                                        </span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit-featured-unit-status" name="status" value="1">
                            <span class="form-check-label">Active</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary ms-auto" id="edit-featured-unit-submit">
                    <span class="btn-content">
                       Update Featured Unit
                    </span>
                    <span class="btn-loading d-none">
                        <span class="spinner-border spinner-border-sm me-2"></span>
                        Updating...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Include Global Delete Modal --}}
@include('components.delete-modal')

@endsection

@push('scripts')
@include('components.alert')
@include('components.toast')

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Smooth scrolling for navigation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Banner type toggle
        const bannerTypeSelect = document.getElementById('banner-type-select');
        const bannerImageSection = document.getElementById('banner-image-section');
        const bannerVideoSection = document.getElementById('banner-video-section');
        
        function toggleBannerType() {
            if (bannerTypeSelect.value === 'video') {
                bannerImageSection.style.display = 'none';
                bannerVideoSection.style.display = 'block';
            } else {
                bannerImageSection.style.display = 'block';
                bannerVideoSection.style.display = 'none';
            }
        }
        
        bannerTypeSelect.addEventListener('change', toggleBannerType);
        toggleBannerType(); // Initial call

        // Image preview functionality
        function setupImagePreview(inputId, previewId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            
            if (input && preview) {
                input.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const maxSize = inputId.includes('banner') ? 10 * 1024 * 1024 : 10 * 1024 * 1024;
                        if (file.size > maxSize) {
                            showAlert(input, 'danger', `File size too large. Maximum ${inputId.includes('banner') ? '10MB' : '10MB'} allowed.`);
                            input.value = '';
                            return;
                        }

                        if (!file.type.startsWith('image/')) {
                            showAlert(input, 'danger', 'Please select a valid image file.');
                            input.value = '';
                            return;
                        }

                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.innerHTML = `
                                <div class="card image-preview-card">
                                    <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h5 class="card-title h6 mb-1">${file.name}</h5>
                                                <small class="text-secondary">
                                                    ${(file.size / 1024 / 1024).toFixed(2)} MB
                                                </small>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearPreview('${inputId}', '${previewId}')">
                                                <i class="ti ti-x"></i>
                                            </button>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-success">
                                                <i class="ti ti-check me-1"></i>
                                                Ready to upload
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            `;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        preview.innerHTML = '';
                    }
                });
            }
        }

        // Video preview functionality
        function setupVideoPreview(inputId, previewId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            
            if (input && preview) {
                input.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const maxSize = 200 * 1024 * 1024; // 200MB
                        if (file.size > maxSize) {
                            showAlert(input, 'danger', 'Video file size too large. Maximum 200MB allowed.');
                            input.value = '';
                            return;
                        }

                        if (!file.type.startsWith('video/')) {
                            showAlert(input, 'danger', 'Please select a valid video file.');
                            input.value = '';
                            return;
                        }

                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.innerHTML = `
                                <div class="card">
                                    <video class="card-img-top" style="height: 150px; object-fit: cover;" controls>
                                        <source src="${e.target.result}" type="${file.type}">
                                        Your browser does not support the video tag.
                                    </video>
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h5 class="card-title h6 mb-1">${file.name}</h5>
                                                <small class="text-secondary">
                                                    ${(file.size / 1024 / 1024).toFixed(2)} MB
                                                </small>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearPreview('${inputId}', '${previewId}')">
                                                <i class="ti ti-x"></i>
                                            </button>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-success">
                                                <i class="ti ti-check me-1"></i>
                                                Ready to upload
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            `;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        preview.innerHTML = '';
                    }
                });
            }
        }

        // Clear preview function
        window.clearPreview = function(inputId, previewId) {
            document.getElementById(inputId).value = '';
            document.getElementById(previewId).innerHTML = '';
        };

        // Setup previews for all inputs
        setupImagePreview('banner-image', 'banner-image-preview');
        setupVideoPreview('banner-video', 'banner-video-preview');
        setupImagePreview('about-image', 'about-image-preview');
        setupImagePreview('features-image', 'features-image-preview');
        setupImagePreview('location-image', 'location-image-preview');

        function setupPdfPreview(inputId, previewId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            
            if (input && preview) {
                input.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const maxSize = 20 * 1024 * 1024; // 20MB
                        if (file.size > maxSize) {
                            showAlert(input, 'danger', 'Ukuran file PDF terlalu besar. Maksimal 20MB diizinkan.');
                            input.value = '';
                            return;
                        }

                        if (file.type !== 'application/pdf') {
                            showAlert(input, 'danger', 'Pilih file PDF yang valid.');
                            input.value = '';
                            return;
                        }

                        preview.innerHTML = `
                            <div class="card">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="ti ti-file-text text-red" style="font-size: 2rem;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">${file.name}</h6>
                                            <small class="text-secondary">
                                                ${(file.size / 1024 / 1024).toFixed(2)} MB • PDF File
                                            </small>
                                            <div class="mt-2">
                                                <small class="text-success">
                                                    <i class="ti ti-check me-1"></i>
                                                    Ready to upload
                                                </small>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearPreview('${inputId}', '${previewId}')">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        preview.innerHTML = '';
                    }
                });
            }
        }

        setupPdfPreview('brochure-file', 'brochure-file-preview');

        // Form submission loading state
        const form = document.getElementById('home-page-form');
        const submitBtn = document.getElementById('submit-btn');
        
        if (form && submitBtn) {
            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                form.classList.add('loading');
            });
        }

        // Icon preview functionality for Add Feature Modal
        const addFeatureIconSelect = document.getElementById('add-feature-icon');
        const addIconPreview = document.getElementById('add-icon-preview');

        if (addFeatureIconSelect && addIconPreview) {
            addFeatureIconSelect.addEventListener('change', function() {
                const selectedIcon = this.value;
                if (selectedIcon) {
                    addIconPreview.innerHTML = `<i class="${selectedIcon}"></i>`;
                } else {
                    addIconPreview.innerHTML = '<i class="bi bi-question-circle"></i>';
                }
            });
        }

        // Icon preview functionality for Edit Feature Modal
        const editFeatureIconSelect = document.getElementById('edit-feature-icon');
        const editIconPreview = document.getElementById('edit-icon-preview');

        if (editFeatureIconSelect && editIconPreview) {
            editFeatureIconSelect.addEventListener('change', function() {
                const selectedIcon = this.value;
                if (selectedIcon) {
                    editIconPreview.innerHTML = `<i class="${selectedIcon}"></i>`;
                } else {
                    editIconPreview.innerHTML = '<i class="bi bi-question-circle"></i>';
                }
            });
        }

        // Icon preview in select
        const iconSelects = document.querySelectorAll('#add-feature-icon, #edit-feature-icon');
        iconSelects.forEach(select => {
            select.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    // You could add icon preview here if needed
                }
            });
        });

        // Handle Edit Feature Modal
        const editFeatureModal = document.getElementById('edit-feature');
        if (editFeatureModal) {
            editFeatureModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const icon = button.getAttribute('data-icon');
                const title = button.getAttribute('data-title');
                const description = button.getAttribute('data-description');
                const status = button.getAttribute('data-status') === '1';

                // Update form action
                const form = document.getElementById('edit-feature-form');
                form.action = `{{ url('home-page/features') }}/${id}`;

                // Fill form fields
                document.getElementById('edit-feature-icon').value = icon;
                document.getElementById('edit-feature-title').value = title;
                document.getElementById('edit-feature-description').value = description;
                document.getElementById('edit-feature-status').checked = status;
                
                // Update icon preview
                if (icon && editIconPreview) {
                    editIconPreview.innerHTML = `<i class="${icon}"></i>`;
                }
            });
        }

        // Handle Edit Featured Unit Modal
        const editFeaturedUnitModal = document.getElementById('edit-featured-unit');
        if (editFeaturedUnitModal) {
            editFeaturedUnitModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const unitId = button.getAttribute('data-unit-id');
                const status = button.getAttribute('data-status') === '1';

                // Update form action
                const form = document.getElementById('edit-featured-unit-form');
                form.action = `{{ url('home-page/featured-units') }}/${id}`;

                // Fill form fields
                const unitRadio = document.querySelector(`#edit-featured-unit input[name="unit_id"][value="${unitId}"]`);
                if (unitRadio) {
                    unitRadio.checked = true;
                    // Trigger change event to show selection
                    unitRadio.dispatchEvent(new Event('change'));
                }
                
                document.getElementById('edit-featured-unit-status').checked = status;
            });
        }

        // Handle Delete Feature
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-feature-btn') || e.target.closest('.delete-feature-btn')) {
                e.preventDefault();
                
                const button = e.target.classList.contains('delete-feature-btn') ? e.target : e.target.closest('.delete-feature-btn');
                const itemName = button.getAttribute('data-name');
                const deleteUrl = button.getAttribute('data-url');
                
                // Update modal content
                const deleteForm = document.getElementById('delete-form');
                const deleteMessage = document.getElementById('delete-message');
                
                if (deleteForm && deleteMessage) {
                    deleteForm.action = deleteUrl;
                    deleteMessage.innerHTML = `Do you really want to delete feature "<strong>${itemName}</strong>"? This process cannot be undone.`;
                    
                    // Show modal
                    const deleteModal = new bootstrap.Modal(document.getElementById('delete-modal'));
                    deleteModal.show();
                }
            }
        });

        // Handle Delete Featured Unit
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-unit-btn') || e.target.closest('.delete-unit-btn')) {
                e.preventDefault();
                
                const button = e.target.classList.contains('delete-unit-btn') ? e.target : e.target.closest('.delete-unit-btn');
                const itemName = button.getAttribute('data-name');
                const deleteUrl = button.getAttribute('data-url');
                
                // Update modal content
                const deleteForm = document.getElementById('delete-form');
                const deleteMessage = document.getElementById('delete-message');
                
                if (deleteForm && deleteMessage) {
                    deleteForm.action = deleteUrl;
                    deleteMessage.innerHTML = `Do you really want to remove unit "<strong>${itemName}</strong>" from featured units? This process cannot be undone.`;
                    
                    // Show modal
                    const deleteModal = new bootstrap.Modal(document.getElementById('delete-modal'));
                    deleteModal.show();
                }
            }
        });

        // Feature Reorder functionality
        let featureSortable = null;
        let isFeatureReorderMode = false;
        
        const toggleFeatureReorderBtn = document.getElementById('toggle-feature-reorder');
        const featureReorderText = document.getElementById('feature-reorder-text');
        const featureContainer = document.getElementById('sortable-features');
        
        if (toggleFeatureReorderBtn && featureContainer) {
            toggleFeatureReorderBtn.addEventListener('click', function() {
                isFeatureReorderMode = !isFeatureReorderMode;
                
                if (isFeatureReorderMode) {
                    enableFeatureReorderMode();
                } else {
                    disableFeatureReorderMode();
                }
            });
        }

        function enableFeatureReorderMode() {
            const reorderHandles = document.querySelectorAll('.feature-reorder-handle');
            reorderHandles.forEach(handle => handle.style.display = 'block');
            
            if (featureContainer) featureContainer.classList.add('feature-reorder-mode');
            
            if (toggleFeatureReorderBtn) {
                toggleFeatureReorderBtn.classList.remove('btn-outline-secondary');
                toggleFeatureReorderBtn.classList.add('btn-success');
            }
            if (featureReorderText) featureReorderText.textContent = 'Done';
            
            const icon = toggleFeatureReorderBtn?.querySelector('i');
            if (icon) icon.className = 'ti ti-check me-1';
            
            featureSortable = new Sortable(featureContainer, {
                handle: '.feature-reorder-handle',
                animation: 150,
                ghostClass: 'sortable-feature-ghost',
                chosenClass: 'sortable-feature-chosen',
                dragClass: 'sortable-feature-drag',
                onEnd: function(evt) {
                    updateFeatureOrder();
                }
            });
            
            showFeatureReorderInstructions();
        }

        function disableFeatureReorderMode() {
            const reorderHandles = document.querySelectorAll('.feature-reorder-handle');
            reorderHandles.forEach(handle => handle.style.display = 'none');
            
            if (featureContainer) featureContainer.classList.remove('feature-reorder-mode');
            
            if (toggleFeatureReorderBtn) {
                toggleFeatureReorderBtn.classList.remove('btn-success');
                toggleFeatureReorderBtn.classList.add('btn-outline-secondary');
            }
            if (featureReorderText) featureReorderText.textContent = 'Reorder';
            
            const icon = toggleFeatureReorderBtn?.querySelector('i');
            if (icon) icon.className = 'ti ti-arrows-sort me-1';
            
            if (featureSortable) {
                featureSortable.destroy();
                featureSortable = null;
            }
            
            hideFeatureReorderInstructions();
        }

        function updateFeatureOrder() {
            const items = document.querySelectorAll('.sortable-feature-item');
            const orderData = [];
            
            items.forEach((item, index) => {
                const id = item.getAttribute('data-id');
                orderData.push({
                    id: id,
                    order: index + 1
                });
            });
            
            fetch('{{ route('home-page.features.reorder') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    orders: orderData
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Feature order updated successfully', 'success');
                } else {
                    showToast('Failed to update feature order', 'error');
                    setTimeout(() => location.reload(), 1000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to update feature order', 'error');
                setTimeout(() => location.reload(), 1000);
            });
        }

        function showFeatureReorderInstructions() {
            const instruction = document.createElement('div');
            instruction.id = 'feature-reorder-instruction';
            instruction.className = 'alert alert-info alert-dismissible mb-3';
            instruction.innerHTML = `
                <div class="d-flex">
                    <div>
                        <h4>Feature Reorder Mode Active</h4>
                        Drag the <i class="ti ti-grip-vertical"></i> handle to reorder features. Editing options are disabled during reorder mode.
                    </div>
                </div>
            `;
            
            if (featureContainer && featureContainer.parentElement) {
                featureContainer.parentElement.insertBefore(instruction, featureContainer);
            }
        }

        function hideFeatureReorderInstructions() {
            const instruction = document.getElementById('feature-reorder-instruction');
            if (instruction) {
                instruction.remove();
            }
        }

        // Featured Unit Reorder functionality
        let unitSortable = null;
        let isUnitReorderMode = false;
        
        const toggleUnitReorderBtn = document.getElementById('toggle-unit-reorder');
        const unitReorderText = document.getElementById('unit-reorder-text');
        const unitContainer = document.getElementById('sortable-units');
        
        if (toggleUnitReorderBtn && unitContainer) {
            toggleUnitReorderBtn.addEventListener('click', function() {
                isUnitReorderMode = !isUnitReorderMode;
                
                if (isUnitReorderMode) {
                    enableUnitReorderMode();
                } else {
                    disableUnitReorderMode();
                }
            });
        }

        function enableUnitReorderMode() {
            const reorderHandles = document.querySelectorAll('.unit-reorder-handle');
            reorderHandles.forEach(handle => handle.style.display = 'block');
            
            if (unitContainer) unitContainer.classList.add('unit-reorder-mode');
            
            if (toggleUnitReorderBtn) {
                toggleUnitReorderBtn.classList.remove('btn-outline-secondary');
                toggleUnitReorderBtn.classList.add('btn-success');
            }
            if (unitReorderText) unitReorderText.textContent = 'Done';
            
            const icon = toggleUnitReorderBtn?.querySelector('i');
            if (icon) icon.className = 'ti ti-check me-1';
            
            unitSortable = new Sortable(unitContainer, {
                handle: '.unit-reorder-handle',
                animation: 150,
                ghostClass: 'sortable-unit-ghost',
                chosenClass: 'sortable-unit-chosen',
                dragClass: 'sortable-unit-drag',
                onEnd: function(evt) {
                    updateUnitOrder();
                }
            });
            
            showUnitReorderInstructions();
        }

        function disableUnitReorderMode() {
            const reorderHandles = document.querySelectorAll('.unit-reorder-handle');
            reorderHandles.forEach(handle => handle.style.display = 'none');
            
            if (unitContainer) unitContainer.classList.remove('unit-reorder-mode');
            
            if (toggleUnitReorderBtn) {
                toggleUnitReorderBtn.classList.remove('btn-success');
                toggleUnitReorderBtn.classList.add('btn-outline-secondary');
            }
            if (unitReorderText) unitReorderText.textContent = 'Reorder';
            
            const icon = toggleUnitReorderBtn?.querySelector('i');
            if (icon) icon.className = 'ti ti-arrows-sort me-1';
            
            if (unitSortable) {
                unitSortable.destroy();
                unitSortable = null;
            }
            
            hideUnitReorderInstructions();
        }

        function updateUnitOrder() {
            const items = document.querySelectorAll('.sortable-unit-item');
            const orderData = [];
            
            items.forEach((item, index) => {
                const id = item.getAttribute('data-id');
                orderData.push({
                    id: id,
                    order: index + 1
                });
            });
            
            fetch('{{ route('home-page.featured-units.reorder') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    orders: orderData
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Featured unit order updated successfully', 'success');
                } else {
                    showToast('Failed to update featured unit order', 'error');
                    setTimeout(() => location.reload(), 1000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to update featured unit order', 'error');
                setTimeout(() => location.reload(), 1000);
            });
        }

        function showUnitReorderInstructions() {
            const instruction = document.createElement('div');
            instruction.id = 'unit-reorder-instruction';
            instruction.className = 'alert alert-info alert-dismissible mb-3';
            instruction.innerHTML = `
                <div class="d-flex">
                    <div>
                        <h4>Featured Unit Reorder Mode Active</h4>
                        Drag the <i class="ti ti-grip-vertical"></i> handle to reorder featured units. Editing options are disabled during reorder mode.
                    </div>
                </div>
            `;
            
            if (unitContainer && unitContainer.parentElement) {
                unitContainer.parentElement.insertBefore(instruction, unitContainer);
            }
        }

        function hideUnitReorderInstructions() {
            const instruction = document.getElementById('unit-reorder-instruction');
            if (instruction) {
                instruction.remove();
            }
        }

        // Reset modal forms
        const addFeatureModal = document.getElementById('add-feature');
        if (addFeatureModal) {
            addFeatureModal.addEventListener('show.bs.modal', function () {
                const form = addFeatureModal.querySelector('form');
                if (form) {
                    form.reset();
                    const statusInput = form.querySelector('input[name="status"]');
                    if (statusInput) statusInput.checked = true;
                }
            });
        }

        const addUnitModal = document.getElementById('add-featured-unit');
        if (addUnitModal) {
            addUnitModal.addEventListener('show.bs.modal', function () {
                const form = addUnitModal.querySelector('form');
                if (form) {
                    form.reset();
                    const statusInput = form.querySelector('input[name="status"]');
                    if (statusInput) statusInput.checked = true;
                }
            });
        }
    });
</script>
@endpush