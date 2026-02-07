@forelse($officeStaff as $user)
<tr>
    <td>
        <img src="{{ $user->profile_pic_url }}" alt="" class="rounded-circle" width="32" height="32" style="object-fit: cover;">
    </td>
    <td>{{ $user->name }}</td>
    <td>{{ $user->email }}</td>
    <td>
        @php $roles = $user->roles->pluck('name')->implode(', '); @endphp
        {{ $roles ?: '-' }}
    </td>
    <td>{{ $user->created_at?->format('Y-m-d H:i') }}</td>
    <td class="text-end">
        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-secondary">{{ __('Edit') }}</a>
        @if(auth()->id() !== $user->id)
        <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.Delete?') }}');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button>
        </form>
        @endif
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="text-muted">{{ __('No office staff.') }}</td>
</tr>
@endforelse
