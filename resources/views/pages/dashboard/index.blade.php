@extends('layouts.main')

@section('title', 'Dashboard')

@section('header')
<h2 class="page-title">Dashboard</h2>
@endsection

@section('content')
{{-- Cards Statistik Utama --}}
<div class="col-sm-6 col-lg-3">
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="subheader">Total Proyek</div>
                {{-- <div class="ms-auto lh-1">
                    <div class="dropdown">
                        <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Bulan Ini
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item active" href="#">Bulan Ini</a>
                            <a class="dropdown-item" href="#">3 Bulan Terakhir</a>
                            <a class="dropdown-item" href="#">Tahun Ini</a>
                        </div>
                    </div>
                </div> --}}
            </div>
            <div class="d-flex align-items-baseline">
                <div class="h1 mb-0 me-2">{{ \App\Models\Project::count() }}</div>
                {{-- <div class="me-auto">
                    <span class="text-green d-inline-flex align-items-center lh-1">
                        12%
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon ms-1 icon-inline icon-sm">
                            <path d="M3 17l6 -6l4 4l8 -8" />
                            <path d="M14 7l7 0l0 7" />
                        </svg>
                    </span>
                </div> --}}
            </div>
            <div class="text-secondary mt-1">Proyek aktif tersedia</div>
        </div>
    </div>
</div>

<div class="col-sm-6 col-lg-3">
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="subheader">Unit Tersedia</div>
                {{-- <div class="ms-auto lh-1">
                    <span class="text-green d-inline-flex align-items-center lh-1">
                        8%
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon ms-1 icon-inline icon-sm">
                            <path d="M3 17l6 -6l4 4l8 -8" />
                            <path d="M14 7l7 0l0 7" />
                        </svg>
                    </span>
                </div> --}}
            </div>
            <div class="h1 mb-3">{{ \App\Models\Unit::count() }}</div>
            <div class="d-flex mb-2">
                <div>Unit aktif</div>
                <div class="ms-auto">
                    <span class="text-green d-inline-flex align-items-center lh-1">
                        {{ \App\Models\Unit::where('is_active', true)->count() }}
                    </span>
                </div>
            </div>
            <div class="progress progress-sm">
                <div class="progress-bar bg-primary" 
                     style="width: {{ \App\Models\Unit::count() > 0 ? (\App\Models\Unit::where('is_active', true)->count() / \App\Models\Unit::count()) * 100 : 0 }}%" 
                     role="progressbar">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-sm-6 col-lg-3">
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="subheader">Pesan Kontak</div>
                {{-- <div class="ms-auto lh-1">
                    <div class="dropdown">
                        <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown">
                            7 Hari Terakhir
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item active" href="#">7 Hari Terakhir</a>
                            <a class="dropdown-item" href="#">30 Hari Terakhir</a>
                            <a class="dropdown-item" href="#">3 Bulan Terakhir</a>
                        </div>
                    </div>
                </div> --}}
            </div>
            <div class="d-flex align-items-baseline">
                <div class="h1 mb-0 me-2">{{ \App\Models\ContactMessage::count() }}</div>
                <div class="me-auto">
                    <span class="text-yellow d-inline-flex align-items-center lh-1">
                        0%
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon ms-1 icon-inline icon-sm">
                            <path d="M5 12l14 0" />
                        </svg>
                    </span>
                </div>
            </div>
            <div class="text-secondary mt-1">{{ \App\Models\ContactMessage::where('status', 'unread')->count() }} belum dibaca</div>
        </div>
    </div>
</div>

<div class="col-sm-6 col-lg-3">
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="subheader">Berita</div>
                {{-- <div class="ms-auto lh-1">
                    <span class="text-green d-inline-flex align-items-center lh-1">
                        4%
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon ms-1 icon-inline icon-sm">
                            <path d="M3 17l6 -6l4 4l8 -8" />
                            <path d="M14 7l7 0l0 7" />
                        </svg>
                    </span>
                </div> --}}
            </div>
            <div class="d-flex align-items-baseline">
                <div class="h1 mb-3 me-2">{{ \App\Models\News::count() }}</div>
                <div class="me-auto">
                    <span class="text-green d-inline-flex align-items-center lh-1">
                        {{ \App\Models\News::where('status', 'published')->count() }}
                    </span>
                </div>
            </div>
            <div class="text-secondary">Artikel dipublikasi</div>
        </div>
    </div>
