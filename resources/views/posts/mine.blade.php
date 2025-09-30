<x-app-layout>
    <x-slot name="title">My Posts</x-slot>

    <!-- Header -->
    <section class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">My Posts</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your drafts and published stories</p>
                </div>
                <a href="{{ route('posts.create') }}" class="btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Write new story
                </a>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="bg-gray-50 dark:bg-gray-950 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            @if($posts->count() > 0)
                <!-- Filter Tabs -->
                <div class="flex flex-wrap gap-2 mb-6">
                    <a href="{{ route('posts.mine') }}"
                       class="px-4 py-2 rounded-lg font-medium transition-colors {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 border border-gray-200 dark:border-gray-800' }}">
                        All Posts
                    </a>
                    <a href="{{ route('posts.mine', ['status' => 'published']) }}"
                       class="px-4 py-2 rounded-lg font-medium transition-colors {{ request('status') === 'published' ? 'bg-green-600 text-white' : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 border border-gray-200 dark:border-gray-800' }}">
                        Published
                    </a>
                    <a href="{{ route('posts.mine', ['status' => 'draft']) }}"
                       class="px-4 py-2 rounded-lg font-medium transition-colors {{ request('status') === 'draft' ? 'bg-yellow-600 text-white' : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 border border-gray-200 dark:border-gray-800' }}">
                        Drafts
                    </a>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Posts</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $posts->total() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Published</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $posts->where('status', 'published')->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Drafts</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $posts->where('status', 'draft')->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Posts List -->
                <div class="space-y-4">
                    @foreach($posts as $post)
                        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 hover:shadow-md transition-shadow">
                            <div class="p-6">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <!-- Status Badge -->
                                        @if($post->status === 'draft')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400 mb-2">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                </svg>
                                                Draft
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 mb-2">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                Published
                                            </span>
                                        @endif

                                        <!-- Title -->
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2 line-clamp-2">
                                            <a href="{{ route('posts.show', $post) }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                                {{ $post->title }}
                                            </a>
                                        </h3>

                                        <!-- Excerpt -->
                                        @if($post->excerpt)
                                            <p class="text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">
                                                {{ $post->excerpt }}
                                            </p>
                                        @endif

                                        <!-- Meta -->
                                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ $post->created_at->format('M d, Y') }}
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                </svg>
                                                {{ $post->likes_count }} {{ Str::plural('like', $post->likes_count) }}
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                </svg>
                                                {{ $post->comments_count }} {{ Str::plural('comment', $post->comments_count) }}
                                            </span>
                                        </div>

                                        <!-- Tags -->
                                        @if($post->tags->count() > 0)
                                            <div class="flex flex-wrap gap-2 mt-3">
                                                @foreach($post->tags as $tag)
                                                    <a href="{{ route('tags.show', $tag) }}"
                                                       class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                                                        #{{ $tag->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex items-start gap-2">
                                        <a href="{{ route('posts.edit', $post) }}"
                                           class="p-2 text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors"
                                           title="Edit post">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('posts.destroy', $post) }}" method="POST"
                                              onsubmit="return confirm('Are you sure you want to delete this post?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                                    title="Delete post">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $posts->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-24">
                    <div class="max-w-md mx-auto">
                        <div class="w-24 h-24 bg-gradient-to-br from-primary-400 to-primary-600 dark:from-primary-500 dark:to-primary-700 rounded-full mx-auto mb-8 flex items-center justify-center">
                            <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">No posts yet</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-8">
                            Start writing your first story and share it with the world.
                        </p>
                        <a href="{{ route('posts.create') }}" class="btn-primary">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Write your first story
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </main>
</x-app-layout>