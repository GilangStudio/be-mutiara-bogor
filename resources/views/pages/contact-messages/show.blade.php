@extends('layouts.main')

@section('title', 'Contact Message Details')

@push('styles')
<style>
    .message-status-unread {
        border-left: 4px solid #dc3545;
        background-color: #fff5f5;
    }

    .message-status-read {
        border-left: 4px solid #ffc107;
        background-color: #fffbf0;
    }

    .message-status-replied {
        border-left: 4px solid #28a745;
        background-color: #f0f9ff;
    }

    .contact-info-card {
        border: 1px solid #e9ecef;
        border-radius: 0.375rem;
        background: #f8f9fa;
    }

    .reply-form {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 0.375rem;
        padding: 1.5rem;
    }

    .message-content {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 0.375rem;
        padding: 1.5rem;
        line-height: 1.6;
    }

    .existing-reply {
        background: #e8f5e8;
        border: 1px solid #c3e6c3;
        border-radius: 0.375rem;
        padding: 1rem;
    }

    .form-control:focus {
        border-color: #0054a6;
        box-shadow: 0 0 0 0.2rem rgba(0, 84, 166, 0.25);
    }

    .loading {
        pointer-events: none;
        opacity: 0.6;
    }
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h2 class="page-title">Message from {{ $contactMessage->name }}</h2>
        <div class="page-subtitle text-secondary">
            Received {{ $contactMessage->created_at->format('d M Y, H:i') }}
        </div>
    </div>
    <div class="btn-list">
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="ti ti-settings me-1"></i> Actions
            </button>
            <div class="dropdown-menu dropdown-menu-end">
                <form action="{{ route('contact-messages.update-status', $contactMessage) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="unread">
                    <button type="submit" class="dropdown-item">
                        <i class="ti ti-mail me-1"></i> Mark as Unread
                    </button>
                </form>
                <form action="{{ route('contact-messages.update-status', $contactMessage) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="read">
                    <button type="submit" class="dropdown-item">
                        <i class="ti ti-mail-opened me-1"></i> Mark as Read
                    </button>
                </form>
                <form action="{{ route('contact-messages.update-status', $contactMessage) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="replied">
                    <button type="submit" class="dropdown-item">
                        <i class="ti ti-message-check me-1"></i> Mark as Replied
                    </button>
                </form>
                <div class="dropdown-divider"></div>
                <button type="button" class="dropdown-item text-danger delete-btn"
                        data-id="{{ $contactMessage->id }}"
                        data-name="{{ $contactMessage->name }}"
                        data-url="{{ route('contact-messages.destroy', $contactMessage) }}">
                    <i class="ti ti-trash me-1"></i> Delete Message
                </button>
            </div>
        </div>
        <a href="{{ route('contact-messages.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Back to Messages
        </a>
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

