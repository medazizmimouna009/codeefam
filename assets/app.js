// Import CSS
import './css/style.css';
import './styles/app.css';

// Import JavaScript
import './js/main.js';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

import { startStimulusApp } from '@symfony/stimulus-bundle';

// Registers Stimulus controllers from controllers.json and in the controllers/ directory
const app = startStimulusApp();

// Import Chart.js
import { Chart } from 'chart.js';

// Register the Chart.js controller (if needed)
import ChartController from '@symfony/ux-chartjs';
app.register('chart', ChartController);