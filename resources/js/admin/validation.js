import { resetForm } from '../common/common.js';

export function loginForm() {
    return {
        email: '',
        password: '',
        submitted: false,
        errors: {
            email: '',
            password: ''
        },

        validateForm() {
            // Reset all errors first
            this.errors = {
                email: '',
                password: ''
            };

            // Validate all fields at once
            if (!this.email) {
                this.errors.email = window.validationMessages.required;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email.trim())) {
                this.errors.email = window.validationMessages.invalid_email;
            }

            if (!this.password) {
                this.errors.password = window.validationMessages.required;
            } else if (this.password.length < 8) {
                this.errors.password = window.validationMessages.password_min;
            }

            // Return true if no errors
            return !this.errors.email && !this.errors.password;
        },

        submitForm() {
            if (this.validateForm()) {
                this.submitted = true;
                this.$el.submit();
            }
        }
    }
}

export function forgotPasswordForm() {
    return {
        email: '',
        submitted: false,
        errors: {},
        init() {
            resetForm(this.$refs.form);
        },
        validateEmail() {
            this.errors = {};
            if (!this.email) {
                this.errors.email = window.validationMessages.required;
                return false;
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email)) {
                this.errors.email = window.validationMessages.invalid_email;
                return false;
            }
            return true;
        },
        submitForm() {
            this.submitted = true;
            if (this.validateEmail()) {
                this.$el.submit();
            }
        }
    }
}

export function resetPasswordForm() {
    return {
        password: '',
        password_confirmation: '',
        submitted: false,
        errors: {},

        init() {
            this.$watch('password', () => {
                if (this.password_confirmation) {
                    this.validateConfirmation();
                }
            });
        },

        validatePassword() {
            this.errors = {};

            if (!this.password) {
                this.errors.password = window.validationMessages.required;
                return false;
            }

            if (!/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?\d)(?=.*?[#?!@$%^&*-]).{8,}$/.test(this.password)) {
                this.errors.password_rules = window.validationMessages.password_rules;
                return false;
            }

            // If password is valid and confirmation exists, validate confirmation
            if (this.password_confirmation) {
                this.validateConfirmation();
            }

            return !Object.keys(this.errors).length;
        },

        validateConfirmation() {
            if (!this.password_confirmation) {
                this.errors.password_confirmation = window.validationMessages.required;
                return false;
            }

            if (this.password !== this.password_confirmation) {
                this.errors.password_confirmation = window.validationMessages.confirm_mismatch;
                return false;
            }

            // Clear confirmation error if passwords match
            delete this.errors.password_confirmation;
            return true;
        },

        submitForm() {
            this.submitted = true;

            // Validate both fields
            const isPasswordValid = this.validatePassword();
            const isConfirmationValid = this.validateConfirmation();

            if (isPasswordValid && isConfirmationValid) {
                this.$el.submit();
            }
        }
    }
}