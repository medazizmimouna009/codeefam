import { startStimulusApp } from '@symfony/stimulus-bundle';

// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);
// Import Stimulus and Chart.js

export const app = startStimulusApp(require.context('@symfony/stimulus-bridge/lazy-controllers-loader!./controllers', 
    true,
   /\.(j|t)sx?$/
));