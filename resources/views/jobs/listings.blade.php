@extends('layouts.app')
@section('title', 'Jobs')

@section('content')

<div class="page-header bg-primary-soft py-4">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('landing') }}" class="text-decoration-none text-primary">Home</a></li>
                <li class="breadcrumb-item active">Jobs</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-0"><i class="fas fa-briefcase me-2 text-primary"></i>Job Listings</h4>
                <p class="text-muted mb-0 small">{{ count($jobs) }} positions — click any card to view details</p>
            </div>
            <a href="{{ route('internships') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="fas fa-graduation-cap me-1"></i>View Internships
            </a>
        </div>
    </div>
</div>

<div class="container py-4">

    {{-- Search & Filter --}}
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
        <form action="{{ route('jobs') }}" method="GET" id="jobFilterForm">
            <div class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" value="{{ $search }}" class="form-control border-start-0" placeholder="Search jobs, companies, skills...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="setup" class="form-select" onchange="document.getElementById('jobFilterForm').submit()">
                        <option value="">All Work Setups</option>
                        @foreach(['On-site','Hybrid','Remote'] as $s)
                        <option value="{{ $s }}" {{ $setup === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="sort" class="form-select" onchange="document.getElementById('jobFilterForm').submit()">
                        <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="salary"  {{ $sort === 'salary'  ? 'selected' : '' }}>Highest Salary</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill flex-grow-1">Search</button>
                    @if($search || $setup)
                    <a href="{{ route('jobs') }}" class="btn btn-outline-secondary rounded-pill px-2"><i class="fas fa-times"></i></a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Setup Chips --}}
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('jobs') }}?search={{ urlencode($search) }}&sort={{ $sort }}"
           class="badge rounded-pill px-3 py-2 text-decoration-none {{ !$setup ? 'bg-primary text-white' : 'bg-light text-muted' }}">All</a>
        @foreach(['On-site','Hybrid','Remote'] as $s)
        <a href="{{ route('jobs') }}?search={{ urlencode($search) }}&sort={{ $sort }}&setup={{ $s }}"
           class="badge rounded-pill px-3 py-2 text-decoration-none {{ $setup === $s ? 'bg-primary text-white' : 'bg-light text-muted' }}">
            <i class="fas fa-{{ $s === 'Remote' ? 'wifi' : ($s === 'Hybrid' ? 'code-branch' : 'building') }} me-1"></i>{{ $s }}
        </a>
        @endforeach
    </div>

    @if(count($jobs) === 0)
    <div class="text-center py-5">
        <div class="empty-state-icon mx-auto mb-3"><i class="fas fa-search fa-2x text-muted"></i></div>
        <h5 class="fw-bold">No jobs found</h5>
        <p class="text-muted">Try adjusting your search or filters.</p>
        <a href="{{ route('jobs') }}" class="btn btn-primary rounded-pill px-4">Clear Filters</a>
    </div>
    @else
    <div class="row g-4">
        @foreach($jobs as $job)
        <div class="col-md-6 col-lg-4">
            <div class="position-relative h-100">

                {{-- FULL CARD IS AN ANCHOR TAG --}}
                <a href="{{ route('jobs.show', $job['id']) }}"
                   class="text-decoration-none d-block h-100 card-link-wrap">
                    <div class="card border-0 shadow-sm rounded-4 h-100 ch-job-card">
                        <div class="card-body p-4 d-flex flex-column">

                            <div class="d-flex align-items-start mb-3" style="padding-right:36px">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="company-logo rounded-3 d-flex align-items-center justify-content-center text-white fw-bold logo-{{ $job['logo'] }}">
                                        {{ strtoupper(substr($job['company'], 0, 1)) }}
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark ch-card-title">{{ $job['title'] }}</h6>
                                        <small class="text-muted">{{ $job['company'] }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge rounded-pill bg-primary-soft text-primary">
                                    <i class="fas fa-briefcase me-1"></i>Full-time
                                </span>
                                <span class="badge rounded-pill {{ $job['setup'] === 'Remote' ? 'bg-info-soft text-info' : ($job['setup'] === 'Hybrid' ? 'bg-warning-soft text-warning' : 'bg-light text-muted') }}">
                                    <i class="fas fa-{{ $job['setup'] === 'Remote' ? 'wifi' : ($job['setup'] === 'Hybrid' ? 'code-branch' : 'building') }} me-1"></i>
                                    {{ $job['setup'] }}
                                </span>
                            </div>

                            <div class="d-flex flex-wrap gap-3 text-muted small mb-3">
                                <span><i class="fas fa-map-marker-alt me-1 text-primary"></i>{{ $job['location'] }}</span>
                                <span><i class="fas fa-clock me-1 text-warning"></i>{{ $job['posted'] }}</span>
                            </div>

                            <div class="d-flex flex-wrap gap-1 mb-3">
                                @foreach(array_slice($job['tags'], 0, 3) as $tag)
                                <span class="badge bg-light text-muted rounded-pill px-2 py-1" style="font-size:11px">{{ $tag }}</span>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                                <span class="fw-bold text-primary">{{ $job['salary'] }}</span>
                                <span class="btn btn-primary btn-sm rounded-pill px-3">
                                    View Details <i class="fas fa-arrow-right ms-1"></i>
                                </span>
                            </div>

                        </div>
                    </div>
                </a>

                {{-- Bookmark floats above --}}
                <button class="ch-bookmark-btn {{ in_array($job['id'], session('saved_jobs', [])) ? 'saved' : '' }}"
                        data-job-id="{{ $job['id'] }}"
                        data-type="job"
                            onclick="handleBookmark(event, this)"
                        title="Save Job">
                    <i class="{{ in_array($job['id'], session('saved_jobs', [])) ? 'fas' : 'far' }} fa-bookmark"></i>
                </button>

            </div>
        </div>
        @endforeach
    </div>

    <nav class="mt-5 d-flex justify-content-center">
        <ul class="pagination gap-1">
            <li class="page-item disabled"><span class="page-link rounded-pill border-0 bg-light">&laquo;</span></li>
            <li class="page-item active"><a class="page-link rounded-pill border-0" href="#">1</a></li>
            <li class="page-item"><a class="page-link rounded-pill border-0 bg-light text-muted" href="#">2</a></li>
            <li class="page-item"><a class="page-link rounded-pill border-0 bg-light text-muted" href="#">&raquo;</a></li>
        </ul>
    </nav>
    @endif

</div>
@endsection