<div class="row g-3">
    {{-- Message Content --}}
    <div class="col-lg-8">
        {{-- Message Status & Info --}}
        <div class="card message-status-{{ $contactMessage->status }}">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="ti ti-message me-2"></i>
                        Contact Message
                    </h3>
                    <span class="badge bg-{{ $contactMessage->status_badge_color }}-lt">
                        @if($contactMessage->status === 'unread')
                        <i class="ti ti-mail me-1"></i>
                        @elseif($contactMessage->status === 'read')
                        <i class="ti ti-mail-opened me-1"></i>
                        @else
                        <i class="ti ti-message-check me-1"></i>
                        @endif
                        {{ $contactMessage->status_text }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-secondary">Received Date</label>
                        <div class="fw-bold">{{ $contactMessage->created_at->format('d M Y, H:i') }}</div>
                        <small class="text-secondary">{{ $contactMessage->created_at->diffForHumans() }}</small>
                    </div>
                    @if($contactMessage->to)
                    <div class="col-md-6">
                        <label class="form-label text-secondary">Sent To</label>
                        <div>
                            <span class="badge bg-blue-lt">{{ $contactMessage->to }}</span>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary">Message Content</label>
                    <div class="message-content">
                        {{ $contactMessage->message }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Existing Reply --}}
        @if($contactMessage->reply)
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-corner-down-right me-2"></i>
                    Your Reply
                </h3>
                <div class="card-actions">
                    <small class="text-secondary">
                        Replied {{ $contactMessage->replied_at->format('d M Y, H:i') }}
                    </small>
                </div>
            </div>
            <div class="card-body">
                <div class="existing-reply">
                    {{ $contactMessage->reply }}
                </div>
            </div>
        </div>
        @endif

        {{-- Reply Form --}}
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-message-circle me-2"></i>
                    {{ $contactMessage->reply ? 'Update Reply' : 'Send Reply' }}
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('contact-messages.reply', $contactMessage) }}" method="POST" id="reply-form">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Reply Message <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('reply') is-invalid @enderror" 
                                  name="reply" rows="6" required
                                  placeholder="Type your reply here...">{{ old('reply', $contactMessage->reply) }}</textarea>
                        @error('reply')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">
                            <i class="ti ti-info-circle me-1"></i>
                            This reply will be saved in the system. You may need to send it separately via email.
                        </small>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" id="reply-btn">
                            <i class="ti ti-send me-1"></i>
                            {{ $contactMessage->reply ? 'Update Reply' : 'Save Reply' }}
                        </button>
                        @if($contactMessage->email)
                        <a href="mailto:{{ $contactMessage->email }}?subject=Re: Contact Message&body=Hi {{ $contactMessage->name }},%0D%0A%0D%0AThank you for contacting us.%0D%0A%0D%0ARegards" 
                           class="btn btn-outline-info" target="_blank">
                            <i class="ti ti-mail me-1"></i> Reply via Email
                        </a>
                        @endif
                        @if($contactMessage->whatsapp_url)
                        <a href="{{ $contactMessage->whatsapp_url }}" 
                           class="btn btn-outline-success" target="_blank">
                            <i class="ti ti-brand-whatsapp me-1"></i> Reply via WhatsApp
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Contact Information --}}
    <div class="col-lg-4">
        {{-- Contact Details --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-user me-2"></i>
                    Contact Information
                </h3>
            </div>
            <div class="card-body">
                <div class="contact-info-card p-3">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-lg bg-primary-lt me-3">
                            <i class="ti ti-user"></i>
                        </div>
                        <div>
                            <div class="fw-bold">{{ $contactMessage->name }}</div>
                            <small class="text-secondary">Contact Person</small>
                        </div>
                    </div>

                    @if($contactMessage->email)
                    <div class="mb-3">
                        <label class="form-label text-secondary mb-1">Email Address</label>
                        <div>
                            <a href="mailto:{{ $contactMessage->email }}" class="text-decoration-none">
                                <i class="ti ti-mail me-1"></i>{{ $contactMessage->email }}
                            </a>
                        </div>
                    </div>
                    @endif

                    @if($contactMessage->phone)
                    <div class="mb-3">
                        <label class="form-label text-secondary mb-1">Phone Number</label>
                        <div>
                            <a href="tel:{{ $contactMessage->phone }}" class="text-decoration-none">
                                <i class="ti ti-phone me-1"></i>{{ $contactMessage->phone }}
                            </a>
                        </div>
                    </div>
                    @endif

                    <div class="mb-0">
                        <label class="form-label text-secondary mb-1">Contact Actions</label>
                        <div class="btn-list">
                            @if($contactMessage->email)
                            <a href="mailto:{{ $contactMessage->email }}" class="btn btn-sm btn-outline-primary">
                                <i class="ti ti-mail me-1"></i> Email
                            </a>
                            @endif
                            @if($contactMessage->whatsapp_url)
                            <a href="{{ $contactMessage->whatsapp_url }}" target="_blank" class="btn btn-sm btn-outline-success">
                                <i class="ti ti-brand-whatsapp me-1"></i> WhatsApp
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Message Meta --}}
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-info-square me-2"></i>
                    Message Details
                </h3>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Status</span>
                        <span class="badge bg-{{ $contactMessage->status_badge_color }}-lt">
                            {{ $contactMessage->status_text }}
                        </span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Received</span>
                        <span>{{ $contactMessage->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Time</span>
                        <span>{{ $contactMessage->created_at->format('H:i') }}</span>
                    </div>
                    @if($contactMessage->replied_at)
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Replied</span>
                        <span>{{ $contactMessage->replied_at->format('d M Y') }}</span>
                    </div>
                    @endif
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>ID</span>
                        <span class="text-secondary">#{{ $contactMessage->id }}</span>
                    </div>
                </div>
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
                <div class="d-grid gap-2">
                    @if($contactMessage->status !== 'replied')
                    <form action="{{ route('contact-messages.update-status', $contactMessage) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="replied">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="ti ti-message-check me-1"></i> Mark as Replied
                        </button>
                    </form>
                    @endif
                    
                    @if($contactMessage->status !== 'read')
                    <form action="{{ route('contact-messages.update-status', $contactMessage) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="read">
                        <button type="submit" class="btn btn-warning w-100">
                            <i class="ti ti-mail-opened me-1"></i> Mark as Read
                        </button>
                    </form>
                    @endif

                    <hr class="my-2">

                    <button type="button" class="btn btn-danger delete-btn"
                            data-id="{{ $contactMessage->id }}"
                            data-name="{{ $contactMessage->name }}"
                            data-url="{{ route('contact-messages.destroy', $contactMessage) }}">
                        <i class="ti ti-trash me-1"></i> Delete Message
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Include Global Delete Modal --}}
@include('components.delete-modal')

