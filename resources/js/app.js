import './bootstrap';
import Alpine from 'alpinejs';

// Blog-specific JavaScript modules
import './blog/social';
import './blog/search';
import './blog/notifications';
import './blog/image-upload';
import './blog/notifications-dropdown';

window.Alpine = Alpine;

Alpine.start();
