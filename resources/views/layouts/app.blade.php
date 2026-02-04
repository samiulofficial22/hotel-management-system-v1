<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('messages.Hotel'))</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar { min-height: 100vh; width: 260px; transition: transform 0.2s, margin 0.2s; }
        .sidebar .nav-link { color: rgba(255,255,255,0.85); padding: 0.6rem 1rem; border-radius: 0.35rem; }
        .sidebar .nav-link:hover { color: #fff; background: rgba(255,255,255,0.1); }
        .sidebar .nav-link.active { color: #fff; background: rgba(255,255,255,0.2); }
        .sidebar .nav-link i { width: 1.4rem; margin-right: 0.5rem; opacity: 0.9; }
        .main-content { flex: 1; min-width: 0; }
        @media (max-width: 991.98px) {
            .sidebar { position: fixed; left: 0; top: 0; z-index: 1050; transform: translateX(-100%); margin: 0; }
            .sidebar.show { transform: translateX(0); }
            .sidebar-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1040; }
            .sidebar-backdrop.show { display: block; }
        }
    </style>
    @stack('styles')
</head>
<body class="d-flex min-vh-100 bg-light">
    <!-- Sidebar -->
    <aside class="sidebar bg-primary flex-shrink-0 d-flex flex-column" id="sidebar">
        <div class="d-flex align-items-center justify-content-between p-3 border-bottom border-secondary border-opacity-25">
            <a href="{{ route('dashboard') }}" class="text-white text-decoration-none fw-bold">
                <i class="bi bi-building"></i> {{ __('messages.Hotel') }}
            </a>
            <button class="btn btn-link text-white d-lg-none p-0 sidebar-close" type="button" aria-label="{{ __('messages.Close sidebar') }}">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <nav class="nav flex-column p-2">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-speedometer2"></i> {{ __('messages.Dashboard') }}
            </a>
            <span class="px-3 py-1 small text-white-50 text-uppercase">{{ __('messages.Rooms') }}</span>
            <a class="nav-link {{ request()->routeIs('room-types.*') ? 'active' : '' }}" href="{{ route('room-types.index') }}">
                <i class="bi bi-grid-3x3-gap"></i> {{ __('messages.Room Types') }}
            </a>
            <a class="nav-link {{ request()->routeIs('rooms.*') ? 'active' : '' }}" href="{{ route('rooms.index') }}">
                <i class="bi bi-door-open"></i> {{ __('messages.Rooms') }}
            </a>
            <span class="px-3 py-1 small text-white-50 text-uppercase">{{ __('messages.Operations') }}</span>
            <a class="nav-link {{ request()->routeIs('guests.*') ? 'active' : '' }}" href="{{ route('guests.index') }}">
                <i class="bi bi-people"></i> {{ __('messages.Guests') }}
            </a>
            <a class="nav-link {{ request()->routeIs('bookings.index') || request()->routeIs('bookings.create') || request()->routeIs('bookings.show') || request()->routeIs('bookings.edit') ? 'active' : '' }}" href="{{ route('bookings.index') }}">
                <i class="bi bi-calendar-check"></i> {{ __('messages.Bookings') }}
            </a>
            <a class="nav-link {{ request()->routeIs('bookings.calendar') ? 'active' : '' }}" href="{{ route('bookings.calendar') }}">
                <i class="bi bi-calendar3"></i> {{ __('messages.Booking Calendar') }}
            </a>
            @can('pos.manage')
            <span class="px-3 py-1 small text-white-50 text-uppercase">{{ __('messages.F&B') }}</span>
            <a class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}" href="{{ route('pos.index') }}">
                <i class="bi bi-cash-coin"></i> {{ __('messages.POS') }}
            </a>
            <a class="nav-link {{ request()->routeIs('outlets.*') ? 'active' : '' }}" href="{{ route('outlets.index') }}">
                <i class="bi bi-shop"></i> {{ __('messages.Outlets') }}
            </a>
            <a class="nav-link {{ request()->routeIs('kitchen.*') ? 'active' : '' }}" href="{{ route('kitchen.index') }}">
                <i class="bi bi-egg-fried"></i> {{ __('messages.Kitchen') }}
            </a>
            @endcan
            @can('banquet.manage')
            <span class="px-3 py-1 small text-white-50 text-uppercase">{{ __('messages.Banquet') }}</span>
            <a class="nav-link {{ request()->routeIs('banquet.venues.*') ? 'active' : '' }}" href="{{ route('banquet.venues.index') }}">
                <i class="bi bi-building"></i> {{ __('messages.Banquet Venues') }}
            </a>
            <a class="nav-link {{ request()->routeIs('banquet.bookings.*') ? 'active' : '' }}" href="{{ route('banquet.bookings.index') }}">
                <i class="bi bi-calendar-event"></i> {{ __('messages.Banquet Bookings') }}
            </a>
            @endcan
            @can('housekeeping.view')
            <span class="px-3 py-1 small text-white-50 text-uppercase">{{ __('messages.Operations') }}</span>
            <a class="nav-link {{ request()->routeIs('housekeeping.*') ? 'active' : '' }}" href="{{ route('housekeeping.index') }}">
                <i class="bi bi-bucket"></i> {{ __('messages.Housekeeping') }}
            </a>
            @endcan
            @can('minibar.manage')
            <a class="nav-link {{ request()->routeIs('minibar.*') ? 'active' : '' }}" href="{{ route('minibar.index') }}">
                <i class="bi bi-cup-straw"></i> {{ __('messages.Minibar') }}
            </a>
            @endcan
            @can('store.manage')
            <a class="nav-link {{ request()->routeIs('store.*') ? 'active' : '' }}" href="{{ route('store.index') }}">
                <i class="bi bi-box-seam"></i> {{ __('messages.Store') }}
            </a>
            @endcan
            @can('maintenance.view')
            <a class="nav-link {{ request()->routeIs('maintenance.*') ? 'active' : '' }}" href="{{ route('maintenance.index') }}">
                <i class="bi bi-tools"></i> {{ __('messages.Maintenance') }}
            </a>
            @endcan
            @can('accounts.view')
            <span class="px-3 py-1 small text-white-50 text-uppercase">{{ __('messages.Business') }}</span>
            <a class="nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}" href="{{ route('accounts.index') }}">
                <i class="bi bi-journal-bookmark"></i> {{ __('messages.Accounts') }}
            </a>
            @endcan
            @can('hr.view')
            <a class="nav-link {{ request()->routeIs('hr.employees.*') ? 'active' : '' }}" href="{{ route('hr.employees.index') }}">
                <i class="bi bi-person-badge"></i> {{ __('messages.Employees') }}
            </a>
            <a class="nav-link {{ request()->routeIs('hr.attendance.*') ? 'active' : '' }}" href="{{ route('hr.attendance.index') }}">
                <i class="bi bi-calendar-check"></i> {{ __('messages.Attendance') }}
            </a>
            <a class="nav-link {{ request()->routeIs('hr.payroll.*') ? 'active' : '' }}" href="{{ route('hr.payroll.index') }}">
                <i class="bi bi-currency-dollar"></i> {{ __('messages.Payroll') }}
            </a>
            @endcan
            @can('marketing.view')
            <a class="nav-link {{ request()->routeIs('marketing.*') ? 'active' : '' }}" href="{{ route('marketing.index') }}">
                <i class="bi bi-megaphone"></i> {{ __('messages.Marketing') }}
            </a>
            @endcan
            @can('reports.view')
            <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                <i class="bi bi-graph-up"></i> {{ __('messages.Reports') }}
            </a>
            @endcan
        </nav>
        <div class="mt-auto p-3 border-top border-secondary border-opacity-25">
            <form action="{{ route('language.switch') }}" method="POST" class="mb-2">
                @csrf
                <select name="locale" class="form-select form-select-sm bg-dark text-light border-secondary" onchange="this.form.submit()">
                    @foreach(config('app.available_locales', ['en' => 'English', 'bn' => 'বাংলা']) as $code => $label)
                    <option value="{{ $code }}" {{ app()->getLocale() === $code ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
            <div class="text-white-50 small px-2">{{ auth()->user()->name }}</div>
            <form action="{{ route('logout') }}" method="POST" class="mt-1">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm w-100">
                    <i class="bi bi-box-arrow-right"></i> {{ __('messages.Logout') }}
                </button>
            </form>
        </div>
    </aside>

    <!-- Mobile backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="main-content d-flex flex-column w-100">
        <!-- Top bar (mobile: menu + title) -->
        <header class="bg-white border-bottom shadow-sm sticky-top">
            <div class="d-flex align-items-center px-3 py-2">
                <button class="btn btn-link text-dark d-lg-none me-2 p-0 sidebar-toggle" type="button" aria-label="{{ __('messages.Open menu') }}">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <h1 class="h5 mb-0 text-muted">@yield('title', __('messages.Hotel Management'))</h1>
            </div>
        </header>

        <main class="p-3 p-md-4 flex-grow-1">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('messages.Close') }}"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('messages.Close') }}"></button>
                </div>
            @endif
            @yield('content')
        </main>

        <footer class="py-2 px-3 bg-white border-top small text-muted text-center">{{ __('messages.Hotel Management') }} &copy; {{ date('Y') }}</footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            var sidebar = document.getElementById('sidebar');
            var backdrop = document.getElementById('sidebarBackdrop');
            function openSidebar() { sidebar && sidebar.classList.add('show'); backdrop && backdrop.classList.add('show'); }
            function closeSidebar() { sidebar && sidebar.classList.remove('show'); backdrop && backdrop.classList.remove('show'); }
            document.querySelectorAll('.sidebar-toggle').forEach(function(btn) { btn.addEventListener('click', openSidebar); });
            document.querySelectorAll('.sidebar-close').forEach(function(btn) { btn.addEventListener('click', closeSidebar); });
            if (backdrop) backdrop.addEventListener('click', closeSidebar);
        })();
    </script>
    @stack('scripts')
</body>
</html>
