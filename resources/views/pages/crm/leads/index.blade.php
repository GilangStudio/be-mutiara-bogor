@extends('layouts.main')

@section('title', 'Leads Management')

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
    
    /* Loading state */
    .loading-overlay {
        opacity: 0.6;
        pointer-events: none;
    }
    
    /* Filter indicators */
    .filter-badge {
        font-size: 0.75rem;
    }

    /* Bulk actions */
    .bulk-actions {
        display: none;
        /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.75rem;
        border-radius: 0.5rem; */
    }

    .bulk-actions.show {
        display: block;
    }

    .select-all-checkbox {
        /* transform: scale(1.2); */
    }
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h2 class="page-title">Leads Management</h2>
    <a href="{{ route('crm.leads.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Add Lead
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

{{-- Statistics Cards --}}
<div class="col-sm-6 col-lg-3">
    <div class="card card-sm">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-auto">
                    <span class="bg-primary text-white avatar">
                        <i class="ti ti-users"></i>
                    </span>
                </div>
                <div class="col">
                    <div class="font-weight-medium">
                        {{ \App\Models\Lead::count() }}
                    </div>
                    <div class="text-secondary">
                        Total Leads
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-sm-6 col-lg-3">
    <div class="card card-sm">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-auto">
                    <span class="bg-blue text-white avatar">
                        <i class="ti ti-user-plus"></i>
                    </span>
                </div>
                <div class="col">
                    <div class="font-weight-medium">
                        {{ \App\Models\Lead::getNewLeadsCount() }}
                    </div>
                    <div class="text-secondary">
                        Leads Baru
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-sm-6 col-lg-3">
    <div class="card card-sm">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-auto">
                    <span class="bg-warning text-white avatar">
                        <i class="ti ti-clock"></i>
                    </span>
                </div>
                <div class="col">
                    <div class="font-weight-medium">
                        {{ \App\Models\Lead::getProcessLeadsCount() }}
                    </div>
                    <div class="text-secondary">
                        Dalam Proses
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-sm-6 col-lg-3">
    <div class="card card-sm">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-auto">
                    <span class="bg-success text-white avatar">
                        <i class="ti ti-check"></i>
                    </span>
                </div>
                <div class="col">
                    <div class="font-weight-medium">
                        {{ \App\Models\Lead::getClosingLeadsCount() }}
                    </div>
                    <div class="text-secondary">
                        Closing
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-sm-6 col-lg-3">
    <div class="card card-sm">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-auto">
                    <span class="bg-orange text-white avatar">
                        <i class="ti ti-repeat"></i>
                    </span>
                </div>
                <div class="col">
                    <div class="font-weight-medium">
                        {{ \App\Models\Lead::getRecontactLeadsCount() }}
                    </div>
                    <div class="text-secondary">
                        Recontact Leads
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Search and Filter Form --}}
<div class="col-12">
    <form method="GET" action="{{ route('crm.leads.index') }}" id="filter-form" class="w-100">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-search me-2"></i>
                    Search & Filter
                </h3>
                <div class="card-actions">
                    @if(request()->hasAny(['search', 'status', 'platform_id', 'sales_id', 'assignment_type', 'date_from', 'date_to']))
                    <a href="{{ route('crm.leads.index') }}" class="btn btn-outline-secondary btn-sm" title="Clear all filters">
                        <i class="ti ti-x me-1"></i>Clear Filters
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Cari nama, telepon, email, pesan..." 
                                   value="{{ request('search') }}" 
                                   autocomplete="off" id="search-input">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="status" id="status-filter">
                            <option value="">Semua Status</option>
                            @foreach($statusOptions as $status)
                            <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                                {{ match($status->value) {
                                    'NEW' => 'Baru',
                                    'PROCESS' => 'Proses', 
                                    'CLOSING' => 'Closing',
                                    default => $status->value
                                } }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="platform_id" id="platform-filter">
                            <option value="">Semua Platform</option>
                            @foreach($platforms as $platform)
                            <option value="{{ $platform->id }}" {{ request('platform_id') == $platform->id ? 'selected' : '' }}>
                                {{ $platform->platform_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="has_recontact" id="recontact-filter">
                            <option value="">Semua Lead</option>
                            <option value="true" {{ request('has_recontact') === 'true' ? 'selected' : '' }}>
                                Dengan Recontact
                            </option>
                            <option value="false" {{ request('has_recontact') === 'false' ? 'selected' : '' }}>
                                Tanpa Recontact
                            </option>
                        </select>
                    </div>
                </div>
                
                <div class="row g-3 mt-2">
                    <div class="col-md-3">
                        <select class="form-select" name="sales_id" id="sales-filter">
                            <option value="">Semua Sales</option>
                            @foreach($sales as $salesItem)
                            <option value="{{ $salesItem->id }}" {{ request('sales_id') == $salesItem->id ? 'selected' : '' }}>
                                {{ $salesItem->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="assignment_type" id="assignment-filter">
                            <option value="">Semua Assignment</option>
                            <option value="auto" {{ request('assignment_type') === 'auto' ? 'selected' : '' }}>
                                Automatic
                            </option>
                            <option value="manual" {{ request('assignment_type') === 'manual' ? 'selected' : '' }}>
                                Manual
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" name="date_from" 
                               placeholder="Tanggal Mulai" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" name="date_to" 
                               placeholder="Tanggal Akhir" value="{{ request('date_to') }}">
                    </div>
                </div>
                
                {{-- Active Filters Display --}}
                @if(request()->hasAny(['search', 'status', 'platform_id', 'has_recontact', 'sales_id', 'assignment_type', 'date_from', 'date_to']))
                <div class="mt-3 d-flex gap-2 align-items-center flex-wrap">
                    <small class="text-secondary">Filter aktif:</small>
                    @if(request('search'))
                    <span class="badge bg-blue-lt filter-badge">
                        <i class="ti ti-search me-1"></i>
                        Search: "{{ request('search') }}"
                    </span>
                    @endif
                    @if(request('status'))
                    <span class="badge bg-green-lt filter-badge">
                        <i class="ti ti-flag me-1"></i>
                        Status: {{ match(request('status')) {
                            'NEW' => 'Baru',
                            'PROCESS' => 'Proses',
                            'CLOSING' => 'Closing',
                            default => request('status')
                        } }}
                    </span>
                    @endif
                    @if(request('platform_id'))
                    @php
                        $selectedPlatform = $platforms->find(request('platform_id'));
                    @endphp
                    <span class="badge bg-purple-lt filter-badge">
                        <i class="ti ti-device-desktop me-1"></i>
                        Platform: {{ $selectedPlatform->platform_name ?? 'Unknown' }}
                    </span>
                    @endif
                    @if(request('sales_id'))
                    @php
                        $selectedSales = $sales->find(request('sales_id'));
                    @endphp
                    <span class="badge bg-yellow-lt filter-badge">
                        <i class="ti ti-user me-1"></i>
                        Sales: {{ $selectedSales->name ?? 'Unknown' }}
                    </span>
                    @endif
                    @if(request('assignment_type'))
                    <span class="badge bg-orange-lt filter-badge">
                        <i class="ti ti-settings me-1"></i>
                        Assignment: {{ request('assignment_type') === 'auto' ? 'Automatic' : 'Manual' }}
                    </span>
                    @endif
                    @if(request('date_from') || request('date_to'))
                    <span class="badge bg-red-lt filter-badge">
                        <i class="ti ti-calendar me-1"></i>
                        Tanggal: {{ request('date_from') ?? '...' }} - {{ request('date_to') ?? '...' }}
                    </span>
                    @endif
                    @if(request('has_recontact'))
                    <span class="badge bg-orange-lt filter-badge">
                        <i class="ti ti-repeat me-1"></i>
                        Recontact: {{ request('has_recontact') === 'true' ? 'Dengan Recontact' : 'Tanpa Recontact' }}
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
    <div class="card bulk-actions" id="bulk-actions">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span id="selected-count">0</span> lead(s) dipilih
                    <button type="button" class="btn btn-sm btn-outline-light ms-2" onclick="clearSelection()">
                        <i class="ti ti-x me-1"></i>Clear
                    </button>
                </div>
                <div class="btn-list">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="ti ti-edit me-1"></i>Update Status
                        </button>
                        <div class="dropdown-menu">
                            @foreach($statusOptions as $status)
                            <a class="dropdown-item" href="#" onclick="bulkUpdateStatus('{{ $status->value }}')">
                                <span class="badge bg-{{ \App\Enum\LeadStatus::color($status->value) }}-lt me-2"></span>
                                {{ match($status->value) {
                                    'NEW' => 'Baru',
                                    'PROCESS' => 'Proses',
                                    'CLOSING' => 'Closing',
                                    default => $status->value
                                } }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                    {{-- <div class="dropdown">
                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="ti ti-user me-1"></i>Assign Sales
                        </button>
                        <div class="dropdown-menu">
                            @foreach($sales as $salesItem)
                            <a class="dropdown-item" href="#" onclick="bulkAssignSales('{{ $salesItem->id }}')">
                                <div class="avatar avatar-xs bg-primary-lt me-2">{{ $salesItem->order }}</div>
                                {{ $salesItem->name }}
                            </a>
                            @endforeach
                        </div>
                    </div> --}}
                    <button type="button" class="btn btn-sm btn-danger" onclick="bulkDelete()">
                        <i class="ti ti-trash me-1"></i>Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Leads Table --}}
<div class="col-12">
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive" id="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="50" class="text-center">
                                <input type="checkbox" class="form-check-input select-all-checkbox" id="select-all">
                            </th>
                            <th width="50">No</th>
                            <th>Lead Info</th>
                            <th width="150">Platform</th>
                            <th width="150">Sales</th>
                            <th width="100" class="text-center">Status</th>
                            <th width="120" class="text-center">Tanggal</th>
                            <th width="150" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads as $index => $lead)
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input lead-checkbox" value="{{ $lead->id }}">
                            </td>
                            <td>{{ ($leads->currentPage() - 1) * $leads->perPage() + $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-blue-lt me-3">
                                        <i class="ti ti-user"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold" data-searchable="name">
                                            {{ $lead->name }}
                                            @if($lead->recontact_count > 0)
                                                @php
                                                    $recontactLabel = $lead->recontact_count === 1 ? 'Recontact' : "Recontact ({$lead->recontact_count}x)";
                                                    $isRecent = $lead->last_contact_at && 
                                                            \Carbon\Carbon::parse($lead->last_contact_at)->isAfter(now()->subHours(24));
                                                @endphp
                                                <span class="badge bg-orange-lt ms-2">{{ $recontactLabel }}</span>
                                                @if($isRecent)
                                                    <span class="badge bg-red-lt ms-1">🔥 Recent</span>
                                                @endif
                                            @endif
                                        </div>
                                        <div class="text-secondary small" data-searchable="phone">
                                            @if($lead->phone)
                                                <i class="ti ti-phone me-1"></i>{{ $lead->phone }}
                                            @endif
                                        </div>
                                        @if($lead->email)
                                        <div class="text-secondary small" data-searchable="email">
                                            <i class="ti ti-mail me-1"></i>{{ $lead->email }}
                                        </div>
                                        @endif
                                        @if($lead->message)
                                        <div class="text-secondary small" data-searchable="message">
                                            <i class="ti ti-message me-1"></i>{{ Str::limit($lead->message, 50) }}
                                        </div>
                                        @endif
                                        @if($lead->recontact_count > 0 && $lead->last_contact_at)
                                        <div class="text-secondary small">
                                            <i class="ti ti-clock me-1"></i>Last contact: {{ \Carbon\Carbon::parse($lead->last_contact_at)->diffForHumans() }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-purple-lt">
                                    {{ $lead->platform->platform_name }}
                                </span>
                                @if($lead->path_referral)
                                <div class="text-secondary small" data-searchable="path_referral">
                                    {{ $lead->path_referral }}
                                </div>
                                @endif
                            </td>
                            <td>
                                @if($lead->historyLead && $lead->historyLead->sales)
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs bg-green-lt me-2">
                                        <i class="ti ti-user"></i>
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $lead->historyLead->sales->name }}</div>
                                        <div class="text-secondary small">
                                            <span class="badge bg-{{ $lead->historyLead->assignment_type_badge_color }}-lt">
                                                {{ $lead->historyLead->assignment_type_text }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <span class="text-secondary">Belum assigned</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $lead->status_badge_color }}-lt">
                                    {{ match($lead->status) {
                                        'NEW' => 'Baru',
                                        'PROCESS' => 'Proses',
                                        'CLOSING' => 'Closing',
                                        default => $lead->status_text
                                    } }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="text-dark">{{ $lead->created_at->format('d M Y') }}</div>
                                <div class="text-secondary small">{{ $lead->created_at->format('H:i') }}</div>
                            </td>
                            <td class="text-center">
                                <div class="btn-list">
                                    @if($lead->phone)
                                    <a href="{{ $lead->whatsapp_url }}" target="_blank" 
                                       class="btn btn-sm btn-outline-success" title="WhatsApp">
                                        <i class="ti ti-brand-whatsapp"></i>
                                    </a>
                                    @endif
                                    <a href="{{ route('crm.leads.edit', $lead) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                            data-id="{{ $lead->id }}"
                                            data-name="{{ $lead->name }}"
                                            data-url="{{ route('crm.leads.destroy', $lead) }}"
                                            title="Delete">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="empty">
                                    <div class="empty-icon">
                                        @if(request()->hasAny(['search', 'status', 'platform_id', 'sales_id', 'assignment_type', 'date_from', 'date_to']))
                                        <i class="ti ti-search icon icon-lg"></i>
                                        @else
                                        <i class="ti ti-users icon icon-lg"></i>
                                        @endif
                                    </div>
                                    <p class="empty-title h3">
                                        @if(request()->hasAny(['search', 'status', 'platform_id', 'sales_id', 'assignment_type', 'date_from', 'date_to']))
                                        Tidak ada lead ditemukan
                                        @else
                                        Belum ada lead
                                        @endif
                                    </p>
                                    <p class="empty-subtitle text-secondary">
                                        @if(request()->hasAny(['search', 'status', 'platform_id', 'sales_id', 'assignment_type', 'date_from', 'date_to']))
                                        Coba sesuaikan filter pencarian atau hapus filter untuk melihat semua lead.
                                        @else
                                        Tambahkan lead pertama untuk mulai mengelola prospek.
                                        @endif
                                    </p>
                                    <div class="empty-action">
                                        @if(request()->hasAny(['search', 'status', 'platform_id', 'sales_id', 'assignment_type', 'date_from', 'date_to']))
                                        <a href="{{ route('crm.leads.index') }}" class="btn btn-outline-secondary">
                                            <i class="ti ti-x me-1"></i> Hapus Filter
                                        </a>
                                        @else
                                        <a href="{{ route('crm.leads.create') }}" class="btn btn-primary">
                                            <i class="ti ti-plus me-1"></i> Tambah Lead Pertama
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
        @if($leads->total() > 0 || request()->hasAny(['search', 'status', 'platform_id', 'sales_id', 'assignment_type', 'date_from', 'date_to']))
        <div class="card-footer d-flex align-items-center">
            <div class="text-secondary">
                @if($leads->total() > 0)
                    Menampilkan <strong>{{ $leads->firstItem() }}</strong> sampai <strong>{{ $leads->lastItem() }}</strong> 
                    dari <strong>{{ $leads->total() }}</strong> hasil
                    @if(request('search'))
                        untuk "<strong>{{ request('search') }}</strong>"
                    @endif
                @else
                    Tidak ada hasil ditemukan
                    @if(request()->hasAny(['search', 'status', 'platform_id', 'sales_id', 'assignment_type', 'date_from', 'date_to']))
                        dengan filter saat ini
                    @endif
                @endif
            </div>
            
            @include('components.pagination', ['paginator' => $leads])
        </div>
        @endif
    </div>
</div>

{{-- Hidden Form for Bulk Actions --}}
<form id="bulk-action-form" method="POST" action="{{ route('crm.leads.bulk-action') }}" style="display: none;">
    @csrf
    <input type="hidden" name="action" id="bulk-action">
    <input type="hidden" name="status" id="bulk-status">
    <input type="hidden" name="sales_id" id="bulk-sales-id">
    <input type="hidden" name="select_all_pages" id="select-all-pages" value="false">
    
    {{-- Preserve current filters for bulk actions --}}
    @if(request('search'))
        <input type="hidden" name="search" value="{{ request('search') }}">
    @endif
    @if(request('status'))
        <input type="hidden" name="status_filter" value="{{ request('status') }}">
    @endif
    @if(request('platform_id'))
        <input type="hidden" name="platform_id" value="{{ request('platform_id') }}">
    @endif
    @if(request('sales_id'))
        <input type="hidden" name="sales_id_filter" value="{{ request('sales_id') }}">
    @endif
    @if(request('assignment_type'))
        <input type="hidden" name="assignment_type" value="{{ request('assignment_type') }}">
    @endif
    @if(request('date_from'))
        <input type="hidden" name="date_from" value="{{ request('date_from') }}">
    @endif
    @if(request('date_to'))
        <input type="hidden" name="date_to" value="{{ request('date_to') }}">
    @endif
    
    <div id="selected-ids-container"></div>
</form>

{{-- Include Global Delete Modal --}}
@include('components.delete-modal')

@endsection

@push('scripts')
@include('components.toast')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Get form and input elements
        const filterForm = document.getElementById('filter-form');
        const searchInput = document.getElementById('search-input');
        const statusFilter = document.getElementById('status-filter');
        const recontactFilter = document.getElementById('recontact-filter');
        const platformFilter = document.getElementById('platform-filter');
        const salesFilter = document.getElementById('sales-filter');
        const assignmentFilter = document.getElementById('assignment-filter');
        const tableContainer = document.getElementById('table-container');
        
        // Debounce function for search
        let searchTimeout;
        
        // Search input with debounce (auto-submit after 600ms)
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                submitFilter();
            }, 600);
        });
        
        // Filter change events (immediate submit)
        [statusFilter, platformFilter, recontactFilter, salesFilter, assignmentFilter].forEach(filter => {
            filter.addEventListener('change', function() {
                submitFilter();
            });
        });
        
        // Date filters
        document.querySelector('input[name="date_from"]').addEventListener('change', submitFilter);
        document.querySelector('input[name="date_to"]').addEventListener('change', submitFilter);
        
        // Handle Enter key in search input
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimeout);
                submitFilter();
            }
        });
        
        // Submit filter function with loading state
        function submitFilter() {
            // Store current cursor position and search value
            const cursorPosition = searchInput.selectionStart;
            const searchValue = searchInput.value;
            
            // Store in sessionStorage for after page reload
            sessionStorage.setItem('searchInputFocus', 'true');
            sessionStorage.setItem('searchCursorPosition', cursorPosition);
            sessionStorage.setItem('searchValue', searchValue);
            
            // Show loading state
            showLoadingState();
            
            // Submit form
            filterForm.submit();
        }
        
        // Restore focus and cursor position after page load
        function restoreSearchFocus() {
            const shouldFocus = sessionStorage.getItem('searchInputFocus');
            const cursorPosition = sessionStorage.getItem('searchCursorPosition');
            const searchValue = sessionStorage.getItem('searchValue');
            
            if (shouldFocus === 'true' && searchInput.value === searchValue) {
                // Focus input
                searchInput.focus();
                
                // Restore cursor position
                if (cursorPosition !== null) {
                    searchInput.setSelectionRange(parseInt(cursorPosition), parseInt(cursorPosition));
                }
                
                // Clear stored values
                sessionStorage.removeItem('searchInputFocus');
                sessionStorage.removeItem('searchCursorPosition');
                sessionStorage.removeItem('searchValue');
            }
        }
        
        // Restore focus on page load
        restoreSearchFocus();
        
        // Show loading state
        function showLoadingState() {
            tableContainer.classList.add('loading-overlay');
            
            // Add loading spinner to search input
            const searchIcon = searchInput.parentElement.querySelector('i');
            const originalClass = searchIcon.className;
            searchIcon.className = 'ti ti-loader-2 animate-spin';
            
            // Reset after a delay (in case form submission fails)
            setTimeout(() => {
                tableContainer.classList.remove('loading-overlay');
                searchIcon.className = originalClass;
            }, 5000);
        }
        
        // Highlight search terms in results
        const searchTerm = '{{ request('search') }}';
        if (searchTerm) {
            highlightSearchResults(searchTerm.toLowerCase());
        }
        
        // Function to highlight search results
        function highlightSearchResults(term) {
            const searchableElements = document.querySelectorAll('[data-searchable]');
            
            searchableElements.forEach(element => {
                const text = element.textContent;
                const lowerText = text.toLowerCase();
                
                if (lowerText.includes(term)) {
                    const regex = new RegExp(`(${escapeRegExp(term)})`, 'gi');
                    element.innerHTML = text.replace(regex, '<mark class="search-highlight">$1</mark>');
                }
            });
        }
        
        // Escape special characters for regex
        function escapeRegExp(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }
        
        // Focus search input on Ctrl+K or Cmd+K
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
            }
        });
        
        // Add loading animation CSS
        const style = document.createElement('style');
        style.textContent = `
            .animate-spin {
                animation: spin 1s linear infinite;
            }
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
        
        // Bulk Actions
        const selectAllCheckbox = document.getElementById('select-all');
        const leadCheckboxes = document.querySelectorAll('.lead-checkbox');
        const bulkActions = document.getElementById('bulk-actions');
        const selectedCountSpan = document.getElementById('selected-count');
        
        // Handle select all checkbox
        selectAllCheckbox.addEventListener('change', function() {
            leadCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });
        
        // Handle individual checkboxes
        leadCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectAllState();
                updateBulkActions();
            });
        });
        
        // Update select all state
        function updateSelectAllState() {
            const checkedCount = document.querySelectorAll('.lead-checkbox:checked').length;
            const totalCount = leadCheckboxes.length;
            
            selectAllCheckbox.checked = checkedCount === totalCount && totalCount > 0;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;
        }
        
        // Update bulk actions visibility
        function updateBulkActions() {
            const checkedCount = document.querySelectorAll('.lead-checkbox:checked').length;
            selectedCountSpan.textContent = checkedCount;
            
            if (checkedCount > 0) {
                bulkActions.classList.add('show');
            } else {
                bulkActions.classList.remove('show');
            }
        }
        
        // Clear selection
        window.clearSelection = function() {
            leadCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
            updateBulkActions();
        };
        
        // Bulk actions functions
        window.bulkUpdateStatus = function(status) {
            if (confirm('Apakah Anda yakin ingin mengubah status lead yang dipilih?')) {
                performBulkAction('update_status', { status: status });
            }
        };
        
        // window.bulkAssignSales = function(salesId) {
        //     if (confirm('Apakah Anda yakin ingin mengubah sales lead yang dipilih? (Hanya berlaku untuk lead dengan status BARU)')) {
        //         performBulkAction('assign_sales', { sales_id: salesId });
        //     }
        // };
        
        window.bulkDelete = function() {
            if (confirm('Apakah Anda yakin ingin menghapus lead yang dipilih? Tindakan ini tidak dapat dibatalkan.')) {
                performBulkAction('delete');
            }
        };
        
        // Perform bulk action
        function performBulkAction(action, params = {}) {
            const form = document.getElementById('bulk-action-form');
            const selectedIds = Array.from(document.querySelectorAll('.lead-checkbox:checked')).map(cb => cb.value);
            
            // Clear previous inputs
            const existingInputs = form.querySelectorAll('input[name="lead_ids[]"]');
            existingInputs.forEach(input => input.remove());
            
            // Set action
            document.getElementById('bulk-action').value = action;
            
            // Set parameters
            if (params.status) {
                document.getElementById('bulk-status').value = params.status;
            }
            if (params.sales_id) {
                document.getElementById('bulk-sales-id').value = params.sales_id;
            }
            
            // Add selected IDs
            const container = document.getElementById('selected-ids-container');
            container.innerHTML = '';
            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'lead_ids[]';
                input.value = id;
                container.appendChild(input);
            });
            
            // Submit form
            form.submit();
        }
    });
</script>
@endpush