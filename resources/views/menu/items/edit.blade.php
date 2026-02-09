@extends('layouts.app')
@section('title', 'Edit Menu Item')
@section('content')
<h1 class="h3 mb-4">Edit Menu Item: {{ $item->name }}</h1>
<form method="POST" action="{{ route('menu.items.update', [$outlet, $item]) }}" enctype="multipart/form-data">
@csrf
@method('PUT')
<div class="mb-3">
    <label class="form-label">Category</label>
    <select name="menu_category_id" class="form-select" required>
        @foreach($categories as $c)
        <option value="{{ $c->id }}" {{ old('menu_category_id', $item->menu_category_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label class="form-label">Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $item->name) }}" required>
    @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label">Price (selling)</label>
    <input type="number" name="price" class="form-control" step="0.01" min="0" value="{{ old('price', $item->price) }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Cost (optional, for profit/loss)</label>
    <input type="number" name="cost" class="form-control" step="0.01" min="0" value="{{ old('cost', $item->cost) }}" placeholder="Unit cost">
</div>
<div class="mb-3">
    <label class="form-label">Description (optional)</label>
    <textarea name="description" class="form-control" rows="2">{{ old('description', $item->description) }}</textarea>
</div>
<div class="mb-3">
    <label class="form-label">Image (optional)</label>
    @if($item->image)
        <div class="mb-2">
            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="img-thumbnail" style="max-height: 80px; object-fit: cover;">
            <small class="d-block text-muted">Current image. Upload new to replace.</small>
        </div>
    @endif
    <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png">
    <small class="text-muted">JPG or PNG, max 2MB. Leave blank to keep current.</small>
    @error('image')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
<div class="mb-3 form-check">
    <input type="checkbox" name="is_available" value="1" class="form-check-input" {{ old('is_available', $item->is_available) ? 'checked' : '' }}>
    <label class="form-check-label">Available</label>
</div>
<button type="submit" class="btn btn-primary">Update</button>
<a href="{{ route('menu.items.index', $outlet) }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
