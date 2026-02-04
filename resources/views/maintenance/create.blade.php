@extends('layouts.app')
@section('title', __('messages.New Maintenance Request'))
@section('content')
<h1 class="h3 mb-4">{{ __('messages.New Maintenance Request') }}</h1>
<form method="POST" action="{{ route('maintenance.store') }}">
@csrf
<div class="mb-3"><label class="form-label">{{ __('messages.Room (optional)') }}</label><select name="room_id" class="form-select"><option value="">{{ __('messages.None') }}</option>@foreach($rooms as $r)<option value="{{ $r->id }}">{{ $r->number }}</option>@endforeach</select></div>
<div class="mb-3"><label class="form-label">{{ __('messages.Title') }}</label><input type="text" name="title" class="form-control" required></div>
<div class="mb-3"><label class="form-label">{{ __('messages.Description') }}</label><textarea name="description" class="form-control" rows="2"></textarea></div>
<div class="mb-3"><label class="form-label">{{ __('messages.Priority') }}</label><select name="priority" class="form-select"><option value="normal" selected>{{ __('messages.Normal') }}</option><option value="high">{{ __('messages.High') }}</option><option value="urgent">{{ __('messages.Urgent') }}</option></select></div>
<button type="submit" class="btn btn-primary">{{ __('messages.Create') }}</button>
<a href="{{ route('maintenance.index') }}" class="btn btn-secondary">{{ __('messages.Cancel') }}</a>
</form>
@endsection
