// Notification dropdown functionality
window.notificationDropdown = function() {
    return {
        open: false,
        loading: false,
        notifications: [],
        unreadCount: 0,

        init() {
            this.fetchUnreadCount();
            // Poll for new notifications every 30 seconds
            setInterval(() => this.fetchUnreadCount(), 30000);
        },

        async toggleDropdown() {
            this.open = !this.open;
            if (this.open && this.notifications.length === 0) {
                await this.fetchNotifications();
            }
        },

        async fetchUnreadCount() {
            try {
                const response = await fetch('/notifications/count', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    }
                });

                // If not authenticated, silently fail
                if (response.status === 401 || response.status === 404) {
                    this.unreadCount = 0;
                    return;
                }

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                this.unreadCount = data.count || 0;
            } catch (error) {
                console.error('Error fetching notification count:', error);
                this.unreadCount = 0;
            }
        },

        async fetchNotifications() {
            this.loading = true;
            try {
                const response = await fetch('/notifications', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    }
                });

                // If not authenticated, silently fail
                if (response.status === 401 || response.status === 404) {
                    this.notifications = [];
                    return;
                }

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                this.notifications = data.notifications || [];
            } catch (error) {
                console.error('Error fetching notifications:', error);
                this.notifications = [];
            } finally {
                this.loading = false;
            }
        },

        async markAsRead(notificationId) {
            try {
                await fetch(`/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    }
                });

                // Update local state
                const notification = this.notifications.find(n => n.id === notificationId);
                if (notification) {
                    notification.read = true;
                    if (this.unreadCount > 0) {
                        this.unreadCount--;
                    }
                }
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        },

        async markAllAsRead() {
            try {
                await fetch('/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    }
                });

                // Update local state
                this.notifications.forEach(n => n.read = true);
                this.unreadCount = 0;
            } catch (error) {
                console.error('Error marking all notifications as read:', error);
            }
        },

        async deleteNotification(notificationId) {
            try {
                const response = await fetch(`/notifications/${notificationId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    }
                });

                if (response.ok) {
                    // Remove notification from list
                    const index = this.notifications.findIndex(n => n.id === notificationId);
                    if (index !== -1) {
                        const wasUnread = !this.notifications[index].read;
                        this.notifications.splice(index, 1);

                        // Update unread count if it was unread
                        if (wasUnread && this.unreadCount > 0) {
                            this.unreadCount--;
                        }
                    }
                }
            } catch (error) {
                console.error('Error deleting notification:', error);
            }
        }
    };
};
