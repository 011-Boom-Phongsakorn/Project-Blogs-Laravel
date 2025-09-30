<x-app-layout>
    <x-slot name="title">Tags - {{ config('app.name', 'Blog') }}</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">Browse by Tags</h1>
            <p class="text-gray-600 dark:text-gray-400">
                Explore posts organized by topics and themes. Click on any tag to see related articles.
            </p>
        </div>

        @if($tags->count() > 0)
            <!-- Tags Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                @foreach($tags as $tag)
                    <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 p-6 hover:shadow-md transition-shadow">
                        <a href="{{ route('tags.show', $tag->name) }}" class="block">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                {{ $tag->name }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">
                                {{ $tag->posts_count }} {{ Str::plural('post', $tag->posts_count) }}
                            </p>
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="flex justify-center">
                {{ $tags->links() }}
            </div>
        @else
            <!-- No Tags -->
            <div class="text-center py-12 bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800">
                <div class="text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No tags found</h3>
                    <p class="text-gray-600 dark:text-gray-400">Tags will appear here as authors create posts with topics.</p>
                </div>
            </div>
        @endif

        <!-- Back to Home -->
        <div class="mt-8 text-center">
            <a href="{{ route('home') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                ← Back to all posts
            </a>
        </div>
    </div>
</x-app-layout>