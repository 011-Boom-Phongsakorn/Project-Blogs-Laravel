@php
$bookmarked = auth()->check() && $post->isBookmarkedBy(auth()->id());
@endphp

@auth
<button class="bookmark-button {{ $bookmarked ? 'bookmarked' : '' }}"
        data-bookmark-toggle
        data-post-id="{{ $post->id }}"
        title="{{ $bookmarked ? 'Remove bookmark' : 'Bookmark this post' }}">
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
        <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
    </svg>
</button>
@else
<a href="{{ route('login') }}" class="bookmark-button" title="Sign in to bookmark this post">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
    </svg>
</a>
@endauth