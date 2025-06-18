@extends('layouts.main')

@section('title', 'Buat Project Baru')

@push('styles')
<style>
    .gallery-item {
        transition: all 0.3s ease;
        position: relative;
    }
    
    .gallery-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .form-label-sm {
        font-size: 0.75rem;
        margin-bottom: 0.25rem;
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
        <h2 class="page-title">Buat Project Baru</h2>
        <div class="page-subtitle text-secondary">Tambahkan project pengembangan baru</div>
    </div>
    <a href="{{ route('development.project.index') }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left me-1"></i> Kembali ke Project
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

<form action="{{ route('development.project.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">
        {{-- Basic Information --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="ti ti-info-circle me-2"></i>Informasi Dasar</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" name="category_id" required>
                                    <option value="">Pilih kategori project</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                <label class="form-label">Nama Project <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Deskripsi Singkat</label>
                                <textarea class="form-control @error('short_description') is-invalid @enderror" 
                                          name="short_description" rows="3" 
                                          placeholder="Deskripsi singkat project (maks 500 karakter)">{{ old('short_description') }}</textarea>
                                @error('short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Deskripsi ini akan ditampilkan sebagai preview text.</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Deskripsi Lengkap</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          name="description" id="editor" rows="8" 
                                          placeholder="Deskripsi lengkap project...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Berikan detail lengkap tentang project ini.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SEO Meta --}}
            @include('components.seo-meta-form', ['data' => 'create', 'type' => 'create'])
        </div>

        {{-- Settings --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="ti ti-settings me-2"></i>Pengaturan</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" value="1" 
                                   {{ old('status', true) ? 'checked' : '' }}>
                            <span class="form-check-label">Aktif</span>
                        </label>
                        <small class="form-hint">Aktifkan project ini untuk ditampilkan di website.</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Images --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="ti ti-photo me-2"></i>Gambar</h3>
                    <div class="card-actions">
                        <small class="text-secondary">Semua gambar akan dikompres dan dikonversi ke format WebP</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Main Image --}}
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Gambar Utama <span class="text-danger">*</span></label>
                                <input type="file" class="form-control @error('main_image') is-invalid @enderror" 
                                       name="main_image" accept="image/*" required id="main-image">
                                @error('main_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Rekomendasi: 1200x800px, Maks: 5MB</small>
                                <div class="mt-2" id="main-image-preview"></div>
                            </div>
                        </div>

                        {{-- Banner Image --}}
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Gambar Banner <span class="text-danger">*</span></label>
                                <input type="file" class="form-control @error('banner_image') is-invalid @enderror" 
                                       name="banner_image" accept="image/*" required id="banner-image">
                                @error('banner_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Rekomendasi: 1920x600px, Maks: 5MB</small>
                                <div class="mt-2" id="banner-image-preview"></div>
                            </div>
                        </div>

                        {{-- Logo Image --}}
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Logo Project</label>
                                <input type="file" class="form-control @error('logo_image') is-invalid @enderror" 
                                       name="logo_image" accept="image/*" id="logo-image">
                                @error('logo_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Rekomendasi: 400x400px, Maks: 2MB</small>
                                <div class="mt-2" id="logo-image-preview"></div>
                            </div>
                        </div>

                        {{-- Siteplan Image --}}
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Gambar Siteplan</label>
                                <input type="file" class="form-control @error('siteplan_image') is-invalid @enderror" 
                                       name="siteplan_image" accept="image/*" id="siteplan-image">
                                @error('siteplan_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Rekomendasi: 1920x1080px, Maks: 5MB</small>
                                <div class="mt-2" id="siteplan-image-preview"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Gallery Images Section --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-library-photo me-2"></i>
                        Galeri Project
                    </h3>
                    <div class="card-actions">
                        <span class="badge bg-yellow-lt">
                            <i class="ti ti-info-circle me-1"></i>
                            Opsional
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Gambar Galeri</label>
                        <input type="file" class="form-control @error('gallery_images.*') is-invalid @enderror" 
                            name="gallery_images[]" accept="image/*" multiple id="gallery-images">
                        @error('gallery_images.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">
                            <i class="ti ti-info-circle me-1"></i>
                            Pilih beberapa gambar untuk galeri project. Maks: 5MB per gambar
                        </small>
                    </div>
                    
                    <div id="gallery-preview" class="row g-3"></div>
                </div>
            </div>
        </div>

        {{-- Facility Images Section --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-building me-2"></i>
                        Fasilitas Project
                    </h3>
                    <div class="card-actions">
                        <span class="badge bg-yellow-lt">
                            <i class="ti ti-info-circle me-1"></i>
                            Opsional
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Gambar Fasilitas</label>
                        <input type="file" class="form-control @error('facility_images.*') is-invalid @enderror" 
                            name="facility_images[]" accept="image/*" multiple id="facility-images">
                        @error('facility_images.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">
                            <i class="ti ti-info-circle me-1"></i>
                            Pilih beberapa gambar fasilitas project. Maks: 5MB per gambar
                        </small>
                    </div>
                    
                    <div id="facility-preview" class="row g-3"></div>
                </div>
            </div>
        </div>

        {{-- Submit Buttons --}}
        <div class="col-12">
            <div class="card">
                <div class="card-footer text-end">
                    <div class="d-flex">
                        <a href="{{ route('development.project.index') }}" class="btn btn-link">Batal</a>
                        <button type="submit" class="btn btn-primary ms-auto">
                            Buat Project
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
        // Image preview functionality
        function setupImagePreview(inputId, previewId, maxFileSize = 5) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            
            if (input && preview) {
                input.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();

                        // Check file size
                        if (file.size > maxFileSize * 1024 * 1024) {
                            showAlert(input, 'danger', `Ukuran file melebihi batas ${maxFileSize} MB.`);
                            input.value = '';
                            return;
                        }

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

        // Setup previews for all image inputs
        setupImagePreview('main-image', 'main-image-preview', 5);
        setupImagePreview('banner-image', 'banner-image-preview', 5);
        setupImagePreview('logo-image', 'logo-image-preview', 2);
        setupImagePreview('siteplan-image', 'siteplan-image-preview', 5);

        // Gallery images handling
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
                        showAlert(galleryInput, 'danger', `File ${file.name} terlalu besar (maks: 5MB)`);
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const col = document.createElement('div');
                        col.className = 'col-md-4';
                        col.innerHTML = `
                            <div class="card gallery-item">
                                <img src="${e.target.result}" class="card-img-top" style="height: 200px; object-fit: cover;">
                                <div class="card-body p-3">
                                    <div class="mb-2">
                                        <label class="form-label form-label-sm">Alt Text</label>
                                        <input type="text" class="form-control form-control-sm" 
                                            name="gallery_alt_texts[${index}]" 
                                            placeholder="Deskripsikan gambar">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label form-label-sm">Caption</label>
                                        <textarea class="form-control form-control-sm" rows="2" 
                                                name="gallery_captions[${index}]" 
                                                placeholder="Caption gambar"></textarea>
                                    </div>
                                    <div class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="removeGalleryImage(${index})">
                                            <i class="ti ti-trash"></i> Hapus
                                        </button>
                                    </div>
                                    <small class="text-secondary d-block mt-2">
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

        // Remove gallery image function
        window.removeGalleryImage = function(index) {
            galleryFiles.splice(index, 1);
            
            // Create new FileList
            const dt = new DataTransfer();
            galleryFiles.forEach(file => dt.items.add(file));
            galleryInput.files = dt.files;
            
            renderGalleryPreview();
        };

        // Facility images handling
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
                        showAlert(facilityInput, 'danger', `File ${file.name} bukan gambar yang valid`);
                        return;
                    }
                    
                    if (file.size > 5 * 1024 * 1024) {
                        showAlert(facilityInput, 'danger', `File ${file.name} terlalu besar (maks: 5MB)`);
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const col = document.createElement('div');
                        col.className = 'col-md-4';
                        col.innerHTML = `
                            <div class="card gallery-item">
                                <img src="${e.target.result}" class="card-img-top" style="height: 200px; object-fit: cover;">
                                <div class="card-body p-3">
                                    <div class="mb-2">
                                        <label class="form-label form-label-sm">Nama Fasilitas</label>
                                        <input type="text" class="form-control form-control-sm" 
                                            name="facility_titles[${index}]" 
                                            placeholder="Nama fasilitas">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label form-label-sm">Deskripsi</label>
                                        <textarea class="form-control form-control-sm" rows="2" 
                                                name="facility_descriptions[${index}]" 
                                                placeholder="Deskripsi fasilitas"></textarea>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label form-label-sm">Alt Text</label>
                                        <input type="text" class="form-control form-control-sm" 
                                            name="facility_alt_texts[${index}]" 
                                            placeholder="Alt text untuk SEO">
                                    </div>
                                    <div class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="removeFacilityImage(${index})">
                                            <i class="ti ti-trash"></i> Hapus
                                        </button>
                                    </div>
                                    <small class="text-secondary d-block mt-2">
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

        // Remove facility image function
        window.removeFacilityImage = function(index) {
            facilityFiles.splice(index, 1);
            
            // Create new FileList
            const dt = new DataTransfer();
            facilityFiles.forEach(file => dt.items.add(file));
            facilityInput.files = dt.files;
            
            renderFacilityPreview();
        };

        // Form submission loading state
        const form = document.querySelector('form');
        const submitBtn = form.querySelector('button[type="submit"]');
        
        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Membuat Project...';
        });
    });
</script>
@endpush