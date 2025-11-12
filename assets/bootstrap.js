import { startStimulusApp } from '@symfony/stimulus-bundle';
import * as bootstrap from 'bootstrap';

// Start Stimulus (if used)
const app = startStimulusApp();
// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);

// Expose Bootstrap on window for convenience (optional)
// This helps when using inline scripts or third-party snippets expecting global `bootstrap`
window.bootstrap = bootstrap;
