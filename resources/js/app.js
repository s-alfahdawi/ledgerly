import './bootstrap';
import * as bootstrap from 'bootstrap';
import Alpine from 'alpinejs';

// Make Bootstrap available globally
window.bootstrap = bootstrap;

window.Alpine = Alpine;

// Start Alpine after DOM is ready to ensure x-data functions are defined
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => Alpine.start());
} else {
    Alpine.start();
}
