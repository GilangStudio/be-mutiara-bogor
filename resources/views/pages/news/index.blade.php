@extends('layouts.main')

@section('title', 'News Management')

@push('styles')
<style>
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .btn-list .btn {
        margin-right: 0.25rem;
    }
    
    .btn-list .btn:last-child {
        margin-right: 0;
    }
    
    .avatar {
        border: 2px solid #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    /* Search highlighting */
    mark.search-highlight {
        background-color: #fff3cd;
        padding: 0.1rem 0.2rem;
        border-radius: 0.25rem;
        font-weight: 600;
    }
    
    /* Custom Pagination Styles */
    .pagination {
        margin-bottom: 0;
    }
    
    .page-link {
        color: #6c757d;
        background-color: #fff;
        border: 1px solid #dee2e6;
        padding: 0.375rem 0.75rem;
        margin: 0 0.125rem;
        border-radius: 0.375rem;
        text-decoration: none;
        transition: all 0.15s ease-in-out;
    }
    
    .page-link:hover {
        color: #0054a6;
        background-color: #f8f9fa;
        border-color: #0054a6;
    }
    
    .page-item.active .page-link {
        color: #fff;
        background-color: #0054a6;
        border-color: #0054a6;
    }
    
    .page-item.disabled .page-link {
        color: #adb5bd;
        background-color: #fff;
        border-color: #dee2e6;
        cursor: not-allowed;
    }
    
    .pagination-sm .page-link {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    /* Loading state */
    .loading-overlay {
        opacity: 0.6;
        pointer-events: none;
    }
    
    /* Filter indicators */
    .filter-badge {
        font-size: 0.75rem;
    }
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h2 class="page-title">News Management</h2>
    <a href="{{ route('news.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Add News
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

{{-- Search and Filter Form --}}
<div class="col-12 mb-3">
    <form method="GET" action="{{ route('news.index') }}" id="filter-form">
        <div class="d-flex justify-content-between align-items-center gap-2">
            <div class="input-icon" style="max-width: 350px;">
                <span class="input-icon-addon">
                    <i class="ti ti-search"></i>
                </span>
                <input type="text" 
                       class="form-control" 
                       name="search" 
                       placeholder="Search in title, content, or author..." 
                       value="{{ request('search') }}" 
                       autocomplete="off" 
                       id="search-input">
            </div>
            <div class="d-flex gap-2 align-items-center">
                <select class="form-select" name="status" id="status-filter" style="min-width: 130px;">
                    <option value="">All Status</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
                @if(request('search') || request('status'))
                <a href="{{ route('news.index') }}" class="btn btn-outline-secondary" title="Clear all filters">
                    <i class="ti ti-x"></i>
                </a>
                @endif
            </div>
        </div>
        
        {{-- Active Filters Display --}}
        @if(request('search') || request('status'))
        <div class="mt-2 d-flex gap-2 align-items-center flex-wrap">
            <small class="text-secondary">Active filters:</small>
            @if(request('search'))
            <span class="badge bg-blue-lt filter-badge">
                <i class="ti ti-search me-1"></i>
                Search: "{{ request('search') }}"
            </span>
            @endif
            @if(request('status'))
            <span class="badge bg-green-lt filter-badge">
                <i class="ti ti-filter me-1"></i>
                Status: {{ ucfirst(request('status')) }}
            </span>
            @endif
        </div>
        @endif
    </form>
</div>

{{-- News Table --}}
<div class="col-12">
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive" id="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="80" class="text-center">Image</th>
                            <th>Title & Content</th>
                            <th width="150">Author</th>
                            <th width="160">Published Date</th>
                            <th width="100" class="text-center">Status</th>
                            <th width="120" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($news as $article)
                        <tr>
                            <td class="text-center">
                                @if($article->image_url)
                                <div class="avatar avatar-md rounded" 
                                     style="background-image: url('{{ $article->image_url }}'); background-size: cover; background-position: center;"
                                     title="{{ $article->title }}"></div>
                                @else
                                <div class="avatar avatar-md rounded bg-secondary-lt">
                                    <i class="ti ti-news"></i>
                                </div>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <div class="fw-bold text-dark mb-1" data-searchable="title">
                                        {{ $article->title }}
                                    </div>
                                    <div class="text-secondary small mb-2" style="line-height: 1.4;">
                                        {{ Str::limit(strip_tags($article->content), 100) }}
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <small class="text-secondary">
                                            <i class="ti ti-link me-1"></i>{{ $article->slug }}
                                        </small>
                                        <small class="text-secondary">
                                            <i class="ti ti-calendar me-1"></i>{{ $article->created_at->format('d M Y') }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div data-searchable="author">
                                    @if($article->author)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs rounded-circle bg-primary-lt me-2">
                                                <i class="ti ti-user"></i>
                                            </div>
                                            <span class="text-dark">{{ $article->author }}</span>
                                        </div>
                                    @else
                                        <span class="text-secondary">No Author</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($article->published_at)
                                    <div class="text-dark">{{ $article->published_at->format('d M Y') }}</div>
                                    <small class="text-secondary">{{ $article->published_at->format('H:i') }}</small>
                                @else
                                    <span class="text-secondary">
                                        <i class="ti ti-clock me-1"></i>Not Published
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($article->status === 'published')
                                <span class="badge bg-green-lt">
                                    <i class="ti ti-world me-1"></i>Published
                                </span>
                                @else
                                <span class="badge bg-yellow-lt">
                                    <i class="ti ti-edit me-1"></i>Draft
                                </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-list">
                                    <a href="{{ route('news.edit', $article) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Edit News">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger delete-btn"
                                            data-id="{{ $article->id }}"
                                            data-name="{{ $article->title }}"
                                            data-url="{{ route('news.destroy', $article) }}"
                                            title="Delete News">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty">
                                    <div class="empty-icon">
                                        @if(request('search') || request('status') || request('page'))
                                        <i class="ti ti-search icon icon-lg"></i>
                                        @else
                                        <i class="ti ti-news icon icon-lg"></i>
                                        @endif
                                    </div>
                                    <p class="empty-title h3">
                                        @if(request('search') || request('status') || request('page'))
                                        No news found
                                        @else
                                        No news articles yet
                                        @endif
                                    </p>
                                    <p class="empty-subtitle text-secondary">
                                        @if(request('search') || request('status') || request('page'))
                                        Try adjusting your search terms or clear the filters to see all news.
                                        @else
                                        Get started by creating your first news article.<br>
                                        Share updates, announcements, and stories with your audience.
                                        @endif
                                    </p>
                                    <div class="empty-action">
                                        @if(request('search') || request('status') || request('page'))
                                        <a href="{{ route('news.index') }}" class="btn btn-outline-secondary">
                                            <i class="ti ti-x me-1"></i> Clear Filters
                                        </a>
                                        @else
                                        <a href="{{ route('news.create') }}" class="btn btn-primary">
                                            <i class="ti ti-plus me-1"></i> Create First News
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Footer with Results Info and Pagination --}}
        @if($news->total() > 0 || request('search') || request('status'))
        <div class="card-footer d-flex align-items-center">
            <div class="text-secondary">
                @if($news->total() > 0)
                    Showing <strong>{{ $news->firstItem() }}</strong> to <strong>{{ $news->lastItem() }}</strong> 
                    of <strong>{{ $news->total() }}</strong> results
                    @if(request('search'))
                        for "<strong>{{ request('search') }}</strong>"
                    @endif
                @else
                    No results found
                    @if(request('search') || request('status'))
                        with current filters
                    @endif
                @endif
            </div>
            
            @include('components.pagination', ['paginator' => $news])
        </div>
        @endif
    </div>
</div>

{{-- Include Global Delete Modal --}}
@include('components.delete-modal')

@endsection

@push('scripts')
@include('components.toast')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Get form and input elements
        const filterForm = document.getElementById('filter-form');
        const searchInput = document.getElementById('search-input');
        const statusFilter = document.getElementById('status-filter');
        const tableContainer = document.getElementById('table-container');
        
        // Debounce function for search
        let searchTimeout;
        
        // Search input with debounce (auto-submit after 600ms)
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                submitFilter();
            }, 600);
        });
        
        // Status filter change (immediate submit)
        statusFilter.addEventListener('change', function() {
            submitFilter();
        });
        
        // Handle Enter key in search input
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimeout);
                submitFilter();
            }
        });
        
        // Submit filter function with loading state
        function submitFilter() {
            // Store current cursor position and search value
            const cursorPosition = searchInput.selectionStart;
            const searchValue = searchInput.value;
            
            // Store in sessionStorage for after page reload
            sessionStorage.setItem('searchInputFocus', 'true');
            sessionStorage.setItem('searchCursorPosition', cursorPosition);
            sessionStorage.setItem('searchValue', searchValue);
            
            // Show loading state
            showLoadingState();
            
            // Submit form
            filterForm.submit();
        }
        
        // Restore focus and cursor position after page load
        function restoreSearchFocus() {
            const shouldFocus = sessionStorage.getItem('searchInputFocus');
            const cursorPosition = sessionStorage.getItem('searchCursorPosition');
            const searchValue = sessionStorage.getItem('searchValue');
            
            if (shouldFocus === 'true' && searchInput.value === searchValue) {
                // Focus input
                searchInput.focus();
                
                // Restore cursor position
                if (cursorPosition !== null) {
                    searchInput.setSelectionRange(parseInt(cursorPosition), parseInt(cursorPosition));
                }
                
                // Clear stored values
                sessionStorage.removeItem('searchInputFocus');
                sessionStorage.removeItem('searchCursorPosition');
                sessionStorage.removeItem('searchValue');
            }
        }
        
        // Restore focus on page load
        restoreSearchFocus();
        
        // Show loading state
        function showLoadingState() {
            tableContainer.classList.add('loading-overlay');
            
            // Add loading spinner to search input
            const searchIcon = searchInput.parentElement.querySelector('i');
            const originalClass = searchIcon.className;
            searchIcon.className = 'ti ti-loader-2 animate-spin';
            
            // Reset after a delay (in case form submission fails)
            setTimeout(() => {
                tableContainer.classList.remove('loading-overlay');
                searchIcon.className = originalClass;
            }, 5000);
        }
        
        // Highlight search terms in results
        const searchTerm = '{{ request('search') }}';
        if (searchTerm) {
            highlightSearchResults(searchTerm.toLowerCase());
        }
        
        // Function to highlight search results
        function highlightSearchResults(term) {
            const searchableElements = document.querySelectorAll('[data-searchable]');
            
            searchableElements.forEach(element => {
                const text = element.textContent;
                const lowerText = text.toLowerCase();
                
                if (lowerText.includes(term)) {
                    const regex = new RegExp(`(${escapeRegExp(term)})`, 'gi');
                    element.innerHTML = text.replace(regex, '<mark class="search-highlight">$1</mark>');
                }
            });
        }
        
        // Escape special characters for regex
        function escapeRegExp(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }
        
        // Focus search input on Ctrl+K or Cmd+K
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
            }
        });
        
        // Add loading animation CSS
        const style = document.createElement('style');
        style.textContent = `
            .animate-spin {
                animation: spin 1s linear infinite;
            }
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
    });
</script>
@endpush