</div>

{{-- Cards Statistik Tambahan --}}
<div class="col-12">
    <div class="row row-cards">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-primary text-white avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                    <path d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                                    <polyline points="3,7 12,13 21,7" />
                                </svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">{{ \App\Models\ContactMessage::whereDate('created_at', today())->count() }} Pesan Hari Ini</div>
                            <div class="text-secondary">{{ \App\Models\ContactMessage::where('status', 'unread')->whereDate('created_at', today())->count() }} belum dibaca</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-green text-white avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                    <polyline points="9,22 9,12 15,12 15,22" />
                                </svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">{{ \App\Models\Accessibility::count() }} Accessibility</div>
                            <div class="text-secondary">{{ \App\Models\Accessibility::where('is_active', true)->count() }} aktif</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-blue text-white avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                    <circle cx="12" cy="12" r="3" />
                                    <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z" />
                                </svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">{{ \App\Models\ProjectCategory::count() }} Kategori</div>
                            <div class="text-secondary">{{ \App\Models\ProjectCategory::where('is_active', true)->count() }} aktif</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-yellow text-white avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3" />
                                    <path d="M12 17h.01" />
                                </svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">{{ \App\Models\Faqs::count() }} FAQ</div>
                            <div class="text-secondary">{{ \App\Models\Faqs::where('is_active', true)->count() }} aktif</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Proyek dan Berita Terbaru --}}
