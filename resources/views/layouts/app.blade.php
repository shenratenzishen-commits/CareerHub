<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CareerHub') — Find Your Dream Career</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield('styles')
</head>
<body>

{{-- ===== NAVBAR ===== --}}
<nav class="navbar navbar-expand-lg bg-white sticky-top ch-navbar">
    <div class="container">
        <a class="navbar-brand ch-brand" href="{{ route('landing') }}">
            <span class="brand-icon"><i class="fas fa-briefcase"></i></span>
            Career<span class="text-primary">Hub</span>
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

       

            <div class="d-flex align-items-center gap-2">
                @if(session('user'))
                    {{-- Notification Bell --}}
                    <div class="dropdown">
                        <button class="btn btn-light btn-sm rounded-circle notif-btn position-relative" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <span class="notif-dot"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 p-0" style="width:300px">
                            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">Notifications</span>
                                <span class="badge bg-primary rounded-pill">3</span>
                            </div>
                            @foreach([
                                ['icon'=>'fas fa-check-circle text-success','msg'=>'Your application to Google was received','time'=>'2 min ago'],
                                ['icon'=>'fas fa-star text-warning','msg'=>'New internship matching your profile','time'=>'1 hr ago'],
                                ['icon'=>'fas fa-building text-primary','msg'=>'Meta is hiring UI/UX Designers','time'=>'3 hrs ago'],
                            ] as $n)
                            <a href="{{ route('profile') }}" class="dropdown-item px-3 py-2 border-bottom d-flex gap-2 align-items-start">
                                <i class="{{ $n['icon'] }} mt-1 flex-shrink-0"></i>
                                <div>
                                    <div class="small">{{ $n['msg'] }}</div>
                                    <div class="text-muted" style="font-size:11px">{{ $n['time'] }}</div>
                                </div>
                            </a>
                            @endforeach
                            <a href="{{ route('profile') }}" class="d-block text-center text-primary small py-2">View all notifications</a>
                        </div>
                    </div>

                    <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 d-none d-md-inline-flex align-items-center gap-1">
                        <i class="fas fa-th-large"></i><span>Dashboard</span>
                    </a>

                    @if(session('user.role') === 'superadmin')
                    <a href="{{ route('superadmin.dashboard') }}" class="btn btn-warning btn-sm rounded-pill px-3 d-none d-md-inline-flex align-items-center gap-1">
                        <i class="fas fa-crown"></i><span>Super Admin</span>
                    </a>
                    @elseif(session('user.role') === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-warning btn-sm rounded-pill px-3 d-none d-md-inline-flex align-items-center gap-1">
                        <i class="fas fa-shield-alt"></i><span>Admin Panel</span>
                    </a>
                    @endif

                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm rounded-pill px-3 dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                            <div class="avatar-xs">{{ strtoupper(substr(session('user.name'), 0, 1)) }}</div>
                            <span class="d-none d-md-inline">{{ explode(' ', session('user.name'))[0] }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 mt-1 py-2">
                            <li class="px-3 py-2 border-bottom mb-1">
                                <div class="fw-semibold small">{{ session('user.name') }}</div>
                                <div class="text-muted" style="font-size:11px">{{ session('user.email') }}</div>
                            </li>
                            <li><a class="dropdown-item rounded-3 mx-1" href="{{ route('dashboard') }}"><i class="fas fa-th-large me-2 text-primary"></i>Dashboard</a></li>
                            <li><a class="dropdown-item rounded-3 mx-1" href="{{ route('profile') }}"><i class="fas fa-user me-2 text-primary"></i>My Profile</a></li>
                            <li><a class="dropdown-item rounded-3 mx-1" href="{{ route('profile') }}"><i class="fas fa-bookmark me-2 text-warning"></i>Saved Jobs <span class="badge bg-warning text-dark ms-1">{{ count(session('saved_jobs',[])) }}</span></a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item rounded-3 mx-1 text-danger"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm rounded-pill px-4">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm rounded-pill px-4">
                        <i class="fas fa-user-plus me-1"></i>Sign Up
                    </a>
                @endif
            </div>
        </div>
    </div>
</nav>

{{-- ===== FLASH MESSAGES ===== --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show m-0 rounded-0 border-0 text-center py-2" role="alert" style="background:#dcfce7;color:#166534">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
</div>
@endif

@yield('content')



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// Unified bookmark handler for all cards
function handleBookmark(e, btn) {
    e = e || window.event;
    e.preventDefault();
    e.stopPropagation();
    const type  = btn.dataset.type;
    const jobId = btn.dataset.jobId;
    const slug  = btn.dataset.slug;
    const url   = type === 'internship' ? `/internships/${slug}/save` : `/jobs/${jobId}/save`;

    fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        const icon = btn.querySelector('i');
        const label = btn.querySelector('span');
        if (data.status === 'saved') {
            icon.className = icon.className.replace('far ', 'fas ');
            btn.classList.add('saved', 'bookmarked');
            if (label) label.textContent = 'Saved';
            showToast('Saved to bookmarks!', 'success');
        } else {
            icon.className = icon.className.replace('fas ', 'far ');
            btn.classList.remove('saved', 'bookmarked');
            if (label) label.textContent = 'Save Job';
            showToast('Removed from bookmarks.', 'info');
        }
    });
}

// Legacy aliases (keep backward compatible signature)
function saveInternshipCard(e, btn) { handleBookmark(e, btn); }
function saveInternship(e, btn)     { handleBookmark(e, btn); }

function showToast(msg, type) {
    const t = document.createElement('div');
    t.className = `ch-toast ch-toast-${type}`;
    t.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>${msg}`;
    document.body.appendChild(t);
    setTimeout(() => t.classList.add('show'), 10);
    setTimeout(() => { t.classList.remove('show'); setTimeout(() => t.remove(), 300); }, 2500);
}
</script>
@yield('scripts')
</body>
</html>
