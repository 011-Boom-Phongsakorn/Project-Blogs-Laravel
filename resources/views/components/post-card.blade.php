<article class="post-card group">
    <!-- Cover Image -->
    @if($post->cover_image)
        <div class="relative overflow-hidden">
            <img src="{{ $post->cover_image }}" alt="{{ $post->title }}"
                 class="post-card-image group-hover:scale-105 transition-transform duration-300">
            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        </div>
    @elseif($post->hasImage())
        <div class="relative overflow-hidden">
            <img src="{{ $post->getImageUrl() }}" alt="{{ $post->getImageAlt() }}"
                 class="post-card-image group-hover:scale-105 transition-transform duration-300">
            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        </div>
    @endif

    <div class="post-card-content">
        <!-- Author Info -->
        <div class="flex items-center mb-4">
            @if($post->user)
                @if($post->user->avatar)
                    <img src="{{ $post->user->avatar }}" alt="{{ $post->user->name }}" class="author-avatar mr-3">
                @else
                    <div class="w-8 h-8 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full mr-3 flex items-center justify-center">
                        <span class="text-white text-xs font-semibold">{{ substr($post->user->name, 0, 1) }}</span>
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <a href="{{ route('users.show', $post->user) }}" class="text-sm font-medium text-gray-900 hover:text-primary-600 transition-colors">
                        {{ $post->user->name }}
                    </a>
                    <div class="flex items-center text-xs text-gray-500 mt-0.5">
                        <time datetime="{{ $post->created_at->toISOString() }}">
                            {{ $post->created_at->format('M j, Y') }}
                        </time>
                        <span class="mx-1">•</span>
                        <span>{{ $post->reading_time ?? '5 min read' }}</span>
                    </div>
                </div>
            @else
                <div class="w-8 h-8 bg-gray-300 rounded-full mr-3 flex items-center justify-center">
                    <span class="text-gray-600 text-xs font-medium">?</span>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="text-sm font-medium text-gray-900">Unknown Author</span>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $post->created_at->format('M j, Y') }}</p>
                </div>
            @endif
        </div>

        <!-- Post Title -->
        <h2 class="post-title line-clamp-2 mb-3">
            <a href="{{ route('posts.show', $post) }}" class="text-balance">
                {{ $post->title }}
            </a>
        </h2>

        <!-- Post Excerpt -->
        @if($post->excerpt)
            <p class="post-excerpt text-balance">{{ $post->excerpt }}</p>
        @else
            <p class="post-excerpt text-balance">{{ Str::limit(strip_tags($post->content), 120) }}</p>
        @endif

        <!-- Tags -->
        @if($post->tags->count() > 0)
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach($post->tags->take(3) as $tag)
                    <a href="{{ route('tags.show', $tag->name) }}" class="tag hover:tag-primary">
                        {{ $tag->name }}
                    </a>
                @endforeach
                @if($post->tags->count() > 3)
                    <span class="tag text-gray-500">+{{ $post->tags->count() - 3 }}</span>
                @endif
            </div>
        @endif

        <!-- Post Stats & Actions -->
        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
            <div class="flex items-center space-x-4">
                <!-- Like Button -->
                <button class="like-button {{ auth()->check() && $post->likes()->where('user_id', auth()->id())->exists() ? 'liked' : '' }}"
                        data-like-toggle data-post-id="{{ $post->id }}">
                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                    <span class="like-count">{{ $post->likes_count ?? $post->like_count ?? 0 }}</span>
                </button>

                <!-- Comments -->
                <a href="{{ route('posts.show', $post) }}#comments" class="social-button">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <span>{{ $post->comments_count ?? 0 }}</span>
                </a>
            </div>

            <!-- Bookmark Button -->
            <button class="bookmark-button {{ auth()->check() && $post->bookmarks()->where('user_id', auth()->id())->exists() ? 'bookmarked' : '' }}"
                    data-bookmark-toggle data-post-id="{{ $post->id }}">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                </svg>
            </button>
        </div>
    </div>
</article>