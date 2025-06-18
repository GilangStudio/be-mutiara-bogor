@extends('layouts.main')

@section('title', 'Edit Project')

@push('styles')
<style>
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
    
    .form-control:focus {
        border-color: #0054a6;
        box-shadow: 0 0 0 0.2rem rgba(0, 84, 166, 0.25);
    }
    
    .card-header h3 {
        margin-bottom: 0;
    }
    
    .page-subtitle {
        color: #6c757d;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    .form-hint {
        color: #6c757d;
        font-size: 0.75rem;
    }
    
    .input-group-text {
        font-size: 0.875rem;
    }
    
    .alert-info {
        background-color: #e3f2fd;
        border-color: #bbdefb;
        color: #1565c0;
    }
    
    .loading {
        pointer-events: none;
        opacity: 0.6;
    }
</style>
<style>
    /* Gallery reorder styling */
    .gallery-reorder-mode .current-gallery-item {
        border: 2px dashed #0054a6;
        background: #f8f9fa;
    }
    
    .gallery-reorder-mode .edit-input {
        pointer-events: none;
        opacity: 0.6;
    }
    
    .sortable-gallery-ghost {
        opacity: 0.5;
        background: #f8f9fa;
    }
    
    .sortable-gallery-chosen {
        background: #e3f2fd;
        transform: rotate(2deg);
    }
    
    .sortable-gallery-drag {
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        transform: rotate(3deg);
    }
    
    .cursor-move {
        cursor: move;
    }
    
    .form-label-sm {
        font-size: 0.75rem;
        margin-bottom: 0.25rem;
        font-weight: 500;
    }
    
    .form-control-sm {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .form-check-label {
        font-size: 0.75rem;
    }
    
    /* Delete state styling */
    .current-gallery-item[style*="grayscale"] {
        border: 2px dashed #dc3545;
        background-color: #f8f9fa;
    }
    
    /* Reorder instruction alert animation */
    #gallery-reorder-instruction {
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
</style>
<style>
    /* Facility Reorder Styles */
    .facility-reorder-mode .current-facility-item {
        border: 2px dashed #28a745;
        background: #f8f9fa;
    }

    .facility-reorder-mode .edit-input {
        pointer-events: none;
        opacity: 0.6;
    }

    .facility-reorder-mode .facility-item {
        border: 2px dashed #28a745;
        background: #f8f9fa;
    }

    .facility-reorder-mode .card-body a {
        pointer-events: none;
        opacity: 0.5;
    }

    .facility-reorder-mode img {
        pointer-events: none;
    }

    .sortable-facility-ghost {
        opacity: 0.5;
        background: #f8f9fa;
    }

    .sortable-facility-chosen {
        background: #d4edda;
        transform: rotate(2deg);
    }

    .sortable-facility-drag {
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        transform: rotate(3deg);
    }

    .facility-item {
        transition: all 0.3s ease;
        position: relative;
    }

    .facility-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .current-facility-item[style*="grayscale"] {
        border: 2px dashed #dc3545;
        background-color: #f8f9fa;
    }

    #facility-reorder-instruction {
        animation: slideDown 0.3s ease-out;
    }
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h2 class="page-title">Edit Project</h2>
        <div class="page-subtitle">{{ $project->name }}</div>
    </div>
    <div class="btn-list">
        <a href="{{ route('development.project.show', $project) }}" class="btn btn-outline-info">
            <i class="ti ti-eye me-1"></i> View Project
        </a>
        <a href="{{ route('development.project.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Back to Projects
        </a>
    </div>
</div>
@endsection

@section('content')
{{-- Alert Messages --}}
@if(session('error'))
<div class="alert alert-danger alert-dismissible" role="alert">
    <div class="d-flex">
        <div>
            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M12 9v4"></path>
                <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.535a1.914 1.914 0 0 0 -3.274 0z"></path>
                <path d="M12 16h.01"></path>
            </svg>
        </div>
        <div>{{ session('error') }}</div>
    </div>
    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
</div>
@endif

