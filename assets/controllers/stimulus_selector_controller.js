import { Controller } from '@hotwired/stimulus';

/*
 * Stimulus Selector Controller
 * 
 * Handles selection of audio/perfume stimuli choices
 */
export default class extends Controller {
    static values = { choice: String }
    static targets = []

    connect() {
        this.element.addEventListener('click', this.selectStimulus.bind(this));
    }

    selectStimulus(event) {
        event.preventDefault();
        
        // Remove selection from all other stimulus cards
        document.querySelectorAll('.stimulus-card').forEach(card => {
            card.classList.remove('selected');
        });
        
        // Add selection to this card
        this.element.classList.add('selected');
        
        // Store the selected choice
        window.selectedChoice = this.choiceValue;
        
        // Enable submit button
        const submitButton = document.getElementById('submit-choice');
        if (submitButton) {
            submitButton.disabled = false;
        }
        
        // Visual feedback
        this.element.style.transform = 'scale(1.05)';
        setTimeout(() => {
            this.element.style.transform = 'scale(1)';
        }, 150);
    }
}