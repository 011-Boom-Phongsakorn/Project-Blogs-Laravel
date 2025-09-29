<x-app-layout>
    <x-slot name="title">Search Results - {{ config('app.name', 'Blog') }}</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Search Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">
                @if($query)
                    Search Results for "{{ $query }}"
                @else
                    Search Posts
                @endif
            </h1>

            <!-- Search Form -->
            <form action="{{ route('posts.search') }}" method="GET" class="max-w-2xl">
                <div class="flex">
                    <input
                        type="text"
                        name="query"
                        value="{{ $query }}"
                        placeholder="Search for posts..."
                        class="flex-1 border-gray-300 rounded-l-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                    <button
                        type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Search
                    </button>
                </div>
            </form>

            @if($query)
                <div class="mt-4 text-gray-600">
                    Found {{ $posts->total() }} {{ Str::plural('result', $posts->total()) }}
                    @if($posts->total() > 0)
                        (showing {{ $posts->firstItem() }}-{{ $posts->lastItem() }})
                    @endif
                </div>
            @endif
        </div>

        @if($query)
            @if($posts->count() > 0)
                <!-- Search Results -->
                <div class="space-y-6">
                    @foreach($posts as $post)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <!-- Post Title -->
                                    <h2 class="text-xl font-bold mb-2">
                                        <a href="{{ route('posts.show', $post) }}" class="text-gray-900 hover:text-blue-600">
                                            {{ $post->title }}
                                        </a>
                                    </h2>

                                    <!-- Post Excerpt -->
                                    @if($post->excerpt)
                                        <p class="text-gray-600 mb-3">{{ Str::limit($post->excerpt, 200) }}</p>
                                    @else
                                        <p class="text-gray-600 mb-3">{{ Str::limit(strip_tags($post->content), 200) }}</p>
                                    @endif

                                    <!-- Post Meta -->
                                    <div class="flex items-center text-sm text-gray-500 space-x-4">
                                        <div class="flex items-center">
                                            @if($post->user)
                                                @if($post->user->avatar)
                                                    <img src="{{ $post->user->avatar }}" alt="{{ $post->user->name }}" class="w-6 h-6 rounded-full mr-2">
                                                @else
                                                    <div class="w-6 h-6 bg-gray-300 rounded-full mr-2 flex items-center justify-center">
                                                        <span class="text-gray-600 text-xs">{{ substr($post->user->name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                                <a href="{{ route('users.show', $post->user) }}" class="hover:text-blue-600">
                                                    {{ $post->user->name }}
                                                </a>
                                            @else
                                                <span>Unknown Author</span>
                                            @endif
                                        </div>
                                        <span>{{ $post->created_at->format('M j, Y') }}</span>
                                        <span>{{ $post->created_at->diffForHumans() }}</span>
                                        @if($post->comments_count > 0)
                                            <span>{{ $post->comments_count }} {{ Str::plural('comment', $post->comments_count) }}</span>
                                        @endif
                                    </div>

                                    <!-- Tags -->
                                    @if($post->tags->count() > 0)
                                        <div class="flex flex-wrap gap-2 mt-3">
                                            @foreach($post->tags as $tag)
                                                <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">
                                                    {{ $tag->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- Cover Image -->
                                @if($post->cover_image)
                                    <div class="ml-6 flex-shrink-0">
                                        <img src="{{ $post->cover_image }}" alt="{{ $post->title }}" class="w-32 h-20 object-cover rounded">
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $posts->appends(request()->query())->links() }}
                </div>
            @else
                <!-- No Results -->
                <div class="text-center py-16 bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="text-gray-500">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <h3 class="text-2xl font-medium text-gray-900 mb-2">No results found</h3>
                        <p class="text-gray-600 mb-6 max-w-md mx-auto">
                            We couldn't find any posts matching "{{ $query }}". Try different keywords or browse our latest posts.
                        </p>
                        <a href="{{ route('home') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700">
                            Browse Latest Posts
                        </a>
                    </div>
                </div>
            @endif
        @else
            <!-- Search Suggestions -->
            <div class="text-center py-16 bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="text-gray-500">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <h3 class="text-2xl font-medium text-gray-900 mb-2">Search for Posts</h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto">
                        Enter keywords above to search through our collection of posts. You can search by title, content, or author.
                    </p>
                    <div class="text-sm text-gray-500">
                        <p class="mb-2"><strong>Tips:</strong></p>
                        <ul class="space-y-1">
                            <li>Use specific keywords for better results</li>
                            <li>Try different spellings or synonyms</li>
                            <li>Search for author names to find their posts</li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Popular Tags (Optional) -->
        @if(!$query && isset($popularTags) && $popularTags->count() > 0)
            <div class="mt-12">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Popular Tags</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($popularTags as $tag)
                        <a href="{{ route('tags.show', $tag->name) }}" class="inline-block bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full hover:bg-blue-200">
                            {{ $tag->name }} ({{ $tag->posts_count }})
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>