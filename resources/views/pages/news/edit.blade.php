@extends('layouts.main')

@section('title', 'Edit News')

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
        <h2 class="page-title">Edit News</h2>
        <div class="page-subtitle">{{ $news->title }}</div>
    </div>
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

<form action="{{ route('news.update', $news) }}" method="POST" enctype="multipart/form-data" id="edit-form">
    @csrf
    @method('PUT')
    <div class="row g-3">
        {{-- Main Content --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-edit me-2"></i>
                        Article Content
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       name="title" value="{{ old('title', $news->title) }}" required 
                                       placeholder="Enter news title">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    <span id="title-count">{{ strlen($news->title ?? '') }}</span>/255 characters
                                </small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Content <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('content') is-invalid @enderror" 
                                          name="content" id="editor" rows="15" 
                                          placeholder="Write your news content here...">{{ old('content', $news->content) }}</textarea>
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

            {{-- SEO Meta --}}
            @include('components.seo-meta-form', ['data' => $news, 'type' => 'edit'])
            
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Publishing Options --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-settings me-2"></i>
                        Publishing
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" name="status" required id="status-select">
                            <option value="draft" {{ old('status', $news->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', $news->status) === 'published' ? 'selected' : '' }}>Published</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">Choose whether to save as draft or publish.</small>
                    </div>
                    
                    <div class="mb-3" id="published-date-group" style="display: none;">
                        <label class="form-label">Published Date</label>
                        <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" 
                               name="published_at" value="{{ old('published_at', $news->published_at ? $news->published_at->format('Y-m-d\TH:i') : '') }}" id="published-date">
                        @error('published_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">Leave empty to use current date and time when publishing.</small>
                    </div>
                    
                    <div class="mb-0">
                        <label class="form-label text-secondary">Article Slug</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">{{ url('/') }}/</span>
                            <input type="text" class="form-control bg-light" value="{{ $news->slug }}" readonly>
                        </div>
                        <small class="form-hint">URL slug will be automatically generated from title.</small>
                    </div>
                </div>
            </div>

            {{-- Article Details --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-user me-2"></i>
                        Article Details
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Author</label>
                        <input type="text" class="form-control @error('author') is-invalid @enderror" 
                               name="author" value="{{ old('author', $news->author) }}" 
                               placeholder="Author name">
                        @error('author')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">Optional. Leave empty if no specific author.</small>
                    </div>
                </div>
            </div>

            {{-- Current Image --}}
            @if($news->image_url)
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-photo me-2"></i>
                        Current Image
                    </h3>
                </div>
                <div class="card-body">
                    <div class="card border">
                        <img src="{{ $news->image_url }}" class="card-img-top" style="height: 150px; object-fit: cover;">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between">
                                <small class="text-secondary fw-medium">Featured Image</small>
                                <a href="{{ $news->image_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
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
                        {{ $news->image_url ? 'Update Image' : 'Add Image' }}
                    </h3>
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
                            {{ $news->image_url ? 'Leave empty to keep current image.' : '' }} Recommended: 1200x630px, Max: 10MB
                        </small>
                        <div class="mt-2" id="image-preview"></div>
                    </div>
                </div>
            </div>

            {{-- Article Meta --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-info-square me-2"></i>
                        Article Meta
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-2">
                                <small class="text-secondary">Created:</small>
                                <div>{{ $news->created_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-2">
                                <small class="text-secondary">Last Updated:</small>
                                <div>{{ $news->updated_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                        @if($news->published_at)
                        <div class="col-12">
                            <div class="mb-0">
                                <small class="text-secondary">Published:</small>
                                <div>{{ $news->published_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                        @endif
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
                                Last saved: {{ $news->updated_at->format('d M Y, H:i') }}
                            </small>
                        </div>
                        <div class="btn-list">
                            <a href="{{ route('news.index') }}" class="btn btn-link">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <i class="ti ti-device-floppy me-1"></i> 
                                Update News
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
                // Set current date/time as default if empty and status is changing to published
                if (!publishedDateInput.value && {{ $news->status === 'draft' ? 'true' : 'false' }}) {
                    const now = new Date();
                    const localDateTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000);
                    publishedDateInput.value = localDateTime.toISOString().slice(0, 16);
                }
            } else {
                publishedDateGroup.style.display = 'none';
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
            
            // Add loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating News...';
            
            // Add loading class to form
            form.classList.add('loading');
        });
    });
</script>
@endpush