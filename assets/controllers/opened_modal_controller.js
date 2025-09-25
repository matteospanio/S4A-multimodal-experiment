import { Controller } from '@hotwired/stimulus';
import * as bootstrap from 'bootstrap';

/*
 * Topbar Controller
 *
 * Handles topbar functionality including theme switching and language selection
 */
export default class extends Controller {
    static targets = ['modal'];
    static values = {
        openModal: Boolean,
    };

    connect() {
        super.connect();
        this.modal = new bootstrap.Modal(this.modalTarget, { backdrop: 'static', keyboard: false });
        if (this.openModalValue === true) {
            this.modal.show();
        }
    }

    disconnect() {
        super.disconnect();
        this.modal.dispose();
    }
}
