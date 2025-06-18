@extends('layouts.main')

@section('title', 'Contact Messages')

@push('styles')
<style>
    .table tbody tr:hover {
        background-color: #f8f9fa;
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
    
    /* Search highlighting */
    mark.search-highlight {
        background-color: #fff3cd;
        padding: 0.1rem 0.2rem;
        border-radius: 0.25rem;
        font-weight: 600;
    }
    
    /* Custom Pagination Styles */
    .pagination {
        margin-bottom: 0;
    }
    
    .page-link {
        color: #6c757d;
        background-color: #fff;
        border: 1px solid #dee2e6;
        padding: 0.375rem 0.75rem;
        margin: 0 0.125rem;
        border-radius: 0.375rem;
        text-decoration: none;
        transition: all 0.15s ease-in-out;
    }
    
    .page-link:hover {
        color: #0054a6;
        background-color: #f8f9fa;
        border-color: #0054a6;
    }
    
    .page-item.active .page-link {
        color: #fff;
        background-color: #0054a6;
        border-color: #0054a6;
    }
    
    .page-item.disabled .page-link {
        color: #adb5bd;
        background-color: #fff;
        border-color: #dee2e6;
        cursor: not-allowed;
    }
    
    .pagination-sm .page-link {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    /* Loading state */
    .loading-overlay {
        opacity: 0.6;
        pointer-events: none;
    }
    
    /* Filter indicators */
    .filter-badge {
        font-size: 0.75rem;
    }

    /* Message status styling */
    .message-unread {
        background-color: #fff5f5;
        border-left: 4px solid #dc3545;
    }

    .message-read {
        background-color: #fffbf0;
        border-left: 4px solid #ffc107;
    }

    .message-replied {
        background-color: #f0f9ff;
        border-left: 4px solid #28a745;
    }

    /* Bulk actions */
    .bulk-actions {
        background-color: #e3f2fd;
        border: 1px solid #bbdefb;
        border-radius: 0.375rem;
        padding: 0.75rem;
        margin-bottom: 1rem;
        display: none;
    }

    .bulk-actions.show {
        display: block;
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
    <h2 class="page-title">Contact Messages</h2>
    <div class="btn-list">
        <form action="{{ route('contact-messages.mark-all-read') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-success" 
                    onclick="return confirm('Mark all messages as read?')">
                <i class="ti ti-check-all me-1"></i> Mark All Read
            </button>
        </form>
    </div>
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

{{-- Statistics Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Unread Messages</div>
                    <div class="ms-auto lh-1">
                        <div class="dropdown">
                            <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Today</a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item active" href="#">Today</a>
                                <a class="dropdown-item" href="#">This Week</a>
                                <a class="dropdown-item" href="#">This Month</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="h1 mb-3">{{ \App\Models\ContactMessage::getUnreadCount() }}</div>
                <div class="d-flex mb-2">
                    <div>Needs attention</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Today's Messages</div>
                </div>
                <div class="h1 mb-3">{{ \App\Models\ContactMessage::getTodayCount() }}</div>
                <div class="d-flex mb-2">
                    <div>Received today</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">This Week</div>
                </div>
                <div class="h1 mb-3">{{ \App\Models\ContactMessage::getThisWeekCount() }}</div>
                <div class="d-flex mb-2">
                    <div>This week's total</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">This Month</div>
                </div>
                <div class="h1 mb-3">{{ \App\Models\ContactMessage::getThisMonthCount() }}</div>
                <div class="d-flex mb-2">
                    <div>Monthly total</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Search and Filter Form --}}
<div class="col-12 mb-3">
    <form method="GET" action="{{ route('contact-messages.index') }}" class="w-100" id="filter-form">
        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text" 
                                   class="form-control" 
                                   name="search" 
                                   placeholder="Search in name, email, phone, or message..." 
                                   value="{{ request('search') }}" 
                                   autocomplete="off" 
                                   id="search-input">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="status" id="status-filter">
                            <option value="">All Status</option>
                            <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Unread</option>
                            <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read</option>
                            <option value="replied" {{ request('status') === 'replied' ? 'selected' : '' }}>Replied</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" name="date_from" 
                               value="{{ request('date_from') }}" placeholder="From Date">
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" name="date_to" 
                               value="{{ request('date_to') }}" placeholder="To Date">
                    </div>
                    <div class="col-md-2">
                        <div class="btn-list">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-search me-1"></i> Filter
                            </button>
                            @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                            <a href="{{ route('contact-messages.index') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-x"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                
                {{-- Active Filters Display --}}
                @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                <div class="mt-2 d-flex gap-2 align-items-center flex-wrap">
                    <small class="text-secondary">Active filters:</small>
                    @if(request('search'))
                    <span class="badge bg-blue-lt filter-badge">
                        <i class="ti ti-search me-1"></i>
                        Search: "{{ request('search') }}"
                    </span>
                    @endif
                    @if(request('status'))
                    <span class="badge bg-green-lt filter-badge">
                        <i class="ti ti-filter me-1"></i>
                        Status: {{ ucfirst(request('status')) }}
                    </span>
                    @endif
                    @if(request('date_from'))
                    <span class="badge bg-yellow-lt filter-badge">
                        <i class="ti ti-calendar me-1"></i>
                        From: {{ request('date_from') }}
                    </span>
                    @endif
                    @if(request('date_to'))
                    <span class="badge bg-yellow-lt filter-badge">
                        <i class="ti ti-calendar me-1"></i>
                        To: {{ request('date_to') }}
                    </span>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </form>
</div>

{{-- Bulk Actions Bar --}}
<div class="col-12">
    <div class="bulk-actions w-100" id="bulk-actions">
        <form action="{{ route('contact-messages.bulk-action') }}" method="POST" id="bulk-form">
            @csrf
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-3">
                        <span class="fw-bold">
                            <span id="selected-count">0</span> message(s) selected
                        </span>
                        <div class="btn-list">
                            <button type="submit" name="action" value="mark_read" class="btn btn-sm btn-outline-success">
                                <i class="ti ti-check me-1"></i> Mark as Read
                            </button>
                            <button type="submit" name="action" value="mark_unread" class="btn btn-sm btn-outline-warning">
                                <i class="ti ti-mail me-1"></i> Mark as Unread
                            </button>
                            <button type="submit" name="action" value="mark_replied" class="btn btn-sm btn-outline-info">
                                <i class="ti ti-message-check me-1"></i> Mark as Replied
                            </button>
                            <button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Are you sure you want to delete selected messages?')">
                                <i class="ti ti-trash me-1"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">
                        <i class="ti ti-x me-1"></i> Clear Selection
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Messages Table --}}
<div class="col-12">
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive" id="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" class="form-check-input" id="select-all">
                            </th>
                            <th width="80">Status</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Subject/Message</th>
                            <th width="150">Date</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($messages as $message)
                        <tr class="message-{{ $message->status }}" data-searchable>
                            <td>
                                <input type="checkbox" class="form-check-input message-checkbox" 
                                       name="message_ids[]" value="{{ $message->id }}">
                            </td>
                            <td>
                                <span class="badge bg-{{ $message->status_badge_color }}-lt">
                                    @if($message->status === 'unread')
                                    <i class="ti ti-mail me-1"></i>
                                    @elseif($message->status === 'read')
                                    <i class="ti ti-mail-opened me-1"></i>
                                    @else
                                    <i class="ti ti-message-check me-1"></i>
                                    @endif
                                    {{ $message->status_text }}
                                </span>
                            </td>
                            <td data-searchable="from">
                                <div>
                                    <div class="fw-bold">{{ $message->name }}</div>
                                    @if($message->email)
                                    <div class="text-secondary small">
                                        <a href="mailto:{{ $message->email }}" class="text-decoration-none">
                                            <i class="ti ti-mail me-1"></i>{{ $message->email }}
                                        </a>
                                    </div>
                                    @endif
                                    @if($message->phone)
                                    <div class="text-secondary small">
                                        <a href="{{ $message->whatsapp_url }}" target="_blank" class="text-decoration-none">
                                            <i class="ti ti-phone me-1"></i>{{ $message->phone }}
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td data-searchable="to">
                                @if($message->to)
                                <span class="badge bg-blue-lt">{{ $message->to }}</span>
                                @else
                                <span class="text-secondary">General</span>
                                @endif
                            </td>
                            <td data-searchable="message">
                                <div>
                                    <div class="text-dark">{{ Str::limit($message->message, 80) }}</div>
                                    @if($message->reply)
                                    <div class="mt-1">
                                        <small class="text-success">
                                            <i class="ti ti-corner-down-right me-1"></i>
                                            Replied: {{ Str::limit($message->reply, 60) }}
                                        </small>
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="text-dark">{{ $message->created_at->format('d M Y') }}</div>
                                <small class="text-secondary">{{ $message->created_at->format('H:i') }}</small>
                                @if($message->replied_at)
                                <div class="mt-1">
                                    <small class="text-success">
                                        <i class="ti ti-message-check me-1"></i>
                                        {{ $message->replied_at->format('d M') }}
                                    </small>
                                </div>
                                @endif
                            </td>
                            <td>
                                <div class="btn-list">
                                    <a href="{{ route('contact-messages.show', $message) }}" 
                                       class="btn btn-sm btn-outline-primary" title="View Message">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="{{ route('contact-messages.show', $message) }}" class="dropdown-item">
                                                <i class="ti ti-eye me-1"></i> View Details
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('contact-messages.update-status', $message) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="read">
                                                <button type="submit" class="dropdown-item">
                                                    <i class="ti ti-mail-opened me-1"></i> Mark as Read
                                                </button>
                                            </form>
                                            <form action="{{ route('contact-messages.update-status', $message) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="replied">
                                                <button type="submit" class="dropdown-item">
                                                    <i class="ti ti-message-check me-1"></i> Mark as Replied
                                                </button>
                                            </form>
                                            <div class="dropdown-divider"></div>
                                            <button type="button" class="dropdown-item text-danger delete-btn"
                                                    data-id="{{ $message->id }}"
                                                    data-name="{{ $message->name }}"
                                                    data-url="{{ route('contact-messages.destroy', $message) }}">
                                                <i class="ti ti-trash me-1"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty">
                                    <div class="empty-icon">
                                        @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                                        <i class="ti ti-search icon icon-lg"></i>
                                        @else
                                        <i class="ti ti-message icon icon-lg"></i>
                                        @endif
                                    </div>
                                    <p class="empty-title h3">
                                        @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                                        No messages found
                                        @else
                                        No contact messages yet
                                        @endif
                                    </p>
                                    <p class="empty-subtitle text-secondary">
                                        @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                                        Try adjusting your search terms or clear the filters to see all messages.
                                        @else
                                        Contact messages from your website will appear here.<br>
                                        You can manage, reply to, and organize all customer inquiries.
                                        @endif
                                    </p>
                                    <div class="empty-action">
                                        @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                                        <a href="{{ route('contact-messages.index') }}" class="btn btn-outline-secondary">
                                            <i class="ti ti-x me-1"></i> Clear Filters
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Footer with Results Info and Pagination --}}
        @if($messages->total() > 0 || request()->hasAny(['search', 'status', 'date_from', 'date_to']))
        <div class="card-footer d-flex align-items-center">
            <div class="text-secondary">
                @if($messages->total() > 0)
                    Showing <strong>{{ $messages->firstItem() }}</strong> to <strong>{{ $messages->lastItem() }}</strong> 
                    of <strong>{{ $messages->total() }}</strong> messages
                    @if(request('search'))
                        for "<strong>{{ request('search') }}</strong>"
                    @endif
                @else
                    No messages found
                    @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                        with current filters
                    @endif
                @endif
            </div>
            
            @include('components.pagination', ['paginator' => $messages])
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
        // Global selection state
        let globalSelectedIds = new Set();
        let isGlobalSelectMode = false;
        let totalRecords = {{ $messages->total() }};
        let currentPageIds = [];
        let allMessageIds = new Set(); // Store all message IDs for true global selection
        
        // Get current page message IDs
        document.querySelectorAll('.message-checkbox').forEach(checkbox => {
            currentPageIds.push(parseInt(checkbox.value));
        });

        // Checkbox functionality
        const selectAll = document.getElementById('select-all');
        const messageCheckboxes = document.querySelectorAll('.message-checkbox');
        const bulkActions = document.getElementById('bulk-actions');
        const selectedCount = document.getElementById('selected-count');
        const bulkForm = document.getElementById('bulk-form');

        // Load saved selection from localStorage (persistent across sessions)
        loadSavedSelection();

        // Select all functionality - enhanced for true global selection
        selectAll.addEventListener('change', function() {
            if (this.checked) {
                // Check if we should go global or just current page
                if (totalRecords > currentPageIds.length) {
                    // Multi-page scenario - enable global mode
                    enableGlobalSelection();
                } else {
                    // Single page - just select current page
                    selectCurrentPage();
                }
            } else {
                // Deselect all
                clearAllSelection();
            }
            
            updateBulkActions();
            saveSelectionState();
        });

        // Individual checkbox functionality - PERBAIKAN UTAMA
        messageCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const messageId = parseInt(this.value);
                
                if (this.checked) {
                    globalSelectedIds.add(messageId);
                } else {
                    globalSelectedIds.delete(messageId);
                    
                    // PERBAIKAN: Jika dalam mode global dan ada yang di-uncheck,
                    // konversi ke mode individual dengan semua ID yang tersisa
                    if (isGlobalSelectMode) {
                        convertGlobalToIndividualSelection();
                    }
                }
                
                updateSelectAllState();
                updateBulkActions();
                saveSelectionState();
            });
        });

        function enableGlobalSelection() {
            isGlobalSelectMode = true;
            
            // Fetch all message IDs from server
            fetchAllMessageIds().then(() => {
                // Add all IDs to selection
                globalSelectedIds = new Set(allMessageIds);
                
                // Check all current page checkboxes
                messageCheckboxes.forEach(checkbox => {
                    checkbox.checked = true;
                });
                
                // Show global selection indicator
                showGlobalSelectionIndicator();
                updateSelectAllState();
                updateBulkActions();
                saveSelectionState();
            });
        }

        function selectCurrentPage() {
            isGlobalSelectMode = false;
            
            // Add current page IDs to selection
            messageCheckboxes.forEach(checkbox => {
                checkbox.checked = true;
                globalSelectedIds.add(parseInt(checkbox.value));
            });
            
            updateSelectAllState();
        }

        // FUNGSI BARU: Konversi dari global ke individual selection
        function convertGlobalToIndividualSelection() {
            console.log('Converting from global to individual selection...');
            
            // Keluar dari mode global
            isGlobalSelectMode = false;
            hideGlobalSelectionIndicator();
            
            // Ambil semua ID kecuali yang baru saja di-uncheck
            // Kita perlu fetch semua ID lagi dan remove yang di-uncheck
            fetchAllMessageIds().then(() => {
                // Mulai dengan semua ID
                const remainingIds = new Set(allMessageIds);
                
                // Remove ID yang di-uncheck di halaman saat ini
                messageCheckboxes.forEach(checkbox => {
                    const messageId = parseInt(checkbox.value);
                    if (!checkbox.checked) {
                        remainingIds.delete(messageId);
                    }
                });
                
                // Update global selection dengan ID yang tersisa
                globalSelectedIds = remainingIds;
                
                // Update UI
                updateSelectAllState();
                updateBulkActions();
                saveSelectionState();
                
                console.log(`Converted to individual selection with ${globalSelectedIds.size} items selected`);
            });
        }

        function exitGlobalMode() {
            isGlobalSelectMode = false;
            hideGlobalSelectionIndicator();
            
            // Keep only currently checked items
            const currentChecked = new Set();
            messageCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    currentChecked.add(parseInt(checkbox.value));
                }
            });
            globalSelectedIds = currentChecked;
        }

        function clearAllSelection() {
            isGlobalSelectMode = false;
            globalSelectedIds.clear();
            allMessageIds.clear();
            
            messageCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            hideGlobalSelectionIndicator();
            updateSelectAllState();
        }

        function updateSelectAllState() {
            const currentPageCheckedCount = Array.from(messageCheckboxes).filter(cb => cb.checked).length;
            const totalOnPage = messageCheckboxes.length;
            
            if (isGlobalSelectMode) {
                selectAll.checked = true;
                selectAll.indeterminate = false;
            } else if (globalSelectedIds.size === 0) {
                selectAll.checked = false;
                selectAll.indeterminate = false;
            } else if (currentPageCheckedCount === totalOnPage && globalSelectedIds.size >= totalOnPage) {
                selectAll.checked = true;
                selectAll.indeterminate = false;
            } else if (currentPageCheckedCount > 0 || globalSelectedIds.size > 0) {
                selectAll.checked = false;
                selectAll.indeterminate = true;
            } else {
                selectAll.checked = false;
                selectAll.indeterminate = false;
            }
        }

        function updateBulkActions() {
            const count = isGlobalSelectMode ? totalRecords : globalSelectedIds.size;
            
            selectedCount.textContent = count;
            
            if (count > 0) {
                bulkActions.classList.add('show');
                updateBulkForm();
            } else {
                bulkActions.classList.remove('show');
            }
        }

        function updateBulkForm() {
            // Clear existing hidden inputs
            const existingInputs = bulkForm.querySelectorAll('input[name="message_ids[]"], input[name="select_all_pages"]');
            existingInputs.forEach(input => input.remove());
            
            // Clear filter inputs
            const filterInputs = bulkForm.querySelectorAll('input[name="search"], input[name="status"], input[name="date_from"], input[name="date_to"]');
            filterInputs.forEach(input => input.remove());
            
            if (isGlobalSelectMode) {
                // Add global selection flag
                const globalInput = document.createElement('input');
                globalInput.type = 'hidden';
                globalInput.name = 'select_all_pages';
                globalInput.value = 'true';
                bulkForm.appendChild(globalInput);
                
                // Add current filter parameters
                const currentUrl = new URL(window.location);
                ['search', 'status', 'date_from', 'date_to'].forEach(param => {
                    const value = currentUrl.searchParams.get(param);
                    if (value) {
                        const filterInput = document.createElement('input');
                        filterInput.type = 'hidden';
                        filterInput.name = param;
                        filterInput.value = value;
                        bulkForm.appendChild(filterInput);
                    }
                });
            } else {
                // Add individual selected IDs
                globalSelectedIds.forEach(id => {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'message_ids[]';
                    hiddenInput.value = id;
                    bulkForm.appendChild(hiddenInput);
                });
            }
        }

        function showGlobalSelectionIndicator() {
            // Remove existing indicator
            hideGlobalSelectionIndicator();
            
            // Create global selection indicator
            // const indicator = document.createElement('div');
            // indicator.id = 'global-selection-indicator';
            // indicator.className = 'alert alert-info mb-3';
            // indicator.innerHTML = `
            //     <div class="d-flex justify-content-between align-items-center">
            //         <div>
            //             <i class="ti ti-info-circle me-2"></i>
            //             <strong>Semua ${totalRecords} pesan dipilih</strong> di seluruh halaman.
            //             ${getCurrentFiltersText()}
            //         </div>
            //         <div class="btn-list">
            //             <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectCurrentPageOnly()">
            //                 <i class="ti ti-file-text me-1"></i>
            //                 Halaman ini saja (${currentPageIds.length})
            //             </button>
            //             <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">
            //                 <i class="ti ti-x me-1"></i>
            //                 Hapus semua
            //             </button>
            //         </div>
            //     </div>
            // `;
            
            // Insert before the table
            // const tableCard = document.querySelector('.col-12 .card');
            // const container = tableCard.parentNode;
            // container.insertBefore(indicator, tableCard);
        }

        function hideGlobalSelectionIndicator() {
            const indicator = document.getElementById('global-selection-indicator');
            if (indicator) {
                indicator.remove();
            }
        }

        function getCurrentFiltersText() {
            const currentUrl = new URL(window.location);
            const filters = [];
            
            if (currentUrl.searchParams.get('search')) {
                filters.push(`pencarian: "${currentUrl.searchParams.get('search')}"`);
            }
            if (currentUrl.searchParams.get('status')) {
                filters.push(`status: ${currentUrl.searchParams.get('status')}`);
            }
            if (currentUrl.searchParams.get('date_from') || currentUrl.searchParams.get('date_to')) {
                filters.push('filter tanggal aktif');
            }
            
            return filters.length > 0 ? `<br><small class="text-muted">Filter: ${filters.join(', ')}</small>` : '';
        }

        // Function to select only current page items
        window.selectCurrentPageOnly = function() {
            exitGlobalMode();
            hideGlobalSelectionIndicator();
            updateBulkActions();
            saveSelectionState();
        };

        // Clear selection function
        window.clearSelection = function() {
            clearAllSelection();
            updateBulkActions();
            saveSelectionState();
        };

        // Fetch all message IDs for global selection - DIPERBAIKI
        async function fetchAllMessageIds() {
            try {
                const currentUrl = new URL(window.location);
                const params = new URLSearchParams();
                
                // Add current filters
                ['search', 'status', 'date_from', 'date_to'].forEach(param => {
                    const value = currentUrl.searchParams.get(param);
                    if (value) {
                        params.append(param, value);
                    }
                });
                
                // Add a flag to get all IDs
                params.append('get_all_ids', 'true');
                
                const response = await fetch(`{{ route('contact-messages.index') }}?${params}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.all_ids) {
                        allMessageIds = new Set(data.all_ids);
                        console.log(`Loaded ${allMessageIds.size} message IDs from server`);
                    } else {
                        console.log('No all_ids in response, using fallback');
                    }
                } else {
                    console.log('Failed to fetch all IDs, using fallback method');
                }
            } catch (error) {
                console.log('Error fetching all IDs:', error);
            }
        }

        // PERBAIKAN: Save selection state dengan lebih detail
        function saveSelectionState() {
            const state = {
                globalSelectedIds: Array.from(globalSelectedIds),
                isGlobalSelectMode: isGlobalSelectMode,
                allMessageIds: Array.from(allMessageIds),
                currentPageIds: currentPageIds,
                totalRecords: totalRecords,
                currentFilters: {
                    search: new URL(window.location).searchParams.get('search'),
                    status: new URL(window.location).searchParams.get('status'),
                    date_from: new URL(window.location).searchParams.get('date_from'),
                    date_to: new URL(window.location).searchParams.get('date_to')
                },
                timestamp: Date.now()
            };
            localStorage.setItem('contactMessagesSelection', JSON.stringify(state));
            console.log(`Saved selection state: ${globalSelectedIds.size} selected, global mode: ${isGlobalSelectMode}`);
        }

        // PERBAIKAN: Load saved selection state dengan validasi yang lebih baik
        function loadSavedSelection() {
            const savedState = localStorage.getItem('contactMessagesSelection');
            if (savedState) {
                try {
                    const state = JSON.parse(savedState);
                    
                    // Check if state is recent (within 1 hour) and filters match
                    const isRecent = (Date.now() - (state.timestamp || 0)) < 3600000; // 1 hour
                    const filtersMatch = checkFiltersMatch(state.currentFilters);
                    
                    if (isRecent && filtersMatch) {
                        globalSelectedIds = new Set(state.globalSelectedIds || []);
                        isGlobalSelectMode = state.isGlobalSelectMode || false;
                        allMessageIds = new Set(state.allMessageIds || []);
                        
                        console.log(`Loaded saved state: ${globalSelectedIds.size} selected, global mode: ${isGlobalSelectMode}`);
                        
                        // Update current page checkboxes based on saved state
                        messageCheckboxes.forEach(checkbox => {
                            const messageId = parseInt(checkbox.value);
                            if (isGlobalSelectMode || globalSelectedIds.has(messageId)) {
                                checkbox.checked = true;
                            }
                        });
                        
                        // Show global indicator if in global mode
                        if (isGlobalSelectMode) {
                            showGlobalSelectionIndicator();
                        }
                        
                        updateSelectAllState();
                        updateBulkActions();
                    } else {
                        // Clear expired or mismatched state
                        console.log('Clearing expired or mismatched selection state');
                        localStorage.removeItem('contactMessagesSelection');
                    }
                } catch (e) {
                    console.log('Error loading selection state:', e);
                    localStorage.removeItem('contactMessagesSelection');
                }
            }
        }

        function checkFiltersMatch(savedFilters) {
            if (!savedFilters) return false;
            
            const currentUrl = new URL(window.location);
            return (
                savedFilters.search === currentUrl.searchParams.get('search') &&
                savedFilters.status === currentUrl.searchParams.get('status') &&
                savedFilters.date_from === currentUrl.searchParams.get('date_from') &&
                savedFilters.date_to === currentUrl.searchParams.get('date_to')
            );
        }

        // Form submission with confirmation
        bulkForm.addEventListener('submit', function(e) {
            const action = e.submitter.value;
            const count = isGlobalSelectMode ? totalRecords : globalSelectedIds.size;
            const scope = isGlobalSelectMode ? ' di seluruh halaman' : '';
            
            let message = '';
            switch(action) {
                case 'mark_read':
                    message = `Tandai ${count} pesan sebagai dibaca${scope}?`;
                    break;
                case 'mark_unread':
                    message = `Tandai ${count} pesan sebagai belum dibaca${scope}?`;
                    break;
                case 'mark_replied':
                    message = `Tandai ${count} pesan sebagai sudah dibalas${scope}?`;
                    break;
                case 'delete':
                    message = `Hapus ${count} pesan${scope}? Tindakan ini tidak dapat dibatalkan.`;
                    break;
            }
            
            if (!confirm(message)) {
                e.preventDefault();
                return false;
            }
            
            // Clear selection state after successful submission
            setTimeout(() => {
                localStorage.removeItem('contactMessagesSelection');
            }, 100);
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + A to select all (global)
            if ((e.ctrlKey || e.metaKey) && e.key === 'a' && !e.target.matches('input, textarea')) {
                e.preventDefault();
                selectAll.checked = true;
                selectAll.dispatchEvent(new Event('change'));
            }
            
            // Escape to clear selection
            if (e.key === 'Escape') {
                clearSelection();
            }
        });

        // Mark messages as read when clicked (optional)
        const messageRows = document.querySelectorAll('tbody tr');
        messageRows.forEach(row => {
            row.addEventListener('click', function(e) {
                // Only if not clicking on checkbox, button, or link
                if (!e.target.closest('input, button, a, .dropdown')) {
                    const viewLink = this.querySelector('a[href*="contact-messages"]');
                    if (viewLink) {
                        window.location.href = viewLink.href;
                    }
                }
            });
        });

        // Initialize on page load
        updateSelectAllState();
        updateBulkActions();

        // Auto-save state when leaving page
        window.addEventListener('beforeunload', function() {
            saveSelectionState();
        });

        // Clear expired states periodically (cleanup)
        setInterval(() => {
            const savedState = localStorage.getItem('contactMessagesSelection');
            if (savedState) {
                try {
                    const state = JSON.parse(savedState);
                    const isExpired = (Date.now() - (state.timestamp || 0)) > 3600000; // 1 hour
                    if (isExpired) {
                        localStorage.removeItem('contactMessagesSelection');
                    }
                } catch (e) {
                    localStorage.removeItem('contactMessagesSelection');
                }
            }
        }, 300000); // Check every 5 minutes
    });
</script>
@endpush