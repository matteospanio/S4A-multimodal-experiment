import { Controller } from '@hotwired/stimulus';

/*
 * Submit Handler Controller
 * 
 * Handles submission of the user's choice
 */
export default class extends Controller {

    connect() {
        this.element.addEventListener('click', this.submitChoice.bind(this));
    }

    submitChoice(event) {
        event.preventDefault();
        
        if (!window.selectedChoice) {
            alert('Please make a selection before submitting.');
            return;
        }
        
        // Show loading state
        this.element.disabled = true;
        this.element.innerHTML = 'Submitting...';
        
        // Here you would normally send the choice to your backend
        // For now, we'll just show a confirmation and redirect
        console.log('Selected choice:', window.selectedChoice);
        
        setTimeout(() => {
            alert(`Thank you! You selected: ${window.selectedChoice}`);
            window.location.href = '/';
        }, 1000);
    }
}