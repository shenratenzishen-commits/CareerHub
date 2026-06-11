@extends('layouts.app')
@section('title', 'Internships')

@section('content')

<div class="page-header bg-primary-soft py-4">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('landing') }}" class="text-decoration-none text-primary">Home</a></li>
                <li class="breadcrumb-item active">Internships</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-0"><i class="fas fa-graduation-cap me-2 text-primary"></i>Internship Listings</h4>
                <p class="text-muted mb-0 small">{{ count($internships) }} internships — click any card to view details</p>
            </div>
            <a href="{{ route('jobs') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="fas fa-briefcase me-1"></i>View Full-time Jobs
            </a>
        </div>
    </div>
</div>

<div class="container py-5">

    {{-- Search --}}
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
        <form action="{{ route('internships') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by company, title, or skill...">
                </div>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill flex-grow-1">Search</button>
                <a href="{{ route('internships') }}" class="btn btn-outline-secondary rounded-pill px-3">Reset</a>
            </div>
        </form>
    </div>

    {{-- Filter Chips --}}
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('internships') }}" class="badge bg-primary text-white rounded-pill px-3 py-2 text-decoration-none">All</a>
        @foreach(['On-site','Hybrid','Remote'] as $s)
        <a href="{{ route('internships') }}?setup={{ $s }}" class="badge bg-light text-muted rounded-pill px-3 py-2 text-decoration-none hover-tag">
            <i class="fas fa-{{ $s === 'Remote' ? 'wifi' : ($s === 'Hybrid' ? 'code-branch' : 'building') }} me-1"></i>{{ $s }}
        </a>
        @endforeach
    </div>

    {{-- Cards --}}
    <div class="row g-4">
        @foreach($internships as $intern)
        <div class="col-md-6 col-lg-4">
            <div class="position-relative h-100">

                {{-- FULL CARD IS AN ANCHOR TAG --}}
                <a href="{{ route('internship.show', $intern['slug']) }}"
                   class="text-decoration-none d-block h-100 card-link-wrap">
                    <div class="card border-0 shadow-sm rounded-4 h-100 ch-job-card">
                        <div class="card-body p-4 d-flex flex-column">

                            <div class="d-flex align-items-start mb-3" style="padding-right:36px">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="company-logo rounded-3 d-flex align-items-center justify-content-center text-white fw-bold logo-{{ $intern['logo'] }}">
                                        {{ strtoupper(substr($intern['company'], 0, 1)) }}
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark ch-card-title">{{ $intern['title'] }}</h6>
                                        <small class="text-muted">{{ $intern['company'] }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge rounded-pill bg-success-soft text-success">
                                    <i class="fas fa-graduation-cap me-1"></i>Internship
                                </span>
                                <span class="badge rounded-pill {{ $intern['setup'] === 'Remote' ? 'bg-info-soft text-info' : ($intern['setup'] === 'Hybrid' ? 'bg-warning-soft text-warning' : 'bg-light text-muted') }}">
                                    <i class="fas fa-{{ $intern['setup'] === 'Remote' ? 'wifi' : ($intern['setup'] === 'Hybrid' ? 'code-branch' : 'building') }} me-1"></i>
                                    {{ $intern['setup'] }}
                                </span>
                                <span class="badge rounded-pill bg-light text-muted">
                                    <i class="fas fa-hourglass-half me-1"></i>{{ $intern['duration'] }}
                                </span>
                            </div>

                            <div class="d-flex flex-wrap gap-3 text-muted small mb-3">
                                <span><i class="fas fa-map-marker-alt me-1 text-primary"></i>{{ $intern['location'] }}</span>
                                <span><i class="fas fa-clock me-1 text-warning"></i>{{ $intern['posted'] }}</span>
                            </div>

                            <div class="d-flex flex-wrap gap-1 mb-3">
                                @foreach(array_slice($intern['tags'], 0, 3) as $tag)
                                <span class="badge bg-light text-muted rounded-pill px-2 py-1" style="font-size:11px">{{ $tag }}</span>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                                <span class="fw-bold text-primary">{{ $intern['salary'] }}</span>
                                <span class="btn btn-primary btn-sm rounded-pill px-3">
                                    View Details <i class="fas fa-arrow-right ms-1"></i>
                                </span>
                            </div>

                        </div>
                    </div>
                </a>

                {{-- Bookmark floats above --}}
                <button class="ch-bookmark-btn {{ in_array($intern['slug'], session('saved_internships', [])) ? 'saved' : '' }}"
                        data-slug="{{ $intern['slug'] }}"
                        data-type="internship"
                            onclick="handleBookmark(event, this)"
                        title="Save">
                    <i class="{{ in_array($intern['slug'], session('saved_internships', [])) ? 'fas' : 'far' }} fa-bookmark"></i>
                </button>

            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection
