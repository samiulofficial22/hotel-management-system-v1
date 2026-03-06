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
        .notifications-dropdown { padding: 0 !important; }
        .notifications-dropdown .dropdown-item { white-space: normal; word-wrap: break-word; overflow-wrap: break-word; max-width: 100%; padding-right: 1rem; padding-left: 1rem; }
        .notifications-dropdown .dropdown-header { padding-left: 1rem; padding-right: 1rem; }
        .card.hover-shadow { transition: box-shadow 0.2s ease; }
        .card.hover-shadow:hover { box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.12) !important; }

        /* Button design – app-wide */
        .btn {
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
            border-width: 1.5px;
        }
        .btn:not(.btn-link):hover { transform: translateY(-1px); }
        .btn:not(.btn-link):active { transform: translateY(0); }
        .btn-primary {
            box-shadow: 0 2px 8px rgba(13, 110, 253, 0.35);
        }
        .btn-primary:hover {
            box-shadow: 0 4px 14px rgba(13, 110, 253, 0.45);
        }
        .btn-success {
            box-shadow: 0 2px 8px rgba(25, 135, 84, 0.35);
        }
        .btn-success:hover {
            box-shadow: 0 4px 14px rgba(25, 135, 84, 0.45);
        }
        .btn-warning {
            color: #000;
            box-shadow: 0 2px 8px rgba(255, 193, 7, 0.4);
        }
        .btn-warning:hover {
            color: #000;
            box-shadow: 0 4px 14px rgba(255, 193, 7, 0.5);
        }
        .btn-danger {
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.35);
        }
        .btn-danger:hover {
            box-shadow: 0 4px 14px rgba(220, 53, 69, 0.45);
        }
        .btn-info {
            color: #000;
            box-shadow: 0 2px 8px rgba(13, 202, 240, 0.35);
        }
        .btn-info:hover {
            color: #000;
            box-shadow: 0 4px 14px rgba(13, 202, 240, 0.45);
        }
        .btn-outline-primary:hover,
        .btn-outline-secondary:hover,
        .btn-outline-success:hover,
        .btn-outline-danger:hover,
        .btn-outline-warning:hover,
        .btn-outline-info:hover {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }
        .btn-sm { border-radius: 0.4rem; padding: 0.35rem 0.7rem; }
        .btn-lg { border-radius: 0.6rem; padding: 0.6rem 1.25rem; }
        .btn-link.text-danger:hover { text-decoration: underline; }
    </style>
    @stack('styles')
    @stack('head')
</head>
<body class="d-flex min-vh-100 bg-light">
    <!-- Sidebar -->
    <aside class="sidebar bg-primary flex-shrink-0 d-flex flex-column" id="sidebar">
        <div class="d-flex align-items-center justify-content-between p-3 border-bottom border-secondary border-opacity-25">
            <a href="{{ auth()->user()->hasRole('Guest') ? route('guest.dashboard') : route('dashboard') }}" class="text-white text-decoration-none fw-bold">
                <i class="bi bi-building"></i> {{ __('messages.Hotel') }}
            </a>
            <button class="btn btn-link text-white d-lg-none p-0 sidebar-close" type="button" aria-label="{{ __('messages.Close sidebar') }}">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <nav class="nav flex-column p-2">
            @role('Guest')
                <a class="nav-link {{ request()->routeIs('guest.dashboard') ? 'active' : '' }}" href="{{ route('guest.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> {{ __('Dashboard') }}
                </a>
                <a class="nav-link {{ request()->routeIs('booking.request') ? 'active' : '' }}" href="{{ route('booking.request') }}">
                    <i class="bi bi-calendar-plus"></i> {{ __('Book a room') }}
                </a>
                <a class="nav-link {{ request()->routeIs('guest.bookings') ? 'active' : '' }}" href="{{ route('guest.bookings') }}">
                    <i class="bi bi-calendar-check"></i> {{ __('My bookings') }}
                </a>
                <a class="nav-link {{ request()->routeIs('guest.profile') || request()->routeIs('guest.profile.update') ? 'active' : '' }}" href="{{ route('guest.profile') }}">
                    <i class="bi bi-person"></i> {{ __('My profile') }}
                </a>
            @else
            @role('Housekeeper')
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i> {{ __('messages.Dashboard') }}
                </a>
                <a class="nav-link {{ request()->routeIs('housekeeping.*') ? 'active' : '' }}" href="{{ route('housekeeping.index') }}">
                    <i class="bi bi-bucket"></i> {{ __('Room cleaning assignments') }}
                </a>
                <a class="nav-link {{ request()->routeIs('maintenance.*') ? 'active' : '' }}" href="{{ route('maintenance.index') }}">
                    <i class="bi bi-tools"></i> {{ __('messages.Maintenance') }}
                </a>
                <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                    <i class="bi bi-person"></i> {{ __('My profile') }}
                </a>
            @else
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
            @can('guest.manage')
            <a class="nav-link {{ request()->routeIs('guest.requests.*') ? 'active' : '' }}" href="{{ route('guest.requests.index') }}">
                <i class="bi bi-envelope-open"></i> {{ __('Guest booking requests') }}
            </a>
            @endcan
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
            <a class="nav-link {{ request()->routeIs('housekeeping.index') ? 'active' : '' }}" href="{{ route('housekeeping.index') }}">
                <i class="bi bi-bucket"></i> {{ __('messages.Housekeeping') }}
            </a>
            @endcan
            @can('housekeeping.assign_others')
            <a class="nav-link {{ request()->routeIs('housekeeping.all-work') ? 'active' : '' }}" href="{{ route('housekeeping.all-work') }}">
                <i class="bi bi-table"></i> {{ __('messages.All Housekeeping Work') }}
            </a>
            @endcan
            <!-- @can('minibar.manage')
            <a class="nav-link {{ request()->routeIs('minibar.*') ? 'active' : '' }}" href="{{ route('minibar.index') }}">
                <i class="bi bi-cup-straw"></i> {{ __('messages.Minibar') }}
            </a>
            @endcan -->
            <!-- @can('store.manage')
            <a class="nav-link {{ request()->routeIs('store.*') ? 'active' : '' }}" href="{{ route('store.index') }}">
                <i class="bi bi-box-seam"></i> {{ __('messages.Store') }}
            </a>
            @endcan -->
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
           
            @canany(['departments.manage', 'users.manage', 'roles.manage'])
            <span class="px-3 py-1 small text-white-50 text-uppercase">{{ __('messages.Settings') }}</span>
            @endcanany
            @can('departments.manage')
            <a class="nav-link {{ request()->routeIs('settings.departments.*') ? 'active' : '' }}" href="{{ route('settings.departments.index') }}">
                <i class="bi bi-diagram-3"></i> {{ __('messages.Departments') }}
            </a>
            @endcan
            @can('users.manage')
            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                <i class="bi bi-people"></i> {{ __('messages.Users') }}
            </a>
            @endcan
            @can('roles.manage')
            <a class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                <i class="bi bi-shield-lock"></i> {{ __('Roles') }}
            </a>
            @endcan
            @endif
            @endif
        </nav>
    </aside>

    <!-- Mobile backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="main-content d-flex flex-column w-100">
        <!-- Top bar: left = menu + title, right = language + user + logout -->
        <header class="bg-white border-bottom shadow-sm sticky-top">
            <div class="d-flex align-items-center justify-content-between px-3 py-2 gap-2">
                <div class="d-flex align-items-center">
                    <button class="btn btn-link text-dark d-lg-none me-2 p-0 sidebar-toggle" type="button" aria-label="{{ __('messages.Open menu') }}">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <h1 class="h5 mb-0 text-muted">@yield('title', __('messages.Hotel Management'))</h1>
                </div>
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    @if(auth()->user()->can('guest.manage') || auth()->user()->can('housekeeping.view') || auth()->user()->can('maintenance.manage'))
                    <div class="dropdown">
                        <button class="btn btn-link text-dark position-relative p-2 text-decoration-none dropdown-toggle" type="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('messages.Notifications') }}" aria-label="{{ __('messages.Notifications') }}">
                            <i class="bi bi-bell fs-5"></i>
                            @if(($headerUnreadCount ?? 0) > 0)
                                <span class="position-absolute top-0 end-0 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem; min-width: 1.25rem;">{{ ($headerUnreadCount ?? 0) > 99 ? '99+' : ($headerUnreadCount ?? 0) }}</span>
                            @endif
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 notifications-dropdown" style="width: 360px; max-height: 70vh; overflow-y: auto;" aria-labelledby="notificationsDropdown">
                            <li class="dropdown-header py-2">{{ __('messages.Notifications') }}</li>
                            @forelse($headerNotifications ?? [] as $n)
                                @php $data = $n->data ?? []; @endphp
                                <li>
                                    <a class="dropdown-item py-2 text-wrap {{ $n->read_at ? '' : 'bg-light' }}" href="{{ route('notifications.read', $n->id) }}">
                                        <div class="small text-break" style="word-wrap: break-word; overflow-wrap: break-word; line-height: 1.35;">{{ $data['message'] ?? __('Notification') }}</div>
                                        <small class="text-muted">{{ $n->created_at?->diffForHumans() }}</small>
                                    </a>
                                </li>
                            @empty
                                <li><span class="dropdown-item text-muted small py-3 text-center">{{ __('No notifications.') }}</span></li>
                            @endforelse
                            <li><hr class="dropdown-divider my-0"></li>
                            <li><a class="dropdown-item small text-center py-2" href="{{ route('notifications.index') }}">{{ __('See all') }}</a></li>
                        </ul>
                    </div>
                    @endif
                    <form action="{{ route('language.switch') }}" method="POST" class="mb-0">
                        @csrf
                        <select name="locale" class="form-select form-select-sm" style="width: auto; min-width: 5rem;" onchange="this.form.submit()">
                            @foreach(config('app.available_locales', ['en' => 'English', 'bn' => 'বাংলা']) as $code => $label)
                            <option value="{{ $code }}" {{ app()->getLocale() === $code ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>
                    @role('Guest')
                    <a href="{{ route('guest.profile') }}" class="d-flex align-items-center gap-2 text-decoration-none text-muted small" title="{{ __('Edit Profile') }}">
                        <img src="{{ auth()->user()->profile_pic_url }}" alt="" class="rounded-circle" width="32" height="32" style="object-fit: cover;">
                        <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                        <i class="bi bi-pencil-square opacity-75 d-none d-md-inline"></i>
                    </a>
                    @else
                    <a href="{{ route('profile.edit') }}" class="d-flex align-items-center gap-2 text-decoration-none text-muted small" title="{{ __('Edit Profile') }}">
                        <img src="{{ auth()->user()->profile_pic_url }}" alt="" class="rounded-circle" width="32" height="32" style="object-fit: cover;">
                        <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                        <i class="bi bi-pencil-square opacity-75 d-none d-md-inline"></i>
                    </a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="mb-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-box-arrow-right"></i> <span class="d-none d-md-inline">{{ __('messages.Logout') }}</span>
                        </button>
                    </form>
                </div>
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
