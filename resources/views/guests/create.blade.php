@extends('layouts.app')
@section('title', 'Add Guest')
@section('content')
<h1 class="h3 mb-4">Add Guest</h1>
<form action="{{ route('guests.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">First Name</label><input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required></div>
        <div class="col-md-6"><label class="form-label">Last Name</label><input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required></div>
        <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email') }}"></div>
        <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="{{ old('phone') }}"></div>
        <div class="col-12"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea></div>

        <div class="col-12"><hr class="my-2"><h6 class="text-muted">NID (optional)</h6></div>
        <div class="col-md-6"><label class="form-label">NID Number</label><input type="text" name="nid_number" class="form-control" value="{{ old('nid_number') }}" placeholder="National ID number"></div>
        <div class="col-md-6"><label class="form-label">NID Photo – Front</label><input type="file" name="nid_photo_front" class="form-control" accept="image/*"></div>
        <div class="col-md-6"><label class="form-label">NID Photo – Back</label><input type="file" name="nid_photo_back" class="form-control" accept="image/*"></div>

        <div class="col-12"><button type="submit" class="btn btn-primary">Save</button> <a href="{{ route('guests.index') }}" class="btn btn-secondary">Cancel</a></div>
    </div>
</form>
@endsection
