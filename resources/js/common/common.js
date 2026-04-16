/**
 * Toggle a property on an object
 * @param {string} prop - The property to toggle
 * @returns {function} A function that toggles the property
 */
export function toggleProperty(prop) {
    return function () {
        this[prop] = !this[prop];
    }
}

/**
 * Create Stripe elements
 * @param {Object} stripe - The Stripe instance
 * @returns {Object} An object containing the Stripe elements
 */
export function createStripeElements(stripe) {
    const elementStyles = {
        base: {
            color: '#6F6F6F',
        },
        invalid: {
            color: '#E25950',
            "::placeholder": {
                color: '#FFCCA5',
            },
        },
    };

    const elements = stripe.elements();

    return {
        cardNumber: elements.create('cardNumber', { style: elementStyles }),
        cardExpiry: elements.create('cardExpiry', { style: elementStyles }),
        cardCvc: elements.create('cardCvc', { style: elementStyles })
    };
}

/**
 * Mount Stripe elements
 * @param {Object} elements - The Stripe elements
 * @returns {Object} The mounted elements
 */
export function mountStripeElements(elements) {
    elements.cardNumber.mount('#card-number');
    elements.cardExpiry.mount('#card-expiry');
    elements.cardCvc.mount('#card-cvc');
    return elements;
}

/**
 * Validate Stripe form
 * @param {Object} options - The options
 * @returns {Object} The errors
 */
export function validateStripeForm(options = {}) {
    const {
        paymentMethodSelectElem = null,
        cardHolderName = '',
        interval = null,
        addCard = 'add-card',
        updateCard = false
    } = options;

    const errors = {};

    // Clear previous visual indicators
    const elements = {
        cardNumber: document.getElementById('card-number'),
        cardCvc: document.getElementById('card-cvc'),
        cardExpiry: document.getElementById('card-expiry'),
        cardHolderName: document.getElementById('card-holder-name')
    };

    // Reset all outline styles
    Object.values(elements).forEach(elem => {
        if (elem) elem.classList.remove("error-message-border");
    });

    // If payment method select exists, validate it
    if (paymentMethodSelectElem) {
        if (!paymentMethodSelectElem.value) {
            paymentMethodSelectElem.classList.add("error-message-border");
            return { payment_method_error: window.paymentMessages.payment_method_required, error: true };
        }

        // If not adding new card, return no errors
        if (paymentMethodSelectElem.value !== addCard) {
            return { error: null };
        }
    }

    // Validate card fields
    const validations = {
        cardNumber: {
            element: elements.cardNumber,
            message: window.paymentMessages.required
        },
        cardCvc: {
            element: elements.cardCvc,
            message: window.paymentMessages.required
        },
        cardExpiry: {
            element: elements.cardExpiry,
            message: window.paymentMessages.required
        }
    };

    // Check each Stripe element
    Object.entries(validations).forEach(([key, validation]) => {
        const elem = validation.element;
        if (elem) {
            const isEmpty = elem.classList.contains("StripeElement--empty");
            const isInvalid = elem.classList.contains("StripeElement--invalid");

            if (isEmpty || isInvalid) {
                errors[key] = validation.message;
                elem.classList.add("error-message-border");
            }

            if (key === 'cardNumber' && isInvalid) {
                errors[key] = window.paymentMessages.invalid_card_number;
            }

            if (key === 'cardCvc' && isInvalid) {
                errors[key] = window.paymentMessages.invalid_cvv;
            }

            if (key === 'cardExpiry' && isInvalid) {
                errors[key] = window.paymentMessages.invalid_card_expiry;
            }
        }
    });

    // Validate cardholder name
    if (elements.cardHolderName) {
        cardHolderNameValidation(cardHolderName, elements, errors);
    }

    // Validate billing cycle if required
    if (interval === null && !updateCard) {
        errors.billing_cycle = window.paymentMessages.billing_cycle_required;
    }

    return { error: Object.keys(errors).length > 0 ? errors : null };
}

/**
 * Parse JSON response
 * @param {Object} response - The response
 * @returns {Object} The parsed response
 */
export async function parseJson(response) {
    const text = await response.text();
    try {
        return JSON.parse(text || '{}');
    } catch (e) {
        console.warn('Invalid JSON response:', text);
        return {};
    }
}

/**
 * Post JSON request
 * @param {string} url - The URL
 * @param {Object} payload - The payload
 * @param {Object} options - The options
 * @returns {Object} The response
 */
export async function postJson(url, payload, options = {}) {
    const {
        timeout = 30000,
        headers = {}
    } = options;

    const controller = new AbortController();
    const timeoutId = setTimeout(() => {
        controller.abort('Request timed out');
    }, timeout);

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                ...headers,
            },
            body: JSON.stringify(payload),
            signal: controller.signal
        });

        clearTimeout(timeoutId);

        const responseData = await parseJson(response);

        if (!response.ok) {
            const errorMessage = responseData?.message || `HTTP error ${response.status}`;
            throw new Error(errorMessage);
        }

        return {
            success: true,
            data: responseData,
        };

    } catch (error) {
        clearTimeout(timeoutId);
        return {
            success: false,
            data: null,
            error: error.message || 'Unknown error occurred'
        };
    } finally {
        clearTimeout(timeoutId);
    }
}


/**
 * Clear form
 * @param {Object} form - The form
 * @returns {void}
 */
export function resetForm(form) {
    window.addEventListener('pageshow', (event) => {
        if (event.persisted || window.performance.getEntriesByType("navigation")[0].type === "back_forward") {
            form.reset();
        }
    });
}

/**
 * Validate only numbers
 * @param {string} number - The number
 * @returns {boolean} The validation result
 */
export function isOnlyNumbers(number) {
    const numberRegex = /^\d+$/; // Matches only digits (at least one)
    return numberRegex.test(number);
}

function cardHolderNameValidation(cardHolderName, elements, errors) {

    if (!cardHolderName.trim()) {
        errors.cardHolderName = window.validation.required;
    } else if (!/^[a-zA-Z\s]+$/.test(cardHolderName.trim())) {
        errors.cardHolderName = window.validation.only_alphabets;
    } else if (cardHolderName.trim().length < 2) {
        errors.cardHolderName = window.validation.min_2;
    } else if (cardHolderName.length > 50) {
        errors.cardHolderName = window.validation.max_50;
    }

    if (errors.cardHolderName) {
        elements.cardHolderName.classList.add("error-message-border");
    }
}


/**
 * Encrypt password
 * @param {string} password - The password
 * @returns {string} The encrypted password
 */
export function encryptPassword(password) {
    const XOR_KEY = 12;
    const encoder = new TextEncoder();
    const bytes = encoder.encode(password);
    const out = new Uint8Array(bytes.length);

    for (let i = 0; i < bytes.length; i++) {
        out[i] = bytes[i] ^ XOR_KEY;
    }
    // Convert bytes → base64
    let binary = '';
    out.forEach(b => binary += String.fromCharCode(b));

    return btoa(binary);
}