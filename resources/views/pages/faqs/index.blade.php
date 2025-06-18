@extends('layouts.main')

@section('title', 'FAQ')

@section('header')
<h2 class="page-title">FAQ (Frequently Asked Questions)</h2>
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
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-faq">
            <i class="ti ti-plus me-1"></i> Add FAQ
        </button>
        <button type="button" class="btn btn-6 btn-outline-secondary w-100 btn-icon" id="toggle-reorder">
            <i class="ti ti-arrows-sort"></i>
        </button>
    </div>
</div>

<div class="col-12">
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="50" class="reorder-handle-header" style="display: none;">
                                <i class="ti ti-arrows-sort"></i>
                            </th>
                            <th width="50">No</th>
                            <th>Question</th>
                            <th width="120">Category</th>
                            <th width="80">Order</th>
                            <th width="80">Status</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-table">
                        @forelse($faqs as $index => $faq)
                        <tr data-id="{{ $faq->id }}" class="sortable-row">
                            <td class="reorder-handle" style="display: none;">
                                <div class="cursor-move text-secondary">
                                    <i class="ti ti-grip-vertical"></i>
                                </div>
                            </td>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold">{{ $faq->question }}</div>
                                <div class="text-secondary small">
                                    {{ Str::limit(strip_tags($faq->answer), 100) }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $faq->category_badge_color }}-lt">
                                    {{ ucfirst($faq->category) }}
                                </span>
                            </td>
                            <td>{{ $faq->order }}</td>
                            <td>
                                <span class="badge badge-sm bg-{{ $faq->is_active ? 'green' : 'red' }}-lt">
                                    {{ $faq->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap action-buttons">
                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#view-faq"
                                            data-id="{{ $faq->id }}"
                                            data-question="{{ $faq->question }}"
                                            data-answer="{{ $faq->answer }}"
                                            data-category="{{ $faq->category }}">
                                        <i class="ti ti-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#edit-faq"
                                            data-id="{{ $faq->id }}"
                                            data-question="{{ $faq->question }}"
                                            data-answer="{{ $faq->answer }}"
                                            data-category="{{ $faq->category }}"
                                            data-status="{{ $faq->is_active }}">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                            data-id="{{ $faq->id }}"
                                            data-name="{{ $faq->question }}"
                                            data-url="{{ route('faqs.destroy', $faq) }}">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="empty">
                                    <div class="empty-icon">
                                        <i class="ti ti-help-circle icon icon-lg"></i>
                                    </div>
                                    <p class="empty-title h3">No FAQs yet</p>
                                    <p class="empty-subtitle text-secondary">
                                        Add your first FAQ to help users
                                    </p>
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

{{-- Modal Add FAQ --}}
<div class="modal modal-blur fade" id="add-faq" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form class="modal-content" action="{{ route('faqs.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add New FAQ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Question <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('question') is-invalid @enderror" 
                                   name="question" value="{{ old('question') }}" required />
                            @error('question')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select @error('category') is-invalid @enderror" name="category" required>
                                <option value="">Select Category</option>
                                <option value="general" {{ old('category') == 'general' ? 'selected' : '' }}>General</option>
                                {{-- <option value="project" {{ old('category') == 'project' ? 'selected' : '' }}>Project</option>
                                <option value="payment" {{ old('category') == 'payment' ? 'selected' : '' }}>Payment</option>
                                <option value="technical" {{ old('category') == 'technical' ? 'selected' : '' }}>Technical</option> --}}
                                <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Answer <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('answer') is-invalid @enderror" 
                                      name="answer" rows="5" required>{{ old('answer') }}</textarea>
                            @error('answer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-hint">Provide a clear and complete answer</small>
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
                <button type="submit" class="btn btn-primary ms-auto">Save</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit FAQ --}}
<div class="modal modal-blur fade" id="edit-faq" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form class="modal-content" id="edit-form" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit FAQ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Question <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit-question" name="question" required />
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit-category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="general">General</option>
                                {{-- <option value="project">Project</option>
                                <option value="payment">Payment</option>
                                <option value="technical">Technical</option> --}}
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Answer <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit-answer" name="answer" rows="5" required></textarea>
                            <small class="form-hint">Provide a clear and complete answer</small>
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

{{-- Modal View FAQ --}}
<div class="modal modal-blur fade" id="view-faq" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">FAQ Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Question:</label>
                            <div class="card">
                                <div class="card-body" id="view-question"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Category:</label>
                            <div id="view-category-badge"></div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Answer:</label>
                            <div class="card">
                                <div class="card-body" id="view-answer"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
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
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let sortable = null;
        let isReorderMode = false;
        
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
                
                // Update nomor urut di tabel secara real-time
                const noCell = row.querySelector('td:nth-child(2)'); // Kolom No (kolom ke-2, karena handle reorder adalah kolom ke-1 tapi hidden)
                const orderCell = row.querySelector('td:nth-child(5)'); // Kolom Urutan (kolom ke-5)
                
                if (noCell) noCell.textContent = index + 1;
                if (orderCell) orderCell.textContent = index + 1;
            });
            
            // Send AJAX request to update order
            fetch('{{ route('faqs.reorder') }}', {
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
                        Drag the <i class="ti ti-grip-vertical"></i> handle to reorder FAQs. Other functions are disabled during reorder mode.
                    </div>
                </div>
            `;
            
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
        const editModal = document.getElementById('edit-faq');
        editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const question = button.getAttribute('data-question');
            const answer = button.getAttribute('data-answer');
            const category = button.getAttribute('data-category');
            const status = button.getAttribute('data-status') === '1';

            // Update form action
            const form = document.getElementById('edit-form');
            form.action = `{{ url('faqs') }}/${id}`;

            // Fill form fields
            document.getElementById('edit-question').value = question;
            document.getElementById('edit-answer').value = answer;
            document.getElementById('edit-category').value = category;
            document.getElementById('edit-status').checked = status;
        });

        // Handle View Modal
        const viewModal = document.getElementById('view-faq');
        viewModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const question = button.getAttribute('data-question');
            const answer = button.getAttribute('data-answer');
            const category = button.getAttribute('data-category');

            // Fill view fields
            document.getElementById('view-question').textContent = question;
            document.getElementById('view-answer').innerHTML = answer.replace(/\n/g, '<br>');
            
            // Set category badge
            const categoryColors = {
                'general': 'blue',
                'project': 'green', 
                'payment': 'yellow',
                'technical': 'red',
                'other': 'gray'
            };
            const color = categoryColors[category] || 'gray';
            document.getElementById('view-category-badge').innerHTML = 
                `<span class="badge bg-${color}-lt">${category.charAt(0).toUpperCase() + category.slice(1)}</span>`;
        });

        // Reset Add Modal when opened
        const addModal = document.getElementById('add-faq');
        addModal.addEventListener('show.bs.modal', function (event) {
            const form = addModal.querySelector('form');
            form.reset();
            form.querySelector('input[name="status"]').checked = true;
        });
    });
</script>
@endpush