@endsection

@push('scripts')
@include('components.toast')

@if(session('success'))
    <script>
        showToast('{{ session('success') }}', 'success');
    </script>
@endif
@if(session('error'))
    <script>
        showToast('{{ session('error') }}','error');
    </script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form submission with loading state
        const replyForm = document.getElementById('reply-form');
        const replyBtn = document.getElementById('reply-btn');
        
        if (replyForm) {
            replyForm.addEventListener('submit', function() {
                replyBtn.disabled = true;
                replyBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
                replyForm.classList.add('loading');
            });
        }

        // Auto-resize textarea
        const textarea = document.querySelector('textarea[name="reply"]');
        if (textarea) {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        }

        // Character counter
        if (textarea) {
            const maxLength = 1000; // Set your desired max length
            const charCounter = document.createElement('small');
            charCounter.className = 'form-hint mt-1';
            charCounter.innerHTML = `<span id="char-count">${textarea.value.length}</span>/${maxLength} characters`;
            textarea.parentNode.appendChild(charCounter);

            textarea.addEventListener('input', function() {
                const currentLength = this.value.length;
                document.getElementById('char-count').textContent = currentLength;
                
                if (currentLength > maxLength * 0.9) {
                    charCounter.classList.add('text-warning');
                } else {
                    charCounter.classList.remove('text-warning');
                }
                
                if (currentLength > maxLength) {
                    charCounter.classList.add('text-danger');
                    this.value = this.value.substring(0, maxLength);
                    document.getElementById('char-count').textContent = maxLength;
                } else {
                    charCounter.classList.remove('text-danger');
                }
            });
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + Enter to submit form
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter' && e.target.tagName === 'TEXTAREA') {
                e.preventDefault();
                replyForm.submit();
            }
        });

        // Delete confirmation with custom message
        const deleteBtn = document.querySelector('.delete-btn');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function() {
                const contactName = this.getAttribute('data-name');
                const customMessage = `Are you sure you want to delete the message from "${contactName}"? This action cannot be undone.`;
                
                // Update the modal message
                const modal = document.getElementById('delete-modal');
                const messageDiv = document.getElementById('delete-message');
                messageDiv.innerHTML = customMessage;
                
                // Update form action
                const form = document.getElementById('delete-form');
                form.action = this.getAttribute('data-url');
                
                // Show modal
                const deleteModal = new bootstrap.Modal(modal);
                deleteModal.show();
            });
        }
    });
</script>
@endpush