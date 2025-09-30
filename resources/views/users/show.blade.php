<x-app-layout>
    <x-slot name="title">{{ $user->name }} - {{ config('app.name', 'Blog') }}</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- User Profile Header -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-8 mb-8">
            <div class="flex items-start justify-between">
                <div class="flex items-center">
                    @if($user->avatar)
                        <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full mr-6">
                    @else
                        <div class="w-24 h-24 bg-gray-300 dark:bg-gray-700 rounded-full mr-6 flex items-center justify-center">
                            <span class="text-gray-600 dark:text-gray-300 text-3xl font-medium">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">{{ $user->name }}</h1>
                        @if($user->bio)
                            <p class="text-gray-600 dark:text-gray-400 mb-4 max-w-2xl">{{ $user->bio }}</p>
                        @endif
                        <div class="flex items-center space-x-6 text-sm text-gray-500 dark:text-gray-400">
                            <span>{{ $user->posts_count }} {{ Str::plural('post', $user->posts_count) }}</span>
                            <a href="{{ route('users.followers', $user) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $user->followers_count }} {{ Str::plural('follower', $user->followers_count) }}
                            </a>
                            <a href="{{ route('users.following', $user) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $user->following_count }} following
                            </a>
                            <span>Joined {{ $user->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <x-follow-button :user="$user" />
                    @if(auth()->id() === $user->id)
                        <a href="{{ route('profile.edit') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                            Edit Profile
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Posts Section -->
        <div>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Posts</h2>
                @if(auth()->id() === $user->id)
                    <a href="{{ route('posts.create') }}" class="bg-blue-600 dark:bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-700 dark:hover:bg-blue-600">
                        New Post
                    </a>
                @endif
            </div>

            @if($posts->count() > 0)
                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($posts as $post)
                        <x-post-card :post="$post" />
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-12">
                    {{ $posts->links() }}
                </div>
            @else
                <div class="text-center py-12 bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800">
                    <div class="text-gray-500 dark:text-gray-400 text-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        @if(auth()->id() === $user->id)
                            <p class="text-xl text-gray-900 dark:text-gray-100">You haven't written any posts yet</p>
                            <p class="text-gray-400 dark:text-gray-500 mt-2">Start sharing your thoughts with the world!</p>
                            <a href="{{ route('posts.create') }}" class="inline-block mt-4 bg-blue-600 dark:bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-700 dark:hover:bg-blue-600">
                                Write your first post
                            </a>
                        @else
                            <p class="text-xl text-gray-900 dark:text-gray-100">{{ $user->name }} hasn't written any posts yet</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>