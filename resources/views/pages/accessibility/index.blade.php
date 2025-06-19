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
    <a href="{{ route('accessibilities.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Add Accessibility
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
            <i class="ti ti-exclamation-circle icon alert-icon me-2"></i>
        </div>
        <div>{{ session('error') }}</div>
    </div>
    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
</div>
@endif

{{-- Actions Bar --}}
<div class="col-12 mb-3">
    <div class="d-flex justify-content-between align-items-center gap-2">
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

{{-- Include Global Delete Modal --}}
@include('components.delete-modal')

@endsection

@push('scripts')
@include('components.toast')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let sortable = null;
        let isReorderMode = false;
        
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
        
        // Toggle Reorder Mode
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
                // setTimeout(() => location.reload(), 1000);
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
        
        // Focus search input on Ctrl+K or Cmd+K
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
            }
        });
    });
</script>
@endpush