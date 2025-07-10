@extends('layouts.main')

@section('title', 'Edit Accessibility')

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
    
    .loading {
        pointer-events: none;
        opacity: 0.6;
    }
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h2 class="page-title">Edit Accessibility</h2>
        <div class="page-subtitle">{{ $accessibility->name }}</div>
    </div>
    <a href="{{ route('accessibilities.index') }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left me-1"></i> Back to Accessibility
    </a>
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

<form action="{{ route('accessibilities.update', $accessibility) }}" method="POST" enctype="multipart/form-data" id="edit-form">
    @csrf
    @method('PUT')
    <div class="row g-3">
        {{-- Main Content --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-building me-2"></i>
                        Accessibility Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Accessibility Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name', $accessibility->name) }}" autocomplete="off" required 
                                       placeholder="Enter accessibility name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    <span id="name-count">{{ strlen($accessibility->name ?? '') }}</span>/255 characters
                                </small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          name="description" rows="6" 
                                          placeholder="Describe the accessibility and its features...">{{ old('description', $accessibility->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Provide details about this accessibility and what it offers.</small>
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
                                   {{ old('status', $accessibility->is_active) ? 'checked' : '' }}>
                            <span class="form-check-label">Active Status</span>
                        </label>
                        <small class="form-hint">Enable this accessibility to be displayed publicly.</small>
                    </div>
                    
                    <div class="mb-0">
                        <label class="form-label text-secondary">Order Position</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">#</span>
                            <input type="text" class="form-control bg-light" value="{{ $accessibility->order }}" readonly>
                        </div>
                        <small class="form-hint">Order position in the accessibilities list.</small>
                    </div>
                </div>
            </div>

            {{-- Current Image --}}
            @if($accessibility->image_url)
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-photo me-2"></i>
                        Current Image
                    </h3>
                </div>
                <div class="card-body">
                    <div class="card border">
                        <img src="{{ $accessibility->image_url }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between">
                                <small class="text-secondary fw-medium">Accessibility Image</small>
                                <a href="{{ $accessibility->image_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="ti ti-external-link"></i> View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Update Image --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-upload me-2"></i>
                        {{ $accessibility->image_url ? 'Update Image' : 'Add Image' }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Image {{ !$accessibility->image_url ? '<span class="text-danger">*</span>' : '' }}</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                               name="image" accept="image/*" id="image-input">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">
                            <i class="ti ti-info-circle me-1"></i>
                            {{ $accessibility->image_url ? 'Leave empty to keep current image.' : '' }} Recommended: 1200x800px, Max: 10MB
                        </small>
                        <div class="mt-2" id="image-preview"></div>
                    </div>
                </div>
            </div>

            {{-- Accessibility Meta --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-info-square me-2"></i>
                        Accessibility Meta
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-2">
                                <small class="text-secondary">Created:</small>
                                <div>{{ $accessibility->created_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-0">
                                <small class="text-secondary">Last Updated:</small>
                                <div>{{ $accessibility->updated_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit Buttons --}}
        <div class="col-12">
            <div class="card">
                <div class="card-footer bg-transparent text-end">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-secondary">
                                <i class="ti ti-clock me-1"></i>
                                Last saved: {{ $accessibility->updated_at->format('d M Y, H:i') }}
                            </small>
                        </div>
                        <div class="btn-list">
                            <a href="{{ route('accessibilities.index') }}" class="btn btn-link">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <i class="ti ti-device-floppy me-1"></i> 
                                Update Accessibility
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
        // Name character counter
        const nameInput = document.querySelector('input[name="name"]');
        const nameCount = document.getElementById('name-count');
        
        nameInput.addEventListener('input', function() {
            const currentLength = this.value.length;
            nameCount.textContent = currentLength;
            
            if (currentLength > 200) {
                nameCount.parentElement.classList.add('text-warning');
            } else if (currentLength > 255) {
                nameCount.parentElement.classList.remove('text-warning');
                nameCount.parentElement.classList.add('text-danger');
            } else {
                nameCount.parentElement.classList.remove('text-warning', 'text-danger');
            }
        });

        // Image preview functionality
        const imageInput = document.getElementById('image-input');
        const imagePreview = document.getElementById('image-preview');
        
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size
                const maxSize = 10 * 1024 * 1024; // 10MB
                if (file.size > maxSize) {
                    showAlert(imageInput, 'danger', 'File size too large. Maximum 10MB allowed.');
                    imageInput.value = '';
                    return;
                }

                // Validate file type
                if (!file.type.startsWith('image/')) {
                    showAlert(imageInput, 'danger', 'Please select a valid image file.');
                    imageInput.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.innerHTML = `
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
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearImagePreview()">
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
                imagePreview.innerHTML = '';
            }
        });

        // Clear image preview function
        window.clearImagePreview = function() {
            imageInput.value = '';
            imagePreview.innerHTML = '';
        };

        // Form submission with loading state
        const form = document.getElementById('edit-form');
        const submitBtn = document.getElementById('submit-btn');
        
        form.addEventListener('submit', function(e) {
            // Add loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating Accessibility...';
            
            // Add loading class to form
            form.classList.add('loading');
        });
    });
</script>
@endpush