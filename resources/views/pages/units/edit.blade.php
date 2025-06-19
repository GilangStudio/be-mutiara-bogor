@extends('layouts.main')

@section('title', 'Edit Unit - ' . $unit->name)

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
    
    .spec-row {
        border: 1px solid #e6e7e9;
        border-radius: 0.375rem;
        padding: 1rem;
        margin-bottom: 0.5rem;
        background: #f8f9fa;
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
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h2 class="page-title">Edit Unit</h2>
        <div class="page-subtitle">{{ $unit->name }} - {{ $project->name }}</div>
    </div>
    <div class="btn-list">
        <a href="{{ route('development.unit.index', $project) }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Back to Units
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

<form action="{{ route('development.unit.update', [$project, $unit]) }}" method="POST" enctype="multipart/form-data" id="edit-form">
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
                                <label class="form-label">Unit Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name', $unit->name) }}" required 
                                       placeholder="Enter unit name">
                                @error('name')
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
                                          maxlength="500">{{ old('short_description', $unit->short_description) }}</textarea>
                                @error('short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    <span id="short-desc-count">{{ strlen($unit->short_description ?? '') }}</span>/500 characters
                                </small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Full Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          name="description" id="editor" rows="8" 
                                          placeholder="Detailed unit description...">{{ old('description', $unit->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Provide comprehensive details about the unit.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SEO Meta --}}
            @include('components.seo-meta-form', ['data' => $unit, 'type' => 'edit'])

            {{-- Unit Specifications --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-info-circle me-2"></i>
                        Unit Specifications
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Bedrooms</label>
                                <input type="number" class="form-control @error('bedrooms') is-invalid @enderror" 
                                       name="bedrooms" value="{{ old('bedrooms', $unit->bedrooms) }}" min="0" placeholder="0">
                                @error('bedrooms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Bathrooms</label>
                                <input type="number" class="form-control @error('bathrooms') is-invalid @enderror" 
                                       name="bathrooms" value="{{ old('bathrooms', $unit->bathrooms) }}" min="0" placeholder="0">
                                @error('bathrooms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Carports</label>
                                <input type="number" class="form-control @error('carports') is-invalid @enderror" 
                                       name="carports" value="{{ old('carports', $unit->carports) }}" min="0" placeholder="0">
                                @error('carports')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Land Area</label>
                                <input type="text" class="form-control @error('land_area') is-invalid @enderror" 
                                       name="land_area" value="{{ old('land_area', $unit->land_area) }}" placeholder="e.g., 120 m²">
                                @error('land_area')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Building Area</label>
                                <input type="text" class="form-control @error('building_area') is-invalid @enderror" 
                                       name="building_area" value="{{ old('building_area', $unit->building_area) }}" placeholder="e.g., 90 m²">
                                @error('building_area')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Additional Specifications --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-list-details me-2"></i>
                        Additional Specifications
                    </h3>
                    <div class="card-actions">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="add-spec">
                            <i class="ti ti-plus me-1"></i> Add Specification
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="specs-container">
                        @foreach($unit->specifications as $spec)
                        <div class="spec-row">
                            <div class="row align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label form-label-sm">Specification Name</label>
                                    <input type="text" class="form-control form-control-sm" 
                                           name="spec_names[]" 
                                           placeholder="e.g., Floor Type, Ceiling Height"
                                           value="{{ $spec->name }}">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label form-label-sm">Value</label>
                                    <input type="text" class="form-control form-control-sm" 
                                           name="spec_values[]" 
                                           placeholder="e.g., Ceramic, 3.5m"
                                           value="{{ $spec->value }}">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="removeSpecRow(this)">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <small class="form-hint">Add custom specifications like materials, features, utilities, etc.</small>
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
                                   {{ old('status', $unit->is_active) ? 'checked' : '' }}>
                            <span class="form-check-label">Active Status</span>
                        </label>
                        <small class="form-hint">Enable this unit to be displayed publicly on your website.</small>
                    </div>
                    
                    <div class="mb-0">
                        <label class="form-label text-secondary">Unit Slug</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">{{ url('/') }}/</span>
                            <input type="text" class="form-control bg-light" value="{{ $unit->slug }}" readonly>
                        </div>
                        <small class="form-hint">URL slug will be automatically generated from unit name.</small>
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
                        @if($unit->main_image_url)
                        <div class="col-6">
                            <div class="card border">
                                <img src="{{ $unit->main_image_url }}" class="card-img-top" style="height: 100px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <small class="text-secondary fw-medium">Main Image</small>
                                    <br>
                                    <a href="{{ $unit->main_image_url }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                        <i class="ti ti-external-link"></i> View
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($unit->banner_url)
                        <div class="col-6">
                            <div class="card border">
                                <img src="{{ $unit->banner_url }}" class="card-img-top" style="height: 100px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <small class="text-secondary fw-medium">Banner</small>
                                    <br>
                                    <a href="{{ $unit->banner_url }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                        <i class="ti ti-external-link"></i> View
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($unit->floor_plan_image_url)
                        <div class="col-12">
                            <div class="card border">
                                <img src="{{ $unit->floor_plan_image_url }}" class="card-img-top" style="height: 100px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <small class="text-secondary fw-medium">Floor Plan</small>
                                    <br>
                                    <a href="{{ $unit->floor_plan_image_url }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                        <i class="ti ti-external-link"></i> View
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    @if(!$unit->main_image_url && !$unit->banner_url && !$unit->floor_plan_image_url)
                    <div class="empty">
                        <div class="empty-icon">
                            <i class="ti ti-photo-off"></i>
                        </div>
                        <p class="empty-title">No images uploaded</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Unit Meta --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-info-square me-2"></i>
                        Unit Meta
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-2">
                                <small class="text-secondary">Created:</small>
                                <div>{{ $unit->created_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-2">
                                <small class="text-secondary">Last Updated:</small>
                                <div>{{ $unit->updated_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-0">
                                <small class="text-secondary">Order Position:</small>
                                <div><span class="badge bg-blue-lt">{{ $unit->order }}</span></div>
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
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label class="form-label">
                                    Main Image 
                                    @if(!$unit->main_image_url)
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
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label class="form-label">
                                    Banner Image 
                                    @if(!$unit->banner_url)
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

                        {{-- Floor Plan Image --}}
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label class="form-label">Floor Plan Image <span class="text-secondary">(Optional)</span></label>
                                <input type="file" class="form-control @error('floor_plan_image') is-invalid @enderror" 
                                       name="floor_plan_image" accept="image/*" id="floor-plan-image">
                                @error('floor_plan_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    <i class="ti ti-info-circle me-1"></i>
                                    Recommended: 1200x800px, Max: 5MB
                                </small>
                                <div class="mt-2" id="floor-plan-image-preview"></div>
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

        {{-- Current Gallery Images Section --}}
        @if($unit->galleries->count() > 0)
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-photo me-2"></i>
                        Current Gallery Images
                    </h3>
                    <div class="card-actions">
                        <span class="badge bg-blue-lt me-2">{{ $unit->galleries->count() }} Images</span>
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
                        @foreach($unit->galleries as $image)
                        <div class="col-md-4 sortable-gallery-item" data-id="{{ $image->id }}" data-image-id="{{ $image->id }}">
                            <div class="card current-gallery-item">
                                {{-- Reorder Handle --}}
                                <div class="gallery-reorder-handle" style="display: none;">
                                    <div class="position-absolute top-0 start-0 m-2 cursor-move bg-primary text-white rounded p-1" style="z-index: 10;">
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

        {{-- Submit Buttons --}}
        <div class="col-12">
            <div class="card">
                <div class="card-footer bg-transparent text-end">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-secondary">
                                <i class="ti ti-clock me-1"></i>
                                Last saved: {{ $unit->updated_at->format('d M Y, H:i') }}
                            </small>
                        </div>
                        <div class="btn-list">
                            <a href="{{ route('development.unit.index', $project) }}" class="btn btn-link">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <i class="ti ti-device-floppy me-1"></i> 
                                Update Unit
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

        // Image preview functionality
        function setupImagePreview(inputId, previewId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validate file size
                    const maxSize = 5 * 1024 * 1024; // 5MB
                    if (file.size > maxSize) {
                        showAlert(input, 'danger', 'File size too large. Maximum 5MB allowed.');
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
        setupImagePreview('floor-plan-image', 'floor-plan-image-preview');

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
                        showAlert(galleryInput, 'danger', `File ${file.name} is not a valid image`);
                        return;
                    }
                    
                    if (file.size > 5 * 1024 * 1024) {
                        showAlert(galleryInput, 'danger', `File ${file.name} is too large (max: 5MB)`);
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
                if (orderNumber) orderNumber.textContent = newOrder;
            });
            
            // Send AJAX request to update order
            fetch('{{ route('development.unit.gallery.reorder', $project) }}', {
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

        // Specifications functionality
        let specIndex = {{ $unit->specifications->count() }};
        const specsContainer = document.getElementById('specs-container');
        const addSpecBtn = document.getElementById('add-spec');

        addSpecBtn.addEventListener('click', function() {
            addSpecificationRow();
        });

        function addSpecificationRow(name = '', value = '') {
            const specRow = document.createElement('div');
            specRow.className = 'spec-row';
            specRow.innerHTML = `
                <div class="row align-items-end">
                    <div class="col-md-5">
                        <label class="form-label form-label-sm">Specification Name</label>
                        <input type="text" class="form-control form-control-sm" 
                               name="spec_names[]" 
                               placeholder="e.g., Floor Type, Ceiling Height"
                               value="${name}">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label form-label-sm">Value</label>
                        <input type="text" class="form-control form-control-sm" 
                               name="spec_values[]" 
                               placeholder="e.g., Ceramic, 3.5m"
                               value="${value}">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-danger" 
                                onclick="removeSpecRow(this)">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            specsContainer.appendChild(specRow);
            specIndex++;
        }

        window.removeSpecRow = function(button) {
            button.closest('.spec-row').remove();
        };

        // Add initial specification row if none exist
        if (specsContainer.children.length === 0) {
            addSpecificationRow();
        }

        // Form submission with loading state
        const form = document.getElementById('edit-form');
        const submitBtn = document.getElementById('submit-btn');
        
        form.addEventListener('submit', function(e) {
            // Add loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating Unit...';
            
            // Add loading class to form
            form.classList.add('loading');
        });
    });
</script>
@endpush