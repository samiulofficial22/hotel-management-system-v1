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

        @can('guests.manage')
        <div class="col-12">
            <hr class="my-2">
            <h6 class="text-muted">Guest Portal (optional)</h6>
            <div class="form-check">
                <input type="checkbox" name="create_portal_user" id="create_portal_user" class="form-check-input" {{ old('create_portal_user') ? 'checked' : '' }}>
                <label class="form-check-label" for="create_portal_user">
                    Also create a portal login (Guest role) for this guest
                </label>
            </div>
            <small class="text-muted d-block mb-2">NEW – SAFE ADDITION: Creates a read-only guest portal account linked to this guest.</small>

            <div class="row mt-2">
                <div class="col-md-4 mb-2">
                    <label class="form-label">{{ __('Password') }} ({{ __('optional') }})</label>
                    <input type="password" name="portal_password" class="form-control @error('portal_password') is-invalid @enderror" autocomplete="new-password">
                    @error('portal_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-2">
                    <label class="form-label">{{ __('Confirm password') }}</label>
                    <input type="password" name="portal_password_confirmation" class="form-control" autocomplete="new-password">
                </div>
                <div class="col-md-4 mb-2 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" name="portal_send_reset" id="portal_send_reset" class="form-check-input" {{ old('portal_send_reset', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="portal_send_reset">
                            {{ __('Send password reset email to guest') }}
                        </label>
                    </div>
                </div>
            </div>
        </div>
        @endcan

        <div class="col-12"><button type="submit" class="btn btn-primary">Save</button> <a href="{{ route('guests.index') }}" class="btn btn-secondary">Cancel</a></div>
    </div>
</form>
@endsection
