<x-app-layout>
    <x-slot name="title">{{ config('app.name', 'Blog') }} - Discover Stories</x-slot>

    <!-- Hero Section -->
    <section class="hero-gradient border-b border-gray-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="text-center">
                <h1 class="text-4xl lg:text-6xl font-bold text-gray-900 mb-6 text-balance">
                    Human stories & ideas
                </h1>
                <p class="text-xl lg:text-2xl text-gray-600 mb-8 max-w-2xl mx-auto text-balance">
                    A place to read, write, and deepen your understanding
                </p>
                @guest
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('register') }}" class="btn-primary text-lg px-8 py-3">
                            Start writing
                        </a>
                        <a href="{{ route('login') }}" class="btn-secondary text-lg px-8 py-3">
                            Sign in
                        </a>
                    </div>
                @else
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('posts.create') }}" class="btn-primary text-lg px-8 py-3">
                            Write a story
                        </a>
                        <a href="{{ route('bookmarks.index') }}" class="btn-secondary text-lg px-8 py-3">
                            Your bookmarks
                        </a>
                    </div>
                @endguest
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            @if($posts->count() > 0)
                <!-- Featured Section -->
                @if($posts->count() > 0)
                    <section class="mb-16">
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-2xl font-bold text-gray-900">Latest Stories</h2>
                            <a href="{{ route('posts.search') }}" class="btn-ghost">
                                Browse all stories
                            </a>
                        </div>

                        <!-- Posts Grid -->
                        <div class="grid gap-8 lg:grid-cols-3 md:grid-cols-2">
                            @foreach($posts as $post)
                                <x-post-card :post="$post" />
                            @endforeach
                        </div>
                    </section>
                @endif

                <!-- Pagination -->
                <div class="flex justify-center">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        {{ $posts->links() }}
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-24">
                    <div class="max-w-md mx-auto">
                        <div class="w-24 h-24 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full mx-auto mb-8 flex items-center justify-center">
                            <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">No stories yet</h3>
                        <p class="text-gray-600 mb-8 text-balance">
                            Be the first to share your thoughts and experiences with our community.
                        </p>
                        @auth
                            <a href="{{ route('posts.create') }}" class="btn-primary">
                                Write your first story
                            </a>
                        @else
                            <div class="space-y-4">
                                <a href="{{ route('register') }}" class="btn-primary block">
                                    Start writing
                                </a>
                                <p class="text-sm text-gray-500">
                                    Already have an account?
                                    <a href="{{ route('login') }}" class="text-primary-600 hover:text-primary-700 font-medium">
                                        Sign in
                                    </a>
                                </p>
                            </div>
                        @endauth
                    </div>
                </div>
            @endif
        </div>
    </main>

    <!-- Newsletter Section -->
    @if($posts->count() > 0)
        <section class="bg-white border-t border-gray-200">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Stay in the know</h2>
                    <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
                        Get the best stories delivered to your inbox every week.
                    </p>
                    <form class="max-w-md mx-auto">
                        <div class="flex gap-3">
                            <input type="email" placeholder="Enter your email"
                                   class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <button type="submit" class="btn-primary whitespace-nowrap">
                                Subscribe
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-3">
                            We'll never share your email. Unsubscribe at any time.
                        </p>
                    </form>
                </div>
            </div>
        </section>
    @endif
</x-app-layout>