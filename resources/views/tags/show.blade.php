<x-app-layout>
    <x-slot name="title">{{ $tag->name }} Posts - {{ config('app.name', 'Blog') }}</x-slot>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li>
                        <a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">Home</a>
                    </li>
                    <li>
                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </li>
                    <li>
                        <a href="{{ route('tags.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">Tags</a>
                    </li>
                    <li>
                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </li>
                    <li class="text-gray-900 dark:text-gray-100 font-medium">{{ $tag->name }}</li>
                </ol>
            </nav>

            <div class="mt-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                    Posts tagged with "{{ $tag->name }}"
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    Found {{ $posts->total() }} {{ Str::plural('post', $posts->total()) }}
                </p>
            </div>
        </div>

        @if($posts->count() > 0)
            <!-- Posts Grid -->
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
            <!-- No Posts -->
            <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C20.832 18.477 19.247 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No posts found</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        No published posts are currently tagged with "{{ $tag->name }}".
                    </p>
                    <div class="flex justify-center space-x-4">
                        <a href="{{ route('tags.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                            Browse other tags
                        </a>
                        <a href="{{ route('home') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                            View all posts
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Related Tags -->
        @if($posts->count() > 0)
            @php
                // Get related tags from posts
                $relatedTags = collect();
                foreach($posts as $post) {
                    $relatedTags = $relatedTags->merge($post->tags);
                }
                $relatedTags = $relatedTags->filter(function($relatedTag) use ($tag) {
                    return $relatedTag->id !== $tag->id;
                })->unique('id')->take(10);
            @endphp

            @if($relatedTags->count() > 0)
                <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Related Tags</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($relatedTags as $relatedTag)
                            <a
                                href="{{ route('tags.show', $relatedTag->name) }}"
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-800 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition-colors"
                            >
                                {{ $relatedTag->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif

        <!-- Back to Tags -->
        <div class="mt-8 text-center">
            <a href="{{ route('tags.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                ← Browse all tags
            </a>
        </div>
    </div>
</x-app-layout>