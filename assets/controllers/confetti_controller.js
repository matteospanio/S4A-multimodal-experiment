import { Controller } from '@hotwired/stimulus';
import JSConfetti from 'js-confetti';

const CONFETTI_ARGS  = [
    {}, // empty object is for default values
    { confettiRadius: 12, confettiNumber: 100 },
    { emojis: ['🌈', '⚡️', '💥', '✨', '💫', '🌸'] },
    { emojis: ['🍋', '🍏', '🍉', '🍒'], confettiNumber: 40 },
    { emojis: [' 🍬', '☕', '🍩', '🍪', '🍫'], confettiNumber: 40 },
    { emojis: ['🦄'], confettiRadius: 100, confettiNumber: 50 },
    {
        confettiColors: ['#ffbe0b', '#fb5607', '#ff006e', '#8338ec', '#3a86ff'],
        confettiRadius: 10,
        confettiNumber: 150,
    },
    {
        confettiColors: ['#9b5de5', '#f15bb5', '#fee440', '#00bbf9', '#00f5d4'],
        confettiRadius: 6,
        confettiNumber: 300,
    },
]

/*
 * Topbar Controller
 *
 * Handles topbar functionality including theme switching and language selection
 */
export default class extends Controller {
    connect() {
        super.connect();
        this.jsConfetti = new JSConfetti();

        this.pickRandomConfetti();
    }

    pickRandomConfetti() {
        const args = CONFETTI_ARGS[Math.floor(Math.random() * CONFETTI_ARGS.length)];
        this.jsConfetti.addConfetti(args);
    }
}
