<x-app-layout>
    <x-slot name="title">Create New Post - {{ config('app.name', 'Blog') }}</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-8">Create New Post</h1>

            <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title</label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title') }}"
                        class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-xl"
                        placeholder="Enter your post title..."
                        required
                    >
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Excerpt -->
                <div>
                    <label for="excerpt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Excerpt (Optional)
                    </label>
                    <textarea
                        id="excerpt"
                        name="excerpt"
                        rows="3"
                        class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Brief summary of your post..."
                    >{{ old('excerpt') }}</textarea>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">A short description that will appear in post previews.</p>
                    @error('excerpt')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Cover Image Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Cover Image (Optional)
                    </label>
                    <div
                        id="image-upload-area"
                        class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-700 border-dashed rounded-md hover:border-blue-400 dark:hover:border-blue-500 transition-colors dark:bg-gray-800"
                    >
                        <div class="space-y-1 text-center pointer-events-none">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 dark:text-gray-400 justify-center">
                                <span class="font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500">Upload a file</span>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF, WebP up to 5MB</p>
                        </div>
                    </div>

                    <!-- Hidden file input -->
                    <input
                        id="cover_image"
                        name="cover_image"
                        type="file"
                        class="hidden"
                        accept="image/*"
                    >

                    <!-- Image preview -->
                    <div id="image-preview" class="mt-4 hidden">
                        <div class="relative">
                            <img id="preview-image" src="" alt="Preview" class="max-w-full h-48 object-cover rounded-md">
                            <button
                                type="button"
                                onclick="removeImage()"
                                class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2" id="image-info"></p>
                    </div>

                    @error('cover_image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Content -->
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Content</label>
                    <textarea
                        id="content"
                        name="content"
                        rows="20"
                        class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Tell your story..."
                        required
                    >{{ old('content') }}</textarea>
                    @error('content')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tags -->
                <div>
                    <label for="tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tags (Optional)
                    </label>
                    <input
                        type="text"
                        id="tags"
                        name="tags"
                        value="{{ old('tags') }}"
                        class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="e.g., javascript, laravel, tutorial (separate with commas)"
                    >
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Add relevant tags separated by commas. Tags help readers find your content.</p>
                    @error('tags')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror

                    <!-- Tag suggestions -->
                    <div id="tag-suggestions" class="mt-2 flex flex-wrap gap-2 hidden">
                        <p class="text-xs text-gray-600 dark:text-gray-400 w-full">Popular tags:</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-800">
                    <a href="{{ route('home') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300">
                        Cancel
                    </a>
                    <div class="flex space-x-3">
                        <button
                            type="submit"
                            name="status"
                            value="draft"
                            class="px-6 py-2 border border-gray-300 dark:border-gray-700 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800"
                        >
                            Save Draft
                        </button>
                        <button
                            type="submit"
                            name="status"
                            value="published"
                            class="px-6 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-md hover:bg-blue-700 dark:hover:bg-blue-600"
                        >
                            Publish
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Image upload handling
        const uploadArea = document.getElementById('image-upload-area');
        const fileInput = document.getElementById('cover_image');
        const imagePreview = document.getElementById('image-preview');
        const previewImage = document.getElementById('preview-image');
        const imageInfo = document.getElementById('image-info');

        // Click to upload
        uploadArea.addEventListener('click', (e) => {
            e.preventDefault();
            fileInput.click();
        });

        // Drag and drop
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('border-blue-400', 'bg-blue-50', 'dark:bg-gray-700');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('border-blue-400', 'bg-blue-50', 'dark:bg-gray-700');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('border-blue-400', 'bg-blue-50', 'dark:bg-gray-700');

            if (e.dataTransfer.files.length) {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(e.dataTransfer.files[0]);
                fileInput.files = dataTransfer.files;
                handleFilePreview(e.dataTransfer.files[0]);
            }
        });

        // File input change
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length) {
                handleFilePreview(e.target.files[0]);
            }
        });

        function handleFilePreview(file) {
            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('Please upload an image file');
                fileInput.value = '';
                return;
            }

            // Validate file size (10MB to match validation)
            if (file.size > 10 * 1024 * 1024) {
                alert('File size must be less than 10MB');
                fileInput.value = '';
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImage.src = e.target.result;
                imagePreview.classList.remove('hidden');
                uploadArea.classList.add('hidden');
                imageInfo.textContent = `${file.name} (${formatFileSize(file.size)})`;
            };
            reader.readAsDataURL(file);
        }

        function removeImage() {
            previewImage.src = '';
            fileInput.value = '';
            imagePreview.classList.add('hidden');
            uploadArea.classList.remove('hidden');
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        // Make removeImage available globally
        window.removeImage = removeImage;

        // Tags functionality
        const tagsInput = document.getElementById('tags');
        const tagSuggestions = document.getElementById('tag-suggestions');

        // Load popular tags
        fetch('{{ route("tags.popular") }}')
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    tagSuggestions.classList.remove('hidden');
                    data.forEach(tag => {
                        const tagButton = document.createElement('button');
                        tagButton.type = 'button';
                        tagButton.textContent = tag.name;
                        tagButton.className = 'px-2 py-1 text-xs bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors';
                        tagButton.onclick = () => addTag(tag.name);
                        tagSuggestions.appendChild(tagButton);
                    });
                }
            })
            .catch(error => console.error('Failed to load tags:', error));

        function addTag(tagName) {
            const currentTags = tagsInput.value.split(',').map(t => t.trim()).filter(t => t);
            if (!currentTags.includes(tagName)) {
                currentTags.push(tagName);
                tagsInput.value = currentTags.join(', ');
            }
        }
    </script>
    @endpush
</x-app-layout>