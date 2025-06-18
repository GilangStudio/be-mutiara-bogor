@extends('layouts.main')

@section('title', 'Company Profile')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">
@endpush

@section('header')
<h2 class="page-title">Company Profile</h2>
@endsection

@section('content')
{{-- Alert Messages --}}
@if(session('success'))
<div class="alert alert-success text-success alert-dismissible" role="alert">
    <div class="d-flex gap-2">
        <div>
            <i class="ti ti-check icon alert-icon me-2"></i>
        </div>
        <div>{{ session('success') }}</div>
    </div>
    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger text-danger alert-dismissible" role="alert">
    <div class="d-flex">
        <div>
            <i class="ti ti-x icon alert-icon me-2"></i>
        </div>
        <div>{{ session('error') }}</div>
    </div>
    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
</div>
@endif

<div class="row g-3">
    {{-- Company Information Section --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-building me-2"></i>
                    Company Information
                </h3>
            </div>
            <div class="card-body">
                @if($profile)
                    {{-- Display Mode --}}
                    <div id="profile-display">
                        <div class="row">
                            <div class="col-md-3 text-center mb-3">
                                @if($profile->logo_url)
                                    <img src="{{ $profile->logo_url }}" alt="Company Logo" 
                                         class="img-fluid rounded" style="max-height: 120px;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                         style="height: 120px;">
                                        <i class="ti ti-building icon icon-lg text-secondary"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label class="form-label fw-bold">PT Name:</label>
                                        <div class="text-muted">{{ $profile->pt_name }}</div>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label fw-bold">Company Name:</label>
                                        <div class="text-muted">{{ $profile->company_name }}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Email:</label>
                                        <div class="text-muted">
                                            <a href="mailto:{{ $profile->email }}" class="text-decoration-none">
                                                <i class="ti ti-mail me-1"></i>{{ $profile->email }}
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Phone:</label>
                                        <div class="text-muted">
                                            <a href="{{ $profile->whatsapp_url }}" target="_blank" class="text-decoration-none">
                                                <i class="ti ti-phone me-1"></i>{{ $profile->phone }}
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label fw-bold">Address:</label>
                                        <div class="text-muted">{{ $profile->address }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="button" class="btn btn-primary" id="edit-profile-btn">
                                <i class="ti ti-edit me-1"></i> Edit Profile
                            </button>
                        </div>
                    </div>

                    {{-- Edit Mode --}}
                    <div id="profile-edit" style="display: none;">
                        <form action="{{ route('company-profile.update', $profile) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">PT Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('pt_name') is-invalid @enderror" 
                                           name="pt_name" value="{{ old('pt_name', $profile->pt_name) }}" required>
                                    @error('pt_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Company Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                           name="company_name" value="{{ old('company_name', $profile->company_name) }}" required>
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           name="email" value="{{ old('email', $profile->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           name="phone" value="{{ old('phone', $profile->phone) }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Address <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              name="address" rows="3" required>{{ old('address', $profile->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Logo</label>
                                    <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                                           name="logo" accept="image/*" id="logo-input">
                                    @error('logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-hint">Leave empty to keep current logo. Max: 2MB</small>
                                    <div class="mt-2" id="logo-preview"></div>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Map Embed Code <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('map_embed') is-invalid @enderror" 
                                              name="map_embed" rows="3" required 
                                              placeholder="Paste Google Maps embed iframe code here...">{{ old('map_embed', $profile->map_embed) }}</textarea>
                                    @error('map_embed')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-hint">Get embed code from Google Maps > Share > Embed a map</small>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="ti ti-check me-1"></i> Save Changes
                                </button>
                                <button type="button" class="btn btn-secondary" id="cancel-edit-btn">
                                    <i class="ti ti-x me-1"></i> Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    {{-- Create New Profile Form --}}
                    <div id="profile-create">
                        <form action="{{ route('company-profile.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">PT Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('pt_name') is-invalid @enderror" 
                                           name="pt_name" value="{{ old('pt_name') }}" required>
                                    @error('pt_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Company Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                           name="company_name" value="{{ old('company_name') }}" required>
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           name="phone" value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Address <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              name="address" rows="3" required>{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Logo <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                                           name="logo" accept="image/*" required id="logo-create-input">
                                    @error('logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-hint">Max: 2MB, Recommended: 400x400px</small>
                                    <div class="mt-2" id="logo-create-preview"></div>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Map Embed Code <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('map_embed') is-invalid @enderror" 
                                              name="map_embed" rows="3" required 
                                              placeholder="Paste Google Maps embed iframe code here...">{{ old('map_embed') }}</textarea>
                                    @error('map_embed')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-hint">Get embed code from Google Maps > Share > Embed a map</small>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-check me-1"></i> Create Profile
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Social Media Section --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-share me-2"></i>
                    Social Media
                </h3>
                <div class="card-actions">
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#add-social-media">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                @if($socialMedias->count() > 0)
                    <div class="list-group list-group-flush" id="social-media-list">
                        @foreach($socialMedias as $social)
                        <div class="list-group-item" data-id="{{ $social->id }}">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="avatar avatar-sm bg-{{ $social->platform_color }}-lt">
                                            <i class="{{ $social->platform_icon }}"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ ucfirst($social->platform) }}</div>
                                        <div class="text-secondary small">
                                            <a href="{{ $social->url }}" target="_blank" class="text-decoration-none">
                                                {{ Str::limit($social->url, 40) }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge badge-sm bg-{{ $social->is_active ? 'green' : 'red' }}-lt">
                                        {{ $social->status_text }}
                                    </span>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-ghost-secondary" data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <button type="button" class="dropdown-item" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#edit-social-media"
                                                    data-id="{{ $social->id }}"
                                                    data-platform="{{ $social->platform }}"
                                                    data-url="{{ $social->url }}"
                                                    data-status="{{ $social->is_active }}">
                                                <i class="ti ti-edit me-1"></i> Edit
                                            </button>
                                            <button type="button" class="dropdown-item text-danger delete-social-btn"
                                                    data-id="{{ $social->id }}"
                                                    data-name="{{ $social->platform }}"
                                                    data-url="{{ route('company-profile.social-media.destroy', $social) }}">
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
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <i class="ti ti-share icon icon-lg text-secondary"></i>
                        </div>
                        <p class="empty-title h5">No Social Media</p>
                        <p class="empty-subtitle text-secondary">
                            Add your social media accounts
                        </p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Map Section --}}
        @if($profile && $profile->map_embed)
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-map me-2"></i>
                    Location
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="ratio ratio-16x9">
                    {!! $profile->map_embed !!}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Modal Add Social Media --}}
<div class="modal modal-blur fade" id="add-social-media" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form class="modal-content" action="{{ route('company-profile.social-media.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add Social Media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Platform <span class="text-danger">*</span></label>
                            <select class="form-select @error('platform') is-invalid @enderror" name="platform" required>
                                <option value="">Select Platform</option>
                                @foreach(\App\Models\SocialMedia::getAvailablePlatforms() as $key => $value)
                                    <option value="{{ $key }}" {{ old('platform') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('platform')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">URL <span class="text-danger">*</span></label>
                            <input type="url" class="form-control @error('url') is-invalid @enderror" 
                                   name="url" value="{{ old('url') }}" 
                                   placeholder="https://..." required>
                            @error('url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" value="1" 
                                   {{ old('status', true) ? 'checked' : '' }}>
                            <span class="form-check-label">Active</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary ms-auto">Add Social Media</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Social Media --}}
<div class="modal modal-blur fade" id="edit-social-media" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form class="modal-content" id="edit-social-form" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Social Media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Platform <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit-platform" name="platform" required>
                                <option value="">Select Platform</option>
                                @foreach(\App\Models\SocialMedia::getAvailablePlatforms() as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">URL <span class="text-danger">*</span></label>
                            <input type="url" class="form-control" id="edit-url" name="url" 
                                   placeholder="https://..." required>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit-social-status" name="status" value="1">
                            <span class="form-check-label">Active</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary ms-auto">Update Social Media</button>
            </div>
        </form>
    </div>
</div>

{{-- Include Global Delete Modal --}}
@include('components.delete-modal')

@endsection

@push('styles')
<style>
    .avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .ratio iframe {
        border: 0;
        border-radius: 0.375rem;
    }
    
    .cursor-move {
        cursor: move;
    }
    
    .list-group-item {
        border-left: none;
        border-right: none;
    }
    
    .list-group-item:first-child {
        border-top: none;
    }
    
    .list-group-item:last-child {
        border-bottom: none;
    }
</style>
@endpush

@push('scripts')
@include('components.toast')
@include('components.alert')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Profile Edit Toggle (tidak perlu create toggle karena form langsung tampil)
        const editBtn = document.getElementById('edit-profile-btn');
        const cancelEditBtn = document.getElementById('cancel-edit-btn');
        
        const profileDisplay = document.getElementById('profile-display');
        const profileEdit = document.getElementById('profile-edit');

        if (editBtn) {
            editBtn.addEventListener('click', function() {
                profileDisplay.style.display = 'none';
                profileEdit.style.display = 'block';
            });
        }

        if (cancelEditBtn) {
            cancelEditBtn.addEventListener('click', function() {
                profileDisplay.style.display = 'block';
                profileEdit.style.display = 'none';
            });
        }

        // Logo Preview
        function setupImagePreview(inputId, previewId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            
            if (input && preview) {
                input.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        // Check file size (2MB)
                        if (file.size > 2 * 1024 * 1024) {
                            showAlert(input, 'danger', 'File size exceeds 2MB limit');
                            input.value = '';
                            return;
                        }

                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.innerHTML = `
                                <div class="card" style="max-width: 200px;">
                                    <img src="${e.target.result}" class="card-img-top" style="height: 120px; object-fit: cover;">
                                    <div class="card-body p-2">
                                        <small class="text-secondary">${file.name}</small><br>
                                        <small class="text-secondary">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
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

        setupImagePreview('logo-input', 'logo-preview');
        setupImagePreview('logo-create-input', 'logo-create-preview');

        // Handle Edit Social Media Modal
        const editSocialModal = document.getElementById('edit-social-media');
        if (editSocialModal) {
            editSocialModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const platform = button.getAttribute('data-platform');
                const url = button.getAttribute('data-url');
                const status = button.getAttribute('data-status') === '1';

                // Update form action
                const form = document.getElementById('edit-social-form');
                form.action = `{{ url('company-profile/social-media') }}/${id}`;

                // Fill form fields
                document.getElementById('edit-platform').value = platform;
                document.getElementById('edit-url').value = url;
                document.getElementById('edit-social-status').checked = status;
            });
        }

        // Handle Delete Social Media
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-social-btn') || e.target.closest('.delete-social-btn')) {
                e.preventDefault();
                
                const button = e.target.classList.contains('delete-social-btn') ? e.target : e.target.closest('.delete-social-btn');
                const itemName = button.getAttribute('data-name');
                const deleteUrl = button.getAttribute('data-url');
                
                // Update modal content
                const deleteForm = document.getElementById('delete-form');
                const deleteMessage = document.getElementById('delete-message');
                
                deleteForm.action = deleteUrl;
                deleteMessage.innerHTML = `Do you really want to delete "<strong>${itemName}</strong>" social media? This process cannot be undone.`;
                
                // Show modal
                const deleteModal = new bootstrap.Modal(document.getElementById('delete-modal'));
                deleteModal.show();
            }
        });

        // Reset Add Modal when opened
        const addSocialModal = document.getElementById('add-social-media');
        if (addSocialModal) {
            addSocialModal.addEventListener('show.bs.modal', function (event) {
                const form = addSocialModal.querySelector('form');
                form.reset();
                form.querySelector('input[name="status"]').checked = true;
            });
        }

        // Form submission loading states
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                    
                    // Re-enable after 3 seconds (fallback)
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }, 3000);
                }
            });
        });
    });
</script>
@endpush