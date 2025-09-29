<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 sticky top-0 z-50 backdrop-blur-sm bg-white/95">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-gray-900 hover:text-primary-600 transition-colors">
                        {{ config('app.name', 'Blog') }}
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:ms-10 sm:flex">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('home') ? 'text-gray-900 border-b-2 border-primary-500' : '' }}">
                        Home
                    </a>
                    <a href="{{ route('tags.index') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('tags.*') ? 'text-gray-900 border-b-2 border-primary-500' : '' }}">
                        Topics
                    </a>
                    @auth
                        <a href="{{ route('posts.create') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('posts.create') ? 'text-gray-900 border-b-2 border-primary-500' : '' }}">
                            Write
                        </a>
                        <a href="{{ route('bookmarks.index') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('bookmarks.index') ? 'text-gray-900 border-b-2 border-primary-500' : '' }}">
                            Bookmarks
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Search and Auth -->
            <div class="hidden sm:flex sm:items-center sm:space-x-4">
                <!-- Enhanced Search Form -->
                <div class="relative" x-data="{ focused: false, suggestions: [] }">
                    <form method="GET" action="{{ route('posts.search') }}" class="relative">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input
                                type="text"
                                name="q"
                                placeholder="Search posts..."
                                value="{{ request('q') }}"
                                data-search-input
                                data-live-search="true"
                                class="pl-10 pr-10 py-2 w-64 border border-gray-300 rounded-full text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition-all hover:border-gray-400"
                                autocomplete="off"
                            >

                            <!-- Advanced Search Link -->
                            <a href="{{ route('posts.search') }}"
                               class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                               title="Advanced Search">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                                </svg>
                            </a>
                        </div>

                        <!-- Search Suggestions Dropdown -->
                        <div x-show="focused && suggestions.length > 0"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             class="absolute top-full mt-1 w-full bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50 max-h-96 overflow-y-auto">

                            <template x-for="suggestion in suggestions" :key="suggestion.id">
                                <a :href="suggestion.url"
                                   class="block px-4 py-2 hover:bg-gray-50 text-sm">
                                    <div class="font-medium text-gray-900" x-text="suggestion.title"></div>
                                    <div class="text-gray-600 text-xs" x-text="suggestion.author"></div>
                                </a>
                            </template>

                            <!-- No suggestions -->
                            <div x-show="suggestions.length === 0"
                                 class="px-4 py-2 text-sm text-gray-500">
                                No suggestions found
                            </div>
                        </div>
                    </form>
                </div>

                @auth
                    <!-- User Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false"
                                class="flex items-center space-x-2 text-gray-600 hover:text-gray-900 px-3 py-2 rounded-full hover:bg-gray-50 transition-colors">
                            @if(Auth::user()->avatar)
                                <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full">
                            @else
                                <div class="w-8 h-8 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-xs font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <span class="text-sm font-medium">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                            <a href="{{ route('users.show', Auth::user()) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">My Profile</a>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Settings</a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    Sign out
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 font-medium transition-colors">
                            Sign in
                        </a>
                        <a href="{{ route('register') }}" class="btn-primary">
                            Get started
                        </a>
                    </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        @auth
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()?->name ?? 'User' }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()?->email ?? '' }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</nav>
