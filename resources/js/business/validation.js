import { resetForm, encryptPassword } from '../common/common.js';

/**
 * Business registration form validation component
 */
export function businessRegistrationForm() {
    return {
        business_name: this.$refs.business_name?.value || '',
        email: this.$refs.email?.value || '',
        password: '',
        password_confirmation: '',
        showPassword: false,
        showConfirmPassword: false,
        errors: {
            business_name: '',
            email: '',
            password: '',
            password_confirmation: ''
        },
        processing: false,

        init() {
            resetForm(this.$refs.form);
        },

        validateBusinessName(value) {
            if (!value.trim()) {
                this.errors.business_name = window.validation.required;
            } else if (!/^[a-zA-Z\s]+$/.test(value.trim())) {
                this.errors.business_name = window.validation.alpha_only;
                return;
            } else if (value.trim().length < 1) {
                this.errors.business_name = window.validation.required;
            } else if (value.trim().length > 50) {
                this.errors.business_name = window.validation.max_50;
            } else {
                delete this.errors.business_name;
            }
        },

        validateEmail(value) {
            if (!value.trim()) {
                this.errors.email = [window.validation.required];
            } else if (!this.isValidEmail(value.trim())) {
                this.errors.email = window.validation.invalid_email;
            } else {
                delete this.errors.email;
            }
        },

        validatePassword(value) {
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?]).{8,}$/;
            if (!value) {
                this.errors.password = window.validation.required;
            } else if (!passwordRegex.test(value)) {
                this.errors.password_rule = window.validation.password_rule;
            } else if (value.trim().length < 8 || value.trim().length > 20) {
                this.errors.password_rule = window.validation.between_2_20;
            } else {
                delete this.errors.password;
                delete this.errors.password_rule;
            }
        },

        validatePasswordConfirmation(value) {
            if (!value) {
                this.errors.password_confirmation = window.validation.required;
            } else if (value !== this.password) {
                this.errors.password_confirmation = window.validation.confirm_mismatch;
            } else {
                delete this.errors.password_confirmation;
            }
        },

        validateField(field) {
            const value = this[field];
            const validators = {
                'business_name': () => this.validateBusinessName(value),
                'email': () => this.validateEmail(value),
                'password': () => this.validatePassword(value),
                'password_confirmation': () => this.validatePasswordConfirmation(value)
            };

            if (validators[field]) {
                validators[field]();
            }
        },

        validate() {
            this.errors = {};
            let valid = true;

            ['business_name', 'email', 'password', 'password_confirmation'].forEach(field => {
                this.validateField(field);
                if (this.errors[field]) valid = false;
            });

            return valid;
        },

        submitForm() {
            this.processing = true;
            if (this.validate()) {
                this.$refs.form.submit();
            } else {
                this.processing = false;
                const first = Object.keys(this.errors)[0];
                if (first) this.$refs[first]?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        },

        isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }
    };
}

// login form validation
export function loginForm() {
    return {
        email: '',
        password: '',
        showPassword: false,
        showConfirmPassword: false,
        processing: false,
        errors: {
            email: '',
            password: ''
        },
        init() {
            resetForm(this.$refs.form);
        },
        validateForm() {
            // Reset all errors first
            this.errors = {
                email: '',
                password: ''
            };

            // Validate all fields at once
            if (!this.email.trim()) {
                this.errors.email = window.validation.required;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email.trim())) {
                this.errors.email = window.validation.invalid_email;
            }

            if (!this.password.trim()) {
                this.errors.password = window.validation.required;
            }

            // Return true if no errors
            return !this.errors.email && !this.errors.password;
        },
        submitForm() {
            this.processing = true;
            if (this.validateForm()) {
                this.$refs.password.value = encryptPassword(this.password);
                this.password = this.$refs.password.value;
                this.$el.submit();
            } else {
                this.processing = false;
            }
        }
    }
}

// forgot password form validation
export function forgotPasswordForm() {
    return {
        email: '',
        processing: false,
        showPhp: true,
        errors: {},
        init() {
            resetForm(this.$refs.form);
        },
        validateEmail() {
            this.errors = {};
            if (!this.email) {
                this.errors.email = window.validation.required;
                return false;
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email.trim())) {
                this.errors.email = window.validation.invalid_email;
                return false;
            }
            return true;
        },
        submitForm() {
            this.showPhp = false;
            this.processing = true;
            if (this.validateEmail()) {
                this.$el.submit();
            } else {
                this.processing = false;
            }
        }
    }
}

