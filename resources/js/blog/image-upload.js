// Image upload functionality with drag & drop and server upload
let uploadedImagePath = null;

// Handle file selection via input
function handleFileSelect(event) {
    const file = event.target.files[0];
    if (file) {
        uploadImage(file);
    }
}

// Handle drag and drop events
function handleDragOver(event) {
    event.preventDefault();
    event.stopPropagation();
    const uploadArea = document.getElementById('image-upload-area');
    uploadArea.classList.add('border-blue-400', 'bg-blue-50');
}

function handleDragLeave(event) {
    event.preventDefault();
    event.stopPropagation();
    const uploadArea = document.getElementById('image-upload-area');
    uploadArea.classList.remove('border-blue-400', 'bg-blue-50');
}

function handleImageDrop(event) {
    event.preventDefault();
    event.stopPropagation();

    const uploadArea = document.getElementById('image-upload-area');
    uploadArea.classList.remove('border-blue-400', 'bg-blue-50');

    const files = event.dataTransfer.files;
    if (files.length > 0) {
        const file = files[0];
        if (file.type.startsWith('image/')) {
            uploadImage(file);
        } else {
            showNotification('Please select an image file.', 'error');
        }
    }
}

// Upload image to server
async function uploadImage(file) {
    // Validate file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
        showNotification('Image must be smaller than 5MB.', 'error');
        return;
    }

    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
    if (!allowedTypes.includes(file.type)) {
        showNotification('Please select a valid image file (JPEG, PNG, GIF, WebP).', 'error');
        return;
    }

    // Show progress
    showUploadProgress(true);

    const formData = new FormData();
    formData.append('image', file);

    try {
        const response = await fetch('/upload/image', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            // Store the uploaded image path
            uploadedImagePath = data.path;

            // Update hidden input with the URL
            document.getElementById('cover_image').value = data.url;

            // Show preview
            showImagePreview(data.url, file.name, file.size);

            showNotification('Image uploaded successfully!', 'success');
        } else {
            throw new Error(data.message || 'Upload failed');
        }
    } catch (error) {
        console.error('Upload error:', error);
        showNotification('Failed to upload image: ' + error.message, 'error');
    } finally {
        showUploadProgress(false);
    }
}

// Show/hide upload progress
function showUploadProgress(show) {
    const progressElement = document.getElementById('upload-progress');
    const uploadArea = document.getElementById('image-upload-area');

    if (show) {
        progressElement.classList.remove('hidden');
        uploadArea.style.pointerEvents = 'none';
        uploadArea.classList.add('opacity-50');

        // Animate progress bar
        const progressBar = document.getElementById('progress-bar');
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 30;
            if (progress > 90) progress = 90;
            progressBar.style.width = progress + '%';
        }, 200);

        // Store interval for cleanup
        progressElement.dataset.interval = interval;
    } else {
        progressElement.classList.add('hidden');
        uploadArea.style.pointerEvents = 'auto';
        uploadArea.classList.remove('opacity-50');

        // Complete progress and cleanup
        const progressBar = document.getElementById('progress-bar');
        progressBar.style.width = '100%';

        const interval = progressElement.dataset.interval;
        if (interval) {
            clearInterval(interval);
        }

        setTimeout(() => {
            progressBar.style.width = '0%';
        }, 500);
    }
}

// Show image preview
function showImagePreview(url, filename, filesize) {
    const preview = document.getElementById('image-preview');
    const previewImage = document.getElementById('preview-image');
    const imageInfo = document.getElementById('image-info');
    const uploadArea = document.getElementById('image-upload-area');

    previewImage.src = url;
    imageInfo.textContent = `${filename} (${formatFileSize(filesize)})`;

    preview.classList.remove('hidden');
    uploadArea.classList.add('hidden');
}

// Remove uploaded image
function removeImage() {
    if (uploadedImagePath) {
        // Delete from server
        fetch('/upload/delete', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ path: uploadedImagePath })
        }).catch(error => {
            console.error('Delete error:', error);
        });
    }

    // Reset form
    const preview = document.getElementById('image-preview');
    const uploadArea = document.getElementById('image-upload-area');
    const fileInput = document.getElementById('cover_image_file');
    const hiddenInput = document.getElementById('cover_image');

    preview.classList.add('hidden');
    uploadArea.classList.remove('hidden');
    fileInput.value = '';
    hiddenInput.value = '';
    uploadedImagePath = null;

    showNotification('Image removed', 'info');
}

// Format file size for display
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';

    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Add event listener for file input
    const fileInput = document.getElementById('cover_image_file');
    if (fileInput) {
        fileInput.addEventListener('change', handleFileSelect);
    }

    // Add drag and drop event listeners
    const uploadArea = document.getElementById('image-upload-area');
    if (uploadArea) {
        uploadArea.addEventListener('drop', handleImageDrop);
        uploadArea.addEventListener('dragover', handleDragOver);
        uploadArea.addEventListener('dragleave', handleDragLeave);
        // Note: Click handler is managed per-page in Blade templates to avoid conflicts
    }

    // Check if there's an existing image (for edit forms)
    const existingImage = document.getElementById('cover_image');
    if (existingImage && existingImage.value) {
        // Show preview for existing image
        const preview = document.getElementById('image-preview');
        const previewImage = document.getElementById('preview-image');
        const imageInfo = document.getElementById('image-info');
        const uploadArea = document.getElementById('image-upload-area');

        if (preview && previewImage && imageInfo && uploadArea) {
            previewImage.src = existingImage.value;
            imageInfo.textContent = 'Current cover image';

            preview.classList.remove('hidden');
            uploadArea.classList.add('hidden');
        }
    }

    // Prevent default drag behaviors on the window
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        document.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
});

// Helper function for notifications (assuming it exists from notifications.js)
function showNotification(message, type = 'info') {
    if (window.showNotification) {
        window.showNotification(message, type);
    } else {
        alert(message);
    }
}