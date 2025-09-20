import { Controller } from '@hotwired/stimulus';

/*
 * Topbar Controller
 *
 * Handles topbar functionality including theme switching and language selection
 */
export default class extends Controller {
    static targets = ['themeSelect', 'languageSelect']

    connect() {
        // Initialize theme from localStorage or default to light
        this.initializeTheme();
        
        // Initialize language from current locale
        this.initializeLanguage();
    }

    initializeTheme() {
        const savedTheme = localStorage.getItem('s4a-theme') || 'light';
        this.setTheme(savedTheme);
        
        if (this.hasThemeSelectTarget) {
            this.themeSelectTarget.value = savedTheme;
        }
    }

    initializeLanguage() {
        const currentLocale = document.documentElement.lang || 'en';
        
        if (this.hasLanguageSelectTarget) {
            this.languageSelectTarget.value = currentLocale;
        }
    }

    changeTheme(event) {
        const theme = event.target.value;
        this.setTheme(theme);
        localStorage.setItem('s4a-theme', theme);
    }

    setTheme(theme) {
        const body = document.body;
        
        // Remove existing theme classes
        body.classList.remove('theme-light', 'theme-dark');
        
        // Add new theme class
        body.classList.add(`theme-${theme}`);
        
        // Update Bootstrap theme attribute for proper styling
        document.documentElement.setAttribute('data-bs-theme', theme);
    }

    changeLanguage(event) {
        const locale = event.target.value;
        // Redirect to current page with new locale
        const currentPath = window.location.pathname;
        const searchParams = new URLSearchParams(window.location.search);
        searchParams.set('_locale', locale);
        
        window.location.href = `${currentPath}?${searchParams.toString()}`;
    }
}