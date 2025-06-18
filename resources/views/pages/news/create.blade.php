@extends('layouts.main')

@section('title', 'Create News')

@push('styles')
<style>
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
    <h2 class="page-title">Create New News</h2>
    <a href="{{ route('news.index') }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left me-1"></i> Back to News
    </a>
</div>
@endsection

@section('content')
{{-- Alert Messages --}}
@if(session('error'))
<div class="alert alert-danger alert-dismissible" role="alert">
    <div class="d-flex">
        <div>
            <i class="ti ti-exclamation-circle me-2"></i>
        </div>
        <div>{{ session('error') }}</div>
    </div>
    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
</div>
@endif

<form action="{{ route('news.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">
        {{-- Main Content --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="ti ti-edit me-2"></i>Article Content</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       name="title" value="{{ old('title') }}" required
                                       placeholder="Enter news title">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    <span id="title-count">0</span>/255 characters
                                </small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Content <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('content') is-invalid @enderror" 
                                          name="content" id="editor" rows="15" 
                                          placeholder="Write your news content here...">{{ old('content') }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" id="content-error" style="display: none;"></div>
                                <small class="form-hint">Write the full content of your news article.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('components.seo-meta-form', ['data' => 'create', 'type' => 'create'])
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Publishing Options --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="ti ti-settings me-2"></i>Publishing</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" name="status" required id="status-select">
                            <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">Choose whether to save as draft or publish immediately.</small>
                    </div>
                    
                    <div class="mb-3" id="published-date-group" style="display: none;">
                        <label class="form-label">Published Date</label>
                        <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" 
                               name="published_at" value="{{ old('published_at') }}" id="published-date">
                        @error('published_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">Leave empty to use current date and time.</small>
                    </div>
                </div>
            </div>

            {{-- Article Details --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="ti ti-user me-2"></i>Article Details</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Author</label>
                        <input type="text" class="form-control @error('author') is-invalid @enderror" 
                               name="author" value="{{ old('author') }}" 
                               placeholder="Author name">
                        @error('author')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">Optional. Leave empty if no specific author.</small>
                    </div>
                </div>
            </div>

            {{-- Featured Image --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="ti ti-photo me-2"></i>Featured Image</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                               name="image" accept="image/*" id="image-input">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">
                            <i class="ti ti-info-circle me-1"></i>
                            Recommended: 1200x630px, Max: 5MB
                        </small>
                        <div class="mt-2" id="image-preview"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit Buttons --}}
        <div class="col-12">
            <div class="card">
                <div class="card-footer text-end">
                    <div class="d-flex">
                        <a href="{{ route('news.index') }}" class="btn btn-link">Cancel</a>
                        <button type="submit" class="btn btn-primary ms-auto" id="submit-btn">
                            <i class="ti ti-device-floppy me-1"></i>
                            Create News
                        </button>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Title character counter
        const titleInput = document.querySelector('input[name="title"]');
        const titleCount = document.getElementById('title-count');
        
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

        // Status change handler
        const statusSelect = document.getElementById('status-select');
        const publishedDateGroup = document.getElementById('published-date-group');
        const publishedDateInput = document.getElementById('published-date');
        
        statusSelect.addEventListener('change', function() {
            if (this.value === 'published') {
                publishedDateGroup.style.display = 'block';
                // Set current date/time as default if empty
                if (!publishedDateInput.value) {
                    const now = new Date();
                    const localDateTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000);
                    publishedDateInput.value = localDateTime.toISOString().slice(0, 16);
                }
            } else {
                publishedDateGroup.style.display = 'none';
                publishedDateInput.value = '';
            }
        });

        // Trigger change event on page load
        statusSelect.dispatchEvent(new Event('change'));

        // Image preview functionality
        const imageInput = document.getElementById('image-input');
        const imagePreview = document.getElementById('image-preview');
        
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size
                const maxSize = 5 * 1024 * 1024; // 5MB
                if (file.size > maxSize) {
                    showAlert(imageInput, 'danger', 'File size too large. Maximum 5MB allowed.');
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
                        <div class="card">
                            <img src="${e.target.result}" class="card-img-top" style="height: 200px; object-fit: cover;">
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

        // Form submission loading state
        const form = document.querySelector('form');
        const submitBtn = document.getElementById('submit-btn');
        
        form.addEventListener('submit', function(e) {
            // Validate content from editor
            const editorContent = hugeRTE.get('editor').getContent();
            const contentTextarea = document.querySelector('textarea[name="content"]');
            const contentError = document.getElementById('content-error');
            
            // Clear previous errors
            contentTextarea.classList.remove('is-invalid');
            contentError.style.display = 'none';
            
            // Check if content is empty
            if (!editorContent.trim() || editorContent.trim() === '<p></p>' || editorContent.trim() === '<p><br></p>') {
                e.preventDefault();
                contentTextarea.classList.add('is-invalid');
                contentError.textContent = 'Content is required.';
                contentError.style.display = 'block';
                
                // Scroll to editor
                document.getElementById('editor').scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
                
                return false;
            }
            
            // Update textarea value with editor content
            contentTextarea.value = editorContent;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
            form.classList.add('loading');
        });
    });
</script>
@endpush