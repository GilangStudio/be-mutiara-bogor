@extends('layouts.main')

@section('title', 'Edit Lead')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h2 class="page-title">Edit Lead</h2>
        <div class="page-subtitle">{{ $lead->name }}</div>
    </div>
    <a href="{{ route('crm.leads.index') }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left me-1"></i> Back
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
            <i class="ti ti-exclamation-circle me-2"></i>
        </div>
        <div>{{ session('error') }}</div>
    </div>
    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
</div>
@endif

<div class="row g-3">
    <div class="col-lg-8">
        {{-- Lead Information Form --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-user me-2"></i>
                    Informasi Lead
                </h3>
            </div>
            <form action="{{ route('crm.leads.update', $lead) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name', $lead->name) }}" required 
                                       placeholder="Enter full name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       name="phone" value="{{ old('phone', $lead->phone) }}" required 
                                       placeholder="08xxxxxxxxxx">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email', $lead->email) }}" 
                                       placeholder="example@email.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Platform <span class="text-danger">*</span></label>
                                <select class="form-select @error('platform_id') is-invalid @enderror" name="platform_id" required>
                                    <option value="">Select Platform</option>
                                    @foreach($platforms as $platform)
                                    <option value="{{ $platform->id }}" 
                                            {{ old('platform_id', $lead->platform_id) == $platform->id ? 'selected' : '' }}>
                                        {{ $platform->platform_name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('platform_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Sales <span class="text-danger">*</span></label>
                                <select class="form-select @error('sales_id') is-invalid @enderror" name="sales_id" required>
                                    <option value="">Select Sales</option>
                                    @foreach($sales as $salesItem)
                                    <option value="{{ $salesItem->id }}" 
                                            {{ old('sales_id', $lead->historyLead->sales_id ?? '') == $salesItem->id ? 'selected' : '' }}>
                                        {{ $salesItem->name }} (Order: {{ $salesItem->order }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('sales_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    @if($lead->status == \App\Enum\LeadStatus::NEW->value)
                                        Sales can be changed for leads with NEW status
                                    @else
                                        Sales cannot be changed for processed leads
                                    @endif
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Path Referral</label>
                                <input type="text" class="form-control @error('path_referral') is-invalid @enderror" 
                                       name="path_referral" value="{{ old('path_referral', $lead->path_referral) }}" 
                                       placeholder="Example: Facebook Ads - Campaign A">
                                @error('path_referral')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Message</label>
                                <textarea class="form-control @error('message') is-invalid @enderror" 
                                          name="message" rows="4" 
                                          placeholder="Enter message from lead...">{{ old('message', $lead->message) }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control @error('note') is-invalid @enderror" 
                                          name="note" rows="3" 
                                          placeholder="Internal notes...">{{ old('note', $lead->note) }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <div class="d-flex">
                        <a href="{{ route('crm.leads.index') }}" class="btn btn-link">Cancel</a>
                        <button type="submit" class="btn btn-primary ms-auto">
                            <i class="ti ti-device-floppy me-1"></i>
                            Update Lead
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- History Move Leads --}}
        @if($historyMoveLeads->count() > 0)
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-history me-2"></i>
                    Riwayat Perpindahan Sales
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Dari Sales</th>
                                <th>Ke Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historyMoveLeads as $history)
                            <tr>
                                <td>{{ $history->created_at->format('d M Y H:i') }}</td>
                                <td>{{ $history->fromSales->name }}</td>
                                <td>{{ $history->toSales->name }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        {{-- Status Management --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-flag me-2"></i>
                    Status Management
                </h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Current Status</label>
                    <div>
                        <span class="badge bg-{{ $lead->status_badge_color }}-lt fs-5">
                            {{ $lead->status_text }}
                        </span>
                    </div>
                </div>

                <form action="{{ route('crm.leads.change-status', $lead) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label">Change Status</label>
                        <select class="form-select" name="status" required>
                            @foreach($statusOptions as $status)
                            <option value="{{ $status->value }}" 
                                    {{ $lead->status == $status->value ? 'selected' : '' }}>
                                {{ match($status->value) {
                                    'NEW' => 'New',
                                    'PROCESS' => 'Process', 
                                    'CLOSING' => 'Closing',
                                    default => $status->value
                                } }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-warning w-100">
                        <i class="ti ti-refresh me-1"></i> Update Status
                    </button>
                </form>
            </div>
        </div>

        {{-- Lead Info --}}
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-info-circle me-2"></i>
                    Lead Information
                </h3>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-secondary">Platform:</small>
                    <div>{{ $lead->platform->platform_name }}</div>
                </div>
                <div class="mb-2">
                    <small class="text-secondary">Sales:</small>
                    <div>{{ $lead->historyLead->sales->name ?? 'Belum assigned' }}</div>
                </div>
                <div class="mb-2">
                    <small class="text-secondary">Assignment Type:</small>
                    <div>
                        <span class="badge bg-{{ $lead->historyLead->assignment_type_badge_color ?? 'gray' }}-lt">
                            {{ $lead->historyLead->assignment_type_text ?? 'N/A' }}
                        </span>
                    </div>
                </div>
                <div class="mb-2">
                    <small class="text-secondary">Created At:</small>
                    <div>{{ $lead->created_at->format('d M Y, H:i') }}</div>
                </div>
                <div class="mb-0">
                    <small class="text-secondary">Last Update:</small>
                    <div>{{ $lead->updated_at->format('d M Y, H:i') }}</div>
                </div>
            </div>
        </div>

        {{-- Movement Timeline --}}
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-timeline me-2"></i>
                    Movement Timeline
                </h3>
            </div>
            <div class="card-body" style="max-height: 300px; overflow-y: auto">
                @if($historyMoveLeads->count() > 0 || $lead->historyLead)
                <div class="steps steps-vertical">
                    {{-- Initial Assignment --}}
                    @if($lead->historyLead)
                    <div class="step-item {{ $historyMoveLeads->count() == 0 ? 'active' : '' }}">
                        <div class="h4 m-0">Initial Assignment</div>
                        <div class="text-secondary">
                            Assigned to <strong>{{ $lead->historyLead->sales->name }}</strong>
                        </div>
                        <div class="text-secondary small">
                            <i class="ti ti-clock me-1"></i>
                            {{ $lead->created_at->format('d M Y, H:i') }}
                        </div>
                        <div class="mt-1">
                            <span class="badge bg-{{ $lead->historyLead->assignment_type_badge_color }}-lt">
                                {{ $lead->historyLead->assignment_type_text }}
                            </span>
                        </div>
                    </div>
                    @endif

                    {{-- Movement History --}}
                    @foreach($historyMoveLeads as $index => $history)
                    {{-- <div class="step-item {{ $index == 0 ? 'active' : '' }}"> --}}
                    <div class="step-item">
                        <div class="h4 m-0">Sales Transfer #{{ $index + 1 }}</div>
                        <div class="text-secondary">
                            From <strong>{{ $history->fromSales->name }}</strong> 
                            to <strong>{{ $history->toSales->name }}</strong>
                        </div>
                        <div class="text-secondary small">
                            <i class="ti ti-clock me-1"></i>
                            {{ $history->created_at->format('d M Y, H:i') }}
                        </div>
                        <div class="text-secondary small">
                            <i class="ti ti-arrow-right me-1"></i>
                            {{ $history->created_at->diffForHumans() }}
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="empty">
                    <div class="empty-icon">
                        <i class="ti ti-timeline icon"></i>
                    </div>
                    <p class="empty-title">No Movement History</p>
                    <p class="empty-subtitle text-secondary">
                        This lead has not been moved between sales yet.
                    </p>
                </div>
                @endif
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-bolt me-2"></i>
                    Quick Actions
                </h3>
            </div>
            <div class="card-body">
                <div class="btn-list w-100">
                    @if($lead->phone)
                    <a href="{{ $lead->whatsapp_url }}" target="_blank" class="btn btn-success w-100">
                        <i class="ti ti-brand-whatsapp me-1"></i> WhatsApp
                    </a>
                    @endif
                    @if($lead->email)
                    <a href="mailto:{{ $lead->email }}" class="btn btn-info w-100">
                        <i class="ti ti-mail me-1"></i> Email
                    </a>
                    @endif
                    @if($lead->historyLead && $lead->historyLead->sales)
                    <a href="{{ route('crm.sales.edit', $lead->historyLead->sales) }}" class="btn btn-outline-primary w-100">
                        <i class="ti ti-user me-1"></i> View Sales
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto format phone number
        const phoneInput = document.querySelector('input[name="phone"]');
        
        phoneInput.addEventListener('input', function() {
            let value = this.value.replace(/[^0-9]/g, '');
            
            if (value.length > 15) {
                value = value.substring(0, 15);
            }
            
            this.value = value;
        });

        // Disable sales selection if status is not NEW
        const leadStatus = '{{ $lead->status }}';
        const salesSelect = document.querySelector('select[name="sales_id"]');
        
        if (leadStatus !== 'NEW') {
            salesSelect.disabled = true;
        }

        // Form validation
        const form = document.querySelector('form[action*="update"]');
        if (form) {
            form.addEventListener('submit', function(e) {
                const phone = phoneInput.value;
                
                if (phone.length < 10) {
                    e.preventDefault();
                    alert('Phone number minimum 10 digits');
                    phoneInput.focus();
                    return false;
                }
                
                if (!phone.startsWith('0')) {
                    e.preventDefault();
                    alert('Phone number must start with 0');
                    phoneInput.focus();
                    return false;
                }
            });
        }
    });
</script>
@endpush