import './bootstrap.js';

// Ensure Bootstrap JavaScript components are properly initialized
// This is crucial for dropdown functionality in production mode
import * as bootstrap from 'bootstrap';

// Make Bootstrap available globally for EasyAdmin components
window.bootstrap = bootstrap;

// Robust initialization function with retry mechanism
function initializeBootstrapDropdowns() {
    console.log('Bootstrap admin.js initializing...', { bootstrap: !!window.bootstrap });
    
    if (!window.bootstrap || !window.bootstrap.Dropdown) {
        console.warn('Bootstrap not fully loaded yet, retrying in 100ms...');
        setTimeout(initializeBootstrapDropdowns, 100);
        return;
    }
    
    // Initialize all dropdowns explicitly
    const dropdownElements = document.querySelectorAll('[data-bs-toggle="dropdown"]');
    console.log(`Found ${dropdownElements.length} dropdown elements to initialize`);
    
    dropdownElements.forEach(function(element) {
        try {
            // Check if already initialized
            if (window.bootstrap.Dropdown.getInstance(element)) {
                console.log('Dropdown already initialized:', element);
                return;
            }
            
            new window.bootstrap.Dropdown(element);
            console.log('Successfully initialized dropdown:', element);
        } catch (error) {
            console.error('Error initializing dropdown:', error, element);
        }
    });
}

// Initialize Bootstrap components after DOM is ready
// This ensures dropdowns work in production mode
document.addEventListener('DOMContentLoaded', function() {
    initializeBootstrapDropdowns();
    
    // Re-initialize dropdowns after any dynamic content is loaded (for AJAX content)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        const newDropdowns = node.querySelectorAll('[data-bs-toggle="dropdown"]');
                        newDropdowns.forEach(function(element) {
                            if (window.bootstrap && window.bootstrap.Dropdown && !window.bootstrap.Dropdown.getInstance(element)) {
                                try {
                                    new window.bootstrap.Dropdown(element);
                                    console.log('Initialized dynamic dropdown:', element);
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

// Additional fallback: try initialization on window load as well
window.addEventListener('load', function() {
    // Wait a bit more to ensure all assets are loaded
    setTimeout(initializeBootstrapDropdowns, 200);
});

console.log('This log comes from assets/admin.js - welcome to AssetMapper! 🎉');
