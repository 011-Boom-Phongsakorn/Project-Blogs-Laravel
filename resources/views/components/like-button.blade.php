@php
$liked = auth()->check() && $post->isLikedBy(auth()->id());
$likeCount = $post->likes_count ?? $post->like_count ?? 0;
@endphp

@auth
<button class="like-button {{ $liked ? 'liked' : '' }}"
        data-like-toggle
        data-post-id="{{ $post->id }}"
        title="{{ $liked ? 'Unlike this post' : 'Like this post' }}">
    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 24 24">
        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
    </svg>
    <span class="like-count">{{ $likeCount }}</span>
</button>
@else
<a href="{{ route('login') }}" class="like-button" title="Sign in to like this post">
    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
    </svg>
    <span class="like-count">{{ $likeCount }}</span>
</a>
@endauth