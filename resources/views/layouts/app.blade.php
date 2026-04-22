<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Mini-LMS') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        :root {
            --brand-1: #0d6efd;
            --brand-2: #0a58ca;
            --page-bg: #eef3f9;
        }

        body {
            background:
                radial-gradient(circle at 5% 5%, rgba(13, 110, 253, 0.10), transparent 35%),
                radial-gradient(circle at 95% 0%, rgba(10, 88, 202, 0.10), transparent 28%),
                var(--page-bg);
        }

        .navbar.bg-primary {
            background: linear-gradient(120deg, var(--brand-1), var(--brand-2)) !important;
        }

        .card {
            border-radius: .9rem;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ route('dashboard') }}">Mini-LMS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @if(auth()->user()->role === 'admin')
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.exams.*') ? 'active' : '' }}" href="{{ route('admin.exams.index') }}">Exams</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.results.*') ? 'active' : '' }}" href="{{ route('admin.results.index') }}">Results</a></li>
                    @else
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('student.exams.*') ? 'active' : '' }}" href="{{ route('student.exams.index') }}">Exams</a></li>
                    @endif
                </ul>
                <div class="d-flex align-items-center gap-2 text-white">
                    <span class="small">{{ auth()->user()->name }}</span>
                    <a class="btn btn-sm btn-outline-light" href="{{ route('profile.edit') }}">Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-sm btn-light text-primary" type="submit">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    @isset($header)
        <div class="bg-white border-bottom">
            <div class="container py-3">
                {{ $header }}
            </div>
        </div>
    @endisset

    <main class="container py-4">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('warning'))
            <div class="alert alert-warning">{{ session('warning') }}</div>
        @endif
        {{ $slot }}
    </main>
</body>
</html>
