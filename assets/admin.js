import './bootstrap.js';

// Ensure Bootstrap JavaScript components are properly initialized
// This is crucial for dropdown functionality in production mode
import * as bootstrap from 'bootstrap';

// Make Bootstrap available globally for EasyAdmin components
window.bootstrap = bootstrap;

// Initialize Bootstrap components after DOM is ready
// This ensures dropdowns work in production mode
document.addEventListener('DOMContentLoaded', function() {
    console.log('Bootstrap admin.js initializing...', { bootstrap: !!window.bootstrap });
    
    // Initialize all dropdowns explicitly
    const dropdownElements = document.querySelectorAll('[data-bs-toggle="dropdown"]');
    console.log(`Found ${dropdownElements.length} dropdown elements to initialize`);
    
    dropdownElements.forEach(function(element) {
        try {
            new bootstrap.Dropdown(element);
        } catch (error) {
            console.error('Error initializing dropdown:', error, element);
        }
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
                                try {
                                    new bootstrap.Dropdown(element);
                                } catch (error) {
                                    console.error('Error initializing new dropdown:', error, element);
                                }
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
