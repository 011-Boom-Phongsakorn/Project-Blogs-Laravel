// Image upload functionality with drag & drop (client-side preview only)
// Images will be uploaded when the form is submitted

// Note: This file provides minimal functionality as most image handling
// is done by inline scripts in create.blade.php and edit.blade.php
// to avoid conflicts and support both forms properly

// Format file size for display (exported for use in blade templates)
window.formatFileSize = function(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};