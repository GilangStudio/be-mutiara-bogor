@extends('layouts.main')

@section('title', 'Project Details')

@push('styles')
<style>
    .cursor-move {
        cursor: move;
    }
    
    .gallery-reorder-mode .gallery-item {
        border: 2px dashed #0054a6;
        background: #f8f9fa;
    }
    
    .gallery-reorder-mode .card-body a {
        pointer-events: none;
        opacity: 0.5;
    }
    
    .gallery-reorder-mode img {
        pointer-events: none;
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
        transform: rotate(5deg);
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
    <h2 class="page-title">{{ $project->name }}</h2>
    <div class="btn-list">
        <a href="{{ route('development.project.edit', $project) }}" class="btn btn-primary">
            <i class="ti ti-edit me-1"></i> Edit Project
        </a>
        <a href="{{ route('development.project.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Back to Projects
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="row mt-3">
    {{-- Project Information --}}
    <div class="col-lg-8">
        {{-- Basic Info --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Project Information</h3>
                <div class="card-actions">
                    <span class="badge bg-{{ $project->is_active ? 'green' : 'red' }}-lt">
                        {{ $project->status_text }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label text-secondary">Project Name</label>
                            <div class="fw-bold">{{ $project->name }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-secondary">Category</label>
                            <div>
                                <span class="badge bg-blue-lt">{{ $project->category->name }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label text-secondary">Order</label>
                            <div class="fw-bold">{{ $project->order }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label text-secondary">Slug</label>
                            <div class="text-secondary">{{ $project->slug }}</div>
                        </div>
                    </div>
                    @if($project->short_description)
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label text-secondary">Short Description</label>
                            <div>{{ $project->short_description }}</div>
                        </div>
                    </div>
                    @endif
                    @if($project->description)
                    <div class="col-12">
                        <div class="mb-0">
                            <label class="form-label text-secondary">Full Description</label>
                            <div class="markdown">
                                {!! nl2br(e($project->description)) !!}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Main Image & Banner --}}
        @if($project->main_image_url || $project->banner_url)
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Main Images</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    @if($project->main_image_url)
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-secondary">Main Image</label>
                            <div class="card">
                                <img src="{{ $project->main_image_url }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between">
                                        <small class="text-secondary">Main Image</small>
                                        <a href="{{ $project->main_image_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="ti ti-external-link"></i> View Full
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($project->banner_url)
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-secondary">Banner Image</label>
                            <div class="card">
                                <img src="{{ $project->banner_url }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between">
                                        <small class="text-secondary">Banner Image</small>
                                        <a href="{{ $project->banner_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="ti ti-external-link"></i> View Full
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Additional Images --}}
        @if($project->logo_url || $project->siteplan_image_url)
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Additional Images</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    @if($project->logo_url)
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-secondary">Logo</label>
                            <div class="card">
                                <img src="{{ $project->logo_url }}" class="card-img-top" style="height: 150px; object-fit: contain; background: #f8f9fa;">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between">
                                        <small class="text-secondary">Logo</small>
                                        <a href="{{ $project->logo_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="ti ti-external-link"></i> View Full
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($project->siteplan_image_url)
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-secondary">Siteplan</label>
                            <div class="card">
                                <img src="{{ $project->siteplan_image_url }}" class="card-img-top" style="height: 150px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between">
                                        <small class="text-secondary">Siteplan</small>
                                        <a href="{{ $project->siteplan_image_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="ti ti-external-link"></i> View Full
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Image Gallery --}}
        @if($project->images->count() > 0)
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Image Gallery</h3>
                <div class="card-actions">
                    <span class="badge bg-blue-lt me-2">{{ $project->images->count() }} Images</span>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="toggle-gallery-reorder">
                        <i class="ti ti-arrows-sort me-1"></i> <span id="gallery-reorder-text">Reorder</span>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3" id="sortable-gallery">
                    @foreach($project->images as $image)
                    <div class="col-md-4 sortable-gallery-item" data-id="{{ $image->id }}">
                        <div class="card gallery-item">
                            {{-- Reorder Handle --}}
                            <div class="gallery-reorder-handle" style="display: none;">
                                <div class="position-absolute top-0 start-0 m-2 cursor-move bg-secondary text-white rounded p-1" style="z-index: 10;">
                                    <i class="ti ti-grip-vertical"></i>
                                </div>
                            </div>
                            
                            {{-- Order Badge --}}
                            <div class="ribbon ribbon-top bg-blue">
                                <span class="order-number">{{ $image->order }}</span>
                            </div>
                            
                            <img src="{{ $image->image_url }}" class="card-img-top" style="height: 200px; object-fit: cover;" 
                                alt="{{ $image->alt_text }}" data-bs-toggle="modal" data-bs-target="#gallery-modal"
                                data-image="{{ $image->image_url }}" data-alt="{{ $image->alt_text }}" 
                                data-caption="{{ $image->caption }}" style="cursor: pointer;">
                            <div class="card-body p-2">
                                @if($image->alt_text)
                                <small class="text-secondary d-block"><strong>Alt:</strong> {{ $image->alt_text }}</small>
                                @endif
                                @if($image->caption)
                                <small class="text-secondary d-block"><strong>Caption:</strong> {{ $image->caption }}</small>
                                @endif
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-secondary">Order: <span class="order-display">{{ $image->order }}</span></small>
                                    <a href="{{ $image->image_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="ti ti-external-link"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Facility Images Section - Add after Image Gallery Section --}}
        @if($project->facilityImages->count() > 0)
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Facility Images</h3>
                <div class="card-actions">
                    <span class="badge bg-green-lt me-2">{{ $project->facilityImages->count() }} Facilities</span>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="toggle-facility-reorder">
                        <i class="ti ti-arrows-sort me-1"></i> <span id="facility-reorder-text">Reorder</span>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3" id="sortable-facility">
                    @foreach($project->facilityImages as $facilityImage)
                    <div class="col-md-4 sortable-facility-item" data-id="{{ $facilityImage->id }}">
                        <div class="card facility-item">
                            {{-- Reorder Handle --}}
                            <div class="facility-reorder-handle" style="display: none;">
                                <div class="position-absolute top-0 start-0 m-2 cursor-move bg-secondary text-white rounded p-1" style="z-index: 10;">
                                    <i class="ti ti-grip-vertical"></i>
                                </div>
                            </div>
                            
                            {{-- Order Badge --}}
                            <div class="ribbon ribbon-top bg-green">
                                <span class="order-number">{{ $facilityImage->order }}</span>
                            </div>
                            
                            <img src="{{ $facilityImage->image_url }}" class="card-img-top" style="height: 200px; object-fit: cover;" 
                                alt="{{ $facilityImage->alt_text }}" data-bs-toggle="modal" data-bs-target="#facility-modal"
                                data-image="{{ $facilityImage->image_url }}" data-title="{{ $facilityImage->title }}"
                                data-description="{{ $facilityImage->description }}" data-alt="{{ $facilityImage->alt_text }}" 
                                style="cursor: pointer;">
                            <div class="card-body p-3">
                                <h5 class="card-title">{{ $facilityImage->title }}</h5>
                                @if($facilityImage->description)
                                <p class="card-text text-secondary">{{ $facilityImage->description }}</p>
                                @endif
                                @if($facilityImage->alt_text)
                                <small class="text-secondary d-block"><strong>Alt:</strong> {{ $facilityImage->alt_text }}</small>
                                @endif
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-secondary">Order: <span class="order-display">{{ $facilityImage->order }}</span></small>
                                    <a href="{{ $facilityImage->image_url }}" target="_blank" class="btn btn-sm btn-outline-success">
                                        <i class="ti ti-external-link"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Facility Image Modal --}}
        <div class="modal modal-blur fade" id="facility-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="facility-modal-title">Facility Image</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="" alt="" class="img-fluid rounded" id="facility-modal-image">
                    </div>
                    <div class="modal-footer" id="facility-modal-footer">
                        <div class="w-100 text-center">
                            <p class="mb-1" id="facility-modal-description"></p>
                            <small class="text-secondary" id="facility-modal-alt"></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>

    {{-- Project Meta --}}
    <div class="col-lg-4">
        {{-- Actions --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Actions</h3>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('development.project.edit', $project) }}" class="btn btn-primary">
                        <i class="ti ti-edit me-1"></i> Edit Project
                    </a>
                    <button type="button" class="btn btn-danger delete-btn"
                            data-id="{{ $project->id }}"
                            data-name="{{ $project->name }}"
                            data-url="{{ route('development.project.destroy', $project) }}">
                        <i class="ti ti-trash me-1"></i> Delete Project
                    </button>
                </div>
            </div>
        </div>

        {{-- Meta Information --}}
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Meta Information</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label text-secondary">Status</label>
                            <div>
                                <span class="badge bg-{{ $project->is_active ? 'green' : 'red' }}-lt">
                                    {{ $project->status_text }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label text-secondary">Created At</label>
                            <div>{{ $project->created_at->format('d M Y, H:i') }}</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-0">
                            <label class="form-label text-secondary">Last Updated</label>
                            <div>{{ $project->updated_at->format('d M Y, H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Images Info --}}
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Images Info</h3>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Main Image</span>
                        <span class="badge bg-{{ $project->main_image_path ? 'green' : 'red' }}-lt">
                            {{ $project->main_image_path ? 'Available' : 'Missing' }}
                        </span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Banner</span>
                        <span class="badge bg-{{ $project->banner_path ? 'green' : 'red' }}-lt">
                            {{ $project->banner_path ? 'Available' : 'Missing' }}
                        </span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Logo</span>
                        <span class="badge bg-{{ $project->logo_path ? 'green' : 'yellow' }}-lt">
                            {{ $project->logo_path ? 'Available' : 'Optional' }}
                        </span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Siteplan</span>
                        <span class="badge bg-{{ $project->siteplan_image_path ? 'green' : 'yellow' }}-lt">
                            {{ $project->siteplan_image_path ? 'Available' : 'Optional' }}
                        </span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Gallery</span>
                        <span class="badge bg-{{ $project->images->count() > 0? 'green' : 'yellow' }}-lt">
                            {{ $project->images->count() > 0 ? $project->images->count() . ' Images' : 'Optional' }}
                        </span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Facilities</span>
                        <span class="badge bg-{{ $project->facilityImages->count() > 0? 'green' : 'yellow' }}-lt">
                            {{ $project->facilityImages->count() > 0 ? $project->facilityImages->count() . ' Images' : 'Optional' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Include Global Delete Modal --}}
@include('components.delete-modal')

@endsection

@push('scripts')
@include('components.toast')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let gallerySortable = null;
        let isGalleryReorderMode = false;
        
        // Toggle Gallery Reorder Mode
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
                
                // Update order display
                const orderNumber = item.querySelector('.order-number');
                const orderDisplay = item.querySelector('.order-display');
                if (orderNumber) orderNumber.textContent = newOrder;
                if (orderDisplay) orderDisplay.textContent = newOrder;
            });
            
            // Send AJAX request
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
            instruction.className = 'alert alert-info alert-dismissible mt-3';
            instruction.innerHTML = `
                <div class="d-flex">
                    <div>
                        <h4>Gallery Reorder Mode Active</h4>
                        Drag the <i class="ti ti-grip-vertical"></i> handle to reorder gallery images. Image view is disabled during reorder mode.
                    </div>
                </div>
            `;
            
            // Insert after card header
            const cardBody = galleryContainer.parentElement;
            cardBody.insertBefore(instruction, galleryContainer);
        }

        function hideGalleryReorderInstructions() {
            const instruction = document.getElementById('gallery-reorder-instruction');
            if (instruction) {
                instruction.remove();
            }
        }


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
        
        // Update order display
        const orderNumber = item.querySelector('.order-number');
        const orderDisplay = item.querySelector('.order-display');
        if (orderNumber) orderNumber.textContent = newOrder;
        if (orderDisplay) orderDisplay.textContent = newOrder;
    });
    
    // Send AJAX request
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
    instruction.className = 'alert alert-info alert-dismissible mt-3';
    instruction.innerHTML = `
        <div class="d-flex">
            <div>
                <h4>Facility Reorder Mode Active</h4>
                Drag the <i class="ti ti-grip-vertical"></i> handle to reorder facility images. Image view is disabled during reorder mode.
            </div>
        </div>
    `;
    
    // Insert after card header
    const cardBody = facilityContainer.parentElement;
    cardBody.insertBefore(instruction, facilityContainer);
}

