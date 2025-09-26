import 'bootstrap';
import './bootstrap.js';

// Ensure Bootstrap JavaScript components are properly initialized
// This is crucial for dropdown functionality in production mode
import * as bootstrap from 'bootstrap';

// Make Bootstrap available globally for EasyAdmin components
window.bootstrap = bootstrap;

console.log('This log comes from assets/admin.js - welcome to AssetMapper! 🎉');
