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
}
