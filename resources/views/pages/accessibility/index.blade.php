@extends('layouts.main')

@section('title', 'Accessibilities Management')

@push('styles')
<style>
    .cursor-move {
        cursor: move;
    }
    
    .sortable-ghost {
        opacity: 0.5;
        background: #f8f9fa;
    }
    
    .sortable-chosen {
        background: #e3f2fd;
    }
    
    .sortable-drag {
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        transform: rotate(2deg);
    }
    
    .reorder-mode .table-sort {
        pointer-events: none;
        opacity: 0.5;
    }
    
    .reorder-mode .action-buttons {
        pointer-events: none;
        opacity: 0.3;
    }
    
    .reorder-active {
        border: 2px dashed #0054a6;
        background: #f8f9fa;
    }

    .avatar {
        border: 2px solid #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    #reorder-instruction {
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
    
    /* Search highlighting */
    .search-highlight {
        background-color: #fff3cd;
        padding: 0.1rem 0.2rem;
        border-radius: 0.25rem;
        font-weight: 600;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h2 class="page-title">Accessibility Management</h2>
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

<div class="row">
    {{-- Accessibility Page Management --}}
    <div class="col-12 mb-5">
        {{-- Create Form --}}
        <form action="{{ route('accessibilities.page.update') }}" id="accessibility-page-form" class="w-100" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-layout-dashboard me-2"></i>
                        Accessibility Page Settings
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Judul Halaman <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                        name="title" value="{{ old('title', $accessibilityPage->title ?? '') }}" required 
                                        placeholder="Masukkan judul halaman aksesibilitas">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                            name="description" id="editor" rows="8"
                                            placeholder="Masukkan deskripsi halaman aksesibilitas...">{{ old('description', $accessibilityPage->description ?? '') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SEO Meta --}}
            @include('components.seo-meta-form', ['data' => $accessibilityPage, 'type' => is_null($accessibilityPage) ? 'create' : 'edit'])

            {{-- Current Banner Images --}}
            @if($accessibilityPage?->bannerImages->count() > 0)
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-photo me-2"></i>
                        Current Banners
                    </h3>
                    <div class="card-actions">
                        <span class="badge bg-blue-lt me-2">{{ $accessibilityPage->bannerImages->count() }} Images</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="toggle-banner-reorder">
                            <i class="ti ti-arrows-sort me-1"></i> <span id="banner-reorder-text">Reorder</span>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <div class="d-flex">
                            <div>
                                <h4>Manage Banner Images</h4>
                                Update metadata, mark for deletion, or click <strong>"Reorder"</strong> button to drag & drop images to change their order.
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3" id="sortable-banner">
                        @foreach($accessibilityPage->bannerImages as $banner)
                        <div class="col-md-4 sortable-banner-item" data-id="{{ $banner->id }}" data-image-id="{{ $banner->id }}">
                            <div class="card current-banner-item">
                                {{-- Reorder Handle --}}
                                <div class="banner-reorder-handle" style="display: none;">
                                    <div class="position-absolute top-0 start-0 m-2 cursor-move bg-secondary text-white rounded p-1" style="z-index: 10;">
                                        <i class="ti ti-grip-vertical"></i>
                                    </div>
                                </div>
                                
                                <div class="ribbon ribbon-top bg-blue">
                                    #<span class="order-number">{{ $banner->order }}</span>
                                </div>
                                <img src="{{ $banner->image_url }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                                <div class="card-body p-3">
                                    <div class="mb-2">
                                        <label class="form-label form-label-sm">Alt Text</label>
                                        <input type="text" class="form-control form-control-sm edit-input" 
                                            name="existing_banner_alt_texts[{{ $banner->id }}]" 
                                            value="{{ $banner->alt_text }}"
                                            placeholder="Describe the image">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label form-label-sm">Caption</label>
                                        <textarea class="form-control form-control-sm edit-input" rows="2" 
                                                name="existing_banner_captions[{{ $banner->id }}]" 
                                                placeholder="Image caption">{{ $banner->caption }}</textarea>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input type="checkbox" class="form-check-input edit-input" 
                                            name="delete_banner_images[]" 
                                            value="{{ $banner->id }}"
                                            id="delete-{{ $banner->id }}">
                                        <label class="form-check-label text-danger" for="delete-{{ $banner->id }}">
                                            <i class="ti ti-trash me-1"></i>Delete this image
                                        </label>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-secondary">
                                            <i class="ti ti-calendar me-1"></i>
                                            {{ $banner->created_at->format('d M Y') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Add New Banner Images --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-photo-plus me-2"></i>
                        Add New Banner Images
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
                        <label class="form-label">New Banner Images</label>
                        <input type="file" class="form-control @error('banner_images.*') is-invalid @enderror" 
                            name="banner_images[]" accept="image/*" multiple id="banner-images">
                        @error('banner_images.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">
                            <i class="ti ti-info-circle me-1"></i>
                            Select multiple images to add to banners. Max: 10MB per image
                        </small>
                    </div>
                    
                    <div id="banner-preview" class="row g-3"></div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        @if($accessibilityPage)
                        <small class="text-secondary">
                            <i class="ti ti-clock me-1"></i>
                            Last saved: {{ $accessibilityPage->updated_at->format('d M Y, H:i') }}
                        </small>
                        @else
                        <div></div>
                        @endif
                        <button type="submit" class="btn btn-primary" id="submit-btn">
                            <i class="ti ti-device-floppy me-1"></i>
                            Save Accessibility Page
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Actions Bar --}}
    <div class="col-12 mb-3">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div class="d-flex gap-2">
                <div class="input-icon" style="max-width: 350px;">
                    <span class="input-icon-addon">
                        <i class="ti ti-search"></i>
                    </span>
                    <input type="text" 
                        class="form-control" 
                        placeholder="Search accessibilities..." 
                        id="search-input">
                </div>
                <button type="button" class="btn btn-outline-secondary" id="toggle-reorder">
                    <i class="ti ti-arrows-sort"></i>
                </button>
            </div>

            <a href="{{ route('accessibilities.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> Add Accessibility
            </a>
        </div>
    </div>

    {{-- Accessibilities Table --}}
    <div class="col-12">
        <div class="card">
            <div class="card-body p-0">
                <div id="table-default" class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="50" class="reorder-handle-header" style="display: none;">
                                    <i class="ti ti-arrows-sort"></i>
                                </th>
                                <th width="80">Image</th>
                                <th><button class="table-sort" data-sort="sort-name">Accessibility Name</button></th>
                                <th><button class="table-sort" data-sort="sort-order">Order</button></th>
                                <th><button class="table-sort" data-sort="sort-status">Status</button></th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-tbody" id="sortable-table">
                            @forelse($accessibilities as $index => $accessibility)
                            <tr data-id="{{ $accessibility->id }}" class="sortable-row">
                                <td class="reorder-handle" style="display: none;">
                                    <div class="cursor-move text-secondary">
                                        <i class="ti ti-grip-vertical"></i>
                                    </div>
                                </td>
                                <td>
                                    @if($accessibility->image_url)
                                    <div class="avatar avatar-md rounded" 
                                        style="background-image: url('{{ $accessibility->image_url }}'); background-size: cover; background-position: center;"></div>
                                    @else
                                    <div class="avatar avatar-md rounded bg-secondary-lt">
                                        <i class="ti ti-building"></i>
                                    </div>
                                    @endif
                                </td>
                                <td class="sort-name">
                                    <div>
                                        <div class="fw-bold">{{ $accessibility->name }}</div>
                                        @if($accessibility->description)
                                        <small class="text-secondary">{{ Str::limit($accessibility->description, 80) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td class="sort-order">{{ $accessibility->order }}</td>
                                <td class="sort-status">
                                    <span class="badge badge-sm bg-{{ $accessibility->is_active ? 'green' : 'red' }}-lt">
                                        {{ $accessibility->status_text }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap action-buttons">
                                        <a href="{{ route('accessibilities.edit', $accessibility) }}" 
                                        class="btn btn-sm btn-outline-primary" title="Edit Accessibility">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                                data-id="{{ $accessibility->id }}"
                                                data-name="{{ $accessibility->name }}"
                                                data-url="{{ route('accessibilities.destroy', $accessibility) }}"
                                                title="Delete Accessibility">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr id="empty-row">
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty">
                                        <div class="empty-icon">
                                            <i class="ti ti-building icon icon-lg"></i>
                                        </div>
                                        <p class="empty-title h3">No accessibilities yet</p>
                                        <p class="empty-subtitle text-secondary">
                                            Get started by creating your first accessibility.<br>
                                            Showcase amenities and features available to your clients.
                                        </p>
                                        <div class="empty-action">
                                            <a href="{{ route('accessibilities.create') }}" class="btn btn-primary">
                                                <i class="ti ti-plus me-1"></i> Create First Accessibility
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Include Global Delete Modal --}}
@include('components.delete-modal')

@endsection

@push('scripts')
@include('components.scripts.wysiwyg')
@include('components.toast')
<script>
    function confirmDeletePage() {
        const deleteModal = new bootstrap.Modal(document.getElementById('delete-page-modal'));
        deleteModal.show();
    }

    document.addEventListener("DOMContentLoaded", function () {
        let sortable = null;
        let isReorderMode = false;
        let bannerSortable = null;
        let isBannerReorderMode = false;
        
        // Initialize List.js for table sorting and search
        const list = new List("table-default", {
            sortClass: "table-sort",
            listClass: "table-tbody",
            valueNames: [
                "sort-name",
                "sort-order",
                "sort-status",
            ],
        });

        // Get elements
        const searchInput = document.getElementById('search-input');
        const table = document.querySelector('.table');
        const reorderHandles = document.querySelectorAll('.reorder-handle');
        const reorderHeader = document.querySelector('.reorder-handle-header');
        const toggleReorderBtn = document.getElementById('toggle-reorder');
        
        // Search functionality
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase().trim();
            filterTable(searchTerm);
        });

        function filterTable(searchTerm) {
            const rows = document.querySelectorAll('tbody tr:not(#empty-row)');
            let visibleCount = 0;

            rows.forEach(row => {
                const nameElement = row.querySelector('.sort-name');
                const nameText = nameElement.textContent.toLowerCase();
                
                let showRow = true;

                // Filter by search term
                if (searchTerm && !nameText.includes(searchTerm)) {
                    showRow = false;
                }

                if (showRow) {
                    row.style.display = '';
                    visibleCount++;
                    
                    // Highlight search term
                    if (searchTerm) {
                        highlightSearchTerm(row, searchTerm);
                    } else {
                        removeHighlight(row);
                    }
                } else {
                    row.style.display = 'none';
                    removeHighlight(row);
                }
            });

            toggleEmptyState(visibleCount, rows.length);
        }

        function highlightSearchTerm(row, term) {
            const nameElement = row.querySelector('.sort-name .fw-bold');
            const descElement = row.querySelector('.sort-name .text-secondary');
            
            [nameElement, descElement].forEach(element => {
                if (element && element.textContent.toLowerCase().includes(term)) {
                    const regex = new RegExp(`(${escapeRegExp(term)})`, 'gi');
                    element.innerHTML = element.textContent.replace(regex, '<span class="search-highlight">$1</span>');
                }
            });
        }

        function removeHighlight(row) {
            row.querySelectorAll('.search-highlight').forEach(el => {
                el.outerHTML = el.innerHTML;
            });
        }

        function escapeRegExp(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        function toggleEmptyState(visibleCount, totalCount) {
            const emptyRow = document.getElementById('empty-row');
            
            if (visibleCount === 0 && totalCount > 0) {
                if (!emptyRow) {
                    const tbody = document.querySelector('.table-tbody');
                    const emptyRowHtml = `
                        <tr id="empty-row">
                            <td colspan="6" class="text-center py-4">
                                <div class="empty">
                                    <div class="empty-icon">
                                        <i class="ti ti-search icon icon-lg"></i>
                                    </div>
                                    <p class="empty-title">No accessibilities found</p>
                                    <p class="empty-subtitle text-secondary">
                                        Try adjusting your search terms.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', emptyRowHtml);
                }
            } else if (emptyRow && visibleCount > 0) {
                emptyRow.remove();
            }
        }
        
        // Toggle Reorder Mode for Accessibility Items
        if (toggleReorderBtn) {
            toggleReorderBtn.addEventListener('click', function() {
                isReorderMode = !isReorderMode;
                
                if (isReorderMode) {
                    enableReorderMode();
                } else {
                    disableReorderMode();
                }
            });
        }

        function enableReorderMode() {
            // Clear search and show all rows
            searchInput.value = '';
            filterTable('');
            
            // Disable search during reorder
            searchInput.disabled = true;
            searchInput.placeholder = 'Search disabled during reorder mode';
            
            // Show reorder handles
            reorderHandles.forEach(handle => handle.style.display = 'table-cell');
            reorderHeader.style.display = 'table-cell';
            
            // Add reorder mode class
            table.classList.add('reorder-mode', 'reorder-active');
            
            // Update button
            toggleReorderBtn.classList.remove('btn-outline-secondary');
            toggleReorderBtn.classList.add('btn-warning');
            toggleReorderBtn.querySelector('i').className = 'ti ti-x';
            
            // Initialize sortable
            const sortableTable = document.getElementById('sortable-table');
            sortable = new Sortable(sortableTable, {
                handle: '.reorder-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                filter: '#empty-row',
                onEnd: function(evt) {
                    updateOrder();
                }
            });
            
            // Show reorder instructions
            showReorderInstructions();
        }

        function disableReorderMode() {
            // Enable search
            searchInput.disabled = false;
            searchInput.placeholder = 'Search accessibilities...';
            
            // Hide reorder handles
            reorderHandles.forEach(handle => handle.style.display = 'none');
            reorderHeader.style.display = 'none';
            
            // Remove reorder mode class
            table.classList.remove('reorder-mode', 'reorder-active');
            
            // Update button
            toggleReorderBtn.classList.remove('btn-warning');
            toggleReorderBtn.classList.add('btn-outline-secondary');
            toggleReorderBtn.querySelector('i').className = 'ti ti-arrows-sort';
            
            // Destroy sortable
            if (sortable) {
                sortable.destroy();
                sortable = null;
            }
            
            // Hide instructions
            hideReorderInstructions();
        }

        function updateOrder() {
            const rows = document.querySelectorAll('.sortable-row[style=""]');
            const orderData = [];
            
            rows.forEach((row, index) => {
                const id = row.getAttribute('data-id');
                orderData.push({
                    id: id,
                    order: index + 1
                });
                
                // Update order display in table
                const orderCell = row.querySelector('.sort-order');
                orderCell.textContent = index + 1;
            });
            
            // Send AJAX request to update order
            fetch('{{ route('accessibilities.reorder') }}', {
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
                    showToast('Order updated successfully', 'success');
                } else {
                    showToast('Failed to update order', 'error');
                    setTimeout(() => location.reload(), 1000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to update order', 'error');
            });
        }

        function showReorderInstructions() {
            const instruction = document.createElement('div');
            instruction.id = 'reorder-instruction';
            instruction.className = 'alert alert-info alert-dismissible mt-3';
            instruction.innerHTML = `
                <div class="d-flex">
                    <div>
                        <h4>Reorder Mode Active</h4>
                        Drag the <i class="ti ti-grip-vertical"></i> handle to reorder accessibilities. Search and other functions are disabled during reorder mode.
                    </div>
                </div>
            `;
            
            // Insert after the actions bar
            const actionsBar = document.querySelector('.col-12.mb-3');
            actionsBar.insertAdjacentElement('afterend', instruction);
        }

        function hideReorderInstructions() {
            const instruction = document.getElementById('reorder-instruction');
            if (instruction) {
                instruction.remove();
            }
        }

        // Banner Images Functionality
        const bannerInput = document.getElementById('banner-images');
        const bannerPreview = document.getElementById('banner-preview');
        let bannerFiles = [];

        if (bannerInput) {
            bannerInput.addEventListener('change', function(e) {
                const files = Array.from(e.target.files);
                bannerFiles = files;
                renderBannerPreview();
            });
        }

        function renderBannerPreview() {
            bannerPreview.innerHTML = '';
            
            if (bannerFiles.length === 0) {
                return;
            }

            bannerFiles.forEach((file, index) => {
                if (file) {
                    // Validate file
                    if (!file.type.startsWith('image/')) {
                        showAlert(bannerInput, 'danger', `File ${file.name} is not a valid image`);
                        return;
                    }
                    
                    if (file.size > 10 * 1024 * 1024) {
                        showAlert(bannerInput, 'danger', `File ${file.name} is too large (max: 10MB)`);
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const col = document.createElement('div');
                        col.className = 'col-md-4';
                        col.innerHTML = `
                            <div class="card banner-item">
                                <div class="ribbon ribbon-top bg-green">
                                    New
                                </div>
                                <img src="${e.target.result}" class="card-img-top" style="height: 200px; object-fit: cover;">
                                <div class="card-body p-3">
                                    <div class="mb-2">
                                        <label class="form-label form-label-sm">Alt Text</label>
                                        <input type="text" class="form-control form-control-sm" 
                                            name="banner_alt_texts[${index}]" 
                                            placeholder="Describe the image">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label form-label-sm">Caption</label>
                                        <textarea class="form-control form-control-sm" rows="2" 
                                                name="banner_captions[${index}]" 
                                                placeholder="Image caption"></textarea>
                                    </div>
                                    <div class="text-center mb-2">
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="removeBannerImage(${index})">
                                            <i class="ti ti-trash"></i> Remove
                                        </button>
                                    </div>
                                    <small class="text-secondary">
                                        ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)
                                    </small>
                                </div>
                            </div>
                        `;
                        bannerPreview.appendChild(col);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Remove new banner image function
        window.removeBannerImage = function(index) {
            bannerFiles.splice(index, 1);
            
            // Create new FileList
            const dt = new DataTransfer();
            bannerFiles.forEach(file => dt.items.add(file));
            bannerInput.files = dt.files;
            
            renderBannerPreview();
        };

        // Handle existing banner image deletion
        const deleteBannerCheckboxes = document.querySelectorAll('input[name="delete_banner_images[]"]');
        deleteBannerCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const card = this.closest('.current-banner-item');
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

        // Banner Reorder functionality
        const toggleBannerReorderBtn = document.getElementById('toggle-banner-reorder');
        const bannerReorderText = document.getElementById('banner-reorder-text');
        const bannerContainer = document.getElementById('sortable-banner');
        
        if (toggleBannerReorderBtn && bannerContainer) {
            toggleBannerReorderBtn.addEventListener('click', function() {
                isBannerReorderMode = !isBannerReorderMode;
                
                if (isBannerReorderMode) {
                    enableBannerReorderMode();
                } else {
                    disableBannerReorderMode();
                }
            });
        }

        function enableBannerReorderMode() {
            // Show reorder handles
            const reorderHandles = document.querySelectorAll('.banner-reorder-handle');
            reorderHandles.forEach(handle => handle.style.display = 'block');
            
            // Add reorder mode class
            bannerContainer.classList.add('banner-reorder-mode');
            
            // Update button
            toggleBannerReorderBtn.classList.remove('btn-outline-secondary');
            toggleBannerReorderBtn.classList.add('btn-success');
            bannerReorderText.textContent = 'Done';
            toggleBannerReorderBtn.querySelector('i').className = 'ti ti-check me-1';
            
            // Initialize sortable
            bannerSortable = new Sortable(bannerContainer, {
                handle: '.banner-reorder-handle',
                animation: 150,
                ghostClass: 'sortable-banner-ghost',
                chosenClass: 'sortable-banner-chosen',
                dragClass: 'sortable-banner-drag',
                onEnd: function(evt) {
                    updateBannerOrder();
                }
            });
            
            // Show instructions
            showBannerReorderInstructions();
        }

        function disableBannerReorderMode() {
            // Hide reorder handles
            const reorderHandles = document.querySelectorAll('.banner-reorder-handle');
            reorderHandles.forEach(handle => handle.style.display = 'none');
            
            // Remove reorder mode class
            bannerContainer.classList.remove('banner-reorder-mode');
            
            // Update button
            toggleBannerReorderBtn.classList.remove('btn-success');
            toggleBannerReorderBtn.classList.add('btn-outline-secondary');
            bannerReorderText.textContent = 'Reorder';
            toggleBannerReorderBtn.querySelector('i').className = 'ti ti-arrows-sort me-1';
            
            // Destroy sortable
            if (bannerSortable) {
                bannerSortable.destroy();
                bannerSortable = null;
            }
            
            // Hide instructions
            hideBannerReorderInstructions();
        }

        function updateBannerOrder() {
            const items = document.querySelectorAll('.sortable-banner-item');
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
            fetch('{{ route('accessibilities.banners.reorder') }}', {
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
                    showToast('Banner order updated successfully', 'success');
                } else {
                    showToast('Failed to update banner order', 'error');
                    setTimeout(() => location.reload(), 1000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to update banner order', 'error');
                setTimeout(() => location.reload(), 1000);
            });
        }

        function showBannerReorderInstructions() {
            const instruction = document.createElement('div');
            instruction.id = 'banner-reorder-instruction';
            instruction.className = 'alert alert-info alert-dismissible mb-3';
            instruction.innerHTML = `
                <div class="d-flex">
                    <div>
                        <h4>Banner Reorder Mode Active</h4>
                        Drag the <i class="ti ti-grip-vertical"></i> handle to reorder banner images. Editing inputs are disabled during reorder mode.
                    </div>
                </div>
            `;
            
            // Insert before banner container
            bannerContainer.parentElement.insertBefore(instruction, bannerContainer);
        }

        function hideBannerReorderInstructions() {
            const instruction = document.getElementById('banner-reorder-instruction');
            if (instruction) {
                instruction.remove();
            }
        }
        
        // Focus search input on Ctrl+K or Cmd+K
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
            }
        });

        const form = document.getElementById('accessibility-page-form');
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
@endpush