@extends('layouts.main')

@section('title', 'Settings')

@section('header')
<h2 class="page-title">Settings</h2>
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

<div class="row g-3">
    {{-- Profile Section --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-user me-2"></i>
                    Profile Information
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.profile.update') }}" method="POST" id="profile-form">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name', auth()->user()->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" 
                               name="username" value="{{ old('username', auth()->user()->username) }}" required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email', auth()->user()->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-check me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Password Section --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-lock me-2"></i>
                    Change Password
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.password.update') }}" method="POST" id="password-form">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Current Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                   name="current_password" id="current_password" required>
                            <button type="button" class="btn btn-outline-secondary toggle-password" 
                                    data-target="current_password">
                                <i class="ti ti-eye" id="current_password_icon"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">New Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   name="password" id="password" required>
                            <button type="button" class="btn btn-outline-secondary toggle-password" 
                                    data-target="password">
                                <i class="ti ti-eye" id="password_icon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">Minimum 8 characters</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   name="password_confirmation" id="password_confirmation" required>
                            <button type="button" class="btn btn-outline-secondary toggle-password" 
                                    data-target="password_confirmation">
                                <i class="ti ti-eye" id="password_confirmation_icon"></i>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="ti ti-key me-1"></i> Change Password
                        </button>
                        <button type="button" class="btn btn-light" id="reset-password-form">
                            <i class="ti ti-refresh me-1"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Account Info Section --}}
    {{-- <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-info-circle me-2"></i>
                    Account Information
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Last Login:</label>
                            <div class="text-muted">
                                @if(auth()->user()->last_login_at)
                                    {{ auth()->user()->last_login_at->format('d M Y, H:i') }}
                                @else
                                    Never logged in
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Account Created:</label>
                            <div class="text-muted">
                                {{ auth()->user()->created_at->format('d M Y, H:i') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Last Updated:</label>
                            <div class="text-muted">
                                {{ auth()->user()->updated_at->format('d M Y, H:i') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email Status:</label>
                            <div class="text-muted">
                                @if(auth()->user()->email_verified_at)
                                    <span class="badge bg-green-lt">Verified</span>
                                @else
                                    <span class="badge bg-yellow-lt">Not Verified</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
</div>

@endsection

@push('scripts')
@include('components.toast')
@include('components.alert')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Toggle Password Visibility
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const targetInput = document.getElementById(targetId);
                const icon = document.getElementById(targetId + '_icon');
                
                if (targetInput.type === 'password') {
                    targetInput.type = 'text';
                    icon.className = 'ti ti-eye-off';
                } else {
                    targetInput.type = 'password';
                    icon.className = 'ti ti-eye';
                }
            });
        });

        // Reset Password Form
        const resetPasswordBtn = document.getElementById('reset-password-form');
        const passwordForm = document.getElementById('password-form');
        
        resetPasswordBtn.addEventListener('click', function() {
            passwordForm.reset();
            // Reset password visibility to hidden
            document.querySelectorAll('input[type="text"]').forEach(input => {
                if (input.name.includes('password')) {
                    input.type = 'password';
                }
            });
            document.querySelectorAll('.toggle-password i').forEach(icon => {
                icon.className = 'ti ti-eye';
            });
        });

        // Form submission loading states
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                    
                    // Re-enable after 5 seconds (fallback)
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }, 5000);
                }
            });
        });

        // Real-time password confirmation validation
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password_confirmation');
        
        function validatePasswordConfirmation() {
            if (confirmPasswordInput.value && passwordInput.value !== confirmPasswordInput.value) {
                confirmPasswordInput.setCustomValidity('Passwords do not match');
                confirmPasswordInput.classList.add('is-invalid');
            } else {
                confirmPasswordInput.setCustomValidity('');
                confirmPasswordInput.classList.remove('is-invalid');
            }
        }
        
        passwordInput.addEventListener('input', validatePasswordConfirmation);
        confirmPasswordInput.addEventListener('input', validatePasswordConfirmation);

        // Password strength indicator
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = getPasswordStrength(password);
            // Bisa ditambahkan indikator kekuatan password di sini
        });

        function getPasswordStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            return strength;
        }
    });
</script>
@endpush