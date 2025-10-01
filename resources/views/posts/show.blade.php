<x-app-layout>
    <x-slot name="title">{{ $post->title }} - {{ config('app.name', 'Blog') }}</x-slot>

    @push('meta')
        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="article">
        <meta property="og:url" content="{{ route('posts.show', $post) }}">
        <meta property="og:title" content="{{ $post->title }}">
        <meta property="og:description" content="{{ $post->excerpt ?? Str::limit(strip_tags($post->content), 160) }}">
        @if($post->hasImage())
            <meta property="og:image" content="{{ $post->getImageUrl() }}">
        @endif
        <meta property="og:site_name" content="{{ config('app.name', 'Blog') }}">
        <meta property="article:published_time" content="{{ $post->created_at->toIso8601String() }}">
        <meta property="article:author" content="{{ $post->user->name }}">

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="{{ route('posts.show', $post) }}">
        <meta name="twitter:title" content="{{ $post->title }}">
        <meta name="twitter:description" content="{{ $post->excerpt ?? Str::limit(strip_tags($post->content), 160) }}">
        @if($post->hasImage())
            <meta name="twitter:image" content="{{ $post->getImageUrl() }}">
        @endif
    @endpush

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Post Header -->
        <header class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-6">{{ $post->title }}</h1>

            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    @if($post->user)
                        @if($post->user->avatar)
                            <img src="{{ $post->user->avatar }}" alt="{{ $post->user->name }}" class="w-12 h-12 rounded-full mr-4">
                        @else
                            <div class="w-12 h-12 bg-gray-300 dark:bg-gray-600 rounded-full mr-4 flex items-center justify-center">
                                <span class="text-gray-600 dark:text-gray-300 text-lg font-medium">{{ substr($post->user->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div>
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('users.show', $post->user) }}" class="text-lg font-medium text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400">
                                    {{ $post->user->name }}
                                </a>
                                <x-follow-button :user="$post->user" />
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $post->created_at->format('M j, Y') }} · {{ $post->created_at->diffForHumans() }}</p>
                        </div>
                    @else
                        <div class="w-12 h-12 bg-gray-300 dark:bg-gray-600 rounded-full mr-4 flex items-center justify-center">
                            <span class="text-gray-600 dark:text-gray-300 text-lg font-medium">?</span>
                        </div>
                        <div>
                            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">Unknown Author</span>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $post->created_at->format('M j, Y') }} · {{ $post->created_at->diffForHumans() }}</p>
                        </div>
                    @endif
                </div>

                <div class="flex items-center space-x-4">
                    <x-like-button :post="$post" />
                    <x-bookmark-button :post="$post" />

                    @can('update', $post)
                        <a href="{{ route('posts.edit', $post) }}" class="text-gray-500 hover:text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                    @endcan
                </div>
            </div>

            <!-- Tags -->
            @if($post->tags->count() > 0)
                <div class="flex flex-wrap gap-2 mb-6">
                    @foreach($post->tags as $tag)
                        <span class="inline-block bg-gray-100 text-gray-700 text-sm px-3 py-1 rounded-full">
                            {{ $tag->name }}
                        </span>
                    @endforeach
                </div>
            @endif

            <!-- Excerpt -->
            @if($post->excerpt)
                <div class="text-xl text-gray-600 dark:text-gray-400 mb-8 font-medium leading-relaxed">
                    {{ $post->excerpt }}
                </div>
            @endif
        </header>

        <!-- Post Image (cover or featured) -->
        @if($post->hasImage())
            <div class="mb-8">
                <img src="{{ $post->getImageUrl() }}" alt="{{ $post->getImageAlt() }}" class="w-full h-auto rounded-lg shadow-sm">
            </div>
        @endif

        <!-- Post Content -->
        <article class="prose prose-lg max-w-none mb-12">
            {!! nl2br(e($post->content)) !!}
        </article>

        <!-- Post Stats -->
        <div class="flex items-center justify-between py-6 border-t border-gray-200 mb-8">
            <div class="flex items-center space-x-6">
                <x-like-button :post="$post" />
                <span class="flex items-center text-gray-500">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    {{ $post->comments_count }} {{ Str::plural('comment', $post->comments_count) }}
                </span>
            </div>
            <x-bookmark-button :post="$post" />
        </div>

        <!-- Comments Section -->
        <section class="comments">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">
                Comments ({{ $post->comments_count }})
            </h3>

            <!-- Comment Form -->
            @auth
                <form action="{{ route('comments.store', $post->id) }}" method="POST" class="mb-8">
                    @csrf
                    <div class="mb-4">
                        <textarea
                            name="content"
                            rows="4"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Write a comment..."
                            required
                        >{{ old('content') }}</textarea>
                        @error('content')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Post Comment
                    </button>
                </form>
            @else
                <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                    <p class="text-gray-600">
                        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800">Sign in</a>
                        to join the conversation.
                    </p>
                </div>
            @endauth

            <!-- Comments List -->
            @if($post->comments->count() > 0)
                <div class="space-y-6">
                    @foreach($post->comments as $comment)
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center mb-2">
                                    @if($comment->user)
                                        @if($comment->user->avatar)
                                            <img src="{{ $comment->user->avatar }}" alt="{{ $comment->user->name }}" class="w-8 h-8 rounded-full mr-3">
                                        @else
                                            <div class="w-8 h-8 bg-gray-300 rounded-full mr-3 flex items-center justify-center">
                                                <span class="text-gray-600 text-xs font-medium">{{ substr($comment->user->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('users.show', $comment->user) }}" class="font-medium text-gray-900 hover:text-blue-600">
                                                {{ $comment->user->name }}
                                            </a>
                                            <p class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                                        </div>
                                    @else
                                        <div class="w-8 h-8 bg-gray-300 rounded-full mr-3 flex items-center justify-center">
                                            <span class="text-gray-600 text-xs font-medium">?</span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-900">Unknown User</span>
                                            <p class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                                        </div>
                                    @endif
                                </div>

                                @can('delete', $comment)
                                    <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="inline" onsubmit="return confirmDelete(event, 'comment')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 text-sm font-medium transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                @endcan
                            </div>

                            <div class="mt-2">
                                <p class="text-gray-700">{{ $comment->content }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <p>No comments yet. Be the first to comment!</p>
                </div>
            @endif
        </section>

        <!-- Social Share Buttons -->
        <div class="mt-12 pt-8 border-t border-gray-200">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Share this post</h4>
            <div class="flex items-center space-x-3">
                <!-- Facebook -->
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('posts.show', $post)) }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="flex items-center justify-center w-10 h-10 bg-[#4267B2] hover:bg-[#365899] text-white rounded-full transition-colors"
                   title="Share on Facebook">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </a>

                <!-- Copy Link -->
                <button onclick="copyToClipboard('{{ route('posts.show', $post) }}')"
                        class="flex items-center justify-center w-10 h-10 bg-gray-600 dark:bg-gray-700 hover:bg-gray-700 dark:hover:bg-gray-600 text-white rounded-full transition-all duration-200 hover:scale-110"
                        title="Copy link">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Related Posts -->
        @php
            $relatedPosts = $post->relatedPosts(3);
        @endphp

        @if($relatedPosts->count() > 0)
            <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-800">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Related Posts</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($relatedPosts as $relatedPost)
                        <article class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-shadow">
                            @if($relatedPost->hasImage())
                                <a href="{{ route('posts.show', $relatedPost) }}">
                                    <img src="{{ $relatedPost->getImageUrl() }}" alt="{{ $relatedPost->getImageAlt() }}"
                                         loading="lazy"
                                         class="w-full h-48 object-cover">
                                </a>
                            @endif
                            <div class="p-4">
                                <h4 class="font-bold text-lg mb-2 line-clamp-2">
                                    <a href="{{ route('posts.show', $relatedPost) }}" class="text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ $relatedPost->title }}
                                    </a>
                                </h4>
                                @if($relatedPost->excerpt)
                                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-3 line-clamp-2">{{ $relatedPost->excerpt }}</p>
                                @endif
                                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                                    <span>{{ $relatedPost->reading_time }}</span>
                                    <span>{{ $relatedPost->likes_count }} likes</span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                showNotification('Link copied to clipboard!', 'success');
            }, function(err) {
                console.error('Could not copy text: ', err);
                showNotification('Failed to copy link', 'error');
            });
        }

        function confirmDelete(event, type) {
            event.preventDefault();
            const form = event.target;

            // Create modal overlay
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 z-[100] flex items-center justify-center p-4';
            modal.style.animation = 'fadeIn 0.2s ease-out';

            // Add backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'absolute inset-0 bg-black/60 backdrop-blur-sm';
            backdrop.style.animation = 'fadeIn 0.2s ease-out';
            modal.appendChild(backdrop);

            // Create modal content
            const modalContent = document.createElement('div');
            modalContent.className = 'relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl max-w-md w-full overflow-hidden';
            modalContent.style.animation = 'slideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)';

            modalContent.innerHTML = `
                <div class="p-6">
                    <!-- Icon -->
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 dark:bg-red-900/20 mb-4">
                        <svg class="h-8 w-8 text-red-600 dark:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>

                    <!-- Title -->
                    <h3 class="text-center text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                        Delete ${type}?
                    </h3>

                    <!-- Description -->
                    <p class="text-center text-gray-600 dark:text-gray-400 mb-6">
                        This action cannot be undone. The ${type} will be permanently removed from the system.
                    </p>

                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="button"
                                class="cancel-btn flex-1 px-4 py-2.5 border-2 border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-50 dark:hover:bg-gray-800 active:scale-95 transition-all duration-150">
                            Cancel
                        </button>
                        <button type="button"
                                class="delete-btn flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl active:scale-95 transition-all duration-150">
                            Delete ${type}
                        </button>
                    </div>
                </div>
            `;

            modal.appendChild(modalContent);
            document.body.appendChild(modal);

            // Add animations
            const style = document.createElement('style');
            style.textContent = `
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                @keyframes slideUp {
                    from {
                        opacity: 0;
                        transform: translateY(20px) scale(0.95);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0) scale(1);
                    }
                }
                @keyframes fadeOut {
                    from { opacity: 1; }
                    to { opacity: 0; }
                }
            `;
            document.head.appendChild(style);

            // Handle cancel
            const cancelBtn = modalContent.querySelector('.cancel-btn');
            const closeModal = () => {
                modal.style.animation = 'fadeOut 0.2s ease-out';
                setTimeout(() => {
                    modal.remove();
                    style.remove();
                }, 200);
            };

            cancelBtn.addEventListener('click', closeModal);
            backdrop.addEventListener('click', closeModal);

            // Handle delete
            const deleteBtn = modalContent.querySelector('.delete-btn');
            deleteBtn.addEventListener('click', () => {
                // Remove the onsubmit handler and submit
                form.onsubmit = null;
                form.submit();
            });

            // ESC key to close
            const handleEsc = (e) => {
                if (e.key === 'Escape') {
                    closeModal();
                    document.removeEventListener('keydown', handleEsc);
                }
            };
            document.addEventListener('keydown', handleEsc);

            return false;
        }

        function showNotification(message, type = 'info') {
            window.dispatchEvent(new CustomEvent('show-notification', {
                detail: { message, type }
            }));
        }
    </script>
    @endpush
</x-app-layout>