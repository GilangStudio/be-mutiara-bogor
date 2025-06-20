@extends('layouts.main')

@section('title', 'Tambah Sales')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h2 class="page-title">Add New Sales</h2>
    <a href="{{ route('crm.sales.index') }}" class="btn btn-outline-secondary">
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

<form action="{{ route('crm.sales.store') }}" method="POST">
    @csrf
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-user me-2"></i>
                        Sales Information
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
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email') }}" required 
                                       placeholder="example@email.com">
                                @error('email')
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
                                <small class="form-hint">Last 6 digits will be used as default password</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Order</label>
                                <input type="number" class="form-control" value="{{ $maxOrder }}" readonly>
                                <small class="form-hint">Order will be set automatically</small>
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
                        <i class="ti ti-info-circle me-2"></i>
                        Account Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h4>Account to be created:</h4>
                        <ul class="mb-0">
                            <li><strong>Username:</strong> Will be generated automatically from name</li>
                            <li><strong>Password:</strong> Last 6 digits of phone number</li>
                            <li><strong>Role:</strong> Sales</li>
                            <li><strong>Status:</strong> Active</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h4>Important Notes:</h4>
                        <ul class="mb-0">
                            <li>Sales will automatically get the next order</li>
                            <li>Active sales will be included in automatic lead rotation</li>
                            <li>Make sure phone number and email are not registered</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-footer text-end">
                    <div class="d-flex">
                        <a href="{{ route('crm.sales.index') }}" class="btn btn-link">Cancel</a>
                        <button type="submit" class="btn btn-primary ms-auto">
                            <i class="ti ti-device-floppy me-1"></i>
                            Save Sales
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
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
        });
    });
</script>
@endpush