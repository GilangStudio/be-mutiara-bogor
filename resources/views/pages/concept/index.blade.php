@extends('layouts.main')

@section('title', 'Concept')

@push('styles')
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

    /* Section Styles */
    .section-item {
        transition: all 0.3s ease;
        position: relative;
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .section-item:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transform: translateY(-1px);
    }

    /* Reorder Styles */
    .section-reorder-mode .section-item {
        border: 2px dashed #0054a6;
        background: #f8f9fa;
    }

    .section-reorder-mode .dropdown {
        pointer-events: none;
        opacity: 0.5;
    }

    .sortable-section-ghost {
        opacity: 0.5;
        background: #f8f9fa;
    }

    .sortable-section-chosen {
        background: #e3f2fd;
        transform: rotate(2deg);
    }

    .sortable-section-drag {
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

    .section-preview-image {
        height: 120px;
        width: 100%;
        object-fit: cover;
        border-radius: 0.375rem;
    }

    #section-reorder-instruction {
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

    .ribbon {
        position: absolute;
        top: 10px;
        left: -5px;
        z-index: 1;
        background: #0054a6;
        color: white;
        padding: 0.25rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 0 0.25rem 0.25rem 0;
    }

    .ribbon::before {
        content: '';
        position: absolute;
        left: 0;
        bottom: -5px;
        width: 0;
        height: 0;
        border-left: 5px solid #003d73;
        border-bottom: 5px solid transparent;
    }
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h2 class="page-title">Concept</h2>
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

{{-- Concept Page Form --}}
<div class="row g-3">
    <div class="col-12">
        <form action="{{ $conceptPage ? route('concept.update') : route('concept.store') }}" method="POST" enctype="multipart/form-data" id="concept-form">
            @csrf
            @if($conceptPage)
                @method('PUT')
            @endif
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-layout-dashboard me-2"></i>
                        Concept Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Left Column - Form Fields --}}
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                               name="title" value="{{ old('title', $conceptPage->title ?? '') }}" required
                                               placeholder="Enter concept title">
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-hint">
                                            <span id="title-count">{{ strlen($conceptPage->title ?? '') }}</span>/255 characters
                                        </small>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Description <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                                  name="description" id="editor" rows="8"
                                                  placeholder="Main description that appears below the banner...">{{ old('description', $conceptPage->description ?? '') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="invalid-feedback" id="description-error" style="display: none;"></div>
                                        <small class="form-hint">This content will be displayed below the banner image.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Meta Title</label>
                                        <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                                               name="meta_title" value="{{ old('meta_title', $conceptPage->meta_title ?? '') }}" 
                                               placeholder="Enter title that will appear in search results"
                                               maxlength="255" id="meta-title-input">
                                        @error('meta_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-hint">
                                            <span id="meta-title-count">{{ strlen($conceptPage->meta_title ?? '') }}</span>/255 characters. 
                                        </small>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Meta Description</label>
                                        <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                                  name="meta_description" rows="3" 
                                                  placeholder="Enter description that will appear in search results"
                                                  maxlength="500" id="meta-desc-input">{{ old('meta_description', $conceptPage->meta_description ?? '') }}</textarea>
                                        @error('meta_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-hint">
                                            <span id="meta-desc-count">{{ strlen($conceptPage->meta_description ?? '') }}</span>/500 characters. 
                                        </small>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Meta Keywords</label>
                                        <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" 
                                               name="meta_keywords" value="{{ old('meta_keywords', $conceptPage->meta_keywords ?? '') }}" 
                                               placeholder="keywords separated by commas. e.g: property, house, Jakarta, residence"
                                               maxlength="255" id="meta-keywords-input">
                                        @error('meta_keywords')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-hint">
                                            <span id="meta-keywords-count">{{ strlen($conceptPage->meta_keywords ?? '') }}</span>/255 characters. 
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Right Column - Image & Meta --}}
                        <div class="col-lg-4">
                            {{-- Current Banner Preview --}}
                            @if($conceptPage && $conceptPage->banner_image_url)
                            <div class="mb-3">
                                <label class="form-label text-secondary">Current Banner</label>
                                <div class="card border">
                                    <img src="{{ $conceptPage->banner_image_url }}" class="card-img-top" style="height: 160px; object-fit: cover;">
                                    <div class="card-body p-2">
                                        <div class="d-flex justify-content-between">
                                            <small class="text-secondary fw-medium">Banner Image</small>
                                            <a href="{{ $conceptPage->banner_image_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="ti ti-external-link"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Banner Upload --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    Banner Image 
                                    @if(!$conceptPage || !$conceptPage->banner_image_url)
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <input type="file" class="form-control @error('banner_image') is-invalid @enderror" 
                                       name="banner_image" accept="image/*" 
                                       {{ !$conceptPage || !$conceptPage->banner_image_url ? 'required' : '' }} 
                                       id="banner-image">
                                @error('banner_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    @if($conceptPage && $conceptPage->banner_image_url)
                                        Leave empty to keep current banner.
                                    @endif
                                    Recommended: 1920x600px, Max: 5MB
                                </small>
                                <div class="mt-2" id="banner-image-preview"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Banner Alt Text</label>
                                <input type="text" class="form-control @error('banner_alt_text') is-invalid @enderror" 
                                       name="banner_alt_text" value="{{ old('banner_alt_text', $conceptPage->banner_alt_text ?? '') }}" 
                                       placeholder="Describe banner for accessibility">
                                @error('banner_alt_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Improves accessibility for screen readers.</small>
                            </div>

                            {{-- Meta Info --}}
                            @if($conceptPage)
                            <div class="card bg-light">
                                <div class="card-body p-3">
                                    <h6 class="card-title">Page Information</h6>
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-1 bg-transparent border-0">
                                            <small class="text-secondary">Created:</small>
                                            <small>{{ $conceptPage->created_at->format('d M Y') }}</small>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-1 bg-transparent border-0">
                                            <small class="text-secondary">Updated:</small>
                                            <small>{{ $conceptPage->updated_at->format('d M Y') }}</small>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-1 bg-transparent border-0">
                                            <small class="text-secondary">Sections:</small>
                                            <span class="badge bg-blue-lt">{{ $sections->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        @if($conceptPage)
                        <small class="text-secondary">
                            <i class="ti ti-clock me-1"></i>
                            Last saved: {{ $conceptPage->updated_at->format('d M Y, H:i') }}
                        </small>
                        @else
                        <div></div>
                        @endif
                        <button type="submit" class="btn btn-primary" id="submit-btn">
                            <i class="ti ti-device-floppy me-1"></i> 
                            {{ $conceptPage ? 'Update' : 'Create' }} Concept
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Sections Management (only show if concept page exists) --}}
@if($conceptPage)
<div class="row g-3 mt-2">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-layout-sections me-2"></i>
                    Concept Sections
                </h3>
                <div class="card-actions">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-blue-lt">{{ $sections->count() }} Sections</span>
                        @if($sections->count() > 0)
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="toggle-section-reorder">
                            <i class="ti ti-arrows-sort me-1"></i> 
                            <span id="section-reorder-text">Reorder</span>
                        </button>
                        @endif
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#add-section">
                            <i class="ti ti-plus me-1"></i> Add Section
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($sections->count() > 0)
                    <div class="row g-3" id="sortable-sections">
                        @foreach($sections as $section)
                        <div class="col-12 sortable-section-item" data-id="{{ $section->id }}">
                            <div class="section-item p-0">
                                {{-- Reorder Handle --}}
                                <div class="section-reorder-handle position-absolute top-0 start-0 m-2 cursor-move bg-secondary text-white rounded p-1" style="display: none; z-index: 10;">
                                    <i class="ti ti-grip-vertical"></i>
                                </div>
                                
                                {{-- Order Badge --}}
                                {{-- <div class="ribbon">
                                    <span class="order-number">{{ $section->order }}</span>
                                </div> --}}

                                <div class="p-3">
                                    <div class="row align-items-center">
                                        @if($section->layout_type === 'image_left')
                                            {{-- Image Left Layout --}}
                                            <div class="col-md-3">
                                                <img src="{{ $section->image_url }}" class="section-preview-image" alt="{{ $section->image_alt_text }}">
                                            </div>
                                            <div class="col-md-9">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div class="flex-grow-1">
                                                        @if($section->title)
                                                        <h5 class="mb-1">{{ $section->title }}</h5>
                                                        @endif
                                                        <div class="mb-2">
                                                            {{-- <span class="badge bg-green-lt me-1">Image Left</span> --}}
                                                            <span class="badge bg-{{ $section->is_active ? 'green' : 'red' }}-lt">
                                                                {{ $section->status_text }}
                                                            </span>
                                                        </div>
                                                        <p class="text-secondary small mb-0">
                                                            {{ Str::limit(strip_tags($section->content), 150) }}
                                                        </p>
                                                    </div>
                                                    <div class="dropdown ms-2">
                                                        <button class="btn btn-sm btn-ghost-secondary" data-bs-toggle="dropdown">
                                                            <i class="ti ti-dots-vertical"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <button type="button" class="dropdown-item" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#edit-section"
                                                                    data-id="{{ $section->id }}"
                                                                    data-title="{{ $section->title }}"
                                                                    data-content="{{ $section->content }}"
                                                                    data-image-alt="{{ $section->image_alt_text }}"
                                                                    data-layout="{{ $section->layout_type }}"
                                                                    data-status="{{ $section->is_active }}">
                                                                <i class="ti ti-edit me-1"></i> Edit
                                                            </button>
                                                            <button type="button" class="dropdown-item text-danger delete-section-btn"
                                                                    data-id="{{ $section->id }}"
                                                                    data-name="{{ $section->title ?: 'Section ' . $section->order }}"
                                                                    data-url="{{ route('concept.sections.destroy', $section) }}">
                                                                <i class="ti ti-trash me-1"></i> Delete
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            {{-- Image Right Layout --}}
                                            <div class="col-md-9">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div class="flex-grow-1">
                                                        @if($section->title)
                                                        <h5 class="mb-1">{{ $section->title }}</h5>
                                                        @endif
                                                        <div class="mb-2">
                                                            {{-- <span class="badge bg-blue-lt me-1">Image Right</span> --}}
                                                            <span class="badge bg-{{ $section->is_active ? 'green' : 'red' }}-lt">
                                                                {{ $section->status_text }}
                                                            </span>
                                                        </div>
                                                        <p class="text-secondary small mb-0">
                                                            {{ Str::limit(strip_tags($section->content), 150) }}
                                                        </p>
                                                    </div>
                                                    <div class="dropdown ms-2">
                                                        <button class="btn btn-ghost-secondary" data-bs-toggle="dropdown">
                                                            <i class="ti ti-dots-vertical"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <button type="button" class="dropdown-item" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#edit-section"
                                                                    data-id="{{ $section->id }}"
                                                                    data-title="{{ $section->title }}"
                                                                    data-content="{{ $section->content }}"
                                                                    data-image-alt="{{ $section->image_alt_text }}"
                                                                    data-layout="{{ $section->layout_type }}"
                                                                    data-status="{{ $section->is_active }}">
                                                                <i class="ti ti-edit me-1"></i> Edit
                                                            </button>
                                                            <button type="button" class="dropdown-item text-danger delete-section-btn"
                                                                    data-id="{{ $section->id }}"
                                                                    data-name="{{ $section->title ?: 'Section ' . $section->order }}"
                                                                    data-url="{{ route('concept.sections.destroy', $section) }}">
                                                                <i class="ti ti-trash me-1"></i> Delete
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <img src="{{ $section->image_url }}" class="section-preview-image" alt="{{ $section->image_alt_text }}">
                                            </div>
                                        @endif
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
                                <i class="ti ti-layout-sections icon icon-lg text-secondary"></i>
                            </div>
                            <p class="empty-title h5">No Sections Yet</p>
                            <p class="empty-subtitle text-secondary">
                                Add sections to create your concept page layout
                            </p>
                            <div class="empty-action">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-section">
                                    <i class="ti ti-plus me-1"></i> Add First Section
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal Add Section --}}
<div class="modal modal-blur fade" id="add-section" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form class="modal-content" action="{{ route('concept.sections.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add New Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Section Title</label>
                            <input type="text" class="form-control" name="title" placeholder="Enter section title (optional)">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Layout Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="layout_type" required>
                                <option value="">Select Layout</option>
                                <option value="image_left">Image Left - Text Right</option>
                                <option value="image_right">Text Left - Image Right</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="content" rows="6" required 
                                      placeholder="Enter section content..."></textarea>
                            <small class="form-hint">Describe this section content.</small>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Section Image <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="image" accept="image/*" required id="add-section-image">
                            <small class="form-hint">Recommended: 1200x800px, Max: 5MB</small>
                            <div class="mt-2" id="add-section-image-preview"></div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Image Alt Text</label>
                            <input type="text" class="form-control" name="image_alt_text" 
                                   placeholder="Describe the image for accessibility">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" value="1" checked>
                            <span class="form-check-label">Active</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary ms-auto">Add Section</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Section --}}
<div class="modal modal-blur fade" id="edit-section" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form class="modal-content" id="edit-section-form" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Section Title</label>
                            <input type="text" class="form-control" id="edit-section-title" name="title" 
                                   placeholder="Enter section title (optional)">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Layout Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit-section-layout" name="layout_type" required>
                                <option value="">Select Layout</option>
                                <option value="image_left">Image Left - Text Right</option>
                                <option value="image_right">Text Left - Image Right</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit-section-content" name="content" rows="6" required 
                                      placeholder="Enter section content..."></textarea>
                            <small class="form-hint">Describe this section content.</small>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Section Image</label>
                            <input type="file" class="form-control" name="image" accept="image/*" id="edit-section-image">
                            <small class="form-hint">Leave empty to keep current image. Recommended: 1200x800px, Max: 5MB</small>
                            <div class="mt-2" id="edit-section-image-preview"></div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Image Alt Text</label>
                            <input type="text" class="form-control" id="edit-section-image-alt" name="image_alt_text" 
                                   placeholder="Describe the image for accessibility">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit-section-status" name="status" value="1">
                            <span class="form-check-label">Active</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary ms-auto">Update Section</button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- Include Global Delete Modal for sections --}}
@include('components.delete-modal')

@endsection

@push('scripts')
@include('components.scripts.wysiwyg')
@include('components.alert')
@include('components.toast')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Character counters
        const titleInput = document.querySelector('input[name="title"]');
        const titleCount = document.getElementById('title-count');
        
        if (titleInput && titleCount) {
            titleInput.addEventListener('input', function() {
                const currentLength = this.value.length;
                titleCount.textContent = currentLength;
                
                if (currentLength > 200) {
                    titleCount.parentElement.classList.add('text-warning');
                } else if (currentLength > 255) {
                    titleCount.parentElement.classList.remove('text-warning');
                    titleCount.parentElement.classList.add('text-danger');
                } else {
                    titleCount.parentElement.classList.remove('text-warning', 'text-danger');
                }
            });
        }

        const metaTextarea = document.querySelector('textarea[name="meta_description"]');
        const metaCount = document.getElementById('meta-count');
        
        if (metaTextarea && metaCount) {
            metaTextarea.addEventListener('input', function() {
                const currentLength = this.value.length;
                metaCount.textContent = currentLength;
                
                if (currentLength > 450) {
                    metaCount.parentElement.classList.add('text-warning');
                } else if (currentLength > 500) {
                    metaCount.parentElement.classList.remove('text-warning');
                    metaCount.parentElement.classList.add('text-danger');
                } else {
                    metaCount.parentElement.classList.remove('text-warning', 'text-danger');
                }
            });
        }

        // Banner image preview
        const bannerInput = document.getElementById('banner-image');
        const bannerPreview = document.getElementById('banner-image-preview');
        
        if (bannerInput && bannerPreview) {
            bannerInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    if (file.size > 5 * 1024 * 1024) {
                        showAlert(bannerInput, 'danger', 'File size too large. Maximum 5MB allowed.');
                        bannerInput.value = '';
                        return;
                    }

                    if (!file.type.startsWith('image/')) {
                        showAlert(bannerInput, 'danger', 'Please select a valid image file.');
                        bannerInput.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        bannerPreview.innerHTML = `
                            <div class="card image-preview-card">
                                <img src="${e.target.result}" class="card-img-top" style="height: 120px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="card-title mb-1">${file.name}</h6>
                                            <small class="text-secondary">
                                                ${(file.size / 1024 / 1024).toFixed(2)} MB
                                            </small>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearBannerPreview()">
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
                    bannerPreview.innerHTML = '';
                }
            });
        }

        window.clearBannerPreview = function() {
            if (bannerInput) bannerInput.value = '';
            if (bannerPreview) bannerPreview.innerHTML = '';
        };

        // Section image previews
        const addSectionImage = document.getElementById('add-section-image');
        const addSectionPreview = document.getElementById('add-section-image-preview');
        
        if (addSectionImage && addSectionPreview) {
            addSectionImage.addEventListener('change', function(e) {
                setupImagePreview(e.target.files[0], addSectionPreview, 'clearAddSectionPreview');
            });
        }

        window.clearAddSectionPreview = function() {
            if (addSectionImage) addSectionImage.value = '';
            if (addSectionPreview) addSectionPreview.innerHTML = '';
        };

        const editSectionImage = document.getElementById('edit-section-image');
        const editSectionPreview = document.getElementById('edit-section-image-preview');
        
        if (editSectionImage && editSectionPreview) {
            editSectionImage.addEventListener('change', function(e) {
                setupImagePreview(e.target.files[0], editSectionPreview, 'clearEditSectionPreview');
            });
        }

        window.clearEditSectionPreview = function() {
            if (editSectionImage) editSectionImage.value = '';
            if (editSectionPreview) editSectionPreview.innerHTML = '';
        };

        function setupImagePreview(file, previewElement, clearFunction) {
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    showAlert(previewElement.previousElementSibling, 'danger', 'File size too large. Maximum 5MB allowed.');
                    return;
                }

                if (!file.type.startsWith('image/')) {
                    showAlert(previewElement.previousElementSibling, 'danger', 'Please select a valid image file.');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewElement.innerHTML = `
                        <div class="card">
                            <img src="${e.target.result}" class="card-img-top" style="height: 100px; object-fit: cover;">
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="fw-medium">${file.name}</small><br>
                                        <small class="text-secondary">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="${clearFunction}()">
                                        <i class="ti ti-x"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            } else {
                previewElement.innerHTML = '';
            }
        }

        // Handle Edit Section Modal
        const editSectionModal = document.getElementById('edit-section');
        if (editSectionModal) {
            editSectionModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const title = button.getAttribute('data-title');
                const content = button.getAttribute('data-content');
                const imageAlt = button.getAttribute('data-image-alt');
                const layout = button.getAttribute('data-layout');
                const status = button.getAttribute('data-status') === '1';

                // Update form action
                const form = document.getElementById('edit-section-form');
                form.action = `{{ url('concept/sections') }}/${id}`;

                // Fill form fields
                document.getElementById('edit-section-title').value = title || '';
                document.getElementById('edit-section-content').value = content || '';
                document.getElementById('edit-section-image-alt').value = imageAlt || '';
                document.getElementById('edit-section-layout').value = layout || '';
                document.getElementById('edit-section-status').checked = status;
            });
        }

        // Handle Delete Section
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-section-btn') || e.target.closest('.delete-section-btn')) {
                e.preventDefault();
                
                const button = e.target.classList.contains('delete-section-btn') ? e.target : e.target.closest('.delete-section-btn');
                const itemName = button.getAttribute('data-name');
                const deleteUrl = button.getAttribute('data-url');
                
                // Update modal content
                const deleteForm = document.getElementById('delete-form');
                const deleteMessage = document.getElementById('delete-message');
                
                if (deleteForm && deleteMessage) {
                    deleteForm.action = deleteUrl;
                    deleteMessage.innerHTML = `Do you really want to delete "<strong>${itemName}</strong>"? This process cannot be undone.`;
                    
                    // Show modal
                    const deleteModal = new bootstrap.Modal(document.getElementById('delete-modal'));
                    deleteModal.show();
                }
            }
        });

        // Section Reorder functionality
        let sectionSortable = null;
        let isSectionReorderMode = false;
        
        const toggleSectionReorderBtn = document.getElementById('toggle-section-reorder');
        const sectionReorderText = document.getElementById('section-reorder-text');
        const sectionContainer = document.getElementById('sortable-sections');
        
        if (toggleSectionReorderBtn && sectionContainer) {
            toggleSectionReorderBtn.addEventListener('click', function() {
                isSectionReorderMode = !isSectionReorderMode;
                
                if (isSectionReorderMode) {
                    enableSectionReorderMode();
                } else {
                    disableSectionReorderMode();
                }
            });
        }

        function enableSectionReorderMode() {
            const reorderHandles = document.querySelectorAll('.section-reorder-handle');
            reorderHandles.forEach(handle => handle.style.display = 'block');
            
            if (sectionContainer) sectionContainer.classList.add('section-reorder-mode');
            
            if (toggleSectionReorderBtn) {
                toggleSectionReorderBtn.classList.remove('btn-outline-secondary');
                toggleSectionReorderBtn.classList.add('btn-success');
            }
            if (sectionReorderText) sectionReorderText.textContent = 'Done';
            
            const icon = toggleSectionReorderBtn?.querySelector('i');
            if (icon) icon.className = 'ti ti-check me-1';
            
            sectionSortable = new Sortable(sectionContainer, {
                handle: '.section-reorder-handle',
                animation: 150,
                ghostClass: 'sortable-section-ghost',
                chosenClass: 'sortable-section-chosen',
                dragClass: 'sortable-section-drag',
                onEnd: function(evt) {
                    updateSectionOrder();
                }
            });
            
            showSectionReorderInstructions();
        }

        function disableSectionReorderMode() {
            const reorderHandles = document.querySelectorAll('.section-reorder-handle');
            reorderHandles.forEach(handle => handle.style.display = 'none');
            
            if (sectionContainer) sectionContainer.classList.remove('section-reorder-mode');
            
            if (toggleSectionReorderBtn) {
                toggleSectionReorderBtn.classList.remove('btn-success');
                toggleSectionReorderBtn.classList.add('btn-outline-secondary');
            }
            if (sectionReorderText) sectionReorderText.textContent = 'Reorder';
            
            const icon = toggleSectionReorderBtn?.querySelector('i');
            if (icon) icon.className = 'ti ti-arrows-sort me-1';
            
            if (sectionSortable) {
                sectionSortable.destroy();
                sectionSortable = null;
            }
            
            hideSectionReorderInstructions();
        }

        function updateSectionOrder() {
            const items = document.querySelectorAll('.sortable-section-item');
            const orderData = [];
            
            items.forEach((item, index) => {
                const id = item.getAttribute('data-id');
                const newOrder = index + 1;
                
                orderData.push({
                    id: id,
                    order: newOrder
                });
                
                const orderNumber = item.querySelector('.order-number');
                if (orderNumber) orderNumber.textContent = newOrder;
            });
            
            fetch('{{ route('concept.sections.reorder') }}', {
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
                    showToast('Section order updated successfully', 'success');
                } else {
                    showToast('Failed to update section order', 'error');
                    setTimeout(() => location.reload(), 1000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to update section order', 'error');
                setTimeout(() => location.reload(), 1000);
            });
        }

        function showSectionReorderInstructions() {
            const instruction = document.createElement('div');
            instruction.id = 'section-reorder-instruction';
            instruction.className = 'alert alert-info alert-dismissible mb-3';
            instruction.innerHTML = `
                <div class="d-flex">
                    <div>
                        <h4>Section Reorder Mode Active</h4>
                        Drag the <i class="ti ti-grip-vertical"></i> handle to reorder sections. Editing options are disabled during reorder mode.
                    </div>
                </div>
            `;
            
            if (sectionContainer && sectionContainer.parentElement) {
                sectionContainer.parentElement.insertBefore(instruction, sectionContainer);
            }
        }

        function hideSectionReorderInstructions() {
            const instruction = document.getElementById('section-reorder-instruction');
            if (instruction) {
                instruction.remove();
            }
        }

        // Reset modals
        const addSectionModal = document.getElementById('add-section');
        if (addSectionModal) {
            addSectionModal.addEventListener('show.bs.modal', function () {
                const form = addSectionModal.querySelector('form');
                if (form) {
                    form.reset();
                    const statusInput = form.querySelector('input[name="status"]');
                    if (statusInput) statusInput.checked = true;
                }
                if (addSectionPreview) addSectionPreview.innerHTML = '';
            });
        }

        const editSectionModalReset = document.getElementById('edit-section');
        if (editSectionModalReset) {
            editSectionModalReset.addEventListener('hide.bs.modal', function () {
                if (editSectionPreview) editSectionPreview.innerHTML = '';
            });
        }

        // Form submission loading state
        const form = document.getElementById('concept-form');
        const submitBtn = document.getElementById('submit-btn');
        
        if (form && submitBtn) {
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Validate description content
                const descriptionTextarea = document.querySelector('textarea[name="description"]');
                const descriptionError = document.getElementById('description-error');
                
                // Clear previous errors
                if (descriptionTextarea) descriptionTextarea.classList.remove('is-invalid');
                if (descriptionError) descriptionError.style.display = 'none';
                
                // Check if WYSIWYG editor is loaded
                let editorContent = '';
                if (typeof hugeRTE !== 'undefined' && hugeRTE.get('editor')) {
                    editorContent = hugeRTE.get('editor').getContent();
                    // Update textarea value with editor content
                    if (descriptionTextarea) descriptionTextarea.value = editorContent;
                } else {
                    // Fallback to textarea value if editor not loaded
                    editorContent = descriptionTextarea ? descriptionTextarea.value : '';
                }
                
                // Validate if content is empty
                if (!editorContent.trim() || editorContent.trim() === '<p></p>' || editorContent.trim() === '<p><br></p>') {
                    e.preventDefault();
                    isValid = false;
                    
                    if (descriptionTextarea) descriptionTextarea.classList.add('is-invalid');
                    if (descriptionError) {
                        descriptionError.textContent = 'Description is required.';
                        descriptionError.style.display = 'block';
                    }
                    
                    const editor = document.getElementById('editor');
                    if (editor) {
                        editor.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'center' 
                        });
                    }
                }
                
                // Only proceed if validation passes
                if (isValid) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                    form.classList.add('loading');
                } else {
                    // Show validation message
                    showToast('Please fill in all required fields', 'error');
                }
                
                return isValid;
            });
        }
    });
</script>

<script>
    function setupCharacterCounter(inputId, countId, maxLength) {
        const input = document.getElementById(inputId);
        const counter = document.getElementById(countId);
        
        if (input && counter) {
            input.addEventListener('input', function() {
                const currentLength = this.value.length;
                counter.textContent = currentLength;
                
                // Add warning colors
                const percentage = (currentLength / maxLength) * 100;
                if (percentage > 90) {
                    counter.parentElement.classList.add('text-danger');
                    counter.parentElement.classList.remove('text-warning');
                } else if (percentage > 80) {
                    counter.parentElement.classList.add('text-warning');
                    counter.parentElement.classList.remove('text-danger');
                } else {
                    counter.parentElement.classList.remove('text-warning', 'text-danger');
                }
            });
        }
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Character counters for meta fields
        setupCharacterCounter('meta-title-input', 'meta-title-count', 255);
        setupCharacterCounter('meta-desc-input', 'meta-desc-count', 500);
        setupCharacterCounter('meta-keywords-input', 'meta-keywords-count', 255);
    });
</script>
@endpush