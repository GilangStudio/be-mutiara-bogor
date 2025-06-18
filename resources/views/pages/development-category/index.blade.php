@extends('layouts.main')

@section('title', 'Development Category')

@section('header')
<h2 class="page-title">Development Category</h2>
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

<div class="col-12">
    <div class="d-flex justify-content-between align-items-center gap-2">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-category">
            <i class="ti ti-plus me-1"></i> Add Category
        </button>
        <button type="button" class="btn btn-6 btn-outline-secondary w-100 btn-icon" id="toggle-reorder">
            <i class="ti ti-arrows-sort"></i>
        </button>
    </div>
</div>

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
                            <th><button class="table-sort" data-sort="sort-no">No</button></th>
                            <th><button class="table-sort" data-sort="sort-name">Category Name</button></th>
                            <th><button class="table-sort" data-sort="sort-order">Order</button></th>
                            <th><button class="table-sort" data-sort="sort-status">Status</button></th>
                            <th width="150"></th>
                        </tr>
                    </thead>
                    <tbody class="table-tbody" id="sortable-table">
                        @forelse($categories as $index => $category)
                        <tr data-id="{{ $category->id }}" class="sortable-row">
                            <td class="reorder-handle" style="display: none;">
                                <div class="cursor-move text-secondary">
                                    <i class="ti ti-grip-vertical"></i>
                                </div>
                            </td>
                            <td class="sort-no">{{ $index + 1 }}</td>
                            <td class="sort-name">{{ $category->name }}</td>
                            <td class="sort-order">{{ $category->order }}</td>
                            <td class="sort-status">
                                <span class="badge badge-sm bg-{{ $category->is_active ? 'green' : 'red' }}-lt">
                                    {{ $category->status_text }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap action-buttons">
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#edit-category"
                                            data-id="{{ $category->id }}"
                                            data-name="{{ $category->name }}"
                                            data-status="{{ $category->is_active }}">
                                        <i class="ti ti-edit"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                            data-id="{{ $category->id }}"
                                            data-name="{{ $category->name }}"
                                            data-url="{{ route('development.category.destroy', $category) }}">
                                        <i class="ti ti-trash"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="empty">
                                    <div class="empty-icon">
                                        <i class="ti ti-folder-off icon icon-lg"></i>
                                    </div>
                                    <p class="empty-title h3">No projects found</p>
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

{{-- Modal Add Category --}}
<div class="modal modal-blur fade" id="add-category" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form class="modal-content" action="{{ route('development.category.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Category Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" autocomplete="off" required />
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-check form-switch form-switch-3">
                            <input class="form-check-input" type="checkbox" name="status" value="1" 
                                   {{ old('status', true) ? 'checked' : '' }}>
                            <span class="form-check-label">Active</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary ms-auto">Create</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Category --}}
<div class="modal modal-blur fade" id="edit-category" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form class="modal-content" id="edit-form" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Category Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit-name" name="name" autocomplete="off" required />
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-check form-switch form-switch-3">
                            <input class="form-check-input" type="checkbox" id="edit-status" name="status" value="1">
                            <span class="form-check-label">Active</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary ms-auto">Update</button>
            </div>
        </form>
    </div>
</div>

{{-- Include Global Delete Modal --}}
@include('components.delete-modal')

@endsection

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
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .reorder-mode .table-sort {
        pointer-events: none;
        opacity: 0.5;
    }
    
    .reorder-mode .action-buttons {
        pointer-events: none;
        opacity: 0.5;
    }
    
    .reorder-active {
        border: 2px dashed #0054a6;
        background: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
@include('components.toast')
{{-- Include SortableJS CDN --}}
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let sortable = null;
        let isReorderMode = false;
        
        // Initialize List.js for table sorting
        const list = new List("table-default", {
            sortClass: "table-sort",
            listClass: "table-tbody",
            valueNames: [
                "sort-no",
                "sort-name",
                "sort-order",
                "sort-status",
            ],
        });

        // Toggle Reorder Mode
        const toggleReorderBtn = document.getElementById('toggle-reorder');
        // const reorderText = document.getElementById('reorder-text');
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
            // Show reorder handles
            reorderHandles.forEach(handle => handle.style.display = 'table-cell');
            reorderHeader.style.display = 'table-cell';
            
            // Add reorder mode class
            table.classList.add('reorder-mode', 'reorder-active');
            
            // Update button
            toggleReorderBtn.classList.remove('btn-outline-secondary');
            toggleReorderBtn.classList.add('btn-warning');
            // reorderText.textContent = 'Disable Reorder';
            toggleReorderBtn.querySelector('i').className = 'ti ti-x';
            
            // Initialize sortable
            const sortableTable = document.getElementById('sortable-table');
            sortable = new Sortable(sortableTable, {
                handle: '.reorder-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onEnd: function(evt) {
                    updateOrder();
                }
            });
            
            // Show reorder instructions
            showReorderInstructions();
        }

        function disableReorderMode() {
            // Hide reorder handles
            reorderHandles.forEach(handle => handle.style.display = 'none');
            reorderHeader.style.display = 'none';
            
            // Remove reorder mode class
            table.classList.remove('reorder-mode', 'reorder-active');
            
            // Update button
            toggleReorderBtn.classList.remove('btn-warning');
            toggleReorderBtn.classList.add('btn-outline-secondary');
            // reorderText.textContent = 'Enable Reorder';
            toggleReorderBtn.querySelector('i').className = 'ti ti-arrows-sort';
            
            // Destroy sortable
            if (sortable) {
                sortable.destroy();
                sortable = null;
            }
            
            // Hide any instructions
            hideReorderInstructions();
        }

        function updateOrder() {
            const rows = document.querySelectorAll('.sortable-row');
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
                
                // Update row number
                const noCell = row.querySelector('.sort-no');
                noCell.textContent = index + 1;
            });
            
            // Send AJAX request to update order
            fetch('{{ route('development.category.reorder') }}', {
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
                    // Reload page to reset order
                    setTimeout(() => location.reload(), 1000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to update order', 'error');
                // Reload page to reset order
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
                        Drag the <i class="ti ti-grip-vertical"></i> handle to reorder categories. Other functions are disabled during reorder mode.
                    </div>
                </div>
            `;
            
            // Insert after the button row
            const buttonRow = document.querySelector('.col-12');
            buttonRow.parentNode.insertBefore(instruction, buttonRow.nextSibling);
        }

        function hideReorderInstructions() {
            const instruction = document.getElementById('reorder-instruction');
            if (instruction) {
                instruction.remove();
            }
        }

        // Handle Edit Modal
        const editModal = document.getElementById('edit-category');
        editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const status = button.getAttribute('data-status') === '1';

            // Update form action
            const form = document.getElementById('edit-form');
            form.action = `{{ url('development/category') }}/${id}`;

            // Fill form fields
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-status').checked = status;
        });

        // Reset Add Modal when opened
        const addModal = document.getElementById('add-category');
        addModal.addEventListener('show.bs.modal', function (event) {
            const form = addModal.querySelector('form');
            form.reset();
            // Set default status to checked
            form.querySelector('input[name="status"]').checked = true;
        });
    });
</script>
@endpush