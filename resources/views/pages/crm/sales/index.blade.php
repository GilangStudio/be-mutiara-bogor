@extends('layouts.main')

@section('title', 'Sales Management')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h2 class="page-title">Sales Management</h2>
    <a href="{{ route('crm.sales.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Add Sales
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

<div class="col-12">
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Sales Info</th>
                            <th width="120" class="text-center">Order</th>
                            <th width="120" class="text-center">Total Leads</th>
                            <th width="100" class="text-center">Status</th>
                            <th width="150" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $index => $salesItem)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-primary-lt me-3">
                                        <i class="ti ti-user"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $salesItem->name }}</div>
                                        <div class="text-secondary small">{{ $salesItem->email }}</div>
                                        <div class="text-secondary small">
                                            <i class="ti ti-phone me-1"></i>{{ $salesItem->phone }}
                                        </div>
                                        <div class="text-secondary small">
                                            <i class="ti ti-user me-1"></i>Username: {{ $salesItem->user->username }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($salesItem->is_active)
                                    <span class="badge bg-blue-lt">{{ $salesItem->order }}</span>
                                @else
                                    <span class="text-secondary">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-green-lt">
                                    {{ $salesItem->historyLeads->first()->total_leads ?? 0 }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $salesItem->status_badge_color }}-lt">
                                    {{ $salesItem->status_text }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-list justify-content-center">
                                    @if($salesItem->is_active)
                                        <a href="{{ route('crm.sales.edit', $salesItem) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="confirmDeactivate({{ $salesItem->id }}, '{{ $salesItem->name }}')"
                                                title="Deactivate">
                                            <i class="ti ti-user-off"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-success"
                                                onclick="confirmActivate({{ $salesItem->id }}, '{{ $salesItem->name }}')"
                                                title="Activate">
                                            <i class="ti ti-user-check"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="empty">
                                    <div class="empty-icon">
                                        <i class="ti ti-users icon icon-lg"></i>
                                    </div>
                                    <p class="empty-title h3">No sales yet</p>
                                    <p class="empty-subtitle text-secondary">
                                        Add your first sales to start managing leads
                                    </p>
                                    <div class="empty-action">
                                        <a href="{{ route('crm.sales.create') }}" class="btn btn-primary">
                                            <i class="ti ti-plus me-1"></i> Add First Sales
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

{{-- Hidden forms for activate/deactivate --}}
<form id="activate-form" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
</form>

<form id="deactivate-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
@include('components.toast')
<script>
    function confirmActivate(salesId, salesName) {
        if (confirm(`Are you sure you want to activate sales "${salesName}"?`)) {
            const form = document.getElementById('activate-form');
            form.action = `{{ url('crm/sales') }}/${salesId}/activate`;
            form.submit();
        }
    }

    function confirmDeactivate(salesId, salesName) {
        if (confirm(`Are you sure you want to deactivate sales "${salesName}"?\n\nDeactivated sales will not receive new leads.`)) {
            const form = document.getElementById('deactivate-form');
            form.action = `{{ url('crm/sales') }}/${salesId}`;
            form.submit();
        }
    }
</script>
@endpush