<div class="col-lg-4">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Proyek Terbaru</h3>
        </div>
        <div class="card-body">
            @php
                $recentProjects = \App\Models\Project::with('category')->orderBy('created_at', 'desc')->limit(5)->get();
            @endphp
            
            @if($recentProjects->count() > 0)
                @foreach($recentProjects as $project)
                <div class="row align-items-center mb-3">
                    <div class="col-auto">
                        @if($project->main_image_url)
                            <span class="avatar" style="background-image: url({{ $project->main_image_url }})"></span>
                        @else
                            <span class="avatar bg-primary-lt">
                                {{ substr($project->name, 0, 2) }}
                            </span>
                        @endif
                    </div>
                    <div class="col">
                        <div class="fw-bold">{{ $project->name }}</div>
                        <div class="text-secondary">
                            <span class="badge bg-blue-lt">{{ $project->category->name ?? 'Kategori' }}</span>
                            <span class="badge bg-{{ $project->is_active ? 'green' : 'red' }}-lt">
                                {{ $project->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="text-secondary">{{ $project->created_at->format('d M') }}</div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center py-4">
                    <div class="mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="icon icon-lg text-muted">
                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <p class="empty-title h5">Belum ada proyek</p>
                    <p class="empty-subtitle text-secondary">
                        Tambahkan proyek pertama Anda
                    </p>
                </div>
            @endif
        </div>
        @if($recentProjects->count() > 0)
        <div class="card-footer">
            <a href="{{ route('development.project.index') }}" class="btn btn-primary w-100">
                Lihat Semua Proyek
            </a>
        </div>
        @endif
    </div>
</div>

<div class="col-lg-4">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Berita Terbaru</h3>
        </div>
        <div class="card-body">
            @php
                $recentNews = \App\Models\News::orderBy('created_at', 'desc')->limit(5)->get();
            @endphp
            
            @if($recentNews->count() > 0)
                @foreach($recentNews as $news)
                <div class="row align-items-center mb-3">
                    <div class="col-auto">
                        @if($news->image_url)
                            <span class="avatar" style="background-image: url({{ $news->image_url }})"></span>
                        @else
                            <span class="avatar bg-yellow-lt">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                                    <polyline points="14,2 14,8 20,8" />
                                    <line x1="16" y1="13" x2="8" y2="13" />
                                    <line x1="16" y1="17" x2="8" y2="17" />
                                    <polyline points="10,9 9,9 8,9" />
                                </svg>
                            </span>
                        @endif
                    </div>
                    <div class="col">
                        <div class="fw-bold">{{ Str::limit($news->title, 40) }}</div>
                        <div class="text-secondary">
                            <span class="badge bg-{{ $news->status === 'published' ? 'green' : 'yellow' }}-lt">
                                {{ $news->status === 'published' ? 'Dipublikasi' : 'Draft' }}
                            </span>
                            @if($news->author)
                                <small>oleh {{ $news->author }}</small>
                            @endif
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="text-secondary">{{ $news->created_at->format('d M') }}</div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center py-4">
                    <div class="mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="icon icon-lg text-muted">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                            <polyline points="14,2 14,8 20,8" />
                        </svg>
                    </div>
                    <p class="empty-title h5">Belum ada berita</p>
                    <p class="empty-subtitle text-secondary">
                        Tambahkan artikel berita pertama
                    </p>
                </div>
            @endif
        </div>
        @if($recentNews->count() > 0)
        <div class="card-footer">
            <a href="{{ route('news.index') }}" class="btn btn-primary w-100">
                Lihat Semua Berita
            </a>
        </div>
        @endif
    </div>
</div>

<div class="col-lg-4">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Pesan Kontak Terbaru</h3>
        </div>
        <div class="card-body card-body-scrollable" style="height: 280px">
            @php
                $recentMessages = \App\Models\ContactMessage::orderBy('created_at', 'desc')->limit(10)->get();
            @endphp
            
            @if($recentMessages->count() > 0)
                <div class="divide-y">
                    @foreach($recentMessages as $message)
                    <div>
                        <div class="row">
                            <div class="col-auto">
                                <span class="avatar avatar-sm">{{ substr($message->name, 0, 2) }}</span>
                            </div>
                            <div class="col">
                                <div class="text-truncate">
                                    <strong>{{ $message->name }}</strong> mengirim pesan baru
                                </div>
                                <div class="text-secondary">{{ $message->created_at->diffForHumans() }}</div>
                            </div>
                            <div class="col-auto align-self-center">
                                <div class="badge bg-{{ $message->status === 'unread' ? 'red' : ($message->status === 'read' ? 'yellow' : 'green') }}"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <div class="mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="icon icon-lg text-muted">
                            <path d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                            <polyline points="3,7 12,13 21,7" />
                        </svg>
                    </div>
                    <p class="empty-title h5">Belum ada pesan</p>
                    <p class="empty-subtitle text-secondary">
                        Pesan kontak akan muncul di sini
                    </p>
                </div>
            @endif
        </div>
        @if($recentMessages->count() > 0)
        <div class="card-footer">
            <a href="{{ route('contact-messages.index') }}" class="btn btn-primary w-100">
                Lihat Semua Pesan
            </a>
        </div>
        @endif
    </div>
</div>

{{-- Quick Actions --}}
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Aksi Cepat</h3>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6 col-lg-3">
                    <a href="{{ route('development.project.create') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 100px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-lg mb-2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            <polyline points="9,22 9,12 15,12 15,22" />
                        </svg>
                        <div class="fw-bold">Tambah Proyek</div>
                        <small class="text-secondary">Buat proyek baru</small>
                    </a>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <a href="{{ route('news.create') }}" class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 100px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-lg mb-2">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                            <polyline points="14,2 14,8 20,8" />
                            <line x1="16" y1="13" x2="8" y2="13" />
                            <line x1="16" y1="17" x2="8" y2="17" />
                        </svg>
                        <div class="fw-bold">Tulis Berita</div>
                        <small class="text-secondary">Buat artikel baru</small>
                    </a>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <a href="{{ route('contact-messages.index') }}" class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 100px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-lg mb-2">
                            <path d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                            <polyline points="3,7 12,13 21,7" />
                        </svg>
                        <div class="fw-bold">Pesan Kontak</div>
                        <small class="text-secondary">Lihat semua pesan</small>
                    </a>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <a href="{{ route('accessibilities.create') }}" class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 100px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-lg mb-2">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2" />
                            <line x1="8" y1="21" x2="16" y2="21" />
                            <line x1="12" y1="17" x2="12" y2="21" />
                        </svg>
                        <div class="fw-bold">Tambah Accessibility</div>
                        <small class="text-secondary">Kelola Accessibility</small>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Status Sistem --}}


