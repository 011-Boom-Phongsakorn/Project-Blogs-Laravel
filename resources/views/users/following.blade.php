<x-app-layout>
    <x-slot name="title">{{ $user->name }} is Following - {{ config('app.name', 'Blog') }}</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:text-blue-800 mr-4">
                    ← Back to {{ $user->name }}'s profile
                </a>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }} is Following</h1>
            <p class="text-gray-600">{{ $following->total() }} {{ Str::plural('person', $following->total()) }}</p>
        </div>

        @if($following->count() > 0)
            <div class="space-y-4">
                @foreach($following as $followedUser)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                @if($followedUser && $followedUser->avatar)
                                    <img src="{{ $followedUser->avatar }}" alt="{{ $followedUser->name }}" class="w-12 h-12 rounded-full mr-4">
                                @elseif($followedUser)
                                    <div class="w-12 h-12 bg-gray-300 rounded-full mr-4 flex items-center justify-center">
                                        <span class="text-gray-600 text-lg font-medium">{{ substr($followedUser->name, 0, 1) }}</span>
                                    </div>
                                @else
                                    <div class="w-12 h-12 bg-gray-300 rounded-full mr-4 flex items-center justify-center">
                                        <span class="text-gray-600 text-lg font-medium">?</span>
                                    </div>
                                @endif
                                <div>
                                    @if($followedUser)
                                        <a href="{{ route('users.show', $followedUser) }}" class="text-lg font-medium text-gray-900 hover:text-blue-600">
                                            {{ $followedUser->name }}
                                        </a>
                                    @else
                                        <span class="text-lg font-medium text-gray-900">Unknown User</span>
                                    @endif
                                    @if($followedUser && $followedUser->bio)
                                        <p class="text-gray-600 text-sm mt-1">{{ Str::limit($followedUser->bio, 100) }}</p>
                                    @endif
                                    @if($followedUser)
                                        <div class="flex items-center space-x-4 text-sm text-gray-500 mt-2">
                                            <span>{{ $followedUser->posts_count ?? 0 }} {{ Str::plural('post', $followedUser->posts_count ?? 0) }}</span>
                                            <span>{{ $followedUser->followers_count ?? 0 }} {{ Str::plural('follower', $followedUser->followers_count ?? 0) }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <x-follow-button :user="$followedUser" />
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $following->links() }}
            </div>
        @else
            <div class="text-center py-16 bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="text-gray-500">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                    <p class="text-xl">{{ $user->name }} isn't following anyone yet</p>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>