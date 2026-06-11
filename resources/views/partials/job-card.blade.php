@php
    $isInternship = ($job['type'] ?? '') === 'internship';
    $slug         = $job['slug']    ?? strtolower($job['company']);
    $jobId        = $job['id']      ?? null;
    $url          = $isInternship
                        ? route('internship.show', $slug)
                        : route('jobs.show', $jobId);
    $isSaved      = $isInternship
                        ? in_array($slug,  session('saved_internships', []))
                        : in_array($jobId, session('saved_jobs', []));
@endphp

<div class="position-relative h-100">

    {{-- ===== ENTIRE CARD IS A LINK ===== --}}
    <a href="{{ $url }}" class="text-decoration-none d-block h-100 card-link-wrap">
        <div class="card border-0 shadow-sm rounded-4 h-100 ch-job-card">
            <div class="card-body p-4 d-flex flex-column">

                {{-- Header --}}
                <div class="d-flex align-items-center gap-3 mb-3" style="padding-right:38px">
                    <div class="company-logo rounded-3 d-flex align-items-center justify-content-center text-white fw-bold logo-{{ $job['logo'] }}">
                        {{ strtoupper(substr($job['company'], 0, 1)) }}
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark ch-card-title">{{ $job['title'] }}</h6>
                        <small class="text-muted">{{ $job['company'] }}</small>
                    </div>
                </div>

                {{-- Badges --}}
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge rounded-pill {{ $isInternship ? 'bg-success-soft text-success' : 'bg-primary-soft text-primary' }}">
                        <i class="fas fa-{{ $isInternship ? 'graduation-cap' : 'briefcase' }} me-1"></i>
                        {{ $isInternship ? 'Internship' : 'Full-time' }}
                    </span>
                    <span class="badge rounded-pill {{ ($job['setup'] ?? '') === 'Remote' ? 'bg-info-soft text-info' : (($job['setup'] ?? '') === 'Hybrid' ? 'bg-warning-soft text-warning' : 'bg-light text-muted') }}">
                        <i class="fas fa-{{ ($job['setup'] ?? '') === 'Remote' ? 'wifi' : (($job['setup'] ?? '') === 'Hybrid' ? 'code-branch' : 'building') }} me-1"></i>
                        {{ $job['setup'] ?? 'On-site' }}
                    </span>
                </div>

                {{-- Meta --}}
                <div class="d-flex flex-wrap gap-3 text-muted small mb-3">
                    <span><i class="fas fa-map-marker-alt me-1 text-primary"></i>{{ $job['location'] ?? '' }}</span>
                    <span><i class="fas fa-clock me-1 text-warning"></i>{{ $job['posted'] ?? '' }}</span>
                </div>

                {{-- Tags --}}
                @if(!empty($job['tags']))
                <div class="d-flex flex-wrap gap-1 mb-3">
                    @foreach(array_slice($job['tags'], 0, 3) as $tag)
                    <span class="badge bg-light text-muted rounded-pill px-2 py-1" style="font-size:11px">{{ $tag }}</span>
                    @endforeach
                </div>
                @endif

                {{-- Footer --}}
                <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                    <span class="fw-bold text-primary">{{ $job['salary'] ?? '' }}</span>
                    <span class="btn btn-primary btn-sm rounded-pill px-3">
                        View Details <i class="fas fa-arrow-right ms-1"></i>
                    </span>
                </div>

            </div>
        </div>
    </a>

    {{-- ===== BOOKMARK — floats above the link ===== --}}
        <button class="ch-bookmark-btn {{ $isSaved ? 'saved' : '' }}"
            data-type="{{ $isInternship ? 'internship' : 'job' }}"
            data-slug="{{ $slug }}"
            data-job-id="{{ $jobId }}"
            onclick="handleBookmark(event, this)"
            title="{{ $isSaved ? 'Saved' : 'Save' }}">
        <i class="{{ $isSaved ? 'fas' : 'far' }} fa-bookmark"></i>
    </button>

</div>
