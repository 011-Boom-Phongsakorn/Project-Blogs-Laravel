@php
$following = auth()->check() && isset($user) && auth()->user()->isFollowing($user->id);
@endphp

@auth
    @if($user && auth()->id() !== $user->id)
        <button class="follow-button {{ $following ? 'following' : '' }}"
                data-follow-toggle
                data-user-id="{{ $user->id }}"
                title="{{ $following ? 'Unfollow' : 'Follow' }} {{ $user->name }}">
            <span class="button-text">{{ $following ? 'Following' : 'Follow' }}</span>
        </button>
    @endif
@else
    @if($user)
        <a href="{{ route('login') }}" class="follow-button" title="Sign in to follow {{ $user->name }}">
            Follow
        </a>
    @endif
@endauth