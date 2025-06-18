@extends('layouts.main')

@section('title', 'Development Projects')

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
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .reorder-mode .table tbody tr:hover {
        background-color: transparent;
    }
    
    .btn-list .btn {
        margin-right: 0.25rem;
    }
    
    .btn-list .btn:last-child {
        margin-right: 0;
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
    
    .search-highlight {
        background-color: #fff3cd;
        padding: 0.1rem 0.2rem;
        border-radius: 0.25rem;
    }
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h2 class="page-title">Development Projects</h2>
    <a href="{{ route('development.project.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Add Project
    </a>
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
            <i class="ti ti-exclamation-circle fs-2"></i>
        </div>
        <div>{{ session('error') }}</div>
    </div>
    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
</div>
@endif

{{-- Actions Bar --}}
<div class="col-12 mb-3">
    <div class="d-flex justify-content-between align-items-center gap-2">
        <div class="input-icon" style="max-width: 300px;">
            <span class="input-icon-addon">
                <i class="ti ti-search"></i>
            </span>
            <input type="text" class="form-control" placeholder="Search projects..." id="search-input">
        </div>
        <div>
            <button type="button" class="btn btn-6 btn-outline-secondary w-100 btn-icon" id="toggle-reorder">
                <i class="ti ti-arrows-sort"></i>
            </button>
        </div>
    </div>
</div>

{{-- Projects Table --}}
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
                            <th><button class="table-sort" data-sort="sort-name">Project Name</button></th>
                            <th><button class="table-sort" data-sort="sort-category">Category</button></th>
                            <th><button class="table-sort" data-sort="sort-order">Order</button></th>
                            <th><button class="table-sort" data-sort="sort-status">Status</button></th>
                            <th width="200">Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-tbody" id="sortable-table">
                        @forelse($projects as $index => $project)
                        <tr data-id="{{ $project->id }}" class="sortable-row">
                            <td class="reorder-handle" style="display: none;">
                                <div class="cursor-move text-secondary">
                                    <i class="ti ti-grip-vertical"></i>
                                </div>
                            </td>
                            <td>
                                @if($project->main_image_url)
                                <div class="avatar avatar-md rounded" 
                                     style="background-image: url('{{ $project->main_image_url }}'); background-size: cover; background-position: center;"></div>
                                @else
                                <div class="avatar avatar-md rounded bg-secondary-lt">
                                    <i class="ti ti-photo"></i>
                                </div>
                                @endif
                            </td>
                            <td class="sort-name">
                                <div>
                                    <div class="fw-bold">{{ $project->name }}</div>
                                    @if($project->short_description)
                                    <small class="text-secondary">{{ Str::limit($project->short_description, 60) }}</small>
                                    @endif
                                    <div class="mt-1">
                                        <span class="badge bg-green-lt">
                                            <i class="ti ti-home me-1"></i>{{ $project->units_count ?? 0 }} Unit{{ ($project->units_count ?? 0) != 1 ? 's' : '' }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="sort-category">
                                <span class="badge bg-blue-lt">{{ $project->category->name }}</span>
                            </td>
                            <td class="sort-order">{{ $project->order }}</td>
                            <td class="sort-status">
                                <span class="badge badge-sm bg-{{ $project->is_active ? 'green' : 'red' }}-lt">
                                    {{ $project->status_text }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap action-buttons">
                                    <a href="{{ route('development.project.show', $project) }}" 
                                       class="btn btn-sm btn-outline-info" title="View Project">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    <a href="{{ route('development.unit.index', $project) }}" 
                                       class="btn btn-sm btn-outline-success" title="Manage Units">
                                        <i class="ti ti-home"></i>
                                    </a>
                                    <a href="{{ route('development.project.edit', $project) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Edit Project">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                            data-id="{{ $project->id }}"
                                            data-name="{{ $project->name }}"
                                            data-url="{{ route('development.project.destroy', $project) }}"
                                            title="Delete Project">
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
                                        <i class="ti ti-building-skyscraper icon icon-lg"></i>
                                    </div>
                                    <p class="empty-title h3">No projects found</p>
                                    <p class="empty-subtitle text-secondary">
                                        Get started by creating your first development project.<br>
                                        You can then add units to each project for better management.
                                    </p>
                                    <div class="empty-action">
                                        <a href="{{ route('development.project.create') }}" class="btn btn-primary">
                                            <i class="ti ti-plus"></i> Add your first project
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
        @if($projects->count() > 0)
        <div class="card-footer d-flex align-items-center">
            <p class="m-0 text-secondary">
                Showing {{ $projects->count() }} project{{ $projects->count() > 1 ? 's' : '' }}
            </p>
            {{-- <div class="ms-auto">
                <span class="text-secondary">
                    <i class="ti ti-info-circle me-1"></i>
                    Drag rows to reorder when reorder mode is enabled
                </span>
            </div> --}}
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
                "sort-category", 
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
                const projectName = item.element.querySelector('.sort-name').textContent.toLowerCase();
                const categoryName = item.element.querySelector('.sort-category').textContent.toLowerCase();
                
                if (projectName.includes(searchTerm) || categoryName.includes(searchTerm)) {
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
            
            // Add new highlights
            const nameCell = row.querySelector('.sort-name .fw-bold');
            const categoryCell = row.querySelector('.sort-category .badge');
            
            [nameCell, categoryCell].forEach(cell => {
                if (cell && cell.textContent.toLowerCase().includes(term)) {
                    const regex = new RegExp(`(${term})`, 'gi');
                    cell.innerHTML = cell.textContent.replace(regex, '<span class="search-highlight">$1</span>');
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
                                    <p class="empty-title">No projects found</p>
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
            searchInput.placeholder = 'Search projects...';
            
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
            fetch('{{ route('development.project.reorder') }}', {
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
                        Drag the <i class="ti ti-grip-vertical"></i> handle to reorder projects. Search and other functions are disabled during reorder mode.
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
    });
</script>
@endpush