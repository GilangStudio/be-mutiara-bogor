@extends('layouts.main')

@section('title', 'Units - ' . $project->name)

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

    .search-highlight {
        background-color: #fff3cd;
        padding: 0.1rem 0.2rem;
        border-radius: 0.25rem;
        font-weight: 600;
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
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h2 class="page-title">Units Management</h2>
        <div class="page-subtitle text-secondary">{{ $project->name }}</div>
    </div>
    <div class="btn-list">
        <a href="{{ route('development.unit.create', $project) }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i> Add Unit
        </a>
        <a href="{{ route('development.project.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Back to Projects
        </a>
    </div>
</div>
@endsection

@section('content')
{{-- Alert Messages --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible" role="alert">
    <div class="d-flex">
        <div>
            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M5 12l5 5l10 -10"></path>
            </svg>
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

{{-- Project Info Card --}}
<div class="col-12 mb-3">
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-auto">
                    @if($project->main_image_url)
                    <div class="avatar avatar-lg rounded" 
                         style="background-image: url('{{ $project->main_image_url }}'); background-size: cover; background-position: center;"></div>
                    @else
                    <div class="avatar avatar-lg rounded bg-primary-lt">
                        <i class="ti ti-building-skyscraper"></i>
                    </div>
                    @endif
                </div>
                <div class="col">
                    <h3 class="card-title mb-1">{{ $project->name }}</h3>
                    <div class="text-secondary">
                        <span class="badge bg-blue-lt me-2">{{ $project->category->name }}</span>
                        <span class="text-secondary">{{ $units->count() }} Units</span>
                    </div>
                    @if($project->short_description)
                    <p class="text-secondary mt-2 mb-0">{{ $project->short_description }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Actions Bar --}}
<div class="col-12 mb-3">
    <div class="d-flex justify-content-between align-items-center gap-2">
        <div class="input-icon" style="max-width: 300px;">
            <span class="input-icon-addon">
                <i class="ti ti-search"></i>
            </span>
            <input type="text" class="form-control" placeholder="Search units..." id="search-input">
        </div>
        <div>
            <button type="button" class="btn btn-6 btn-outline-secondary w-100 btn-icon" id="toggle-reorder">
                <i class="ti ti-arrows-sort"></i>
            </button>
        </div>
    </div>
</div>

{{-- Units Table --}}
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
                            <th><button class="table-sort" data-sort="sort-name">Unit Name</button></th>
                            <th><button class="table-sort" data-sort="sort-specs">Specifications</button></th>
                            <th><button class="table-sort" data-sort="sort-order">Order</button></th>
                            <th><button class="table-sort" data-sort="sort-status">Status</button></th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-tbody" id="sortable-table">
                        @forelse($units as $index => $unit)
                        <tr data-id="{{ $unit->id }}" class="sortable-row">
                            <td class="reorder-handle" style="display: none;">
                                <div class="cursor-move text-secondary">
                                    <i class="ti ti-grip-vertical"></i>
                                </div>
                            </td>
                            <td>
                                @if($unit->main_image_url)
                                <div class="avatar avatar-md rounded" 
                                     style="background-image: url('{{ $unit->main_image_url }}'); background-size: cover; background-position: center;"></div>
                                @else
                                <div class="avatar avatar-md rounded bg-secondary-lt">
                                    <i class="ti ti-home"></i>
                                </div>
                                @endif
                            </td>
                            <td class="sort-name">
                                <div>
                                    <div class="fw-bold">{{ $unit->name }}</div>
                                    @if($unit->short_description)
                                    <small class="text-secondary">{{ Str::limit($unit->short_description, 60) }}</small>
                                    @endif
                                </div>
                            </td>
                            <td class="sort-specs">
                                <div class="d-flex gap-1 flex-wrap">
                                    @if($unit->bedrooms)
                                    <span class="badge bg-blue-lt">
                                        <i class="ti ti-bed me-1"></i>{{ $unit->bedrooms }}
                                    </span>
                                    @endif
                                    @if($unit->bathrooms)
                                    <span class="badge bg-green-lt">
                                        <i class="ti ti-bath me-1"></i>{{ $unit->bathrooms }}
                                    </span>
                                    @endif
                                    @if($unit->carports)
                                    <span class="badge bg-orange-lt">
                                        <i class="ti ti-car me-1"></i>{{ $unit->carports }}
                                    </span>
                                    @endif
                                </div>
                                @if($unit->land_area || $unit->building_area)
                                <div class="mt-1">
                                    @if($unit->land_area)
                                    <small class="text-secondary">Land: {{ $unit->land_area }}</small>
                                    @endif
                                    @if($unit->building_area)
                                    <small class="text-secondary">{{ $unit->land_area ? ' | ' : '' }}Building: {{ $unit->building_area }}</small>
                                    @endif
                                </div>
                                @endif
                            </td>
                            <td class="sort-order">{{ $unit->order }}</td>
                            <td class="sort-status">
                                <span class="badge badge-sm bg-{{ $unit->is_active ? 'green' : 'red' }}-lt">
                                    {{ $unit->status_text }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap action-buttons">
                                    <a href="{{ route('development.unit.edit', [$project, $unit]) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Edit Unit">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                            data-id="{{ $unit->id }}"
                                            data-name="{{ $unit->name }}"
                                            data-url="{{ route('development.unit.destroy', [$project, $unit]) }}"
                                            title="Delete Unit">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="empty-row">
                            <td colspan="7" class="text-center py-5">
                                <div class="empty">
                                    <div class="empty-icon">
                                        <i class="ti ti-home icon icon-lg"></i>
                                    </div>
                                    <p class="empty-title h3">No units found</p>
                                    <p class="empty-subtitle text-secondary">
                                        Get started by creating your first unit for this project.
                                    </p>
                                    <div class="empty-action">
                                        <a href="{{ route('development.unit.create', $project) }}" class="btn btn-primary">
                                            <i class="ti ti-plus"></i> Add your first unit
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
        @if($units->count() > 0)
        <div class="card-footer d-flex align-items-center">
            <p class="m-0 text-secondary">
                Showing {{ $units->count() }} unit{{ $units->count() > 1 ? 's' : '' }} for {{ $project->name }}
            </p>
        </div>
        @endif
    </div>
</div>

{{-- Include Global Delete Modal --}}
@include('components.delete-modal')

@endsection

@push('scripts')
@include('components.toast')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let sortable = null;
        let isReorderMode = false;
        let originalData = [];
        
        // Initialize List.js for table sorting and search
        const list = new List("table-default", {
            sortClass: "table-sort",
            listClass: "table-tbody",
            valueNames: [
                "sort-name",
                "sort-specs", 
                "sort-order",
                "sort-status",
            ],
        });

        // Store original data for search reset
        originalData = Array.from(document.querySelectorAll('.sortable-row')).map(row => ({
            element: row,
            visible: true
        }));

        // Search functionality
        const searchInput = document.getElementById('search-input');
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase().trim();
            
            if (searchTerm === '') {
                // Show all rows
                originalData.forEach(item => {
                    item.element.style.display = '';
                    item.visible = true;
                    // Remove highlight
                    item.element.querySelectorAll('.search-highlight').forEach(el => {
                        el.outerHTML = el.innerHTML;
                    });
                });
                toggleEmptyState();
                return;
            }

            let visibleCount = 0;
            originalData.forEach(item => {
                const unitName = item.element.querySelector('.sort-name .fw-bold').textContent.toLowerCase();
                const unitDesc = item.element.querySelector('.sort-name small')?.textContent.toLowerCase() || '';
                const landArea = item.element.querySelector('.sort-specs small')?.textContent.toLowerCase() || '';
                
                if (unitName.includes(searchTerm) || unitDesc.includes(searchTerm) || landArea.includes(searchTerm)) {
                    item.element.style.display = '';
                    item.visible = true;
                    visibleCount++;
                    
                    // Highlight search term
                    highlightSearchTerm(item.element, searchTerm);
                } else {
                    item.element.style.display = 'none';
                    item.visible = false;
                }
            });
            
            toggleEmptyState();
        });

        function highlightSearchTerm(row, term) {
            // Remove existing highlights
            row.querySelectorAll('.search-highlight').forEach(el => {
                el.outerHTML = el.innerHTML;
            });
            
            // Add new highlights to unit name
            const nameCell = row.querySelector('.sort-name .fw-bold');
            if (nameCell && nameCell.textContent.toLowerCase().includes(term)) {
                const regex = new RegExp(`(${term})`, 'gi');
                nameCell.innerHTML = nameCell.textContent.replace(regex, '<span class="search-highlight">$1</span>');
            }

            // Add highlights to short description
            const descCell = row.querySelector('.sort-name small');
            if (descCell && descCell.textContent.toLowerCase().includes(term)) {
                const regex = new RegExp(`(${term})`, 'gi');
                descCell.innerHTML = descCell.textContent.replace(regex, '<span class="search-highlight">$1</span>');
            }

            // Add highlights to specifications text (land area, building area)
            const specsTexts = row.querySelectorAll('.sort-specs small');
            specsTexts.forEach(specText => {
                if (specText.textContent.toLowerCase().includes(term)) {
                    const regex = new RegExp(`(${term})`, 'gi');
                    specText.innerHTML = specText.textContent.replace(regex, '<span class="search-highlight">$1</span>');
                }
            });
        }

        function toggleEmptyState() {
            const visibleRows = originalData.filter(item => item.visible).length;
            const emptyRow = document.getElementById('empty-row');
            
            if (visibleRows === 0 && originalData.length > 0) {
                if (!emptyRow) {
                    const tbody = document.querySelector('#sortable-table');
                    const emptyRowHtml = `
                        <tr id="empty-row">
                            <td colspan="7" class="text-center py-4">
                                <div class="empty">
                                    <div class="empty-icon">
                                        <i class="ti ti-search icon icon-lg"></i>
                                    </div>
                                    <p class="empty-title">No units found</p>
                                    <p class="empty-subtitle text-secondary">
                                        Try adjusting your search terms.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', emptyRowHtml);
                }
            } else if (emptyRow && visibleRows > 0) {
                emptyRow.remove();
            }
        }

        // Toggle Reorder Mode
        const toggleReorderBtn = document.getElementById('toggle-reorder');
        const table = document.querySelector('.table');
        const reorderHandles = document.querySelectorAll('.reorder-handle');
        const reorderHeader = document.querySelector('.reorder-handle-header');

        toggleReorderBtn.addEventListener('click', function() {
            isReorderMode = !isReorderMode;
            
            if (isReorderMode) {
                enableReorderMode();
            } else {
                disableReorderMode();
            }
        });

        function enableReorderMode() {
            // Clear search and show all rows
            searchInput.value = '';
            originalData.forEach(item => {
                item.element.style.display = '';
                item.visible = true;
                // Remove highlights
                item.element.querySelectorAll('.search-highlight').forEach(el => {
                    el.outerHTML = el.innerHTML;
                });
            });
            
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
            searchInput.placeholder = 'Search units...';
            
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
            fetch('{{ route('development.unit.reorder', $project) }}', {
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
                setTimeout(() => location.reload(), 1000);
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
                        Drag the <i class="ti ti-grip-vertical"></i> handle to reorder units. Search and other functions are disabled during reorder mode.
                    </div>
                </div>
            `;
            
            // Insert after the actions bar
            const actionsBar = document.querySelector('.col-12.mb-3:last-of-type');
            actionsBar.insertAdjacentElement('afterend', instruction);
        }

        function hideReorderInstructions() {
            const instruction = document.getElementById('reorder-instruction');
            if (instruction) {
                instruction.remove();
            }
        }
    });
</script>
@endpush