@extends('layouts.app')
@section('title', 'Add Menu Item')
@section('content')
<h1 class="h3 mb-4">Add Menu Item: {{ $outlet->name }}</h1>
<form method="POST" action="{{ route('menu.items.store', $outlet) }}">
@csrf
<div class="mb-3">
    <label class="form-label">Category</label>
    <select name="menu_category_id" class="form-select" required>
        <option value="">— Select —</option>
        @foreach($categories as $c)
        <option value="{{ $c->id }}" {{ old('menu_category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
        @endforeach
    </select>
    @error('menu_category_id')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label">Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
    @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label">Price (selling)</label>
    <input type="number" name="price" class="form-control" step="0.01" min="0" value="{{ old('price') }}" required>
    @error('price')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label">Cost (optional, for profit/loss)</label>
    <input type="number" name="cost" class="form-control" step="0.01" min="0" value="{{ old('cost') }}" placeholder="Unit cost">
    @error('cost')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label">Description (optional)</label>
    <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
</div>
<div class="mb-3 form-check">
    <input type="checkbox" name="is_available" value="1" class="form-check-input" {{ old('is_available', true) ? 'checked' : '' }}>
    <label class="form-check-label">Available</label>
</div>
<button type="submit" class="btn btn-primary">Create</button>
<a href="{{ route('menu.items.index', $outlet) }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
