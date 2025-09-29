<x-app-layout>
    <x-slot name="title">{{ $post->title }} - {{ config('app.name', 'Blog') }}</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Post Header -->
        <header class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-6">{{ $post->title }}</h1>

            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    @if($post->user)
                        @if($post->user->avatar)
                            <img src="{{ $post->user->avatar }}" alt="{{ $post->user->name }}" class="w-12 h-12 rounded-full mr-4">
                        @else
                            <div class="w-12 h-12 bg-gray-300 rounded-full mr-4 flex items-center justify-center">
                                <span class="text-gray-600 text-lg font-medium">{{ substr($post->user->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div>
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('users.show', $post->user) }}" class="text-lg font-medium text-gray-900 hover:text-blue-600">
                                    {{ $post->user->name }}
                                </a>
                                <x-follow-button :user="$post->user" />
                            </div>
                            <p class="text-sm text-gray-500">{{ $post->created_at->format('M j, Y') }} · {{ $post->created_at->diffForHumans() }}</p>
                        </div>
                    @else
                        <div class="w-12 h-12 bg-gray-300 rounded-full mr-4 flex items-center justify-center">
                            <span class="text-gray-600 text-lg font-medium">?</span>
                        </div>
                        <div>
                            <span class="text-lg font-medium text-gray-900">Unknown Author</span>
                            <p class="text-sm text-gray-500">{{ $post->created_at->format('M j, Y') }} · {{ $post->created_at->diffForHumans() }}</p>
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

            <!-- Cover Image -->
            @if($post->cover_image)
                <div class="mb-8">
                    <img src="{{ $post->cover_image }}" alt="{{ $post->title }}" class="w-full h-64 md:h-96 object-cover rounded-lg">
                </div>
            @endif

            <!-- Excerpt -->
            @if($post->excerpt)
                <div class="text-xl text-gray-600 mb-8 font-medium leading-relaxed">
                    {{ $post->excerpt }}
                </div>
            @endif
        </header>

        <!-- Featured Image -->
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
                                    <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm"
                                                onclick="return confirm('Are you sure you want to delete this comment?')">
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
    </div>
</x-app-layout>