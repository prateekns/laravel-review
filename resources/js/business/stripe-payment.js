/**
 * Stripe payment process
 * @returns {Object} The stripe payment form
 */
import { postJson, toggleProperty, createStripeElements, mountStripeElements, validateStripeForm, resetForm, isOnlyNumbers } from '../common/common.js';

/**
 * Create the team size
 * @returns {Object} The team size form
 */
export function createTeamSize() {
    return {
        admin: parseInt(this.$refs.admin?.value) || 1,
        technician: parseInt(this.$refs.technician?.value) || 1,
        minAdmin: 0,
        minTechnician: 0,
        errors: {},
        processing: false,
        pricing: {
            admin: 0,
            technician: 0
        },
        trial: false,
        isSubscribed: false,

        /**
         * Initialize the team size default values
         */
        init() {
            resetForm(this.$refs.form);
            this.pricing = window.pricing;
            this.trial = window.subscription.isTrialActive;
            this.isSubscribed = window.subscription.isSubscribed;
            this.minAdmin = window.subscription.adminCount;
            this.minTechnician = window.subscription.technicianCount;

            this.$watch('admin', () => {
                if (this.trial && this.admin < this.minAdmin) {
                    this.admin = this.minAdmin;
                } else if (!this.isSubscribed && this.trial && this.admin < 1) {
                    this.admin = 0;
                } else if (!this.trial && !this.isSubscribed && this.admin < 1) {
                    this.admin = 0;
                } else if (this.minAdmin > 0 && !this.isSubscribed && this.admin < this.minTechnician) {
                    this.admin = this.minAdmin;
                } else if (this.admin < 1) {
                    this.admin = 0;
                } else if (this.admin > window.max_team_qty) {
                    this.admin = 100;
                }
                this.validateInputs()
            });
            this.$watch('technician', () => {
                if (this.trial && this.technician < this.minAdmin) {
                    this.technician = this.minTechnician;
                } else if (!this.isSubscribed && this.trial && this.technician < 1) {
                    this.technician = 0;
                } else if (!this.trial && !this.isSubscribed && this.technician < 1) {
                    this.admin = 0;
                } else if (this.minTechnician > 0 && !this.isSubscribed && this.technician < this.minTechnician) {
                    this.technician = this.minTechnician;
                } else if (this.technician < 1) {
                    this.technician = 0;
                } else if (this.technician > window.max_team_qty) {
                    this.technician = 100;
                }
                this.validateInputs()
            });
        },

        /**
         * Increment the admin count
         */
        incrementAdmin() {
            this.admin = parseInt(this.admin) + 1;
            this.validateInputs();
        },

        /**
         * Check if count can be decremented
         * @param {number} currentCount - Current count
         * @param {number} minCount - Minimum count allowed
         * @returns {boolean} Whether count can be decremented
         */
        canDecrementCount(currentCount, minCount) {

            if (this.trial && currentCount > minCount) {
                return true;
            }

            if (!this.trial && !this.isSubscribed) {
                return true;
            }

            if (this.isSubscribed) {
                return true;
            }
            return currentCount > minCount;
        },

        /**
         * Decrement the admin count
         */
        decrementAdmin() {
            if (this.canDecrementCount(this.admin, this.minAdmin)) {
                this.admin = parseInt(this.admin) - 1;
            }
            this.validateInputs();
        },

        /**
         * Increment the technician count
         */
        incrementTechnician() {
            this.technician = parseInt(this.technician) + 1;
            this.validateInputs();
        },

        /**
         * Decrement the technician count
         */
        decrementTechnician() {
            if (this.canDecrementCount(this.technician, this.minTechnician)) {
                this.technician = parseInt(this.technician) - 1;
            }
            this.validateInputs();
        },

        /**
         * Validate the inputs
         */
        validateInputs() {
            this.errors = {};

            if (this.admin < 1) {
                this.errors.admin = window.validation.required;
            }

            if (this.technician < 1) {
                this.errors.technician = window.validation.required;
            }

            if (this.admin > 0 || this.technician > 0) {
                delete this.errors.admin;
                delete this.errors.technician;
            }

            if (this.technician < 1 && this.admin < 1) {
                this.errors.team_size = window.validation.team_size_required;
            }

            if (this.admin >= window.max_team_qty) {
                this.errors.admin = window.validation.team_size_max;
            }

            if (this.technician >= window.max_team_qty) {
                this.errors.technician = window.validation.team_size_max;
            }

            return Object.keys(this.errors).length === 0;
        },

        /**
         * Get the admin total
         * @returns {number} The admin total
         */
        getAdminTotal() {
            const adminCount = parseInt(this.admin) || 0;
            return adminCount > 0 && adminCount <= 100 ? adminCount * this.pricing.admin : 0;
        },

        /**
         * Get the technician total
         * @returns {number} The technician total
         */
        getTechnicianTotal() {
            const techCount = parseInt(this.technician) || 0;
            return techCount > 0 && techCount <= 100 ? techCount * this.pricing.technician : 0;
        },

        /**
         * Get the total price
         * @returns {number} The total price
         */
        getTotalPrice() {
            return this.getAdminTotal() + this.getTechnicianTotal();
        },

        submitForm() {
            this.processing = true;

            if (this.validateInputs()) {
                this.$el.submit();
            } else {
                this.processing = false;
            }
        }
    };
}

