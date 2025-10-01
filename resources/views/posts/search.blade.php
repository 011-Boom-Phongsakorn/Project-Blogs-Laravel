<x-app-layout>
    <x-slot name="title">
        @if($query)
            Search results for "{{ $query }}" - {{ config('app.name', 'Blog') }}
        @else
            Search - {{ config('app.name', 'Blog') }}
        @endif
    </x-slot>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Search Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                @if($query)
                    Search Results for "{{ $query }}"
                @else
                    Search Posts
                @endif
            </h1>

            @if($query)
                <p class="text-gray-600 dark:text-gray-400">
                    Found {{ $totalResults }} {{ Str::plural('result', $totalResults) }}
                </p>
            @endif
        </div>

        <!-- Advanced Search Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <form method="GET" action="{{ route('posts.search') }}" class="space-y-4">
                <!-- Search Query -->
                <div>
                    <label for="search-q" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Search Posts
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            id="search-q"
                            name="q"
                            value="{{ $query }}"
                            class="w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 pl-10"
                            placeholder="Search by title, content, or excerpt..."
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Filters Row -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Tag Filter -->
                    <div>
                        <label for="search-tag" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Filter by Tag
                        </label>
                        <select
                            id="search-tag"
                            name="tag"
                            class="w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400"
                        >
                            <option value="">All Tags</option>
                            @foreach($availableTags as $tagOption)
                                <option value="{{ $tagOption->name }}" {{ $tag === $tagOption->name ? 'selected' : '' }}>
                                    {{ $tagOption->name }} ({{ $tagOption->posts_count }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Author Filter -->
                    <div>
                        <label for="search-author" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Filter by Author
                        </label>
                        <input
                            type="text"
                            id="search-author"
                            name="author"
                            value="{{ $author }}"
                            class="w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400"
                            placeholder="Author name or email..."
                        >
                    </div>

                    <!-- Sort By -->
                    <div>
                        <label for="search-sort" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Sort By
                        </label>
                        <select
                            id="search-sort"
                            name="sort"
                            class="w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400"
                        >
                            <option value="latest" {{ $sortBy === 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="oldest" {{ $sortBy === 'oldest' ? 'selected' : '' }}>Oldest</option>
                            <option value="popular" {{ $sortBy === 'popular' ? 'selected' : '' }}>Most Popular</option>
                            <option value="most_commented" {{ $sortBy === 'most_commented' ? 'selected' : '' }}>Most Commented</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-4">
                    <a href="{{ route('posts.search') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                        Clear Filters
                    </a>
                    <button
                        type="submit"
                        class="px-6 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-md hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                    >
                        Search
                    </button>
                </div>
            </form>
        </div>

        <!-- Active Filters -->
        @if($query || $tag || $author || $sortBy !== 'latest')
            <div class="mb-6">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Active filters:</span>

                    @if($query)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                            Query: "{{ $query }}"
                            <a href="{{ request()->fullUrlWithQuery(['q' => null]) }}" class="ml-2 text-blue-600 dark:text-blue-300 hover:text-blue-800 dark:hover:text-blue-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </a>
                        </span>
                    @endif

                    @if($tag)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                            Tag: {{ $tag }}
                            <a href="{{ request()->fullUrlWithQuery(['tag' => null]) }}" class="ml-2 text-green-600 dark:text-green-300 hover:text-green-800 dark:hover:text-green-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </a>
                        </span>
                    @endif

                    @if($author)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                            Author: {{ $author }}
                            <a href="{{ request()->fullUrlWithQuery(['author' => null]) }}" class="ml-2 text-purple-600 dark:text-purple-300 hover:text-purple-800 dark:hover:text-purple-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </a>
                        </span>
                    @endif

                    @if($sortBy !== 'latest')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200">
                            Sort: {{ ucfirst(str_replace('_', ' ', $sortBy)) }}
                            <a href="{{ request()->fullUrlWithQuery(['sort' => null]) }}" class="ml-2 text-orange-600 dark:text-orange-300 hover:text-orange-800 dark:hover:text-orange-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </a>
                        </span>
                    @endif
                </div>
            </div>
        @endif

        <!-- Search Results -->
        @if($posts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($posts as $post)
                    <x-post-card :post="$post" />
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="flex justify-center">
                {{ $posts->links() }}
            </div>
        @else
            <!-- No Results -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">No posts found</h3>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    @if($query)
                        Try adjusting your search terms or filters.
                    @else
                        Start by entering a search query above.
                    @endif
                </p>
                @if($query || $tag || $author || $sortBy !== 'latest')
                    <a href="{{ route('posts.search') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900 hover:bg-blue-200 dark:hover:bg-blue-800">
                        Clear all filters
                    </a>
                @endif
            </div>
        @endif

        <!-- Popular Tags -->
        @if($availableTags->count() > 0 && !$tag)
            <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Popular Tags</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($availableTags as $popularTag)
                        <a
                            href="{{ route('posts.search', ['tag' => $popularTag->name]) }}"
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                        >
                            {{ $popularTag->name }}
                            <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">({{ $popularTag->posts_count }})</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>