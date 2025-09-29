<x-app-layout>
    <x-slot name="title">Edit: {{ $post->title }} - {{ config('app.name', 'Blog') }}</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Edit Post</h1>
                <div class="flex space-x-3">
                    <a href="{{ route('posts.show', $post) }}" class="text-gray-600 hover:text-gray-800">
                        View Post
                    </a>
                    <form action="{{ route('posts.destroy', $post) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="text-red-600 hover:text-red-800"
                            onclick="return confirm('Are you sure you want to delete this post?')"
                        >
                            Delete
                        </button>
                    </form>
                </div>
            </div>

            <form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PATCH')

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title', $post->title) }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-xl"
                        placeholder="Enter your post title..."
                        required
                    >
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Excerpt -->
                <div>
                    <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">
                        Excerpt (Optional)
                    </label>
                    <textarea
                        id="excerpt"
                        name="excerpt"
                        rows="3"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Brief summary of your post..."
                    >{{ old('excerpt', $post->excerpt) }}</textarea>
                    <p class="text-sm text-gray-500 mt-1">A short description that will appear in post previews.</p>
                    @error('excerpt')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Cover Image -->
                <div>
                    <label for="cover_image" class="block text-sm font-medium text-gray-700 mb-2">
                        Cover Image URL (Optional)
                    </label>
                    <input
                        type="url"
                        id="cover_image"
                        name="cover_image"
                        value="{{ old('cover_image', $post->cover_image) }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="https://example.com/image.jpg"
                    >
                    @if($post->cover_image)
                        <div class="mt-2">
                            <img src="{{ $post->cover_image }}" alt="Current cover" class="h-32 w-auto rounded">
                        </div>
                    @endif
                    @error('cover_image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Featured Image Upload -->
                <div>
                    <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-2">
                        Featured Image Upload (Optional)
                    </label>

                    @if($post->featured_image)
                        <div class="mb-4">
                            <img src="{{ $post->featured_image_url }}" alt="{{ $post->featured_image_alt ?? $post->title }}" class="h-32 w-auto rounded">
                            <p class="text-sm text-gray-500 mt-1">Current featured image</p>
                        </div>
                    @endif

                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="featured_image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload a file</span>
                                    <input id="featured_image" name="featured_image" type="file" class="sr-only" accept="image/*">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF, WebP up to 2MB</p>
                        </div>
                    </div>
                    @error('featured_image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Featured Image Alt Text -->
                <div>
                    <label for="featured_image_alt" class="block text-sm font-medium text-gray-700 mb-2">
                        Image Alt Text (Optional)
                    </label>
                    <input
                        type="text"
                        id="featured_image_alt"
                        name="featured_image_alt"
                        value="{{ old('featured_image_alt', $post->featured_image_alt) }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Describe the image for accessibility..."
                    >
                    <p class="text-sm text-gray-500 mt-1">Helps screen readers understand the image content.</p>
                    @error('featured_image_alt')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Content -->
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                    <textarea
                        id="content"
                        name="content"
                        rows="20"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Tell your story..."
                        required
                    >{{ old('content', $post->content) }}</textarea>
                    @error('content')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input
                                type="radio"
                                name="status"
                                value="draft"
                                {{ old('status', $post->status) === 'draft' ? 'checked' : '' }}
                                class="form-radio h-4 w-4 text-blue-600"
                            >
                            <span class="ml-2 text-gray-700">Draft</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input
                                type="radio"
                                name="status"
                                value="published"
                                {{ old('status', $post->status) === 'published' ? 'checked' : '' }}
                                class="form-radio h-4 w-4 text-blue-600"
                            >
                            <span class="ml-2 text-gray-700">Published</span>
                        </label>
                    </div>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('posts.show', $post) }}" class="text-gray-600 hover:text-gray-800">
                        Cancel
                    </a>
                    <button
                        type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                    >
                        Update Post
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>