/**
 * Create the team size
 * @returns {Object} The team size form
 */
export function downgradePlan() {
    return {
        admin: parseInt(this.$refs.admin?.value) || 1,
        technician: parseInt(this.$refs.technician?.value) || 1,
        errors: {},
        pricing: {
            admin: 0,
            technician: 0
        },
        processing: false,

        /**
         * Initialize the team size default values
         */
        init() {
            this.pricing = window.pricing;
            resetForm(this.$refs.form);

            this.$watch('admin', () => {
                if (!isOnlyNumbers(this.admin)) {
                    this.admin = 0;
                }
                this.validateInputs()
            });
            this.$watch('technician', () => {
                if (!isOnlyNumbers(this.technician)) {
                    this.technician = 0;
                }
                this.validateInputs()
            });
        },

        /**
         * Increment the admin count
         */
        incrementAdmin() {
            if (this.admin < window.subscription.adminCount) {
                this.admin = parseInt(this.admin) + 1;
            }
            this.validateInputs();
        },

        /**
         * Decrement the admin count
         */
        decrementAdmin() {
            if (this.admin > 0) {
                this.admin = parseInt(this.admin) - 1;
            }
            this.validateInputs();
        },

        /**
         * Increment the technician count
         */
        incrementTechnician() {
            if (this.technician < window.subscription.technicianCount) {
                this.technician = parseInt(this.technician) + 1;
            }
            this.validateInputs();
        },

        /**
         * Decrement the technician count
         */
        decrementTechnician() {
            if (this.technician > 0) {
                this.technician = parseInt(this.technician) - 1;
            }
            this.validateInputs();
        },

        /**
         * Validate the inputs
         */
        validateInputs() {
            this.errors = {};

            if (this.admin >= window.subscription.adminCount) {
                this.admin = window.subscription.adminCount;
            }

            if (this.technician >= window.subscription.technicianCount) {
                this.technician = window.subscription.technicianCount;
            }

            if (this.technician < 1 && this.admin < 1) {
                this.errors.team_size = window.validation.no_downgrade;
                this.errors.technician = window.validation.required;
                this.errors.admin = window.validation.required;
            }

            if (this.admin > 0 || this.technician > 0) {
                delete this.errors.admin;
                delete this.errors.technician;
            }

            if (this.technician >= window.subscription.technicianCount && this.admin >= window.subscription.adminCount) {
                this.errors.team_size = window.validation.no_downgrade_exceed;
            }

            return Object.keys(this.errors).length === 0;
        },

        /**
         * Get the admin total
         * @returns {number} The admin total
         */
        getAdminTotal() {
            const adminCount = parseInt(this.admin) || 0;
            return adminCount > 0 && adminCount <= 100 ? adminCount * this.pricing.admin : 0;
        },

        /**
         * Get the technician total
         * @returns {number} The technician total
         */
        getTechnicianTotal() {
            const techCount = parseInt(this.technician) || 0;
            return techCount > 0 && techCount <= 100 ? techCount * this.pricing.technician : 0;
        },

        /**
         * Get the total price
         * @returns {number} The total price
         */
        getTotalPrice() {
            return this.getAdminTotal() + this.getTechnicianTotal();
        },

        submitForm() {
            this.processing = true;

            if (this.validateInputs()) {
                this.$el.submit();
            } else {
                this.processing = false;
            }
        }
    };
}


/**
 * Stripe payment process
 * @returns {Object} The stripe payment form
 */