// reset password form validation
export function resetPasswordForm() {
    return {
        password: '',
        password_confirmation: '',
        showPassword: false,
        showConfirmPassword: false,
        showPhp: true,
        processing: false,
        errors: {},

        init() {
            resetForm(this.$refs.form);
            this.$watch('password', () => {
                if (this.password_confirmation) {
                    this.validateConfirmation();
                }
            });
        },

        validatePassword() {
            this.errors = {};

            if (!this.password) {
                this.errors.password = window.validation.required;
                return false;
            }

            if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?]).{8,}$/.test(this.password)) {
                this.errors.password_rule = window.validation.password_rule;
                return false;
            } else if (this.password.trim().length < 8 || this.password.trim().length > 20) {
                this.errors.password_rule = window.validation.between_2_20;
            }

            // If password is valid and confirmation exists, validate confirmation
            if (this.password_confirmation) {
                this.validateConfirmation();
            }

            return !Object.keys(this.errors).length;
        },

        validateConfirmation() {
            if (!this.password_confirmation) {
                this.errors.password_confirmation = window.validation.required;
                return false;
            }

            if (this.password !== this.password_confirmation) {
                this.errors.password_confirmation = window.validation.confirm_mismatch;
                return false;
            }

            // Clear confirmation error if passwords match
            delete this.errors.password_confirmation;
            return true;
        },

        submitForm() {
            this.showPhp = false;
            this.processing = true;

            // Validate both fields
            const isPasswordValid = this.validatePassword();
            const isConfirmationValid = this.validateConfirmation();

            if (isPasswordValid && isConfirmationValid) {
                this.$el.submit();
            } else {
                this.processing = false;
            }
        }
    }
}

