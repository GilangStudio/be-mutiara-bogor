@extends('layouts.main')

@section('title', 'Edit Sales')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h2 class="page-title">Edit Sales</h2>
        <div class="page-subtitle">{{ $sales->name }}</div>
    </div>
    <a href="{{ route('crm.sales.index') }}" class="btn btn-outline-secondary">
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

<form action="{{ route('crm.sales.update', $sales) }}" method="POST">
    @csrf
    @method('PUT')
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
                                       name="name" value="{{ old('name', $sales->name) }}" required 
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
                                       name="email" value="{{ old('email', $sales->email) }}" required 
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
                                       name="phone" value="{{ old('phone', $sales->phone) }}" required 
                                       placeholder="08xxxxxxxxxx">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                       name="username" value="{{ old('username', $sales->user->username) }}" required 
                                       placeholder="Username for login">
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       name="password" placeholder="Leave empty if you don't want to change">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Leave empty if you don't want to change password</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Order</label>
                                <input type="number" class="form-control" value="{{ $sales->order }}" readonly>
                                <small class="form-hint">Order in automatic lead rotation</small>
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
                        Status Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Current Status</label>
                        <div>
                            <span class="badge bg-{{ $sales->status_badge_color }}-lt fs-5">
                                {{ $sales->status_text }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Total Leads</label>
                        <div class="h4 text-primary">{{ $sales->total_leads }}</div>
                    </div>

                    <div class="alert alert-info d-block">
                        <h4>Quick Actions:</h4>
                        <div class="btn-list">
                            @if($sales->phone)
                                <a href="{{ $sales->whatsapp_url }}" target="_blank" class="btn btn-success btn-sm">
                                    <i class="ti ti-brand-whatsapp me-1"></i> WhatsApp
                                </a>
                            @endif
                            @if($sales->email)
                                <a href="mailto:{{ $sales->email }}" class="btn btn-info btn-sm">
                                    <i class="ti ti-mail me-1"></i> Email
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-clock me-2"></i>
                        Time Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-2">
                                <small class="text-secondary">Created:</small>
                                <div>{{ $sales->created_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-2">
                                <small class="text-secondary">Last Updated:</small>
                                <div>{{ $sales->updated_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                        @if($sales->createdBy)
                        <div class="col-12">
                            <div class="mb-0">
                                <small class="text-secondary">Created by:</small>
                                <div>{{ $sales->createdBy->name }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-footer bg-transparent text-end">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-secondary">
                                <i class="ti ti-clock me-1"></i>
                                Last saved: {{ $sales->updated_at->format('d M Y, H:i') }}
                            </small>
                        </div>
                        <div class="btn-list">
                            <a href="{{ route('crm.sales.index') }}" class="btn btn-link">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-1"></i> 
                                Update Sales
                            </button>
                        </div>
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