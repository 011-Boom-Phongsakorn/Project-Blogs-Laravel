<x-app-layout>
    <x-slot name="title">My Bookmarks - {{ config('app.name', 'Blog') }}</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">My Bookmarks</h1>
            <p class="text-gray-600 dark:text-gray-400">Posts you've saved for later reading</p>
        </div>

        @if($bookmarkedPosts->count() > 0)
            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach($bookmarkedPosts as $post)
                    <x-post-card :post="$post" />
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $bookmarkedPosts->links() }}
            </div>
        @else
            <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                    </svg>
                    <h3 class="text-2xl font-medium text-gray-900 dark:text-gray-100 mb-2">No bookmarks yet</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
                        When you find interesting posts, click the bookmark icon to save them here for later reading.
                    </p>
                    <a href="{{ route('home') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                        Discover Posts
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>