<form action="{{ route('development.project.update', $project) }}" method="POST" enctype="multipart/form-data" id="edit-form">
    @csrf
    @method('PUT')
    <div class="row g-3">
        {{-- Basic Information --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-info-circle me-2"></i>
                        Basic Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Project Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name', $project->name) }}" required 
                                       placeholder="Enter project name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" name="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('category_id', $project->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Short Description</label>
                                <textarea class="form-control @error('short_description') is-invalid @enderror" 
                                          name="short_description" rows="3" 
                                          placeholder="Brief description that will be displayed as preview text..."
                                          maxlength="500">{{ old('short_description', $project->short_description) }}</textarea>
                                @error('short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    <span id="short-desc-count">{{ strlen($project->short_description ?? '') }}</span>/500 characters
                                </small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Full Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          name="description" id="editor" rows="8" 
                                          placeholder="Detailed project description...">{{ old('description', $project->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Provide comprehensive details about the project.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Settings --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-settings me-2"></i>
                        Settings
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" value="1" 
                                   {{ old('status', $project->is_active) ? 'checked' : '' }}>
                            <span class="form-check-label">Active Status</span>
                        </label>
                        <small class="form-hint">Enable this project to be displayed publicly on your website.</small>
                    </div>
                    
                    <div class="mb-0">
                        <label class="form-label text-secondary">Project Slug</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">{{ url('/') }}/</span>
                            <input type="text" class="form-control bg-light" value="{{ $project->slug }}" readonly>
                        </div>
                        <small class="form-hint">URL slug will be automatically generated from project name.</small>
                    </div>
                </div>
            </div>

            {{-- Current Images Preview --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-photo me-2"></i>
                        Current Images
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @if($project->main_image_url)
                        <div class="col-6">
                            <div class="card border">
                                <img src="{{ $project->main_image_url }}" class="card-img-top" style="height: 100px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <small class="text-secondary fw-medium">Main Image</small>
                                    <br>
                                    <a href="{{ $project->main_image_url }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                        <i class="ti ti-external-link"></i> View
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($project->banner_url)
                        <div class="col-6">
                            <div class="card border">
                                <img src="{{ $project->banner_url }}" class="card-img-top" style="height: 100px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <small class="text-secondary fw-medium">Banner</small>
                                    <br>
                                    <a href="{{ $project->banner_url }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                        <i class="ti ti-external-link"></i> View
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($project->logo_url)
                        <div class="col-6">
                            <div class="card border">
                                <img src="{{ $project->logo_url }}" class="card-img-top" style="height: 100px; object-fit: contain; background: #f8f9fa;">
                                <div class="card-body p-2">
                                    <small class="text-secondary fw-medium">Logo</small>
                                    <br>
                                    <a href="{{ $project->logo_url }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                        <i class="ti ti-external-link"></i> View
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($project->siteplan_image_url)
                        <div class="col-6">
                            <div class="card border">
                                <img src="{{ $project->siteplan_image_url }}" class="card-img-top" style="height: 100px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <small class="text-secondary fw-medium">Siteplan</small>
                                    <br>
                                    <a href="{{ $project->siteplan_image_url }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                        <i class="ti ti-external-link"></i> View
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    @if(!$project->main_image_url && !$project->banner_url && !$project->logo_url && !$project->siteplan_image_url)
                    <div class="empty">
                        <div class="empty-icon">
                            <i class="ti ti-photo-off"></i>
                        </div>
                        <p class="empty-title">No images uploaded</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Project Meta --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-info-square me-2"></i>
                        Project Meta
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-2">
                                <small class="text-secondary">Created:</small>
                                <div>{{ $project->created_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-2">
                                <small class="text-secondary">Last Updated:</small>
                                <div>{{ $project->updated_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-0">
                                <small class="text-secondary">Order Position:</small>
                                <div><span class="badge bg-blue-lt">{{ $project->order }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Image Upload Section --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-upload me-2"></i>
                        Update Images
                    </h3>
                    <div class="card-actions">
                        <span class="badge bg-blue-lt">
                            <i class="ti ti-info-circle me-1"></i>
                            Leave empty to keep current images
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <div class="d-flex">
                            <div>
                                All uploaded images will be automatically compressed and converted to WebP format for optimal performance.
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Main Image --}}
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">
                                    Main Image 
                                    @if(!$project->main_image_url)
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <input type="file" class="form-control @error('main_image') is-invalid @enderror" 
                                       name="main_image" accept="image/*" id="main-image">
                                @error('main_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    <i class="ti ti-info-circle me-1"></i>
                                    Recommended: 1200x800px, Max: 5MB
                                </small>
                                <div class="mt-2" id="main-image-preview"></div>
                            </div>
                        </div>

                        {{-- Banner Image --}}
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">
                                    Banner Image 
                                    @if(!$project->banner_url)
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <input type="file" class="form-control @error('banner_image') is-invalid @enderror" 
                                       name="banner_image" accept="image/*" id="banner-image">
                                @error('banner_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    <i class="ti ti-info-circle me-1"></i>
                                    Recommended: 1920x600px, Max: 5MB
                                </small>
                                <div class="mt-2" id="banner-image-preview"></div>
                            </div>
                        </div>

                        {{-- Logo Image --}}
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Logo Image <span class="text-secondary">(Optional)</span></label>
                                <input type="file" class="form-control @error('logo_image') is-invalid @enderror" 
                                       name="logo_image" accept="image/*" id="logo-image">
                                @error('logo_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    <i class="ti ti-info-circle me-1"></i>
                                    Recommended: 400x400px, Max: 2MB
                                </small>
                                <div class="mt-2" id="logo-image-preview"></div>
                            </div>
                        </div>

                        {{-- Siteplan Image --}}
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Siteplan Image <span class="text-secondary">(Optional)</span></label>
                                <input type="file" class="form-control @error('siteplan_image') is-invalid @enderror" 
                                       name="siteplan_image" accept="image/*" id="siteplan-image">
                                @error('siteplan_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    <i class="ti ti-info-circle me-1"></i>
                                    Recommended: 1920x1080px, Max: 5MB
                                </small>
                                <div class="mt-2" id="siteplan-image-preview"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Add New Gallery Images Section --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-photo-plus me-2"></i>
                        Add New Gallery Images
                    </h3>
                    <div class="card-actions">
                        <span class="badge bg-warning-lt">
                            <i class="ti ti-info-circle me-1"></i>
                            Optional
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">New Gallery Images</label>
                        <input type="file" class="form-control @error('gallery_images.*') is-invalid @enderror" 
                            name="gallery_images[]" accept="image/*" multiple id="gallery-images">
                        @error('gallery_images.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">
                            <i class="ti ti-info-circle me-1"></i>
                            Select multiple images to add to gallery. Max: 5MB per image
                        </small>
                    </div>
                    
                    <div id="gallery-preview" class="row g-3"></div>
                </div>
            </div>
        </div>

        {{-- Current Gallery Images Section - Add after main images section --}}
        @if($project->images->count() > 0)
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-photo me-2"></i>
                        Current Gallery Images
                    </h3>
                    <div class="card-actions">
                        <span class="badge bg-blue-lt me-2">{{ $project->images->count() }} Images</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="toggle-gallery-reorder">
                            <i class="ti ti-arrows-sort me-1"></i> <span id="gallery-reorder-text">Reorder</span>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <div class="d-flex">
                            <div>
                                <h4>Manage Gallery Images</h4>
                                Update metadata, mark for deletion, or click <strong>"Reorder"</strong> button to drag & drop images to change their order.
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3" id="sortable-gallery">
                        @foreach($project->images as $image)
                        <div class="col-md-4 sortable-gallery-item" data-id="{{ $image->id }}" data-image-id="{{ $image->id }}">
                            <div class="card current-gallery-item">
                                {{-- Reorder Handle --}}
                                <div class="gallery-reorder-handle" style="display: none;">
                                    <div class="position-absolute top-0 start-0 m-2 cursor-move bg-secondary text-white rounded p-1" style="z-index: 10;">
                                        <i class="ti ti-grip-vertical"></i>
                                    </div>
                                </div>
                                
                                <div class="ribbon ribbon-top bg-blue">
                                    #<span class="order-number">{{ $image->order }}</span>
                                </div>
                                <img src="{{ $image->image_url }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                                <div class="card-body p-3">
                                    <div class="mb-2">
                                        <label class="form-label form-label-sm">Alt Text</label>
                                        <input type="text" class="form-control form-control-sm edit-input" 
                                            name="existing_gallery_alt_texts[{{ $image->id }}]" 
                                            value="{{ $image->alt_text }}"
                                            placeholder="Describe the image">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label form-label-sm">Caption</label>
                                        <textarea class="form-control form-control-sm edit-input" rows="2" 
                                                name="existing_gallery_captions[{{ $image->id }}]" 
                                                placeholder="Image caption">{{ $image->caption }}</textarea>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input type="checkbox" class="form-check-input edit-input" 
                                            name="delete_gallery_images[]" 
                                            value="{{ $image->id }}"
                                            id="delete-{{ $image->id }}">
                                        <label class="form-check-label text-danger" for="delete-{{ $image->id }}">
                                            <i class="ti ti-trash me-1"></i>Delete this image
                                        </label>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-secondary">
                                            <i class="ti ti-calendar me-1"></i>
                                            {{ $image->created_at->format('d M Y') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

        

        {{-- Add New Facility Images Section --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-building-plus me-2"></i>
                        Add New Facility Images
                    </h3>
                    <div class="card-actions">
                        <span class="badge bg-warning-lt">
                            <i class="ti ti-info-circle me-1"></i>
                            Optional
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">New Facility Images</label>
                        <input type="file" class="form-control @error('facility_images.*') is-invalid @enderror" 
                            name="facility_images[]" accept="image/*" multiple id="facility-images">
                        @error('facility_images.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">
                            <i class="ti ti-info-circle me-1"></i>
                            Select multiple images to add to facility gallery. Max: 5MB per image
                        </small>
                    </div>
                    
                    <div id="facility-preview" class="row g-3"></div>
                </div>
            </div>
        </div>

        {{-- Current Facility Images Section - Add after Gallery Section --}}
        @if($project->facilityImages->count() > 0)
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-building me-2"></i>
                        Current Facility Images
                    </h3>
                    <div class="card-actions">
                        <span class="badge bg-blue-lt me-2">{{ $project->facilityImages->count() }} Images</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="toggle-facility-reorder">
                            <i class="ti ti-arrows-sort me-1"></i> <span id="facility-reorder-text">Reorder</span>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <div class="d-flex">
                            <div>
                                <h4>Manage Facility Images</h4>
                                Update metadata, mark for deletion, or click <strong>"Reorder"</strong> button to drag & drop images to change their order.
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3" id="sortable-facility">
                        @foreach($project->facilityImages as $facilityImage)
                        <div class="col-md-4 sortable-facility-item" data-id="{{ $facilityImage->id }}" data-image-id="{{ $facilityImage->id }}">
                            <div class="card current-facility-item">
                                {{-- Reorder Handle --}}
                                <div class="facility-reorder-handle" style="display: none;">
                                    <div class="position-absolute top-0 start-0 m-2 cursor-move bg-secondary text-white rounded p-1" style="z-index: 10;">
                                        <i class="ti ti-grip-vertical"></i>
                                    </div>
                                </div>
                                
                                <div class="ribbon ribbon-top bg-green">
                                    #<span class="order-number">{{ $facilityImage->order }}</span>
                                </div>
                                <img src="{{ $facilityImage->image_url }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                                <div class="card-body p-3">
                                    <div class="mb-2">
                                        <label class="form-label form-label-sm">Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm edit-input" 
                                            name="existing_facility_titles[{{ $facilityImage->id }}]" 
                                            value="{{ $facilityImage->title }}"
                                            placeholder="Facility name" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label form-label-sm">Description</label>
                                        <textarea class="form-control form-control-sm edit-input" rows="2" 
                                                name="existing_facility_descriptions[{{ $facilityImage->id }}]" 
                                                placeholder="Facility description">{{ $facilityImage->description }}</textarea>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label form-label-sm">Alt Text</label>
                                        <input type="text" class="form-control form-control-sm edit-input" 
                                            name="existing_facility_alt_texts[{{ $facilityImage->id }}]" 
                                            value="{{ $facilityImage->alt_text }}"
                                            placeholder="Image description">
                                    </div>
                                    <div class="form-check mb-2">
                                        <input type="checkbox" class="form-check-input edit-input" 
                                            name="delete_facility_images[]" 
                                            value="{{ $facilityImage->id }}"
                                            id="delete-facility-{{ $facilityImage->id }}">
                                        <label class="form-check-label text-danger" for="delete-facility-{{ $facilityImage->id }}">
                                            <i class="ti ti-trash me-1"></i>Delete this image
                                        </label>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-secondary">
                                            <i class="ti ti-calendar me-1"></i>
                                            {{ $facilityImage->created_at->format('d M Y') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

        

        {{-- Submit Buttons --}}
        <div class="col-12">
            <div class="card">
                <div class="card-footer bg-transparent text-end">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-secondary">
                                <i class="ti ti-clock me-1"></i>
                                Last saved: {{ $project->updated_at->format('d M Y, H:i') }}
                            </small>
                        </div>
                        <div class="btn-list">
                            <a href="{{ route('development.project.index') }}" class="btn btn-link">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <i class="ti ti-device-floppy me-1"></i> 
                                Update Project
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
@include('components.scripts.wysiwyg')
@include('components.alert')
@include('components.toast')

@if(session('success'))
    <script>
        showToast('{{ session('success') }}', 'success');
    </script>
@endif
@if(session('error'))
    <script>
        showToast('{{ session('error') }}','error');
    </script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Character counter for short description
        const shortDescTextarea = document.querySelector('textarea[name="short_description"]');
        const charCount = document.getElementById('short-desc-count');
        
        shortDescTextarea.addEventListener('input', function() {
            const currentLength = this.value.length;
            charCount.textContent = currentLength;
            
            if (currentLength > 450) {
                charCount.parentElement.classList.add('text-warning');
            } else if (currentLength > 500) {
                charCount.parentElement.classList.remove('text-warning');
                charCount.parentElement.classList.add('text-danger');
            } else {
                charCount.parentElement.classList.remove('text-warning', 'text-danger');
            }
        });

        // Image preview functionality with enhanced UI
        function setupImagePreview(inputId, previewId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validate file size
                    const maxSize = inputId.includes('logo') ? 2 * 1024 * 1024 : 5 * 1024 * 1024; // 2MB for logo, 5MB for others
                    if (file.size > maxSize) {
                        showAlert(input, 'danger', `File size too large. Maximum ${inputId.includes('logo') ? '2MB' : '5MB'} allowed.`);
                        input.value = '';
                        return;
                    }

                    // Validate file type
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

        // Clear preview function
        window.clearPreview = function(inputId, previewId) {
            document.getElementById(inputId).value = '';
            document.getElementById(previewId).innerHTML = '';
        };

        // Setup previews for all image inputs
        setupImagePreview('main-image', 'main-image-preview');
        setupImagePreview('banner-image', 'banner-image-preview');
        setupImagePreview('logo-image', 'logo-image-preview');
        setupImagePreview('siteplan-image', 'siteplan-image-preview');

        // Gallery images handling for new uploads
        const galleryInput = document.getElementById('gallery-images');
        const galleryPreview = document.getElementById('gallery-preview');
        let galleryFiles = [];

        if (galleryInput) {
            galleryInput.addEventListener('change', function(e) {
                const files = Array.from(e.target.files);
                galleryFiles = files;
                renderGalleryPreview();
            });
        }

        function renderGalleryPreview() {
            galleryPreview.innerHTML = '';
            
            if (galleryFiles.length === 0) {
                return;
            }

            galleryFiles.forEach((file, index) => {
                if (file) {
                    // Validate file
                    if (!file.type.startsWith('image/')) {
                        showAlert(galleryInput, 'danger', `File ${file.name} bukan gambar yang valid`);
                        return;
                    }
                    
                    if (file.size > 5 * 1024 * 1024) {
                        showAlert(galleryInput, 'danger', `File ${file.name} terlalu besar (max: 5MB)`);
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const col = document.createElement('div');
                        col.className = 'col-md-4';
                        col.innerHTML = `
                            <div class="card gallery-item">
                                <div class="ribbon ribbon-top bg-green">
                                    New
                                </div>
                                <img src="${e.target.result}" class="card-img-top" style="height: 200px; object-fit: cover;">
                                <div class="card-body p-3">
                                    <div class="mb-2">
                                        <label class="form-label form-label-sm">Alt Text</label>
                                        <input type="text" class="form-control form-control-sm" 
                                            name="gallery_alt_texts[${index}]" 
                                            placeholder="Describe the image">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label form-label-sm">Caption</label>
                                        <textarea class="form-control form-control-sm" rows="2" 
                                                name="gallery_captions[${index}]" 
                                                placeholder="Image caption"></textarea>
                                    </div>
                                    <div class="text-center mb-2">
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="removeGalleryImage(${index})">
                                            <i class="ti ti-trash"></i> Remove
                                        </button>
                                    </div>
                                    <small class="text-secondary">
                                        ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)
                                    </small>
                                </div>
                            </div>
                        `;
                        galleryPreview.appendChild(col);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Remove new gallery image function
        window.removeGalleryImage = function(index) {
            galleryFiles.splice(index, 1);
            
            // Create new FileList
            const dt = new DataTransfer();
            galleryFiles.forEach(file => dt.items.add(file));
            galleryInput.files = dt.files;
            
            renderGalleryPreview();
        };

        // Handle existing gallery image deletion
        const deleteCheckboxes = document.querySelectorAll('input[name="delete_gallery_images[]"]');
        deleteCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const card = this.closest('.current-gallery-item');
                if (this.checked) {
                    card.classList.add('opacity-50');
                    card.style.filter = 'grayscale(100%)';
                    card.style.border = '2px dashed #dc3545';
                    
                    // Add visual indicator
                    let indicator = card.querySelector('.delete-indicator');
                    if (!indicator) {
                        indicator = document.createElement('div');
                        indicator.className = 'delete-indicator position-absolute top-50 start-50 translate-middle';
                        indicator.innerHTML = `
                            <div class="bg-danger text-white rounded-circle p-2" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="ti ti-trash" style="font-size: 1.5rem;"></i>
                            </div>
                        `;
                        card.style.position = 'relative';
                        card.appendChild(indicator);
                    }
                } else {
                    card.classList.remove('opacity-50');
                    card.style.filter = 'none';
                    card.style.border = '';
                    
                    // Remove visual indicator
                    const indicator = card.querySelector('.delete-indicator');
                    if (indicator) {
                        indicator.remove();
                    }
                }
            });
        });

        // Gallery Reorder functionality
        let gallerySortable = null;
        let isGalleryReorderMode = false;
        
        const toggleGalleryReorderBtn = document.getElementById('toggle-gallery-reorder');
        const galleryReorderText = document.getElementById('gallery-reorder-text');
        const galleryContainer = document.getElementById('sortable-gallery');
        
        if (toggleGalleryReorderBtn && galleryContainer) {
            toggleGalleryReorderBtn.addEventListener('click', function() {
                isGalleryReorderMode = !isGalleryReorderMode;
                
                if (isGalleryReorderMode) {
                    enableGalleryReorderMode();
                } else {
                    disableGalleryReorderMode();
                }
            });
        }

        function enableGalleryReorderMode() {
            // Show reorder handles
            const reorderHandles = document.querySelectorAll('.gallery-reorder-handle');
            reorderHandles.forEach(handle => handle.style.display = 'block');
            
            // Add reorder mode class
            galleryContainer.classList.add('gallery-reorder-mode');
            
            // Update button
            toggleGalleryReorderBtn.classList.remove('btn-outline-secondary');
            toggleGalleryReorderBtn.classList.add('btn-success');
            galleryReorderText.textContent = 'Done';
            toggleGalleryReorderBtn.querySelector('i').className = 'ti ti-check me-1';
            
            // Initialize sortable
            gallerySortable = new Sortable(galleryContainer, {
                handle: '.gallery-reorder-handle',
                animation: 150,
                ghostClass: 'sortable-gallery-ghost',
                chosenClass: 'sortable-gallery-chosen',
                dragClass: 'sortable-gallery-drag',
                onEnd: function(evt) {
                    updateGalleryOrder();
                }
            });
            
            // Show instructions
            showGalleryReorderInstructions();
        }

        function disableGalleryReorderMode() {
            // Hide reorder handles
            const reorderHandles = document.querySelectorAll('.gallery-reorder-handle');
            reorderHandles.forEach(handle => handle.style.display = 'none');
            
            // Remove reorder mode class
            galleryContainer.classList.remove('gallery-reorder-mode');
            
            // Update button
            toggleGalleryReorderBtn.classList.remove('btn-success');
            toggleGalleryReorderBtn.classList.add('btn-outline-secondary');
            galleryReorderText.textContent = 'Reorder';
            toggleGalleryReorderBtn.querySelector('i').className = 'ti ti-arrows-sort me-1';
            
            // Destroy sortable
            if (gallerySortable) {
                gallerySortable.destroy();
                gallerySortable = null;
            }
            
            // Hide instructions
            hideGalleryReorderInstructions();
        }

        function updateGalleryOrder() {
            const items = document.querySelectorAll('.sortable-gallery-item');
            const orderData = [];
            
            items.forEach((item, index) => {
                const id = item.getAttribute('data-id');
                const newOrder = index + 1;
                
                orderData.push({
                    id: id,
                    order: newOrder
                });
                
                // Update order display in UI
                const orderNumber = item.querySelector('.order-number');
                const orderDisplay = item.querySelector('.order-display');
                if (orderNumber) orderNumber.textContent = newOrder;
                if (orderDisplay) orderDisplay.textContent = newOrder;
            });
            
            // Send AJAX request to update order
            fetch('{{ route('development.project.gallery.reorder') }}', {
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
                    showToast('Gallery order updated successfully', 'success');
                } else {
                    showToast('Failed to update gallery order', 'error');
                    setTimeout(() => location.reload(), 1000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to update gallery order', 'error');
                setTimeout(() => location.reload(), 1000);
            });
        }

        function showGalleryReorderInstructions() {
            const instruction = document.createElement('div');
            instruction.id = 'gallery-reorder-instruction';
            instruction.className = 'alert alert-info alert-dismissible mb-3';
            instruction.innerHTML = `
                <div class="d-flex">
                    <div>
                        <h4>Gallery Reorder Mode Active</h4>
                        Drag the <i class="ti ti-grip-vertical"></i> handle to reorder gallery images. Editing inputs are disabled during reorder mode.
                    </div>
                </div>
            `;
            
            // Insert before gallery container
            galleryContainer.parentElement.insertBefore(instruction, galleryContainer);
        }

        function hideGalleryReorderInstructions() {
            const instruction = document.getElementById('gallery-reorder-instruction');
            if (instruction) {
                instruction.remove();
            }
        }

        // Facility images handling for new uploads
        const facilityInput = document.getElementById('facility-images');
        const facilityPreview = document.getElementById('facility-preview');
        let facilityFiles = [];

        if (facilityInput) {
            facilityInput.addEventListener('change', function(e) {
                const files = Array.from(e.target.files);
                facilityFiles = files;
                renderFacilityPreview();
            });
        }

        function renderFacilityPreview() {
            facilityPreview.innerHTML = '';
            
            if (facilityFiles.length === 0) {
                return;
            }

            facilityFiles.forEach((file, index) => {
                if (file) {
                    // Validate file
                    if (!file.type.startsWith('image/')) {
                        showAlert(facilityInput, 'danger', `File ${file.name} is not a valid image`);
                        return;
                    }
                    
                    if (file.size > 5 * 1024 * 1024) {
                        showAlert(facilityInput, 'danger', `File ${file.name} is too large (max: 5MB)`);
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const col = document.createElement('div');
                        col.className = 'col-md-4';
                        col.innerHTML = `
                            <div class="card facility-item">
                                <div class="ribbon ribbon-top bg-green">
                                    New
                                </div>
                                <img src="${e.target.result}" class="card-img-top" style="height: 200px; object-fit: cover;">
                                <div class="card-body p-3">
                                    <div class="mb-2">
                                        <label class="form-label form-label-sm">Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm" 
                                            name="facility_titles[${index}]" 
                                            placeholder="Enter facility name" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label form-label-sm">Description</label>
                                        <textarea class="form-control form-control-sm" rows="2" 
                                                name="facility_descriptions[${index}]" 
                                                placeholder="Facility description"></textarea>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label form-label-sm">Alt Text</label>
                                        <input type="text" class="form-control form-control-sm" 
                                            name="facility_alt_texts[${index}]" 
                                            placeholder="Image description">
                                    </div>
                                    <div class="text-center mb-2">
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="removeFacilityImage(${index})">
                                            <i class="ti ti-trash"></i> Remove
                                        </button>
                                    </div>
                                    <small class="text-secondary">
                                        ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)
                                    </small>
                                </div>
                            </div>
                        `;
                        facilityPreview.appendChild(col);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Remove new facility image function
        window.removeFacilityImage = function(index) {
            facilityFiles.splice(index, 1);
            
            // Create new FileList
            const dt = new DataTransfer();
            facilityFiles.forEach(file => dt.items.add(file));
            facilityInput.files = dt.files;
            
            renderFacilityPreview();
        };

        // Handle existing facility image deletion
        const deleteFacilityCheckboxes = document.querySelectorAll('input[name="delete_facility_images[]"]');
        deleteFacilityCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const card = this.closest('.current-facility-item');
                if (this.checked) {
                    card.classList.add('opacity-50');
                    card.style.filter = 'grayscale(100%)';
                    card.style.border = '2px dashed #dc3545';
                    
                    // Add visual indicator
                    let indicator = card.querySelector('.delete-indicator');
                    if (!indicator) {
                        indicator = document.createElement('div');
                        indicator.className = 'delete-indicator position-absolute top-50 start-50 translate-middle';
                        indicator.innerHTML = `
                            <div class="bg-danger text-white rounded-circle p-2" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="ti ti-trash" style="font-size: 1.5rem;"></i>
                            </div>
                        `;
                        card.style.position = 'relative';
                        card.appendChild(indicator);
                    }
                } else {
                    card.classList.remove('opacity-50');
                    card.style.filter = 'none';
                    card.style.border = '';
                    
                    // Remove visual indicator
                    const indicator = card.querySelector('.delete-indicator');
                    if (indicator) {
                        indicator.remove();
                    }
                }
            });
        });

        // Facility Reorder functionality
        let facilitySortable = null;
        let isFacilityReorderMode = false;

        const toggleFacilityReorderBtn = document.getElementById('toggle-facility-reorder');
        const facilityReorderText = document.getElementById('facility-reorder-text');
        const facilityContainer = document.getElementById('sortable-facility');

        if (toggleFacilityReorderBtn && facilityContainer) {
            toggleFacilityReorderBtn.addEventListener('click', function() {
                isFacilityReorderMode = !isFacilityReorderMode;
                
                if (isFacilityReorderMode) {
                    enableFacilityReorderMode();
                } else {
                    disableFacilityReorderMode();
                }
            });
        }

        function enableFacilityReorderMode() {
            // Show reorder handles
            const reorderHandles = document.querySelectorAll('.facility-reorder-handle');
            reorderHandles.forEach(handle => handle.style.display = 'block');
            
            // Add reorder mode class
            facilityContainer.classList.add('facility-reorder-mode');
            
            // Update button
            toggleFacilityReorderBtn.classList.remove('btn-outline-secondary');
            toggleFacilityReorderBtn.classList.add('btn-success');
            facilityReorderText.textContent = 'Done';
            toggleFacilityReorderBtn.querySelector('i').className = 'ti ti-check me-1';
            
            // Initialize sortable
            facilitySortable = new Sortable(facilityContainer, {
                handle: '.facility-reorder-handle',
                animation: 150,
                ghostClass: 'sortable-facility-ghost',
                chosenClass: 'sortable-facility-chosen',
                dragClass: 'sortable-facility-drag',
                onEnd: function(evt) {
                    updateFacilityOrder();
                }
            });
            
            // Show instructions
            showFacilityReorderInstructions();
        }

        function disableFacilityReorderMode() {
            // Hide reorder handles
            const reorderHandles = document.querySelectorAll('.facility-reorder-handle');
            reorderHandles.forEach(handle => handle.style.display = 'none');
            
            // Remove reorder mode class
            facilityContainer.classList.remove('facility-reorder-mode');
            
            // Update button
            toggleFacilityReorderBtn.classList.remove('btn-success');
            toggleFacilityReorderBtn.classList.add('btn-outline-secondary');
            facilityReorderText.textContent = 'Reorder';
            toggleFacilityReorderBtn.querySelector('i').className = 'ti ti-arrows-sort me-1';
            
            // Destroy sortable
            if (facilitySortable) {
                facilitySortable.destroy();
                facilitySortable = null;
            }
            
            // Hide instructions
            hideFacilityReorderInstructions();
        }

        function updateFacilityOrder() {
            const items = document.querySelectorAll('.sortable-facility-item');
            const orderData = [];
            
            items.forEach((item, index) => {
                const id = item.getAttribute('data-id');
                const newOrder = index + 1;
                
                orderData.push({
                    id: id,
                    order: newOrder
                });
                
                // Update order display in UI
                const orderNumber = item.querySelector('.order-number');
                const orderDisplay = item.querySelector('.order-display');
                if (orderNumber) orderNumber.textContent = newOrder;
                if (orderDisplay) orderDisplay.textContent = newOrder;
            });
            
            // Send AJAX request to update order
            fetch('{{ route('development.project.facility.reorder') }}', {
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
                    showToast('Facility order updated successfully', 'success');
                } else {
                    showToast('Failed to update facility order', 'error');
                    setTimeout(() => location.reload(), 1000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to update facility order', 'error');
                setTimeout(() => location.reload(), 1000);
            });
        }

        function showFacilityReorderInstructions() {
            const instruction = document.createElement('div');
            instruction.id = 'facility-reorder-instruction';
            instruction.className = 'alert alert-info alert-dismissible mb-3';
            instruction.innerHTML = `
                <div class="d-flex">
                    <div>
                        <h4>Facility Reorder Mode Active</h4>
                        Drag the <i class="ti ti-grip-vertical"></i> handle to reorder facility images. Editing inputs are disabled during reorder mode.
                    </div>
                </div>
            `;
            
            // Insert before facility container
            facilityContainer.parentElement.insertBefore(instruction, facilityContainer);
        }

        function hideFacilityReorderInstructions() {
            const instruction = document.getElementById('facility-reorder-instruction');
            if (instruction) {
                instruction.remove();
            }
        }


        // Form submission with loading state
        const form = document.getElementById('edit-form');
        const submitBtn = document.getElementById('submit-btn');
        
        form.addEventListener('submit', function(e) {
            // Add loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating Project...';
            
            // Add loading class to form
            form.classList.add('loading');
            
            // Show processing message
            showProcessingMessage();
        });

        // Show processing message
        function showProcessingMessage() {
            const alert = document.createElement('div');
            alert.className = 'alert alert-info alert-dismissible fade show';
            alert.innerHTML = `
                <div class="d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M12 6l0 6l4 0"></path>
                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                        </svg>
                    </div>
                    <div>
                        <h4>Processing Update</h4>
                        Please wait while we process your project updates and optimize images...
                    </div>
                </div>
            `;
            
            // Insert at top of content
            const content = document.querySelector('#content');
            if (content) {
                content.insertBefore(alert, content.firstChild);
            }
        }

        // Auto-save functionality (optional)
        let autoSaveTimeout;
        const autoSaveInputs = form.querySelectorAll('input[type="text"], textarea, select');
        
        autoSaveInputs.forEach(input => {
            input.addEventListener('input', function() {
                clearTimeout(autoSaveTimeout);
                autoSaveTimeout = setTimeout(() => {
                    // You can implement auto-save to localStorage here
                    console.log('Auto-saving draft...');
                }, 2000);
            });
        });

        // Warn before leaving if form is dirty
        let formDirty = false;
        autoSaveInputs.forEach(input => {
            input.addEventListener('input', () => {
                formDirty = true;
            });
        });

        window.addEventListener('beforeunload', function(e) {
            if (formDirty && !form.classList.contains('loading')) {
                e.preventDefault();
                e.returnValue = '';
                return '';
            }
        });

        // Reset dirty flag on successful submission
        form.addEventListener('submit', () => {
            formDirty = false;
        });
    });
</script>
@endpush