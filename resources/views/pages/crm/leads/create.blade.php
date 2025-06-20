@extends('layouts.main')

@section('title', 'Tambah Lead')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h2 class="page-title">Add New Lead</h2>
    <a href="{{ route('crm.leads.index') }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left me-1"></i> Back
    </a>
</div>
@endsection

@section('content')
{{-- Alert Messages --}}
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

{{-- Check Prerequisites --}}
@if($platforms->isEmpty())
<div class="alert alert-warning">
    <h4>Platform Diperlukan</h4>
    <p>Tidak ada platform yang tersedia. Silakan tambah platform terlebih dahulu.</p>
    <a href="{{ route('crm.platform.index') }}" class="btn btn-warning">
        <i class="ti ti-plus me-1"></i> Tambah Platform
    </a>
</div>
@elseif($sales->isEmpty())
<div class="alert alert-warning">
    <h4>Sales Diperlukan</h4>
    <p>Tidak ada sales aktif yang tersedia. Silakan tambah sales terlebih dahulu.</p>
    <a href="{{ route('crm.sales.create') }}" class="btn btn-warning">
        <i class="ti ti-plus me-1"></i> Tambah Sales
    </a>
</div>
@else

<form action="{{ route('crm.leads.store') }}" method="POST">
    @csrf
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-user me-2"></i>
                        Lead Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name') }}" required 
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
                                       name="phone" value="{{ old('phone') }}" required 
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
                                       name="email" value="{{ old('email') }}" 
                                       placeholder="example@email.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Optional</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Platform <span class="text-danger">*</span></label>
                                <select class="form-select @error('platform_id') is-invalid @enderror" name="platform_id" required>
                                    <option value="">Select Platform</option>
                                    @foreach($platforms as $platform)
                                    <option value="{{ $platform->id }}" {{ old('platform_id') == $platform->id ? 'selected' : '' }}>
                                        {{ $platform->platform_name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('platform_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Path Referral</label>
                                <input type="text" class="form-control @error('path_referral') is-invalid @enderror" 
                                       name="path_referral" value="{{ old('path_referral') }}" 
                                       placeholder="Example: Facebook Ads - Campaign A">
                                @error('path_referral')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Optional - Specific referral source/path</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Message</label>
                                <textarea class="form-control @error('message') is-invalid @enderror" 
                                          name="message" rows="4" 
                                          placeholder="Enter message from lead...">{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Optional - Message or inquiry from lead</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control @error('note') is-invalid @enderror" 
                                          name="note" rows="3" 
                                          placeholder="Internal notes...">{{ old('note') }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Optional - Internal notes for team</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-users me-2"></i>
                        Sales Assignment
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Assignment Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('sales_assignment') is-invalid @enderror" 
                                name="sales_assignment" required id="sales-assignment">
                            <option value="">Select Assignment Type</option>
                            <option value="auto" {{ old('sales_assignment', 'auto') == 'auto' ? 'selected' : '' }}>
                                Automatic (Rotation)
                            </option>
                            <option value="manual" {{ old('sales_assignment') == 'manual' ? 'selected' : '' }}>
                                Manual (Select Sales)
                            </option>
                        </select>
                        @error('sales_assignment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" id="manual-sales-selection" style="display: none;">
                        <label class="form-label">Select Sales <span class="text-danger">*</span></label>
                        <select class="form-select @error('sales_id') is-invalid @enderror" name="sales_id">
                            <option value="">Select Sales</option>
                            @foreach($sales as $salesItem)
                            <option value="{{ $salesItem->id }}" {{ old('sales_id') == $salesItem->id ? 'selected' : '' }}>
                                {{ $salesItem->name }} (Order: {{ $salesItem->order }})
                            </option>
                            @endforeach
                        </select>
                        @error('sales_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info">
                        <h4>Assignment Information:</h4>
                        <ul class="mb-0">
                            <li><strong>Automatic:</strong> Lead will be assigned to the next sales in rotation</li>
                            <li><strong>Manual:</strong> You choose specific sales for this lead</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-info-circle me-2"></i>
                        Active Sales
                    </h3>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($sales as $salesItem)
                        <div class="list-group-item">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xs bg-primary-lt me-2">
                                    {{ $salesItem->order }}
                                </div>
                                <div class="flex-fill">
                                    <div class="fw-bold">{{ $salesItem->name }}</div>
                                    <div class="text-secondary small">
                                        {{ $salesItem->total_leads }} leads
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <small class="form-hint mt-2">
                        Automatic rotation order follows the order above
                    </small>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-footer text-end">
                    <div class="d-flex">
                        <a href="{{ route('crm.leads.index') }}" class="btn btn-link">Cancel</a>
                        <button type="submit" class="btn btn-primary ms-auto">
                            <i class="ti ti-device-floppy me-1"></i>
                            Save Lead
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto format phone number
        const phoneInput = document.querySelector('input[name="phone"]');
        
        phoneInput.addEventListener('input', function() {
            let value = this.value.replace(/[^0-9]/g, '');
            
            // Limit to 15 digits
            if (value.length > 15) {
                value = value.substring(0, 15);
            }
            
            this.value = value;
        });

        // Sales assignment toggle
        const salesAssignment = document.getElementById('sales-assignment');
        const manualSalesSelection = document.getElementById('manual-sales-selection');
        const salesSelect = document.querySelector('select[name="sales_id"]');

        salesAssignment.addEventListener('change', function() {
            if (this.value === 'manual') {
                manualSalesSelection.style.display = 'block';
                salesSelect.required = true;
            } else {
                manualSalesSelection.style.display = 'none';
                salesSelect.required = false;
                salesSelect.value = '';
            }
        });

        // Trigger change event on page load
        salesAssignment.dispatchEvent(new Event('change'));

        // Form validation on submit
        const form = document.querySelector('form');
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

            // Check sales assignment
            const assignmentType = salesAssignment.value;
            if (assignmentType === 'manual' && !salesSelect.value) {
                e.preventDefault();
                alert('Select sales for manual assignment');
                salesSelect.focus();
                return false;
            }
        });
    });
</script>
@endpush