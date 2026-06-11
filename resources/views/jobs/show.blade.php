@extends('layouts.app')
@section('title', $job['title'] . ' — ' . $job['company'])

@section('content')

<div class="page-header bg-primary-soft py-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('landing') }}" class="text-decoration-none text-primary">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('jobs') }}" class="text-decoration-none text-primary">Jobs</a></li>
                <li class="breadcrumb-item active">{{ $job['title'] }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">

        {{-- ===== MAIN CONTENT ===== --}}
        <div class="col-lg-8">

            {{-- Job Header --}}
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <div class="d-flex align-items-start gap-4 flex-wrap">
                    <div class="company-logo-lg rounded-4 d-flex align-items-center justify-content-center text-white fw-bold fs-2 logo-{{ $job['logo'] }} flex-shrink-0">
                        {{ strtoupper(substr($job['company'], 0, 1)) }}
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <span class="badge rounded-pill bg-primary-soft text-primary">
                                <i class="fas fa-briefcase me-1"></i>Full-time
                            </span>
                            <span class="badge rounded-pill {{ $job['setup'] === 'Remote' ? 'bg-info-soft text-info' : ($job['setup'] === 'Hybrid' ? 'bg-warning-soft text-warning' : 'bg-light text-muted') }}">
                                <i class="fas fa-{{ $job['setup'] === 'Remote' ? 'wifi' : ($job['setup'] === 'Hybrid' ? 'code-branch' : 'building') }} me-1"></i>
                                {{ $job['setup'] }}
                            </span>
                        </div>
                        <h2 class="fw-bold mb-1">{{ $job['title'] }}</h2>
                        <div class="d-flex flex-wrap gap-3 text-muted small mb-3">
                            <span><i class="fas fa-building me-1 text-primary"></i>{{ $job['company'] }}</span>
                            <span><i class="fas fa-map-marker-alt me-1 text-primary"></i>{{ $job['location'] }}</span>
                            <span><i class="fas fa-clock me-1 text-warning"></i>{{ $job['posted'] }}</span>
                            <span><i class="fas fa-user-tie me-1 text-info"></i>{{ $job['experience'] }}</span>
                        </div>
                        <div class="d-flex flex-wrap gap-1 mb-4">
                            @foreach($job['tags'] as $tag)
                            <span class="badge bg-light text-muted rounded-pill px-3 py-2">{{ $tag }}</span>
                            @endforeach
                        </div>
                        {{-- Action Buttons --}}
                        <div class="d-flex gap-2 flex-wrap">
                            @if(in_array($job['id'], session('applied_jobs', [])))
                                <button class="btn btn-success rounded-pill px-4" disabled>
                                    <i class="fas fa-check me-2"></i>Applied
                                </button>
                            @else
                                <form action="{{ route('jobs.apply', $job['id']) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-semibold">
                                        <i class="fas fa-paper-plane me-2"></i>Apply Now
                                    </button>
                                </form>
                            @endif
                                <button class="bookmark-btn btn rounded-pill px-4 py-2 {{ in_array($job['id'], session('saved_jobs', [])) ? 'btn-warning bookmarked' : 'btn-outline-warning' }}"
                                    data-job-id="{{ $job['id'] }}"
                                    onclick="handleBookmark(event, this)">
                                <i class="{{ in_array($job['id'], session('saved_jobs', [])) ? 'fas' : 'far' }} fa-bookmark me-2"></i>
                                <span class="bm-label">{{ in_array($job['id'], session('saved_jobs', [])) ? 'Saved' : 'Save Job' }}</span>
                            </button>
                            <a href="{{ route('jobs') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="fas fa-arrow-left me-1"></i>Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
            <div class="alert alert-success rounded-4 border-0 shadow-sm mb-4 d-flex align-items-center gap-2">
                <i class="fas fa-check-circle fs-5"></i>{{ session('success') }}
            </div>
            @endif

            {{-- Description --}}
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <span class="section-icon bg-primary-soft text-primary rounded-3"><i class="fas fa-align-left"></i></span>
                    Job Description
                </h5>
                <p class="text-muted lh-lg mb-0">{{ $job['description'] }}</p>
            </div>

            {{-- Responsibilities --}}
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <span class="section-icon bg-success-soft text-success rounded-3"><i class="fas fa-tasks"></i></span>
                    Responsibilities
                </h5>
                <ul class="list-unstyled mb-0">
                    @foreach($job['responsibilities'] as $item)
                    <li class="d-flex align-items-start gap-3 mb-3">
                        <i class="fas fa-check-circle text-success mt-1 flex-shrink-0"></i>
                        <span class="text-muted">{{ $item }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Requirements --}}
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <span class="section-icon bg-warning-soft text-warning rounded-3"><i class="fas fa-clipboard-list"></i></span>
                    Requirements
                </h5>
                <ul class="list-unstyled mb-0">
                    @foreach($job['requirements'] as $item)
                    <li class="d-flex align-items-start gap-3 mb-3">
                        <span class="req-dot flex-shrink-0 mt-2"></span>
                        <span class="text-muted">{{ $item }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Benefits --}}
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <span class="section-icon bg-info-soft text-info rounded-3"><i class="fas fa-gift"></i></span>
                    Benefits & Perks
                </h5>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($job['benefits'] as $benefit)
                    <span class="badge bg-primary-soft text-primary rounded-pill px-3 py-2">
                        <i class="fas fa-star me-1"></i>{{ $benefit }}
                    </span>
                    @endforeach
                </div>
            </div>

            {{-- Related Jobs --}}
            @if(count($related) > 0)
            <h5 class="fw-bold mb-4">Similar Jobs You May Like</h5>
            <div class="row g-3">
                @foreach($related as $r)
                <div class="col-md-4">
                    <div class="job-card-click"
                         onclick="window.location='{{ route('jobs.show', $r['id']) }}'"
                         style="cursor:pointer">
                        <div class="card border-0 shadow-sm rounded-4 p-3 hover-card">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="company-logo-sm rounded-3 d-flex align-items-center justify-content-center text-white fw-bold logo-{{ $r['logo'] }}">
                                    {{ strtoupper(substr($r['company'], 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold small text-dark lh-1">{{ $r['title'] }}</div>
                                    <div class="text-muted" style="font-size:11px">{{ $r['company'] }}</div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-primary fw-bold small">{{ $r['salary'] }}</span>
                                <span class="badge bg-primary-soft text-primary rounded-pill" style="font-size:10px">Full-time</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

        </div>

        {{-- ===== SIDE PANEL ===== --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top:80px">

                <div class="text-center mb-4 pb-3 border-bottom">
                    <div class="company-logo-lg rounded-4 d-flex align-items-center justify-content-center text-white fw-bold fs-2 logo-{{ $job['logo'] }} mx-auto mb-3">
                        {{ strtoupper(substr($job['company'], 0, 1)) }}
                    </div>
                    <h6 class="fw-bold mb-0">{{ $job['company'] }}</h6>
                    <p class="text-muted small mb-0">{{ $job['location'] }}</p>
                </div>

                <h6 class="fw-bold mb-3 text-primary small text-uppercase letter-spacing">Job Overview</h6>

                @foreach([
                    ['icon'=>'fas fa-calendar-alt',   'color'=>'primary', 'label'=>'Posted',     'value'=>$job['posted']],
                    ['icon'=>'fas fa-briefcase',       'color'=>'success', 'label'=>'Job Type',   'value'=>'Full-time'],
                    ['icon'=>'fas fa-map-marker-alt',  'color'=>'warning', 'label'=>'Work Setup', 'value'=>$job['setup']],
                    ['icon'=>'fas fa-user-tie',        'color'=>'info',    'label'=>'Experience', 'value'=>$job['experience']],
                    ['icon'=>'fas fa-money-bill-wave', 'color'=>'danger',  'label'=>'Salary',     'value'=>$job['salary']],
                ] as $d)
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="overview-icon bg-{{ $d['color'] }}-soft text-{{ $d['color'] }} rounded-3 flex-shrink-0">
                        <i class="{{ $d['icon'] }}"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:11px">{{ $d['label'] }}</div>
                        <div class="fw-semibold small">{{ $d['value'] }}</div>
                    </div>
                </div>
                @endforeach

                <div class="d-grid gap-2 mt-4">
                    @if(in_array($job['id'], session('applied_jobs', [])))
                        <button class="btn btn-success rounded-pill py-2" disabled>
                            <i class="fas fa-check me-2"></i>Already Applied
                        </button>
                    @else
                        <form action="{{ route('jobs.apply', $job['id']) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold">
                                <i class="fas fa-paper-plane me-2"></i>Apply Now
                            </button>
                        </form>
                    @endif
                        <button class="bookmark-btn btn rounded-pill py-2 {{ in_array($job['id'], session('saved_jobs', [])) ? 'btn-warning bookmarked' : 'btn-outline-warning' }}"
                            data-job-id="{{ $job['id'] }}"
                            onclick="handleBookmark(event, this)">
                        <i class="{{ in_array($job['id'], session('saved_jobs', [])) ? 'fas' : 'far' }} fa-bookmark me-2"></i>
                        <span class="bm-label">{{ in_array($job['id'], session('saved_jobs', [])) ? 'Saved' : 'Save Job' }}</span>
                    </button>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
