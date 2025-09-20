import { Controller } from '@hotwired/stimulus';

/*
 * Task Selector Controller
 *
 * Handles clicking on task cards and redirecting to the appropriate task page
 */
export default class extends Controller {
    static values = { url: String }

    selectTask(event) {
        event.preventDefault();

        // Add visual feedback
        this.element.style.transform = 'scale(0.98)';

        setTimeout(() => {
            window.location.href = this.urlValue;
        }, 150);
    }
}