// Onboarding form validation
export function onBoardingFormHandler() {
    return {
        currentStep: 'businessDetails',
        first_name: this.$refs.first_name?.value || '',
        last_name: this.$refs.last_name?.value || '',
        email: this.$refs.email?.value || '',
        phone_number: this.$refs.phone_number?.value || '',
        business_name: this.$refs.business_name?.value || '',
        website_url: this.$refs.website_url?.value || '',
        address: this.$refs.address?.value || '',
        street: this.$refs.street?.value || '',
        country: window.oldOnboarding?.country || '',
        state: window.oldOnboarding?.state || '',
        city: window.oldOnboarding?.city || '',
        zipcode: this.$refs.zipcode?.value || '',
        timezone: this.$refs.timezone?.value || '',
        processing: false,
        states: [],
        cities: [],
        selectedState: '',
        selectedCity: '',
        errors: {},

        init() {
            resetForm(this.$refs.form);
            if (this.country) {
                this.fetchStates();
            }
            if (this.state) {
                this.fetchCities();
            }
        },

        validatePersonalName(value, fieldName) {
            if (!value.trim()) {
                this.errors[fieldName] = window.validation.required;
                return;
            }
            if (!/^[a-zA-Z\s]+$/.test(value.trim())) {
                this.errors[fieldName] = window.validation.alpha_only;
                return;
            }
            if (value.trim().length < 1) {
                this.errors[fieldName] = window.validation.min_1;
            }
            if (value.trim().length > 50) {
                this.errors[fieldName] = window.validation.max_50;
            }
        },

        validateBusinessDetails() {
            if (!this.business_name.trim()) {
                this.errors.business_name = window.validation.required;
                return;
            }
            if (!/^[a-zA-Z\s]+$/.test(this.business_name.trim())) {
                this.errors.business_name = window.validation.alpha_only;
                return;
            }
            if (this.business_name.trim().length < 1) {
                this.errors.business_name = window.validation.required;
            }
            if (this.business_name.trim().length > 50) {
                this.errors.business_name = window.validation.max_50;
            }
        },

        validateWebsiteUrl() {
            if (!this.website_url.trim()) return;

            if (this.website_url.trim().length < 5 || this.website_url.trim().length > 100) {
                this.errors.website_url = window.validation.website_between;
            }
        },

        validateContactInfo() {
            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!this.email.trim()) {
                this.errors.email = window.validation.required;
            } else if (!emailRegex.test(this.email.trim())) {
                this.errors.email = window.validation.invalid_email;
            }

            // Phone validation
            const phoneRegex = /^\d{10}$/;
            if (!this.phone_number.trim()) {
                this.errors.phone_number = window.validation.required;
            } else if (!phoneRegex.test(this.phone_number.trim())) {
                this.errors.phone_number = window.validation.invalid_phone;
            }
        },

        businessAddressStep() {
            this.errors = {};

            // Validate personal names
            this.validatePersonalName(this.first_name, 'first_name');
            this.validatePersonalName(this.last_name, 'last_name');

            // Validate contact information
            this.validateContactInfo();

            // Validate business details
            this.validateBusinessDetails();

            // Validate website URL (optional)
            this.validateWebsiteUrl();

            if (Object.keys(this.errors).length === 0) {
                this.currentStep = 'businessAddress';
            }
        },

        fetchStates() {
            if (this.country) {
                fetch(`/states/${this.country}`)
                    .then(response => response.json())
                    .then(data => {
                        this.states = data;
                    });
                this.selectedState = this.state ? `${this.state}` : '';
            } else {
                this.states = [];
            }
        },
        fetchCities() {
            if (this.state) {
                fetch(`/cities/${this.state}`)
                    .then(response => response.json())
                    .then(data => {
                        this.cities = data;
                    });
                this.selectedCity = this.city ? `${this.city}` : '';
            } else {
                this.cities = [];
            }
        },

        // Validate address or street field
        validateLocation(value, fieldName) {
            const trimmedValue = value.trim();

            if (!trimmedValue) {
                this.errors[fieldName] = window.validation.required;
                return;
            }

            const lengthLimits = {
                address: { min: 5, max: 200, message: window.validation.min_5_max_200 },
                street: { min: 3, max: 100, message: window.validation.min_3_max_100 }
            };

            const limits = lengthLimits[fieldName];
            if (trimmedValue.length < limits.min || trimmedValue.length > limits.max) {
                this.errors[fieldName] = limits.message;
            }
        },

        // Validate postal code
        validatePostalCode() {
            const trimmedZipcode = this.zipcode.trim();
            const zipcodeRegex = /^[a-zA-Z0-9 .\-_]+$/;

            if (!trimmedZipcode) {
                this.errors.zipcode = window.validation.required;
                return;
            }

            if (!zipcodeRegex.test(trimmedZipcode)) {
                this.errors.zipcode = window.validation.zipcode_error;
                return;
            }

            if (trimmedZipcode.length < 3 || trimmedZipcode.length > 12) {
                this.errors.zipcode = window.validation.zipcode_min_3_max_12;
            }
        },

        // Validate basic required fields
        validateRequiredFields() {
            ['country', 'city', 'state', 'timezone'].forEach(field => {
                if (!this[field].trim()) {
                    this.errors[field] = window.validation.required;
                }
            });
        },

        // Main submit handler
        handleSubmit() {
            this.errors = {};
            this.processing = true;

            // Validate address and street
            this.validateLocation(this.address, 'address');
            this.validateLocation(this.street, 'street');

            // Validate required fields
            this.validateRequiredFields();

            // Validate postal code
            this.validatePostalCode();

            // Submit or show errors
            const hasErrors = Object.keys(this.errors).length > 0;
            if (!hasErrors) {
                this.$el.submit();
            } else {
                this.processing = false;
            }
        }
    }
}

