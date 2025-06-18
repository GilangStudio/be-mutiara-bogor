<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Contact Us - Property Development</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons@2.40.0/tabler-icons.min.css">
    
    <style>
        :root {
            --primary-color: #0054a6;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        .contact-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .contact-header {
            text-align: center;
            color: white;
            margin-bottom: 3rem;
        }

        .contact-header h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .contact-header p {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 0;
        }

        .contact-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }

        .contact-info {
            background: linear-gradient(135deg, var(--primary-color) 0%, #4a90e2 100%);
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .contact-info h3 {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .contact-info p {
            opacity: 0.9;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }

        .contact-item:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .contact-item i {
            font-size: 1.5rem;
            margin-right: 1rem;
            width: 24px;
            text-align: center;
        }

        .contact-item .info {
            flex: 1;
        }

        .contact-item .label {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-bottom: 0.25rem;
        }

        .contact-item .value {
            font-weight: 500;
            font-size: 1.1rem;
        }

        .contact-form {
            padding: 3rem;
        }

        .form-title {
            color: var(--primary-color);
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 2rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 500;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .form-label i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 84, 166, 0.25);
            background: white;
        }

        .form-select {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 84, 166, 0.25);
            background: white;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary-color) 0%, #4a90e2 100%);
            border: none;
            border-radius: 12px;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 84, 166, 0.3);
            color: white;
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: linear-gradient(135deg, var(--success-color) 0%, #20c997 100%);
            color: white;
        }

        .alert-danger {
            background: linear-gradient(135deg, var(--danger-color) 0%, #e74c3c 100%);
            color: white;
        }

        .floating-labels {
            position: relative;
        }

        .floating-labels .form-control,
        .floating-labels .form-select {
            padding-top: 1.625rem;
            padding-bottom: 0.625rem;
        }

        .floating-labels label {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            padding: 1rem;
            pointer-events: none;
            border: 2px solid transparent;
            transform-origin: 0 0;
            transition: all 0.1s ease-in-out;
            color: var(--secondary-color);
        }

        .floating-labels .form-control:focus ~ label,
        .floating-labels .form-control:not(:placeholder-shown) ~ label,
        .floating-labels .form-select:focus ~ label,
        .floating-labels .form-select:not([value=""]) ~ label {
            transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
            background: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            color: var(--primary-color);
        }

        .char-counter {
            font-size: 0.875rem;
            color: var(--secondary-color);
            text-align: right;
            margin-top: 0.25rem;
        }

        .required-asterisk {
            color: var(--danger-color);
            margin-left: 0.25rem;
        }

        @media (max-width: 768px) {
            .contact-header h1 {
                font-size: 2rem;
            }
            
            .contact-header p {
                font-size: 1rem;
            }
            
            .contact-info,
            .contact-form {
                padding: 2rem;
            }
            
            .form-title {
                font-size: 1.5rem;
            }
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .contact-card {
            animation: fadeInUp 0.6s ease-out;
        }

        .contact-item {
            animation: fadeInUp 0.6s ease-out;
        }

        .contact-item:nth-child(1) { animation-delay: 0.1s; }
        .contact-item:nth-child(2) { animation-delay: 0.2s; }
        .contact-item:nth-child(3) { animation-delay: 0.3s; }
        .contact-item:nth-child(4) { animation-delay: 0.4s; }
    </style>
</head>
<body>
    <div class="contact-container">
        <!-- Header -->
        <div class="contact-header">
            <h1>Contact Us</h1>
            <p>Get in touch with our property development team</p>
        </div>

        <!-- Main Contact Card -->
        <div class="contact-card">
            <div class="row g-0">
                <!-- Contact Information -->
                <div class="col-lg-5">
                    <div class="contact-info">
                        <h3>Let's Get in Touch</h3>
                        <p>We're here to help you find your dream property. Reach out to us through any of the channels below or send us a message.</p>
                        
                        <div class="contact-item">
                            <i class="ti ti-map-pin"></i>
                            <div class="info">
                                <div class="label">Address</div>
                                <div class="value">123 Property Street, Jakarta 12345</div>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <i class="ti ti-phone"></i>
                            <div class="info">
                                <div class="label">Phone</div>
                                <div class="value">+62 21 1234 5678</div>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <i class="ti ti-mail"></i>
                            <div class="info">
                                <div class="label">Email</div>
                                <div class="value">info@propertydeveloper.com</div>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <i class="ti ti-clock"></i>
                            <div class="info">
                                <div class="label">Working Hours</div>
                                <div class="value">Mon - Fri: 9:00 AM - 6:00 PM</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Form -->
                <div class="col-lg-7">
                    <div class="contact-form">
                        <h3 class="form-title">Send us a Message</h3>
                        
                        <!-- Alert Messages -->
                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <i class="ti ti-check me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        @if(session('error'))
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <i class="ti ti-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                        </div>
                        @endif
                        
                        <form action="{{ route('contact.store') }}" method="POST" id="contact-form">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="ti ti-user"></i>
                                            Full Name<span class="required-asterisk">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               name="name" 
                                               value="{{ old('name') }}" 
                                               required 
                                               placeholder="Enter your full name">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="ti ti-mail"></i>
                                            Email Address<span class="required-asterisk">*</span>
                                        </label>
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               name="email" 
                                               value="{{ old('email') }}" 
                                               required 
                                               placeholder="Enter your email address">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="ti ti-phone"></i>
                                            Phone Number
                                        </label>
                                        <input type="tel" 
                                               class="form-control @error('phone') is-invalid @enderror" 
                                               name="phone" 
                                               value="{{ old('phone') }}" 
                                               placeholder="Enter your phone number">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="ti ti-building"></i>
                                            Department
                                        </label>
                                        <select class="form-select @error('to') is-invalid @enderror" name="to">
                                            <option value="">Select Department</option>
                                            <option value="General Inquiry" {{ old('to') == 'General Inquiry' ? 'selected' : '' }}>General Inquiry</option>
                                            <option value="Sales" {{ old('to') == 'Sales' ? 'selected' : '' }}>Sales</option>
                                            <option value="Customer Service" {{ old('to') == 'Customer Service' ? 'selected' : '' }}>Customer Service</option>
                                            <option value="Technical Support" {{ old('to') == 'Technical Support' ? 'selected' : '' }}>Technical Support</option>
                                            <option value="Partnership" {{ old('to') == 'Partnership' ? 'selected' : '' }}>Partnership</option>
                                        </select>
                                        @error('to')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="ti ti-message"></i>
                                    Message<span class="required-asterisk">*</span>
                                </label>
                                <textarea class="form-control @error('message') is-invalid @enderror" 
                                          name="message" 
                                          rows="6" 
                                          required 
                                          placeholder="Enter your message here..." 
                                          maxlength="1000">{{ old('message') }}</textarea>
                                <div class="char-counter">
                                    <span id="char-count">{{ strlen(old('message', '')) }}</span>/1000 characters
                                </div>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <button type="submit" class="btn-submit" id="submit-btn">
                                <i class="ti ti-send me-2"></i>
                                Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back to Admin Link -->
        <div class="text-center mt-4">
            <a href="{{ route('dashboard') }}" class="text-white text-decoration-none">
                <i class="ti ti-arrow-left me-1"></i>
                Back to Admin Dashboard
            </a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Character counter for message textarea
            const messageTextarea = document.querySelector('textarea[name="message"]');
            const charCount = document.getElementById('char-count');
            
            if (messageTextarea && charCount) {
                messageTextarea.addEventListener('input', function() {
                    const currentLength = this.value.length;
                    charCount.textContent = currentLength;
                    
                    const counter = charCount.parentElement;
                    if (currentLength > 900) {
                        counter.style.color = '#dc3545';
                    } else if (currentLength > 800) {
                        counter.style.color = '#ffc107';
                    } else {
                        counter.style.color = '#6c757d';
                    }
                });
            }

            // Form submission with loading state
            const form = document.getElementById('contact-form');
            const submitBtn = document.getElementById('submit-btn');
            
            if (form && submitBtn) {
                form.addEventListener('submit', function() {
                    submitBtn.classList.add('loading');
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';
                    submitBtn.disabled = true;
                });
            }

            // Phone number formatting (Indonesian format)
            const phoneInput = document.querySelector('input[name="phone"]');
            if (phoneInput) {
                phoneInput.addEventListener('input', function() {
                    let value = this.value.replace(/\D/g, '');
                    
                    if (value.startsWith('62')) {
                        value = '+' + value;
                    } else if (value.startsWith('0')) {
                        value = '+62' + value.substring(1);
                    } else if (value.length > 0 && !value.startsWith('+62')) {
                        value = '+62' + value;
                    }
                    
                    this.value = value;
                });
            }

            // Auto-dismiss alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });

            // Form validation enhancement
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.hasAttribute('required') && !this.value.trim()) {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                    }
                });

                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid') && this.value.trim()) {
                        this.classList.remove('is-invalid');
                    }
                });
            });

            // Email validation
            const emailInput = document.querySelector('input[name="email"]');
            if (emailInput) {
                emailInput.addEventListener('blur', function() {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (this.value && !emailRegex.test(this.value)) {
                        this.classList.add('is-invalid');
                        this.setCustomValidity('Please enter a valid email address');
                    } else {
                        this.classList.remove('is-invalid');
                        this.setCustomValidity('');
                    }
                });
            }
        });
    </script>
</body>
</html>