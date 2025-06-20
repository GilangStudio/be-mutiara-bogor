@extends('layouts.main')

@section('title', 'Leads Management')

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

<div class="col-12">
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Lead Info</th>
                            <th width="150">Platform</th>
                            <th width="150">Sales</th>
                            <th width="100" class="text-center">Status</th>
                            <th width="120" class="text-center">Date</th>
                            <th width="150" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads as $index => $lead)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-blue-lt me-3">
                                        <i class="ti ti-user"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $lead->name }}</div>
                                        <div class="text-secondary small">
                                            @if($lead->phone)
                                                <i class="ti ti-phone me-1"></i>{{ $lead->phone }}
                                            @endif
                                        </div>
                                        @if($lead->email)
                                        <div class="text-secondary small">
                                            <i class="ti ti-mail me-1"></i>{{ $lead->email }}
                                        </div>
                                        @endif
                                        @if($lead->message)
                                        <div class="text-secondary small">
                                            <i class="ti ti-message me-1"></i>{{ Str::limit($lead->message, 50) }}
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
                                <div class="text-secondary small">
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
                                <span class="text-secondary">Not assigned yet</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $lead->status_badge_color }}-lt">
                                    {{ $lead->status_text }}
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
                            <td colspan="7" class="text-center py-4">
                                <div class="empty">
                                    <div class="empty-icon">
                                        <i class="ti ti-users icon icon-lg"></i>
                                    </div>
                                    <p class="empty-title h3">No leads yet</p>
                                    <p class="empty-subtitle text-secondary">
                                        Add your first lead to start managing prospects
                                    </p>
                                    <div class="empty-action">
                                        <a href="{{ route('crm.leads.create') }}" class="btn btn-primary">
                                            <i class="ti ti-plus me-1"></i> Add First Lead
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
@endpush