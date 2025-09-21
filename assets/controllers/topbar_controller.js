import { Controller } from '@hotwired/stimulus';

/*
 * Topbar Controller
 *
 * Handles topbar functionality including theme switching and language selection
 */
export default class extends Controller {
    static targets = ['themeSelect', 'languageSelect']
    static values = {
        theme: { type: String, default: 'light' },
        language: String,
    }

    themeValueChanged() {
        document.documentElement.setAttribute('data-bs-theme', this.themeValue);
    }

    /**
     * @param {Event} event
     */
    changeTheme(event) {
        this.themeValue = event.currentTarget.value;
    }

    /**
     * @param {Event} event
     */
    changeLanguage(event) {
        const locale = event.target.value;
        // Redirect to current page with new locale
        const currentPath = window.location.pathname;
        const searchParams = new URLSearchParams(window.location.search);
        searchParams.set('_locale', locale);

        window.location.href = `${currentPath}?${searchParams.toString()}`;
    }
}
