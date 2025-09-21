import { Controller } from '@hotwired/stimulus';

/*
 * Stimulus Selector Controller
 *
 * Handles selection of audio/perfume stimuli choices
 */
export default class extends Controller {
    static values = { choice: String }
    static targets = ['card', 'choiceInput', 'submitButton']

    choiceValueChanged() {
        this.choiceInputTarget.value = this.choiceValue;
    }

    /**
     * @param {Event} event
     */
    unselectAllCards(event) {
        this.cardTargets.forEach(card => {
            card.classList.remove('selected');
            if (card === event.currentTarget) {
                card.classList.add('selected');
            }
        });
    }

    /**
     * @param {Event} event
     */
    selectStimulus(event) {
        event.preventDefault();
        const target = event.currentTarget;
        console.log(target);

        this.unselectAllCards(event);

        // Store the selected choice
        this.choiceValue = target.id;
        this.submitButtonTarget.disabled = false;

        // Visual feedback
        target.style.transform = 'scale(1.05)';
        setTimeout(() => {
            target.style.transform = 'scale(1)';
        }, 150);
    }
}
