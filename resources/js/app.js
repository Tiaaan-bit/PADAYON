import './bootstrap';

import Alpine from 'alpinejs';
import appointmentWizard from './appointmentWizard';

window.Alpine = Alpine;

Alpine.data(
    'appointmentWizard',
    appointmentWizard
);

Alpine.start();