// Technician form validation
export function technicianForm() {
    return {
        first_name: this.$refs.first_name?.value || '',
        last_name: this.$refs.last_name?.value || '',
        email: this.$refs.email?.value || '',
        phone: this.$refs.phone?.value || '',
        skill_type: this.$refs.skill_type?.value || [],
        working_days: [],
        submitted: false,
        isSubmitting: false,
        showConfirm: false,
        showUpdateConfirm: false,
        previousStatus: this.$refs.status?.value || true,
        previousWorkingDays: [],
        originalWorkingDays: [],
        status: window.oldTechnician?.status ? Number(window.oldTechnician.status) : 1,
        formError: false,
        errors: {},
        isEdit: false,
        init() {
            resetForm(this.$refs.form);
            this.initializeWorkingDays();

            // Listen for close-cancel event
            this.$el.addEventListener('close-cancel', () => {
                this.showConfirm = false;
                this.status = this.previousStatus;
            });

            // Listen for close-confirm event
            this.$el.addEventListener('close-confirm', () => {
                this.showConfirm = false;
            });
        },

        // Initialize working days from checkboxes
        initializeWorkingDays() {
            const workingDayCheckboxes = document.querySelectorAll('input[name="working_days[]"]:checked');
            this.working_days = Array.from(workingDayCheckboxes).map(cb => cb.value);
            this.previousWorkingDays = [...this.working_days];
            this.originalWorkingDays = [...this.working_days];
        },

        setSkillType(value) {
            this.skill_type = value;
            this.$refs.skill_type.value = value;
        },

        // Validate name field (first or last)
        validateName(value, fieldName) {
            const trimmedValue = value.trim();
            if (!trimmedValue) {
                this.errors[fieldName] = window.validation.required;
                return;
            }
            if (!/^[a-zA-Z\s]+$/.test(trimmedValue)) {
                this.errors[fieldName] = window.validation.only_alphabets;
                return;
            }
            if (trimmedValue.length < 1) {
                this.errors[fieldName] = window.validation.min_1;
                return;
            }
            if (trimmedValue.length > 50) {
                this.errors[fieldName] = window.validation.max_50;
            }
        },

        // Validate email field
        validateEmail() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const trimmedEmail = this.email.trim();

            if (!trimmedEmail) {
                this.errors.email = window.validation.required;
                return;
            }
            if (!emailRegex.test(trimmedEmail)) {
                this.errors.email = window.validation.invalid_email;
            }
        },

        // Validate phone field
        validatePhone() {
            const phoneRegex = /^\d{10}$/;
            const trimmedPhone = this.phone.trim();

            if (!trimmedPhone) {
                this.errors.phone = window.validation.required;
                return;
            }
            if (!phoneRegex.test(trimmedPhone)) {
                this.errors.phone = window.validation.invalid_phone;
            }
        },

        // Validate skills and working days
        validateSkillsAndDays() {
            if (!Object.keys(this.skill_type).length) {
                this.errors.skill_type = window.validation.required;
            }

            const checkboxes = document.querySelectorAll('input[name="working_days[]"]:checked');
            if (checkboxes.length === 0) {
                this.errors.working_days = window.validation.one_working_day_required;
            }
        },

        // Main validation method
        validateForm() {
            this.errors = {};

            this.validateName(this.first_name, 'first_name');
            this.validateName(this.last_name, 'last_name');
            this.validateEmail();
            this.validatePhone();
            this.validateSkillsAndDays();

            return Object.keys(this.errors).length === 0;
        },

        // Check if confirmation is needed
        needsConfirmation() {
            if (!this.isEdit) return false;

            // Check if status is changing from active to inactive
            if (this.previousStatus && !this.status) {
                return true;
            }

            // Check if working days have changed from original
            const removedDays = this.originalWorkingDays.filter(day => !this.working_days.includes(day));
            const addedDays = this.working_days.filter(day => !this.originalWorkingDays.includes(day));

            return (removedDays.length > 0 || addedDays.length > 0);
        },

        // Submit form with confirmation
        submitWithConfirmation() {
            this.showUpdateConfirm = false;

            // Add confirmation flag to form
            const confirmInput = document.createElement('input');
            confirmInput.type = 'hidden';
            confirmInput.name = 'confirmed';
            confirmInput.value = '1';
            this.$refs.form.appendChild(confirmInput);

            this.submitForm();
        },

        // Handle update confirmation
        handleUpdateConfirm() {
            this.showUpdateConfirm = false;

            // Update hidden status input before submitting
            if (this.$refs.status) {
                this.$refs.status.value = this.status;
            }

            // Add confirmation flag to form
            const confirmInput = document.createElement('input');
            confirmInput.type = 'hidden';
            confirmInput.name = 'confirmed';
            confirmInput.value = '1';
            this.$refs.form.appendChild(confirmInput);

            // Submit the form
            this.$refs.form.submit();
        },

        // Handle update cancel
        handleUpdateCancel() {
            this.showUpdateConfirm = false;
            // Reset to previous values
            this.status = this.previousStatus;
            this.working_days = [...this.previousWorkingDays];

            // Update UI to reflect reset values
            this.updateUIAfterReset();
        },

        // Cancel confirmation
        cancelConfirmation() {
            this.showUpdateConfirm = false;
            // Reset to previous values
            this.status = this.previousStatus;
            this.working_days = [...this.previousWorkingDays];

            // Update UI to reflect reset values
            this.updateUIAfterReset();
        },

        // Update UI after resetting values
        updateUIAfterReset() {
            // Update status radio buttons
            const activeRadio = document.getElementById('status_active');
            const inactiveRadio = document.getElementById('status_inactive');

            if (this.status) {
                if (activeRadio) activeRadio.checked = true;
                if (inactiveRadio) inactiveRadio.checked = false;
            } else {
                if (activeRadio) activeRadio.checked = false;
                if (inactiveRadio) inactiveRadio.checked = true;
            }

            // Update working day checkboxes
            const workingDayCheckboxes = document.querySelectorAll('input[name="working_days[]"]');
            workingDayCheckboxes.forEach(checkbox => {
                checkbox.checked = this.working_days.includes(checkbox.value);
            });
        },

        // Handle status change
        handleStatusChange() {
            const activeRadio = document.getElementById('status_active');
            const inactiveRadio = document.getElementById('status_inactive');

            if (activeRadio?.checked) {
                this.status = 1;
            } else if (inactiveRadio?.checked) {
                this.status = 0;
            }

            // Update hidden status input
            if (this.$refs.status) {
                this.$refs.status.value = this.status;
            }

            // Check if status changed from active to inactive
            if (this.previousStatus == 1 && this.status === 0) {
                this.showConfirm = true;
            } else {
                this.previousStatus = this.status;
            }
        },

        checkStatusChange(event) {
            const newStatus = Number(event.target.value);
            if (this.previousStatus == 1 && newStatus === 0) {
                this.showConfirm = true;
            } else {
                this.previousStatus = this.status;
                // Check if confirmation is needed
                if (this.needsConfirmation()) {
                    this.showUpdateConfirm = true;
                } else if (this.validateForm()) {
                    // Submit form directly if no confirmation needed
                    this.$el.submit();
                } else {
                    this.formError = true;
                    this.scrollHandler();
                }
            }
        },

        // Handle working days change
        handleWorkingDaysChange() {
            // Update working_days array from checkboxes
            const workingDayCheckboxes = document.querySelectorAll('input[name="working_days[]"]:checked');
            this.working_days = Array.from(workingDayCheckboxes).map(cb => cb.value);
        },

        cancelDeactivate() {
            this.showConfirm = false;
            this.status = this.previousStatus;
        },

        scrollHandler() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        submitForm() {
            this.submitted = true;
            this.isSubmitting = true;

            if (this.validateForm()) {
                // Check if confirmation is needed
                if (this.needsConfirmation()) {
                    this.showUpdateConfirm = true;
                    this.isSubmitting = false;
                } else {
                    // Submit form directly
                    this.$refs.form.submit();
                }
            } else {
                this.formError = true;
                this.isSubmitting = false;
                this.scrollHandler();
            }
        }
    }
}

export function selectAllTechnicians() {
    return {
        search: '',
        showToast: false,
        showSuccess: false,
        successMessage: '',
        showMessageModal: false,
        selectedTechnicians: [],
        selectedMessage: '',
        selectAll: false,

        toggleAll() {
            this.selectAll = !this.selectAll;
            const checkboxes = document.querySelectorAll('.technician-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.selectAll;
                checkbox.dispatchEvent(new Event('change'));
            });
            this.updateSelected();
        },

        updateSelected() {
            this.selectedTechnicians = Array.from(document.querySelectorAll('.technician-checkbox:checked')).map(cb => cb.value);
        },

        selectTechnician() {
            const checkbox = this.$el.closest('tr').querySelector('.technician-checkbox');
            if (checkbox && !checkbox.checked) {
                checkbox.checked = true;
                checkbox.dispatchEvent(new Event('change'));
            }
            this.selectedMessage = '';
            this.showMessageModal = true;
        },

        cancelMessageModal() {
            document.querySelectorAll('.technician-checkbox').forEach(cb => {
                if (cb.checked) {
                    cb.checked = false;
                    cb.dispatchEvent(new Event('change'));
                }
            });
            this.showMessageModal = false;
        },
    }
}
