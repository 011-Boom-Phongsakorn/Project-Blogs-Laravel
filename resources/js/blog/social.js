// Social interactions: likes, bookmarks, follows
document.addEventListener('DOMContentLoaded', function() {
    // Like button functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('[data-like-toggle]')) {
            e.preventDefault();
            const button = e.target.closest('[data-like-toggle]');
            const postId = button.dataset.postId;
            const likeCountElement = button.querySelector('.like-count');
            const icon = button.querySelector('svg');

            // Optimistic UI update
            const wasLiked = button.classList.contains('liked');
            button.classList.toggle('liked');

            if (likeCountElement) {
                const currentCount = parseInt(likeCountElement.textContent) || 0;
                likeCountElement.textContent = wasLiked ? currentCount - 1 : currentCount + 1;
            }

            // Add loading state
            button.disabled = true;
            button.classList.add('opacity-75', 'cursor-not-allowed');

            // Add heart beating animation
            icon.classList.add('animate-bounce');

            // Send AJAX request
            console.log('Attempting to like post:', postId, 'URL:', `/posts/${postId}/like`);
            console.log('CSRF token:', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')?.substring(0, 10) + '...');

            fetch(`/posts/${postId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Response status:', response.status, 'URL:', response.url);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update UI with server response
                    if (likeCountElement) {
                        likeCountElement.textContent = data.like_count;
                    }

                    if (data.liked) {
                        button.classList.add('liked');
                        button.setAttribute('title', 'Unlike this post');
                    } else {
                        button.classList.remove('liked');
                        button.setAttribute('title', 'Like this post');
                    }

                    // Show success feedback
                    showNotification(data.liked ? 'Post liked!' : 'Post unliked!', 'success');
                } else {
                    // Revert optimistic update on error
                    button.classList.toggle('liked');
                    if (likeCountElement) {
                        const currentCount = parseInt(likeCountElement.textContent) || 0;
                        likeCountElement.textContent = wasLiked ? currentCount + 1 : currentCount - 1;
                    }
                    showNotification('Something went wrong. Please try again.', 'error');
                }
            })
            .catch(error => {
                console.error('Like error:', error);
                // Revert optimistic update on error
                button.classList.toggle('liked');
                if (likeCountElement) {
                    const currentCount = parseInt(likeCountElement.textContent) || 0;
                    likeCountElement.textContent = wasLiked ? currentCount + 1 : currentCount - 1;
                }
                showNotification('Network error. Please try again.', 'error');
            })
            .finally(() => {
                button.disabled = false;
                button.classList.remove('opacity-75', 'cursor-not-allowed');
                icon.classList.remove('animate-bounce');
            });
        }
    });

    // Bookmark button functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('[data-bookmark-toggle]')) {
            e.preventDefault();
            const button = e.target.closest('[data-bookmark-toggle]');
            const postId = button.dataset.postId;
            const icon = button.querySelector('svg');

            // Optimistic UI update
            const wasBookmarked = button.classList.contains('bookmarked');
            button.classList.toggle('bookmarked');

            // Add loading state
            button.disabled = true;
            icon.classList.add('animate-pulse');

            // Send AJAX request
            fetch(`/posts/${postId}/bookmark`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.bookmarked) {
                        button.classList.add('bookmarked');
                        button.setAttribute('title', 'Remove bookmark');
                    } else {
                        button.classList.remove('bookmarked');
                        button.setAttribute('title', 'Bookmark this post');
                    }

                    showNotification(data.bookmarked ? 'Post bookmarked!' : 'Bookmark removed!', 'success');
                } else {
                    // Revert optimistic update on error
                    button.classList.toggle('bookmarked');
                    showNotification('Something went wrong. Please try again.', 'error');
                }
            })
            .catch(error => {
                console.error('Bookmark error:', error);
                // Revert optimistic update on error
                button.classList.toggle('bookmarked');
                showNotification('Network error. Please try again.', 'error');
            })
            .finally(() => {
                button.disabled = false;
                icon.classList.remove('animate-pulse');
            });
        }
    });

    // Follow button functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('[data-follow-toggle]')) {
            e.preventDefault();
            const button = e.target.closest('[data-follow-toggle]');
            const userId = button.dataset.userId;
            const buttonText = button.querySelector('.button-text');

            // Optimistic UI update
            const wasFollowing = button.classList.contains('following');
            button.classList.toggle('following');

            if (buttonText) {
                buttonText.textContent = wasFollowing ? 'Follow' : 'Following';
            }

            // Add loading state
            button.disabled = true;

            // Send AJAX request
            fetch(`/users/${userId}/follow`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.following) {
                        button.classList.add('following');
                        if (buttonText) buttonText.textContent = 'Following';
                        button.setAttribute('title', 'Unfollow');
                    } else {
                        button.classList.remove('following');
                        if (buttonText) buttonText.textContent = 'Follow';
                        button.setAttribute('title', 'Follow');
                    }

                    // Update follower counts if present
                    const followerCounts = document.querySelectorAll(`[data-follower-count="${userId}"]`);
                    followerCounts.forEach(count => {
                        count.textContent = data.followers_count;
                    });

                    showNotification(data.following ? 'Now following!' : 'Unfollowed!', 'success');
                } else {
                    // Revert optimistic update on error
                    button.classList.toggle('following');
                    if (buttonText) {
                        buttonText.textContent = wasFollowing ? 'Following' : 'Follow';
                    }
                    showNotification('Something went wrong. Please try again.', 'error');
                }
            })
            .catch(error => {
                console.error('Follow error:', error);
                // Revert optimistic update on error
                button.classList.toggle('following');
                if (buttonText) {
                    buttonText.textContent = wasFollowing ? 'Following' : 'Follow';
                }
                showNotification('Network error. Please try again.', 'error');
            })
            .finally(() => {
                button.disabled = false;
            });
        }
    });
});

// Helper function for notifications (will be implemented in notifications.js)
function showNotification(message, type = 'info') {
    window.dispatchEvent(new CustomEvent('show-notification', {
        detail: { message, type }
    }));
}