<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Aktivitas Terbaru</h3>
        </div>
        <div class="card-body card-body-scrollable" style="height: 240px">
            <div class="divide-y">
                @php
                    $activities = collect();
                    
                    // Ambil aktivitas terbaru dari berbagai model
                    $recentProjects = \App\Models\Project::orderBy('created_at', 'desc')->limit(3)->get()->map(function($item) {
                        return [
                            'type' => 'project',
                            'title' => 'Proyek "' . $item->name . '" ditambahkan',
                            'time' => $item->created_at,
                            'icon' => 'home',
                            'color' => 'blue'
                        ];
                    });
                    
                    $recentNews = \App\Models\News::orderBy('created_at', 'desc')->limit(3)->get()->map(function($item) {
                        return [
                            'type' => 'news',
                            'title' => 'Berita "' . Str::limit($item->title, 30) . '" ' . ($item->status === 'published' ? 'dipublikasi' : 'dibuat'),
                            'time' => $item->created_at,
                            'icon' => 'news',
                            'color' => 'green'
                        ];
                    });
                    
                    $recentContacts = \App\Models\ContactMessage::orderBy('created_at', 'desc')->limit(3)->get()->map(function($item) {
                        return [
                            'type' => 'contact',
                            'title' => 'Pesan baru dari ' . $item->name,
                            'time' => $item->created_at,
                            'icon' => 'mail',
                            'color' => 'yellow'
                        ];
                    });
                    
                    $activities = $activities->merge($recentProjects)->merge($recentNews)->merge($recentContacts);
                    $activities = $activities->sortByDesc('time')->take(8);
                @endphp
                
                @if($activities->count() > 0)
                    @foreach($activities as $activity)
                    <div>
                        <div class="row">
                            <div class="col-auto">
                                <span class="avatar avatar-sm bg-{{ $activity['color'] }}-lt">
                                    @if($activity['icon'] === 'home')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                        </svg>
                                    @elseif($activity['icon'] === 'news')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                            <path d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                                        </svg>
                                    @endif
                                </span>
                            </div>
                            <div class="col">
                                <div class="text-truncate">{{ $activity['title'] }}</div>
                                <div class="text-secondary">{{ $activity['time']->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="icon icon-lg text-muted">
                                <path d="M12 2v6l3-3" />
                                <path d="M9 13h6" />
                                <path d="M9 17h3" />
                                <path d="M6 2a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z" />
                            </svg>
                        </div>
                        <p class="empty-title h5">Belum ada aktivitas</p>
                        <p class="empty-subtitle text-secondary">
                            Aktivitas akan muncul di sini
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Ringkasan Konten --}}
{{-- <div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Ringkasan Konten Website</h3>
        </div>
        <div class="table-responsive">
            <table class="table card-table table-vcenter">
                <thead>
                    <tr>
                        <th>Jenis Konten</th>
                        <th>Total</th>
                        <th>Aktif</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm bg-blue-lt me-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                    </svg>
                                </span>
                                <div class="font-weight-medium">Proyek</div>
                            </div>
                        </td>
                        <td>{{ \App\Models\Project::count() }}</td>
                        <td>{{ \App\Models\Project::where('is_active', true)->count() }}</td>
                        <td>
                            @if(\App\Models\Project::count() > 0)
                                <span class="badge bg-green">Tersedia</span>
                            @else
                                <span class="badge bg-red">Kosong</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('development.project.index') }}" class="btn btn-sm btn-outline-primary">
                                Kelola
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm bg-green-lt me-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                                    </svg>
                                </span>
                                <div class="font-weight-medium">Berita</div>
                            </div>
                        </td>
                        <td>{{ \App\Models\News::count() }}</td>
                        <td>{{ \App\Models\News::where('status', 'published')->count() }}</td>
                        <td>
                            @if(\App\Models\News::where('status', 'published')->count() > 0)
                                <span class="badge bg-green">Dipublikasi</span>
                            @else
                                <span class="badge bg-yellow">Perlu Konten</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('news.index') }}" class="btn btn-sm btn-outline-primary">
                                Kelola
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm bg-yellow-lt me-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2" />
                                        <line x1="8" y1="21" x2="16" y2="21" />
                                        <line x1="12" y1="17" x2="12" y2="21" />
                                    </svg>
                                </span>
                                <div class="font-weight-medium">Accessibility</div>
                            </div>
                        </td>
                        <td>{{ \App\Models\Facility::count() }}</td>
                        <td>{{ \App\Models\Facility::where('is_active', true)->count() }}</td>
                        <td>
                            @if(\App\Models\Facility::where('is_active', true)->count() > 0)
                                <span class="badge bg-green">Aktif</span>
                            @else
                                <span class="badge bg-red">Tidak Aktif</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('accessibilities.index') }}" class="btn btn-sm btn-outline-primary">
                                Kelola
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm bg-purple-lt me-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                        <circle cx="12" cy="12" r="10" />
                                        <path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3" />
                                        <path d="M12 17h.01" />
                                    </svg>
                                </span>
                                <div class="font-weight-medium">FAQ</div>
                            </div>
                        </td>
                        <td>{{ \App\Models\Faqs::count() }}</td>
                        <td>{{ \App\Models\Faqs::where('is_active', true)->count() }}</td>
                        <td>
                            @if(\App\Models\Faqs::where('is_active', true)->count() > 0)
                                <span class="badge bg-green">Aktif</span>
                            @else
                                <span class="badge bg-red">Tidak Aktif</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('faqs.index') }}" class="btn btn-sm btn-outline-primary">
                                Kelola
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm bg-cyan-lt me-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
                                        <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z" />
                                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" />
                                    </svg>
                                </span>
                                <div class="font-weight-medium">Halaman Konsep</div>
                            </div>
                        </td>
                        <td>{{ \App\Models\ConceptPage::count() }}</td>
                        <td>{{ \App\Models\ConceptPage::count() > 0 ? '1' : '0' }}</td>
                        <td>
                            @if(\App\Models\ConceptPage::count() > 0)
                                <span class="badge bg-green">Tersedia</span>
                            @else
                                <span class="badge bg-red">Belum Ada</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('concept.index') }}" class="btn btn-sm btn-outline-primary">
                                Kelola
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm bg-orange-lt me-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                                        <circle cx="12" cy="7" r="4" />
                                    </svg>
                                </span>
                                <div class="font-weight-medium">Profil Perusahaan</div>
                            </div>
                        </td>
                        <td>{{ \App\Models\CompanyProfile::count() }}</td>
                        <td>{{ \App\Models\CompanyProfile::count() > 0 ? '1' : '0' }}</td>
                        <td>
                            @if(\App\Models\CompanyProfile::count() > 0)
                                <span class="badge bg-green">Lengkap</span>
                            @else
                                <span class="badge bg-red">Belum Diisi</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('company-profile.index') }}" class="btn btn-sm btn-outline-primary">
                                Kelola
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div> --}}

{{-- Informasi Tambahan --}}
<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informasi Sistem</h3>
        </div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-6">
                    <div class="small text-secondary">PHP Version</div>
                    <div class="fw-bold">{{ PHP_VERSION }}</div>
                </div>
                <div class="col-6">
                    <div class="small text-secondary">Laravel</div>
                    <div class="fw-bold">{{ app()->version() }}</div>
                </div>
                <div class="col-6">
                    <div class="small text-secondary">Environment</div>
                    <div class="fw-bold">{{ app()->environment() }}</div>
                </div>
                <div class="col-6">
                    <div class="small text-secondary">Debug</div>
                    <div class="fw-bold">{{ config('app.debug') ? 'On' : 'Off' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection