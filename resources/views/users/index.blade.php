@extends('layouts.app')
@section('title', __('messages.Users'))
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <h1 class="h3 mb-0">{{ __('messages.Users') }}</h1>
    <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">{{ __('Add User') }}</a>
</div>

<div id="users-tables-wrap">
{{-- Office staff table --}}
<h2 class="h5 mb-3 mt-4">{{ __('Office staff') }}</h2>
<div class="d-flex flex-wrap align-items-center gap-2 mb-2">
    <input type="text" id="search-staff" class="form-control form-control-sm" style="min-width: 200px;" placeholder="{{ __('Search staff by name or email...') }}" value="{{ old('q_staff', $searchStaff ?? '') }}">
    <button type="button" id="btn-search-staff" class="btn btn-outline-primary btn-sm">{{ __('Search') }}</button>
    <button type="button" id="btn-clear-staff" class="btn btn-outline-secondary btn-sm">{{ __('Clear') }}</button>
</div>
<div class="table-responsive mb-4">
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th></th>
                <th>{{ __('messages.Name') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Role') }}</th>
                <th>{{ __('messages.Created at') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="staff-tbody">
            @include('users.partials.staff-rows')
        </tbody>
    </table>
</div>
<div id="staff-pagination"></div>

{{-- Guests (portal users) table --}}
<h2 class="h5 mb-3 mt-4">{{ __('Guests') }} ({{ __('Portal users') }})</h2>
<div class="d-flex flex-wrap align-items-center gap-2 mb-2">
    <input type="text" id="search-guests" class="form-control form-control-sm" style="min-width: 200px;" placeholder="{{ __('Search guests by name or email...') }}" value="{{ old('q_guests', $searchGuests ?? '') }}">
    <button type="button" id="btn-search-guests" class="btn btn-outline-primary btn-sm">{{ __('Search') }}</button>
    <button type="button" id="btn-clear-guests" class="btn btn-outline-secondary btn-sm">{{ __('Clear') }}</button>
</div>
<div class="table-responsive mb-4">
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th></th>
                <th>{{ __('messages.Name') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Guest profile') }}</th>
                <th>{{ __('messages.Created at') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="guests-tbody">
            @include('users.partials.guests-rows')
        </tbody>
    </table>
</div>
<div id="guests-pagination">{{ $guests->links() }}</div>
</div>

@push('scripts')
<script>
(function() {
    var baseUrl = '{{ route("users.index") }}';
    var loading = false;

    function getParams() {
        var url = new URL(baseUrl, window.location.origin);
        url.searchParams.set('q_staff', document.getElementById('search-staff').value.trim());
        url.searchParams.set('q_guests', document.getElementById('search-guests').value.trim());
        return url;
    }

    function fetchAndUpdate(targetUrl) {
        if (loading) return;
        loading = true;
        var url = targetUrl || getParams().toString();
        var req = new XMLHttpRequest();
        req.open('GET', url + (url.indexOf('?') >= 0 ? '&' : '?') + 'X-Requested-With=XMLHttpRequest');
        req.setRequestHeader('Accept', 'application/json');
        req.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        req.onreadystatechange = function() {
            if (req.readyState !== 4) return;
            loading = false;
            if (req.status !== 200) return;
            try {
                var data = JSON.parse(req.responseText);
                document.getElementById('staff-tbody').innerHTML = data.staff_rows || '';
                document.getElementById('staff-pagination').innerHTML = data.staff_pagination || '';
                document.getElementById('guests-tbody').innerHTML = data.guests_rows || '';
                document.getElementById('guests-pagination').innerHTML = data.guests_pagination || '';
            } catch (e) {}
        };
        req.send();
    }

    function doSearch() { fetchAndUpdate(getParams().toString()); }

    document.getElementById('btn-search-staff').addEventListener('click', doSearch);
    document.getElementById('btn-search-guests').addEventListener('click', doSearch);
    document.getElementById('btn-clear-staff').addEventListener('click', function() {
        document.getElementById('search-staff').value = '';
        doSearch();
    });
    document.getElementById('btn-clear-guests').addEventListener('click', function() {
        document.getElementById('search-guests').value = '';
        doSearch();
    });

    document.getElementById('search-staff').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); doSearch(); }
    });
    document.getElementById('search-guests').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); doSearch(); }
    });

    document.getElementById('users-tables-wrap').addEventListener('click', function(e) {
        var a = e.target.closest('a[href*="users"]');
        if (!a || !a.href) return;
        var path = new URL(a.href).pathname;
        var indexPath = new URL(baseUrl, window.location.origin).pathname;
        if (path !== indexPath) return;
        e.preventDefault();
        fetchAndUpdate(a.getAttribute('href'));
    });
})();
</script>
@endpush
@endsection

