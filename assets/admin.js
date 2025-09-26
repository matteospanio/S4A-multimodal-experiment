import 'bootstrap';
import './bootstrap.js';

// Ensure Bootstrap JavaScript components are properly initialized
// This is crucial for dropdown functionality in production mode
import * as bootstrap from 'bootstrap';

// Make Bootstrap available globally for EasyAdmin components
window.bootstrap = bootstrap;

// Initialize Bootstrap components after DOM is ready
// This ensures dropdowns work in production mode
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all dropdowns explicitly
    const dropdownElements = document.querySelectorAll('[data-bs-toggle="dropdown"]');
    dropdownElements.forEach(function(element) {
        new bootstrap.Dropdown(element);
    });
    
    // Re-initialize dropdowns after any dynamic content is loaded (for AJAX content)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        const newDropdowns = node.querySelectorAll('[data-bs-toggle="dropdown"]');
                        newDropdowns.forEach(function(element) {
                            if (!bootstrap.Dropdown.getInstance(element)) {
                                new bootstrap.Dropdown(element);
                            }
                        });
                    }
                });
            }
        });
    });
    
    // Start observing the document body for changes
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});

console.log('This log comes from assets/admin.js - welcome to AssetMapper! 🎉');
