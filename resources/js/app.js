import './bootstrap';
import '../css/app.css';

import {
    businessRegistrationForm,
    loginForm,
    forgotPasswordForm,
    resetPasswordForm,
    onBoardingFormHandler,
    technicianForm,
    selectAllTechnicians,
} from './business/validation.js';

import {
    stripePayment,
    createTeamSize,
    updateCard,
    downgradePlan,
    processDowngrade,
} from './business/stripe-payment.js';

import {
    dzUpload
} from './business/dz.js';

import {
    multiSelect
} from './common/multi-select.js';

import './business/customer-form.js';
import './business/import-customers.js';
import './business/work-order.js';
import './business/maintenance.js';
import './business/scheduler.js';
import { templateForm } from './business/template-form.js';

// Register business registration form
Alpine.data('businessRegistrationForm', businessRegistrationForm);
Alpine.data('loginForm', loginForm);
Alpine.data('forgotPasswordForm', forgotPasswordForm);
Alpine.data('resetPasswordForm', resetPasswordForm);
Alpine.data('onBoardingFormHandler', onBoardingFormHandler);
Alpine.data('stripePayment', stripePayment);
Alpine.data('processDowngrade', processDowngrade);
Alpine.data('updateCard', updateCard);
Alpine.data('technicianForm', technicianForm);
Alpine.data('createTeamSize', createTeamSize);
Alpine.data('downgradePlan', downgradePlan);
Alpine.data('dzUpload', dzUpload);
Alpine.data('multiSelect', multiSelect);
Alpine.data('selectAllTechnicians', selectAllTechnicians);
Alpine.data('templateForm', templateForm);
