import '../bootstrap';

import {
    loginForm,
    forgotPasswordForm,
    resetPasswordForm
} from './validation.js';

// Import the barChart functionality and make it globally available
import { barChart } from './barChart.js';
// Register Alpine Components for Admin
Alpine.data('loginForm', loginForm);
Alpine.data('forgotPassword', forgotPasswordForm);
Alpine.data('resetPassword', resetPasswordForm);
Alpine.data('barChart', barChart);
