<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="bio" :value="__('Bio')" />
            <textarea id="bio" name="bio" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="4" placeholder="Tell us about yourself...">{{ old('bio', $user->bio) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        <!-- Avatar Upload -->
        <div>
            <x-input-label for="avatar" :value="__('Avatar')" />

            <!-- Current Avatar -->
            @if($user->avatar)
                <div class="mt-2 mb-4">
                    <img src="{{ $user->avatar }}" alt="Current avatar" class="w-24 h-24 rounded-full object-cover border-2 border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Current avatar</p>
                </div>
            @endif

            <!-- Upload Area -->
            <div
                id="avatar-upload-area"
                class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-700 border-dashed rounded-md hover:border-blue-400 dark:hover:border-blue-500 transition-colors dark:bg-gray-800 {{ $user->avatar ? 'hidden' : '' }}"
            >
                <div class="space-y-1 text-center pointer-events-none">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="flex text-sm text-gray-600 dark:text-gray-400 justify-center">
                        <span class="font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500">Upload a file</span>
                        <p class="pl-1">or drag and drop</p>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF up to 2MB</p>
                </div>
            </div>

            <!-- Hidden file input -->
            <input
                id="avatar_file"
                type="file"
                class="hidden"
                accept="image/*"
            >

            <!-- Hidden input for the actual URL that will be submitted -->
            <input type="hidden" name="avatar" id="avatar_url" value="{{ old('avatar', $user->avatar) }}">

            <!-- Image preview -->
            <div id="avatar-preview" class="mt-4 {{ $user->avatar ? '' : 'hidden' }}">
                <div class="relative inline-block">
                    <img id="preview-avatar" src="{{ $user->avatar }}" alt="Preview" class="w-24 h-24 rounded-full object-cover border-2 border-gray-200 dark:border-gray-700">
                    <button
                        type="button"
                        onclick="removeAvatar()"
                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-2" id="avatar-info">{{ $user->avatar ? 'Current avatar' : '' }}</p>
            </div>

            <!-- Upload progress -->
            <div id="avatar-upload-progress" class="mt-4 hidden">
                <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div id="avatar-progress-bar" class="bg-blue-600 dark:bg-blue-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Uploading...</p>
            </div>

            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>

    @push('scripts')
    <script>
        // Avatar upload handling
        const avatarUploadArea = document.getElementById('avatar-upload-area');
        const avatarFileInput = document.getElementById('avatar_file');
        const avatarPreview = document.getElementById('avatar-preview');
        const previewAvatar = document.getElementById('preview-avatar');
        const avatarInfo = document.getElementById('avatar-info');
        const avatarUploadProgress = document.getElementById('avatar-upload-progress');
        const avatarProgressBar = document.getElementById('avatar-progress-bar');
        const avatarUrlInput = document.getElementById('avatar_url');

        let isAvatarClickPending = false;

        // Remove any existing event listeners by cloning
        const newAvatarUploadArea = avatarUploadArea.cloneNode(true);
        avatarUploadArea.parentNode.replaceChild(newAvatarUploadArea, avatarUploadArea);
        const avatarUploadAreaClean = document.getElementById('avatar-upload-area');

        // Show upload area if no avatar exists
        if (!avatarUrlInput.value) {
            avatarUploadAreaClean.classList.remove('hidden');
        }

        // Click to upload
        avatarUploadAreaClean.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            if (isAvatarClickPending) return;

            isAvatarClickPending = true;
            avatarFileInput.click();

            setTimeout(() => {
                isAvatarClickPending = false;
            }, 1000);
        });

        // Drag and drop
        avatarUploadAreaClean.addEventListener('dragover', (e) => {
            e.preventDefault();
            avatarUploadAreaClean.classList.add('border-blue-400', 'bg-blue-50');
        });

        avatarUploadAreaClean.addEventListener('dragleave', () => {
            avatarUploadAreaClean.classList.remove('border-blue-400', 'bg-blue-50');
        });

        avatarUploadAreaClean.addEventListener('drop', (e) => {
            e.preventDefault();
            avatarUploadAreaClean.classList.remove('border-blue-400', 'bg-blue-50');

            if (e.dataTransfer.files.length) {
                avatarFileInput.files = e.dataTransfer.files;
                handleAvatarUpload(e.dataTransfer.files[0]);
            }
        });

        // File input change
        avatarFileInput.addEventListener('change', (e) => {
            if (e.target.files.length) {
                handleAvatarUpload(e.target.files[0]);
            }
        });

        function handleAvatarUpload(file) {
            if (!file.type.startsWith('image/')) {
                alert('Please upload an image file');
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                alert('File size must be less than 2MB');
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                previewAvatar.src = e.target.result;
                avatarPreview.classList.remove('hidden');
                avatarUploadAreaClean.classList.add('hidden');
            };
            reader.readAsDataURL(file);

            uploadAvatarToServer(file);
        }

        function uploadAvatarToServer(file) {
            const formData = new FormData();
            formData.append('image', file);

            avatarUploadProgress.classList.remove('hidden');

            fetch('{{ route("upload.avatar") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    avatarUrlInput.value = data.url;
                    avatarInfo.textContent = `Uploaded: ${file.name} (${formatFileSize(file.size)})`;
                } else {
                    throw new Error(data.message || 'Upload failed');
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                alert('Upload failed: ' + error.message);
                removeAvatar();
            })
            .finally(() => {
                avatarUploadProgress.classList.add('hidden');
                avatarProgressBar.style.width = '0%';
            });
        }

        function removeAvatar() {
            previewAvatar.src = '';
            avatarUrlInput.value = '';
            avatarPreview.classList.add('hidden');
            avatarUploadAreaClean.classList.remove('hidden');
            avatarFileInput.value = '';
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        window.removeAvatar = removeAvatar;
    </script>
    @endpush
</section>
