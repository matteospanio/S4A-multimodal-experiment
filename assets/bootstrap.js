import { startStimulusApp } from '@symfony/stimulus-bundle';
import ConfettiController from './controllers/confetti_controller.js';
import CsrfProtectionController from './controllers/csrf_protection_controller.js';
import OpenedModalController from './controllers/opened_modal_controller.js';
import StimulusSelectorController from './controllers/stimulus_selector_controller.js';
import TopbarController from './controllers/topbar_controller.js';

const app = startStimulusApp();
app.register('confetti', ConfettiController);
app.register('csrf-protection', CsrfProtectionController);
app.register('opened-modal', OpenedModalController);
app.register('stimulus-selector', StimulusSelectorController);
app.register('topbar', TopbarController);
