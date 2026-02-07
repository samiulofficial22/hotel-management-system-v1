@forelse($guests as $user)
<tr>
    <td>
        <img src="{{ $user->profile_pic_url }}" alt="" class="rounded-circle" width="32" height="32" style="object-fit: cover;">
    </td>
    <td>{{ $user->name }}</td>
    <td>{{ $user->email }}</td>
    <td>
        @if($user->guest)
            <a href="{{ route('guests.show', $user->guest) }}">{{ $user->guest->full_name }}</a>
        @else
            <span class="text-muted">-</span>
        @endif
    </td>
    <td>{{ $user->created_at?->format('Y-m-d H:i') }}</td>
    <td class="text-end">
        @if($user->guest)
            <a href="{{ route('guests.show', $user->guest) }}" class="btn btn-sm btn-outline-primary me-1">{{ __('View') }}</a>
            <a href="{{ route('guests.edit', $user->guest) }}" class="btn btn-sm btn-outline-secondary me-1">{{ __('Edit') }}</a>
        @else
            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-secondary me-1">{{ __('Edit') }}</a>
        @endif
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
    <td colspan="6" class="text-muted">{{ __('No guest portal users.') }}</td>
</tr>
@endforelse
