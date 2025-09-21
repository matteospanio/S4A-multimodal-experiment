import 'bootstrap';
import './bootstrap.js';
import './pwa-manager.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import 'bootstrap/dist/css/bootstrap.min.css';
import './styles/bootstrap.css';
import './styles/app.css';
import './styles/pwa.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

// PWA Service Worker Registration
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                console.log('SW registered: ', registration);
                
                // Check for updates
                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            // New content is available
                            console.log('New content is available; please refresh.');
                        }
                    });
                });
            })
            .catch(registrationError => {
                console.log('SW registration failed: ', registrationError);
            });
    });
}

// PWA Install Prompt
let deferredPrompt;
window.addEventListener('beforeinstallprompt', (e) => {
    // Prevent Chrome 67 and earlier from automatically showing the prompt
    e.preventDefault();
    // Stash the event so it can be triggered later
    deferredPrompt = e;
    
    // Show install button if needed (could be added to UI later)
    console.log('PWA install prompt available');
});

// Handle PWA install
window.addEventListener('appinstalled', (evt) => {
    console.log('PWA was installed');
});

// Add to home screen functionality for iOS
function addToHomeScreenIOS() {
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
    const isStandalone = window.navigator.standalone;
    
    if (isIOS && !isStandalone) {
        // Could show instructions for iOS users to add to home screen
        console.log('iOS user can add to home screen');
    }
}

// Check if running as PWA
function isPWA() {
    return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;
}

// Initialize PWA features
document.addEventListener('DOMContentLoaded', () => {
    addToHomeScreenIOS();
    
    if (isPWA()) {
        console.log('Running as PWA');
        // Add PWA-specific functionality here
    }
});