function hideFacilityReorderInstructions() {
    const instruction = document.getElementById('facility-reorder-instruction');
    if (instruction) {
        instruction.remove();
    }
}

// Facility modal functionality
const facilityModal = document.getElementById('facility-modal');
if (facilityModal) {
    facilityModal.addEventListener('show.bs.modal', function(event) {
        // Prevent modal if in reorder mode
        if (isFacilityReorderMode) {
            event.preventDefault();
            return;
        }
        
        const trigger = event.relatedTarget;
        const imageSrc = trigger.getAttribute('data-image');
        const title = trigger.getAttribute('data-title');
        const description = trigger.getAttribute('data-description');
        const altText = trigger.getAttribute('data-alt');
        
        document.getElementById('facility-modal-image').src = imageSrc;
        document.getElementById('facility-modal-image').alt = altText || '';
        document.getElementById('facility-modal-title').textContent = title || 'Facility Image';
        document.getElementById('facility-modal-description').textContent = description || '';
        document.getElementById('facility-modal-alt').textContent = altText || '';
        
        // Hide footer if no description and alt text
        const footer = document.getElementById('facility-modal-footer');
        if (!description && !altText) {
            footer.style.display = 'none';
        } else {
            footer.style.display = 'block';
        }
    });
}

        // Existing gallery modal functionality...
        const galleryModal = document.getElementById('gallery-modal');
        if (galleryModal) {
            galleryModal.addEventListener('show.bs.modal', function(event) {
                // Prevent modal if in reorder mode
                if (isGalleryReorderMode) {
                    event.preventDefault();
                    return;
                }
                
                const trigger = event.relatedTarget;
                const imageSrc = trigger.getAttribute('data-image');
                const altText = trigger.getAttribute('data-alt');
                const caption = trigger.getAttribute('data-caption');
                
                document.getElementById('gallery-modal-image').src = imageSrc;
                document.getElementById('gallery-modal-image').alt = altText || '';
                document.getElementById('gallery-modal-alt').textContent = altText || 'Gallery Image';
                document.getElementById('gallery-modal-caption').textContent = caption || '';
                
                // Hide footer if no caption
                const footer = document.getElementById('gallery-modal-footer');
                if (!altText && !caption) {
                    footer.style.display = 'none';
                } else {
                    footer.style.display = 'block';
                }
            });
        }
    });
</script>
@endpush