export function stripePayment() {
    return {
        stripe: null,
        elements: null,
        cardNumber: null,
        cardExpiry: null,
        cardCvc: null,
        cardHolderName: this.$refs.cardHolderName?.value || '',
        payment_uuid: this.$refs.payment_uuid?.value || '',
        interval: 'monthly',
        priceId: null,
        clientSecret: this.$refs.clientSecret?.value || null,
        processing: false,
        form: null,
        paymentError: '',
        paymentMethodSelectElem: null,
        showPaymentForm: true,
        paymentMethod: '',
        addCard: "add-card",
        stripeError: false,
        cardError: {},
        errors: {},

        /**
         * Initialize the default actions of payment process
         * @returns {Promise<void>}
         */
        async init() {

            if (!window.stripeKey) {
                this.stripeError = true;
                this.paymentError = window.paymentMessages.stripe_not_initialized;
                return
            }

            this.paymentMethodSelectElem = document.getElementById('sel-payment-method');
            if (this.paymentMethodSelectElem) {
                this.showPaymentForm = false;
            }

            if (this.stripeError && !this.paymentMethodSelectElem) {
                this.paymentError = window.paymentMessages.payment_init_error;
            }
            this.stripe = Stripe(window.stripeKey);

            // Create and mount card elements
            const stripeElements = createStripeElements(this.stripe);
            this.elements = mountStripeElements(stripeElements);

            // Store references to individual elements
            this.cardNumber = stripeElements.cardNumber;
            this.cardExpiry = stripeElements.cardExpiry;
            this.cardCvc = stripeElements.cardCvc;

            this.currency = window.pricing.currency;
            this.totalAmount = this.currency + window.pricing.monthly;
            this.totalAmountInterval = window.pricing.monthlyInterval;
            this.totalAmountInDecimal = this.formatToTwoDecimals(window.pricing.monthly);

            // Watch for interval changes
            this.$watch('interval', (value) => {
                // For now using static data, will be replaced with dynamic data later
                if (value === 'monthly') {
                    this.totalAmount = this.currency + window.pricing.monthly;
                    this.totalAmountInterval = window.pricing.monthlyInterval;
                    this.totalAmountInDecimal = this.formatToTwoDecimals(window.pricing.monthly);
                } else if (value === 'half-yearly') {
                    this.totalAmount = this.currency + window.pricing.halfyearly;
                    this.totalAmountInterval = window.pricing.halfyearlyInterval;
                    this.totalAmountInDecimal = this.formatToTwoDecimals(window.pricing.halfyearly);
                } else if (value === 'yearly') {
                    this.totalAmount = this.currency + window.pricing.yearly;
                    this.totalAmountInterval = window.pricing.yearlyInterval;
                    this.totalAmountInDecimal = this.formatToTwoDecimals(window.pricing.yearly);
                } else if (value === 'daily') {
                    this.totalAmount = this.currency + window.pricing.daily;
                    this.totalAmountInterval = window.pricing.dailyInterval;
                    this.totalAmountInDecimal = this.formatToTwoDecimals(window.pricing.daily);
                }
            });

            this.$watch('cardHolderName', (value) => {
                if (value.trim()) {
                    this.cardError.cardHolderName = '';
                    document.getElementById('card-holder-name').classList.remove("error-message-border");
                }
            });

            this.cardNumber.on('change', (event) => {
                if (event.complete) {
                    this.cardError.cardNumber = '';
                    document.getElementById('card-number').classList.remove("error-message-border");
                }
            });

            this.cardExpiry.on('change', (event) => {
                if (event.complete) {
                    this.cardError.cardExpiry = '';
                    document.getElementById('card-expiry').classList.remove("error-message-border");
                }
            });

            this.cardCvc.on('change', (event) => {
                if (event.complete) {
                    this.cardError.cardCvc = '';
                    document.getElementById('card-cvc').classList.remove("error-message-border");
                }
            });
        },

        formatToTwoDecimals(value) {
            // Convert to number if it's a string
            let num = parseFloat(value);

            // If already has two decimals, return as-is
            if (num % 1 !== 0) {
                return num.toString();
            }

            // If it's an integer, format to 2 decimal places
            return this.currency + num.toFixed(2);
        },

        /**
         * Handle new card payment process
         * @returns {Promise<void>}
         */
        async handleNewCardPayment() {
            try {
                const { paymentMethod, error } = await this.stripe.createPaymentMethod({
                    type: 'card',
                    card: this.cardNumber,
                    billing_details: {
                        name: this.cardHolderName,
                    },
                });

                if (error) {
                    this.paymentError = error.message;
                    return false;
                }

                this.paymentMethod = paymentMethod.id;
                return true;
            } catch (error) {
                this.paymentError = error.message;
                return false;
            }
        },

        /**
         * Handle payment confirmation
         * @param {Object} paymentResponse - The payment response
         * @returns {Promise<boolean>} - Returns true if payment succeeded
         */
        async handlePaymentConfirmation(paymentResponse) {
            if (!paymentResponse.requires_action) {
                return paymentResponse.success;
            }

            const result = await this.stripe.confirmCardPayment(paymentResponse.client_secret);

            if (result.error) {
                this.paymentError = result.error.message;
                return false;
            }

            return result.paymentIntent.status === "succeeded";
        },

        /**
         * Initialize the payment process
         * @returns {Promise<void>}
         */
        async actionPay() {
            this.paymentError = '';
            this.processing = true;
            this.validatePaymentForm();

            if (this.checkErrors()) {
                this.processing = false;
                return;
            }

            try {
                const isNewCard = !this.paymentMethodSelectElem || this.paymentMethodSelectElem?.value === this.addCard;

                if (isNewCard) {
                    const success = await this.handleNewCardPayment();
                    if (!success) {
                        this.processing = false;
                        return;
                    }
                    const paymentResponse = await this.processPayment();

                    if (paymentResponse?.requires_action) {
                        const paymentConfirmation = await this.stripe.confirmCardPayment(paymentResponse.client_secret);
                        if (paymentConfirmation.error) {
                            this.paymentError = paymentConfirmation.error.message;
                            this.processing = false;
                            await this.rollbackSubscription();
                            return;
                        } else if (paymentConfirmation.paymentIntent.status === "succeeded") {
                            window.location.href = '/business/account/payment-success/' + this.payment_uuid;
                        }

                    } else if (paymentResponse.success) {
                        window.location.href = '/business/account/payment-success/' + this.payment_uuid;
                    } else {
                        this.paymentError = paymentResponse.message;
                        this.processing = false;
                        return;
                    }

                } else {
                    await this.updateSubscription();
                }
            } catch (error) {
                this.paymentError = error.message;
            } finally {
                this.processing = false;
            }
        },

        /**
         * Process the payment
         * @returns {Promise<Object>} The response data
         */
        async processPayment() {
            try {
                const response = await postJson('/business/payment/process', {
                    payment_uuid: this.payment_uuid,
                    interval: this.interval,
                    payment_method: this.paymentMethod,
                    card_holder_name: this.cardHolderName,
                }, {
                    timeout: 60000
                });

                if (!response.success) {
                    throw new Error(response.error);
                }

                return response.data;

            } catch (error) {
                throw new Error(error.message);
            }
        },


        /**
         * Rollback the subscription updates if payment fails
         * @returns {Promise<void>}
         */
        async rollbackSubscription() {

            try {
                const response = await postJson('/business/stripe/rollback-subscription', {
                    payment_uuid: this.payment_uuid,
                });

                if (!response.success) {
                    throw new Error(response.error || 'Rollback subscription failed.');
                }

                return response.data;

            } catch (error) {
                throw new Error(`Error: RollbackSubscription: ${error}`);
            }
        },

        /**
         * Update the subscription
         * @returns {Promise<void>}
         */
        async updateSubscription() {
            try {
                const paymentResponse = await this.processPayment();
                if (paymentResponse?.requires_action) {
                    const confirmation = await this.stripe.confirmCardPayment(paymentResponse.client_secret);
                    if (confirmation.error) {
                        this.paymentError = confirmation.error.message;
                        this.processing = false;
                        await this.rollbackSubscription();
                        return;
                    } else if (confirmation.paymentIntent.status === "succeeded") {
                        window.location.href = '/business/account/payment-success/' + this.payment_uuid;
                    }
                } else if (paymentResponse.success) {
                    window.location.href = '/business/account/payment-success/' + this.payment_uuid;
                } else {
                    this.paymentError = paymentResponse.message;
                    this.processing = false;
                    return;
                }
            } catch (error) {
                this.processing = false;
                this.paymentError = window.paymentMessages.payment_init_error;
                return;
            }
        },

        /**
         * Payment method on change
         * @param {Event} event - The event
         */
        paymentMethodOnChange(event) {
            this.paymentMethod = '';
            this.showPaymentForm = event.target.value === this.addCard;
            if (this.paymentMethodSelectElem.value) {
                this.paymentError = false;
                this.paymentMethodSelectElem.classList.remove("outline-red-500");
                if (this.paymentMethodSelectElem.value != 'new' && this.paymentMethodSelectElem.value != '') {
                    this.paymentMethod = this.paymentMethodSelectElem.value;
                    document.getElementById('payment-method').value = this.paymentMethodSelectElem.value;
                    this.errors.payment_method_error = '';
                }
            }
        },

        /**
         * Validate the payment form
         * @returns {boolean} True if the form is valid, false otherwise
         */
        validatePaymentForm() {
            this.errors = {};
            const validation = validateStripeForm({
                paymentMethodSelectElem: this.paymentMethodSelectElem,
                cardHolderName: this.cardHolderName,
                interval: this.interval,
                addCard: this.addCard
            });

            this.cardError = validation.error || {};
            if (validation?.payment_method_error && validation?.payment_method_error != '') {
                this.errors.payment_method_error = validation?.payment_method_error || '';
            }
            return !validation.error;
        },

        /**
         * Check if there are any errors
         * @returns {boolean} True if there are any errors, false otherwise
         */
        checkErrors() {
            return (Object.keys(this.cardError).length > 0 || Object.keys(this.paymentError).length > 0 || Object.keys(this.errors).length > 0)
        }
    };
}


