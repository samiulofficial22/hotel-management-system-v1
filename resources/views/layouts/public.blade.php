<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('meta_description', __('messages.Welcome to our hotel') . ' - ' . __('messages.Hotel'))">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('messages.Hotel'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --hotel-dark: #1a1a1a;
            --hotel-gold: #c9a962;
            --hotel-gold-light: #ddc88a;
        }
        body { font-family: 'Inter', sans-serif; }
        .font-serif { font-family: 'Cormorant Garamond', serif; }
        .navbar-public { background-color: var(--hotel-dark) !important; }
        .navbar-public .navbar-brand { font-weight: 600; letter-spacing: 0.05em; }
        .navbar-public .nav-link { text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.08em; font-weight: 500; color: rgba(255,255,255,0.9); }
        .navbar-public .nav-link:hover { color: var(--hotel-gold); }
        .btn-reservation { background-color: transparent; color: #fff; border: 1px solid rgba(255,255,255,0.5); text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.08em; padding: 0.5rem 1.25rem; }
        .btn-reservation:hover { background-color: var(--hotel-gold); border-color: var(--hotel-gold); color: #1a1a1a; }
        .btn-gold { background-color: var(--hotel-gold); color: var(--hotel-dark); border: none; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.08em; font-weight: 600; padding: 0.75rem 1.75rem; }
        .btn-gold:hover { background-color: var(--hotel-gold-light); color: var(--hotel-dark); }
        .section-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.2em; color: var(--hotel-gold); font-weight: 600; }
        footer { background-color: var(--hotel-dark) !important; }
    </style>
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    <header class="navbar navbar-expand-lg navbar-dark navbar-public py-3">
        <div class="container">
            <a class="navbar-brand text-white" href="{{ route('home') }}">{{ __('messages.Hotel') }}</a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPublic" aria-controls="navbarPublic" aria-expanded="false" aria-label="{{ __('messages.Open menu') }}">
                <span class="navbar-toggler-icon"></span>
            </button>
            <nav class="collapse navbar-collapse" id="navbarPublic">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-1 gap-lg-3">
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">{{ __('messages.Home') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#about">{{ __('messages.About Us') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#rooms">{{ __('messages.Our Rooms') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#services">{{ __('messages.Services & Facilities') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#contact">{{ __('messages.Contact') }}</a></li>
                    <li class="nav-item">
                        <a class="btn btn-reservation rounded-0" href="{{ route('booking.request') }}">{{ __('messages.Reservation') }}</a>
                    </li>
                    @auth
                        <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">{{ __('messages.Dashboard') }}</a></li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">@csrf<button type="submit" class="nav-link btn btn-link text-light p-0 border-0 text-uppercase" style="font-size:0.8rem">{{ __('messages.Logout') }}</button></form>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">{{ __('messages.Login') }}</a></li>
                    @endauth
                </ul>
            </nav>
        </div>
    </header>

    <main class="flex-grow-1">
        @yield('content')
    </main>

    <footer class="text-white py-4 mt-auto">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <span class="fw-semibold">{{ __('messages.Hotel') }}</span>
                    <span class="text-white-50 ms-2">&copy; {{ date('Y') }}</span>
                </div>
                <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
                    <a href="{{ route('home') }}#contact" class="text-white-50 text-decoration-none">{{ __('messages.Contact Us') }}</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
