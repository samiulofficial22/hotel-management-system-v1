@extends('layouts.app')
@section('title', 'New Campaign')
@section('content')
<h1 class="h3 mb-4">New Marketing Campaign</h1>
<form method="POST" action="{{ route('marketing.store') }}">
@csrf
<div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" required></div>
<div class="mb-3"><label class="form-label">Type</label><select name="type" class="form-select"><option value="email">Email</option><option value="sms">SMS</option><option value="both">Both</option></select></div>
<div class="mb-3"><label class="form-label">Start date</label><input type="date" name="start_date" class="form-control" required></div>
<div class="mb-3"><label class="form-label">End date</label><input type="date" name="end_date" class="form-control"></div>
<div class="mb-3"><label class="form-label">Subject</label><input type="text" name="subject" class="form-control"></div>
<div class="mb-3"><label class="form-label">Body</label><textarea name="body" class="form-control" rows="4"></textarea></div>
<button type="submit" class="btn btn-primary">Create</button>
<a href="{{ route('marketing.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