/**
 * Update the card
 * @returns {Object} The update card form
 */
export function updateCard() {
    return {
        updateCard: true,
        showUpdateCard: false,
        stripe: null,
        elements: null,
        cardNumber: null,
        cardExpiry: null,
        cardCvc: null,
        cardHolderName: this.$refs.cardHolderName?.value || '',
        processing: false,
        paymentMethod: '',
        successMessage: '',
        lastFour: '',
        showConfirm: false,
        showToast: false,
        cardError: {
            error: ''
        },

        /**
         * Initialize the update card form
         */
        init() {

            if (!window.stripeKey) {
                this.stripeError = true;
                return
            }

            this.showUpdateCard = false;
            this.stripe = Stripe(window.stripeKey);

            // Create and mount card elements
            const stripeElements = createStripeElements(this.stripe);
            this.elements = mountStripeElements(stripeElements);

            // Store references to individual elements
            this.cardNumber = stripeElements.cardNumber;
            this.cardExpiry = stripeElements.cardExpiry;
            this.cardCvc = stripeElements.cardCvc;
        },

        /**
         * Toggle the update card form
         */
        toggleUpdateCard: toggleProperty('showUpdateCard'),


        /**
         * Save the card
         * @returns {Promise<void>}
         */
        async saveCard() {

            try {
                this.processing = true;
                this.validatePaymentForm();

                if (Object.keys(this.cardError).length > 0) {
                    this.processing = false;
                    return;
                }

                await this.confirmCardSetup();

            } catch (error) {
                this.processing = false;
                this.cardError.error = window.paymentMessages.update_card_error;
            }
        },

        /**
         * Confirm the card setup
         * @returns {Promise<void>}
         */
        async confirmCardSetup() {
            this.cardError = {};
            try {
                const { paymentMethod, error } = await this.stripe.createPaymentMethod({
                    type: 'card',
                    card: this.cardNumber,
                    billing_details: {
                        name: this.cardHolderName,
                    },
                });

                if (error) {
                    this.cardError.error = window.paymentMessages.payment_init_error;
                } else {

                    this.paymentMethod = paymentMethod.id;
                    document.getElementById('payment-method').value = paymentMethod.id;
                    const response = await this.storeCard();

                    if (response.error || !response.success) {
                        this.$dispatch('card-updated');
                        this.cardError.error = response.message;
                        this.processing = false;
                        this.successMessage = '';
                        return;
                    } else {
                        this.$dispatch('card-updated');
                        this.lastFour = response.pm_last_four;
                        this.showUpdateCard = false;
                        this.successMessage = response.message;
                        setTimeout(() => {
                            window.location.reload();
                        }, 5000);
                    }
                }
            } catch (err) {
                this.processing = false;
                this.cardError.error = window.paymentMessages.update_card_error;
            } finally {
                this.processing = false;
            }
        },

        /**
         * Store the card
         * @returns {Promise<Object>} The response data
         */
        async storeCard() {

            try {
                const response = await postJson('/business/stripe/update-card', {
                    payment_method: this.paymentMethod,
                });

                if (!response.success) {
                    throw new Error(window.paymentMessages.update_card_error);
                }

                return response.data;

            } catch (error) {
                throw new Error(window.paymentMessages.update_card_error);
            }

        },

        /**
         * Validate the payment form
         * @returns {boolean} True if the form is valid, false otherwise
         */
        validatePaymentForm() {
            const validation = validateStripeForm({
                cardHolderName: this.cardHolderName,
                updateCard: this.updateCard
            });

            this.cardError = validation.error || {};
            return !validation.error;
        }
    }
}


/**
 * Downgrade plan
 * @returns {Object} The downgrade plan form
 */
export function processDowngrade() {
    return {
        downgrade: true,
        processing: false,
        paymentError: false,
        clientSecret: false,

        actionDowngrade() {
            this.processing = true;

            setTimeout(() => {
                this.$el.submit();
            }, 5000);
        }
    }
}
