@extends('layouts.main')

@section('title', 'Platform Management')

@section('header')
<h2 class="page-title">Platform Management</h2>
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
    <div class="d-flex justify-content-between align-items-center mb-3">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-platform">
            <i class="ti ti-plus me-1"></i> Add Platform
        </button>
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
                            <th>Platform Name</th>
                            <th width="120" class="text-center">Total Leads</th>
                            <th width="150" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($platforms as $index => $platform)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold">{{ $platform->platform_name }}</div>
                                {{-- <div class="text-secondary small">
                                    Created: {{ $platform->created_at->format('d M Y') }}
                                </div> --}}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-blue-lt">{{ $platform->leads_count ?? 0 }}</span>
                            </td>
                            <td class="text-end">
                                <div class="btn-list justify-content-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#edit-platform"
                                            data-id="{{ $platform->id }}"
                                            data-name="{{ $platform->platform_name }}">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                            data-id="{{ $platform->id }}"
                                            data-name="{{ $platform->platform_name }}"
                                            data-url="{{ route('crm.platform.destroy', $platform) }}">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <div class="empty">
                                    <div class="empty-icon">
                                        <i class="ti ti-device-desktop icon icon-lg"></i>
                                    </div>
                                    <p class="empty-title h3">No platforms yet</p>
                                    <p class="empty-subtitle text-secondary">
                                        Add your first platform to start managing leads
                                    </p>
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

{{-- Modal Add Platform --}}
<div class="modal modal-blur fade" id="add-platform" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form class="modal-content" action="{{ route('crm.platform.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add New Platform</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Platform Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('platform_name') is-invalid @enderror" 
                           name="platform_name" value="{{ old('platform_name') }}" required 
                           placeholder="Example: Website, WhatsApp, Instagram, etc">
                    @error('platform_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-hint">Enter the platform name as the source of leads</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary ms-auto">Save</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Platform --}}
<div class="modal modal-blur fade" id="edit-platform" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form class="modal-content" id="edit-form" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Platform</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Platform Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="edit-platform-name" name="platform_name" required 
                           placeholder="Example: Website, WhatsApp, Instagram, etc">
                    <small class="form-hint">Enter the platform name as the source of leads</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary ms-auto">Update</button>
            </div>
        </form>
    </div>
</div>

{{-- Include Global Delete Modal --}}
@include('components.delete-modal')

@endsection

@push('scripts')
@include('components.toast')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Handle Edit Modal
        const editModal = document.getElementById('edit-platform');
        editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');

            // Update form action
            const form = document.getElementById('edit-form');
            form.action = `{{ url('crm/platform') }}/${id}`;

            // Fill form fields
            document.getElementById('edit-platform-name').value = name;
        });

        // Reset Add Modal when opened
        const addModal = document.getElementById('add-platform');
        addModal.addEventListener('show.bs.modal', function (event) {
            const form = addModal.querySelector('form');
            form.reset();
        });
    });
</script>
@endpush