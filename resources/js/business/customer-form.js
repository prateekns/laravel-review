// Constants for validation
const VALIDATION_RULES = {
    NAME_REGEX: /^[a-zA-Z\s'-]+$/,
    EMAIL_REGEX: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
    PHONE_REGEX: /^(\d{3}\s\d{3}\s\d{4}|\d{10})$/,
    ZIP_REGEX: /^[a-zA-Z0-9\s\-.]+$/,
    EQUIPMENT_REGEX: /^[a-zA-Z0-9\s'-]+$/,
    MAX_POOL_SIZE: 999999,
    MIN_POOL_SIZE: 100,
    MAX_POOL_LENGTH: 999,
    MAX_POOL_DEPTH: 50,
    MIN_DIMENSION: 1,
    NAME_MIN_LENGTH: 1,
    NAME_MAX_LENGTH: 50,
    MAX_FILE_SIZE: 3 * 1024 * 1024, // 3MB in bytes
    MIN_EQUIPMENT_LENGTH: 1,
    MAX_EQUIPMENT_LENGTH: 100,
    MIN_NOTES_LENGTH: 1,
    MAX_NOTES_LENGTH: 500,
    MAX_COMMERCIAL_DETAILS_LENGTH: 200,
    ZIP_MIN_LENGTH: 3,
    ZIP_MAX_LENGTH: 12,
};

const ALLOWED_IMAGE_TYPES = [
    "image/jpeg",
    "image/jpg",
    "image/png",
    "image/webp",
];

// Add validation messages
const validation = {
    ...(window?.validation ?? {}),
    // Basic field validation messages
    required: "This field is required.",
    decimal_places: "Only up to 2 decimal places are allowed.",
    invalid_email: "Please enter a valid email address",
    invalid_phone: "Invalid phone number format.",
    invalid_zipcode: "Please enter a valid ZIP Code.",

    // Pool size validation messages
    pool_size_integer: "Only whole numbers are allowed.",
    pool_size_min: "Minimum pool size is 100 gallons.",
    pool_size_max: "Maximum pool size is 9,99,999 gallons.",
    pool_length_min: "Pool length must be at least 1 foot.",
    pool_width_min: "Pool width must be at least 1 foot.",
    pool_depth_min: "Pool depth must be at least 1 foot.",
    pool_length_max: "Pool length cannot exceed 999 feet.",
    pool_width_max: "Pool width cannot exceed 999 feet.",
    pool_depth_max: "Pool depth cannot exceed 50 feet.",

    // Equipment details validation messages
    equipment_details_max: "Character limit exceeded (100 characters max).",
    special_chars_not_allowed: "Special characters are not allowed.",
    image_type: "Only image files (jpg, jpeg, png, webp) are allowed.",
    image_size: "File size exceeds the allowed limit.",

    // Notes fields validation messages
    tech_notes_max: "Character limit exceeded (500 characters max).",
    admin_notes_max: "Character limit exceeded (500 characters max).",
    entry_instruction_max: "Character limit exceeded (500 characters max).",
    commercial_pool_details_max: "Character limit exceeded.",
};

// Make validation available globally if needed
window.validation ??= validation;

// Helper to format dynamic min-max character message using translations if available
function formatMinMaxMessage(min, max) {
    const template = window?.validation?.min_max_characters;
    if (typeof template === "string") {
        return template
            .replace("{min}", String(min))
            .replace("{max}", String(max));
    }
    return `Character limit must be between ${min} and ${max} characters.`;
}

// Component for handling image uploads and previews
window.Alpine?.data("customerImageUpload", () => ({
    file: null,
    dragover: false,
    fileInput: null,
    errorMessage: "",
    existingPhoto: false,
    existingPhotoUrl: "",
    existingPhotoThumbUrl: "",
    existingPhotoName: "",
    shouldDeletePhoto: false,
    maxSize: 3 * 1024 * 1024, // 3MB in bytes
    allowedTypes: ALLOWED_IMAGE_TYPES,
    fieldName: "",
    deleteFieldName: "",
    thumbFieldName: "",
    init() {
        this.fieldName = this.$el.dataset.field;
        this.thumbFieldName = this.$el.dataset.thumbField;
        this.deleteFieldName = `delete_${this.fieldName}`;
        this.fileInput = this.$refs[this.fieldName];

        // Initialize existing photo if available
        if (window.oldCustomer?.[this.fieldName]) {
            this.existingPhoto = true;
            this.existingPhotoUrl = window.oldCustomer[this.fieldName];
            this.existingPhotoThumbUrl =
                window.oldCustomer[this.thumbFieldName];
            this.existingPhotoName = window.oldCustomer[this.fieldName]
                .split("/")
                .pop();
        }

        // Check if there's a pending delete operation
        if (window.oldInput?.[this.deleteFieldName] === "1") {
            this.shouldDeletePhoto = true;
            this.existingPhoto = false;
        }
    },

    handleFileSelect(event) {
        const files = event.target.files || event.dataTransfer.files;
        if (!files.length) return;

        this.validateAndSetFile(files[0]);
    },

    validateAndSetFile(selectedFile) {
        // Reset error
        this.errorMessage = "";

        // Validate file type
        if (!this.allowedTypes.includes(selectedFile.type)) {
            this.errorMessage = validation.image_type;
            if (this.fileInput) this.fileInput.value = "";
            return;
        }

        // Validate file size
        if (selectedFile.size > this.maxSize) {
            this.errorMessage = validation.image_size;
            if (this.fileInput) this.fileInput.value = "";
            return;
        }

        // Create preview URL
        const reader = new FileReader();
        reader.onload = (e) => {
            this.file = {
                file: selectedFile,
                preview: e.target.result,
                name: selectedFile.name,
            };
            this.existingPhoto = false;
            this.shouldDeletePhoto = false;
        };
        reader.readAsDataURL(selectedFile);
    },

    removeFile() {
        this.file = null;
        this.errorMessage = "";
        this.existingPhoto = false;
        if (this.fileInput) this.fileInput.value = "";
    },

    markPhotoForDeletion() {
        this.shouldDeletePhoto = true;
        this.file = null;
        this.existingPhoto = false;
        if (this.fileInput) this.fileInput.value = "";

        // Find the form
        const form = this.$el.closest("form");
        if (!form) {
            return;
        }

        // Find or create delete input
        let deleteInput = form.querySelector(
            `[name="${this.deleteFieldName}"]`
        );
        if (!deleteInput) {
            deleteInput = document.createElement("input");
            deleteInput.type = "hidden";
            deleteInput.name = this.deleteFieldName;
            form.appendChild(deleteInput);
        }
        deleteInput.value = "true";

        // Clear the file input
        if (this.fileInput) {
            this.fileInput.value = "";
        }
    },

    formatFileSize(bytes) {
        if (bytes === 0) return "0 Bytes";
        const k = 1024;
        const sizes = ["Bytes", "KB", "MB"];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + " " + sizes[i];
    },

    handleDragOver(event) {
        event.preventDefault();
        this.dragover = true;
    },

    handleDragLeave(event) {
        event.preventDefault();
        this.dragover = false;
    },

    handleDrop(event) {
        event.preventDefault();
        this.dragover = false;
        this.handleFileSelect(event);
    },
}));

document.addEventListener("alpine:init", () => {
    Alpine.data("customerForm", () => ({
        // Initialize form fields with empty strings
        pool_type: "1", // Default to Residential (1=Residential, 2=Commercial)
        country_id: "",
        action: "save", // Default action
        formData: null, // Store form data
        processing: false, // Loading state for form submission
        ...Object.fromEntries(
            [
                "first_name",
                "last_name",
                "email_1",
                "email_2",
                "phone_1",
                "phone_2",
                "commercial_pool_details",
                "address",
                "street",
                "zip_code",
                "country_id",
                "state",
                "city",
                "commercial_pool_details",
                "pool_size_gallons",
                "pool_length",
                "pool_width",
                "pool_depth",
                "filter_details",
                "pump_details",
                "cleaner_details",
                "heater_details",
                "heat_pump_details",
                "aux_details",
                "aux2_details",
                "salt_system_details",
                "entry_instruction",
                "tech_notes",
                "admin_notes",
            ].map((field) => [field, ""])
        ),
        showConfirm: false,
        previousStatus: null,
        status: null,
        errors: {},
        formError: false,
        clean_psi: oldInput?.clean_psi || oldCustomer?.clean_psi || "",

        initializeField(fieldName) {
            // First check old input data, then customer data, then ref value
            const oldValue = window.oldInput?.[fieldName];
            const customerValue = window.oldCustomer?.[fieldName];
            const refValue = this.$refs[fieldName]?.value;

            if (fieldName === "country_id" && window.businessCountry) {
                return window.businessCountry?.["id"];
            }

            return oldValue ?? customerValue ?? refValue ?? "";
        },

        initializeContactFields() {
            this.first_name = this.initializeField("first_name");
            this.last_name = this.initializeField("last_name");
            this.email_1 = this.initializeField("email_1");
            this.email_2 = this.initializeField("email_2");
            this.phone_1 = this.initializeField("phone_1");
            this.phone_2 = this.initializeField("phone_2");
            this.commercial_pool_details = this.initializeField(
                "commercial_pool_details"
            );
        },

        async initializeAddressFields() {
            this.address = this.initializeField("address");
            this.street = this.initializeField("street");
            this.zip_code = this.initializeField("zip_code");

            // Store the initial values
            this.city = this.initializeField('city');
            this.state = this.initializeField('state');

            const initialCountryId = this.initializeField("country_id") || "";

            // Set country_id first
            this.country_id = initialCountryId;
        },

        async handleCountryChange() {
            if (!this.country_id) {
                return;
            }

            // Get the selected country's ISD code and update the input fields
            const selectedOption = document.querySelector(
                `#country option[value="${this.country_id}"]`
            );
            if (selectedOption) {
                const isdCode = selectedOption.dataset.isdCode;
                document
                    .querySelectorAll('input[name="isd_code"]')
                    .forEach((input) => {
                        input.value = isdCode;
                    });
            }
        },

        initializePoolFields() {
            this.commercial_pool_details = this.initializeField(
                "commercial_pool_details"
            );
            this.pool_size_gallons = this.initializeField("pool_size_gallons");
            this.pool_length = this.initializeField("pool_length");
            this.pool_width = this.initializeField("pool_width");
            this.pool_depth = this.initializeField("pool_depth");
        },

        initializeEquipmentFields() {
            this.filter_details = this.initializeField("filter_details");
            this.pump_details = this.initializeField("pump_details");
            this.cleaner_details = this.initializeField("cleaner_details");
            this.heater_details = this.initializeField("heater_details");
            this.heat_pump_details = this.initializeField("heat_pump_details");
            this.aux_details = this.initializeField("aux_details");
            this.aux2_details = this.initializeField("aux2_details");
            this.salt_system_details = this.initializeField(
                "salt_system_details"
            );
        },

        initializeNotesFields() {
            this.entry_instruction = this.initializeField("entry_instruction");
            this.tech_notes = this.initializeField("tech_notes");
            this.admin_notes = this.initializeField("admin_notes");
        },

        initializeStatus() {
            if (window.oldCustomer) {
                this.status = Number(window.oldCustomer.status);
                this.previousStatus = Number(window.oldCustomer.status);
            } else {
                this.status = 1;
                this.previousStatus = 1;
            }
        },

        // Initialize the form
        async init() {
            this.formData = new FormData(this.$refs.form);

            // Initialize pool type from existing data if in edit mode
            if (window.oldCustomer?.pool_type) {
                this.pool_type = window.oldCustomer.pool_type.toString();
            }

            this.initializeContactFields();
            await this.initializeAddressFields(); // Wait for address fields to initialize
            this.initializePoolFields();
            this.initializeEquipmentFields();
            this.initializeNotesFields();
            this.initializeStatus();

            // Initialize validation rules based on pool type
            this.handlePoolTypeChange();
        },

        handleStatusChange() {
            // If changing from active to inactive, show confirmation
            if (this.previousStatus === 1 && this.status === 0) {
                this.showConfirm = true;
            } else {
                // For any other change, just update the previous status
                this.previousStatus = this.status;
            }
        },

        handleCancel() {
            this.showConfirm = false;
            this.status = this.previousStatus;
        },

        handleConfirm() {
            this.showConfirm = false;
            this.previousStatus = this.status;
        },

        // Method to handle form submission with action
        submitWithAction(action) {
            try {
                // Set the action
                this.action = action;

                // Update hidden input
                if (this.$refs.action) {
                    this.$refs.action.value = action;
                }

                // Validate form
                if (!this.validateForm()) {
                    this.formError = true;
                    this.scrollHandler();
                    return;
                }

                // Get the form element
                const form = this.$refs.form;
                if (!form) {
                    return;
                }

                // Update form data
                this.formData = new FormData(form);

                // Set loading and submit on next tick so overlay can render
                this.processing = true;
                requestAnimationFrame(() => {
                    form.submit();
                });
            } catch (error) {
                console.error("Error in submitWithAction:", error);
                this.processing = false;
            }
        },

        submitForm(event) {
            if (!this.validateForm()) {
                event.preventDefault();
                this.formError = true;
                this.scrollHandler();
                return;
            }

            // Set loading state
            this.processing = true;

            // Update form data before submission
            this.formData = new FormData(this.$refs.form);
        },

        calculatePoolSize() {
            try {
                // Convert values to numbers, defaulting to 0 if invalid
                const length = parseFloat(this.pool_length || 0);
                const width = parseFloat(this.pool_width || 0);
                const depth = parseFloat(this.pool_depth || 0);

                // Only calculate if all dimensions are present
                if (length && width && depth) {
                    this.pool_size_gallons = parseFloat(
                        (length * width * depth * 7.5).toFixed(2)
                    );
                } else {
                    this.pool_size_gallons = "";
                }
            } catch (error) {
                console.error("Error calculating pool size:", error);
                this.pool_size_gallons = "";
            }
        },

        handleImageUpload(event, field) {
            const file = event.target?.files?.[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                this[`${field}Preview`] = e.target?.result ?? "";
            };
            reader.readAsDataURL(file);
        },

        validateRequired(field) {
            const value = this[field];
            if (value === null || value === undefined || value === "") {
                return validation.required;
            }
            if (typeof value === "string" && !value.trim()) {
                return validation.required;
            }
            return false;
        },

        validateName(value) {
            return value?.trim() &&
                !VALIDATION_RULES.NAME_REGEX.test(value.trim())
                ? validation.only_alphabets
                : "";
        },

        validateEmail(value) {
            // Return empty string if value is empty or undefined (for optional emails)
            if (!value || value.trim() === "") {
                return "";
            }
            // Test the email format
            return !VALIDATION_RULES.EMAIL_REGEX.test(value.trim())
                ? validation.invalid_email
                : "";
        },

        validatePhone(value) {
            // Return empty string if value is empty or undefined (for optional phones)
            if (!value || value.trim() === "") {
                return "";
            }
            // Test the phone format
            return !VALIDATION_RULES.PHONE_REGEX.test(value.trim())
                ? validation.invalid_phone
                : "";
        },

        validateZipCode(value) {
            // Return empty string if value is empty or undefined
            if (!value || value.trim() === "") {
                return "";
            }

            const trimmedValue = value.trim();

            // Check length (3-12 characters)
            if (
                trimmedValue.length < VALIDATION_RULES.ZIP_MIN_LENGTH ||
                trimmedValue.length > VALIDATION_RULES.ZIP_MAX_LENGTH
            ) {
                return formatMinMaxMessage(
                    VALIDATION_RULES.ZIP_MIN_LENGTH,
                    VALIDATION_RULES.ZIP_MAX_LENGTH
                );
            }

            // Check for allowed characters (letters, numbers, spaces, hyphens, periods)
            return !VALIDATION_RULES.ZIP_REGEX.test(trimmedValue)
                ? validation.invalid_zipcode
                : "";
        },

        validateLength(field, minLength, maxLength) {
            if (
                this[field] &&
                (this[field]?.length < minLength ||
                    this[field]?.length > maxLength)
            ) {
                this.errors[field] = formatMinMaxMessage(minLength, maxLength);
            }
        },

        validateRequiredFields() {
            const requiredFields = [
                "first_name",
                "last_name",
                "email_1",
                "phone_1",
                "address",
                "zip_code",
                "country_id",
                "state",
                "city",
            ];

            // Validate all required fields
            requiredFields.forEach((field) => {
                const error = this.validateRequired(field);
                if (error) this.errors[field] = error;
            });

            // Add zip code validation with trimming
            if (this.zip_code) {
                this.zip_code = this.zip_code.trim();
            }
            const zipError = this.validateZipCode(this.zip_code);
            if (zipError) this.errors.zip_code = zipError;
        },

        validateNameFields() {
            const nameError1 = this.validateName(this.first_name);
            if (nameError1) this.errors.first_name = nameError1;

            const nameError2 = this.validateName(this.last_name);
            if (nameError2) this.errors.last_name = nameError2;

            // Enforce min/max when provided
            if (!this.errors.first_name && this.first_name) {
                const len1 = this.first_name.length;
                if (
                    len1 < VALIDATION_RULES.NAME_MIN_LENGTH ||
                    len1 > VALIDATION_RULES.NAME_MAX_LENGTH
                ) {
                    this.errors.first_name = formatMinMaxMessage(
                        VALIDATION_RULES.NAME_MIN_LENGTH,
                        VALIDATION_RULES.NAME_MAX_LENGTH
                    );
                }
            }
            if (!this.errors.last_name && this.last_name) {
                const len2 = this.last_name.length;
                if (
                    len2 < VALIDATION_RULES.NAME_MIN_LENGTH ||
                    len2 > VALIDATION_RULES.NAME_MAX_LENGTH
                ) {
                    this.errors.last_name = formatMinMaxMessage(
                        VALIDATION_RULES.NAME_MIN_LENGTH,
                        VALIDATION_RULES.NAME_MAX_LENGTH
                    );
                }
            }
        },

        validateEmailFields() {
            // Email 1 is required, so check for required first
            if (!this.email_1?.trim()) {
                this.errors.email_1 = validation.required;
            } else {
                const emailError1 = this.validateEmail(this.email_1);
                if (emailError1) this.errors.email_1 = emailError1;
            }

            // Email 2 is optional, only validate format if provided
            const emailError2 = this.validateEmail(this.email_2);
            if (emailError2) this.errors.email_2 = emailError2;
        },

        validatePhoneFields() {
            // Phone 1 is required, so check for required first
            if (!this.phone_1?.trim()) {
                this.errors.phone_1 = validation.required;
            } else {
                const phoneError1 = this.validatePhone(this.phone_1);
                if (phoneError1) this.errors.phone_1 = phoneError1;
            }

            // Phone 2 is optional, only validate format if provided
            const phoneError2 = this.validatePhone(this.phone_2);
            if (phoneError2) this.errors.phone_2 = phoneError2;
        },

        validatePoolDimensions(value, field) {
            try {
                // Handle empty or undefined values (but allow 0)
                if (value === null || value === undefined || value === "") {
                    return "";
                }

                // Convert to number and validate
                const numValue = parseFloat(value);

                // Check if it's a valid number
                if (isNaN(numValue)) {
                    return validation.pool_size_integer;
                }

                // Check if it has more than 2 decimal places
                if ((numValue.toString().split(".")[1] || "").length > 2) {
                    return validation.decimal_places;
                }

                // Check minimum value
                if (numValue < VALIDATION_RULES.MIN_DIMENSION) {
                    return validation[`${field}_min`];
                }

                // Check maximum values
                const maxValues = {
                    pool_length: VALIDATION_RULES.MAX_POOL_LENGTH,
                    pool_width: VALIDATION_RULES.MAX_POOL_LENGTH,
                    pool_depth: VALIDATION_RULES.MAX_POOL_DEPTH,
                };

                if (numValue > maxValues[field]) {
                    return validation[`${field}_max`];
                }

                return "";
            } catch (error) {
                console.error(`Error validating ${field}:`, error);
                return validation[`${field}_integer`];
            }
        },

        validatePool() {
            const { pool_length, pool_width, pool_depth } = {
                pool_length: this.pool_length,
                pool_width: this.pool_width,
                pool_depth: this.pool_depth,
            };

            // Check if at least one is filled
            const anyFilled = [pool_length, pool_width, pool_depth].some(
                (v) => v !== "" && v !== null && v !== undefined
            );

            // If one is filled, all must be filled
            if (anyFilled) {
                if (!pool_length) this.errors.pool_length = validation.required;
                if (!pool_width) this.errors.pool_width = validation.required;
                if (!pool_depth) this.errors.pool_depth = validation.required;

                return true;
            }

            return false;
        },

        validatePoolSize(value) {
            try {
                // Handle empty or undefined values
                if (!value || value === '') {
                    return '';
                }

                // Convert to number and validate
                const numValue = parseFloat(value);

                // Check if it's a valid number
                if (isNaN(numValue)) {
                    return validation.pool_size_integer;
                }

                // Check minimum value
                if (numValue < VALIDATION_RULES.MIN_POOL_SIZE) {
                    return validation.pool_size_min;
                }

                // Check maximum value
                if (numValue > VALIDATION_RULES.MAX_POOL_SIZE) {
                    return validation.pool_size_max;
                }

                // Check if it has more than 2 decimal places
                if ((numValue.toString().split(".")[1] || "").length > 2) {
                    return validation.decimal_places;
                }

                return "";
            } catch (error) {
                return validation.pool_size_integer;
            }
        },

        validateMaxLength(value, maxLength, field) {
            return value?.length > maxLength ? validation[`${field}_max`] : "";
        },

        validateEquipmentDetails(value) {
            if (!value?.trim()) return "";

            const len = value.length;
            if (
                len < VALIDATION_RULES.MIN_EQUIPMENT_LENGTH ||
                len > VALIDATION_RULES.MAX_EQUIPMENT_LENGTH
            ) {
                return formatMinMaxMessage(
                    VALIDATION_RULES.MIN_EQUIPMENT_LENGTH,
                    VALIDATION_RULES.MAX_EQUIPMENT_LENGTH
                );
            }

            return !VALIDATION_RULES.EQUIPMENT_REGEX.test(value)
                ? validation.special_chars_not_allowed
                : "";
        },

        validateImageFile(file) {
            if (!file) return "";

            if (!ALLOWED_IMAGE_TYPES.includes(file.type)) {
                return validation.image_type;
            }

            if (file.size > VALIDATION_RULES.MAX_FILE_SIZE) {
                return validation.image_size;
            }

            return "";
        },

        validatePoolFields() {
            // Pool dimensions validation
            ["pool_length", "pool_width", "pool_depth"].forEach((field) => {
                const error = this.validatePoolDimensions(this[field], field);
                if (error) this.errors[field] = error;
            });

            // Pool size validation
            const poolSizeError = this.validatePoolSize(this.pool_size_gallons);
            if (poolSizeError) this.errors.pool_size_gallons = poolSizeError;
        },

        validateNoteFields() {
            // Dynamic min-max messages for note fields
            this.validateLength(
                "tech_notes",
                VALIDATION_RULES.MIN_NOTES_LENGTH,
                VALIDATION_RULES.MAX_NOTES_LENGTH
            );
            this.validateLength(
                "admin_notes",
                VALIDATION_RULES.MIN_NOTES_LENGTH,
                VALIDATION_RULES.MAX_NOTES_LENGTH
            );
            this.validateLength(
                "entry_instruction",
                VALIDATION_RULES.MIN_NOTES_LENGTH,
                VALIDATION_RULES.MAX_NOTES_LENGTH
            );
            this.validateLength(
                "commercial_pool_details",
                VALIDATION_RULES.MIN_NOTES_LENGTH,
                VALIDATION_RULES.MAX_COMMERCIAL_DETAILS_LENGTH
            );
        },

        validateEquipmentFields() {
            const equipmentFields = [
                "filter_details",
                "pump_details",
                "cleaner_details",
                "heater_details",
                "heat_pump_details",
                "aux_details",
                "aux2_details",
                "salt_system_details",
                "clean_psi",
            ];

            equipmentFields.forEach((field) => {
                const error = this.validateEquipmentDetails(this[field]);
                if (error) this.errors[field] = error;
            });
        },

        validateImageFields() {
            const imageFields = [
                "filter_image",
                "pump_image",
                "cleaner_image",
                "heater_image",
                "heat_pump_image",
                "aux_image",
                "aux2_image",
                "salt_system_image",
            ];

            imageFields.forEach((field) => {
                const file = this.$refs[field]?.files?.[0];
                if (file) {
                    const error = this.validateImageFile(file);
                    if (error) this.errors[field] = error;
                }
            });
        },

        // Check if there are any validation errors
        hasErrors() {
            return Object.keys(this.errors).length > 0;
        },

        validateForm() {
            this.errors = {};

            // Basic field validations
            this.validateRequiredFields();
            this.validateLength("address", 3, 200);
            this.validateLength("street", 3, 200);
            this.validateNameFields();
            this.validateEmailFields();
            this.validatePhoneFields();

            // Additional field validations
            this.validatePoolFields();
            this.validateNoteFields();
            this.validateEquipmentFields();
            this.validateImageFields();

            // Debug: Log errors to console
            if (this.hasErrors()) {
                this.$nextTick(() => this.scrollToError());
            }

            return Object.keys(this.errors).length === 0;
        },

        scrollHandler() {
            window.scrollTo({ top: 0, behavior: "smooth" });
        },

        // Scroll to first error field
        scrollToError() {
            const firstErrorField = this.getFirstErrorField();
            if (!firstErrorField) return;

            const errorBox = this.getErrorBox(firstErrorField);
            if (!errorBox) return;

            this.ensureAccordionOpen(errorBox);

            // Wait a bit for the accordion to open
            setTimeout(() => {
                const scrollPosition = this.calculateScrollPosition(errorBox);
                window.scrollTo({
                    top: scrollPosition,
                    behavior: "smooth",
                });
                this.focusErrorField(firstErrorField);
            }, 100);
        },

        // Get the first error field
        getFirstErrorField() {
            return Object.keys(this.errors)[0];
        },

        // Get error box element
        getErrorBox(fieldName) {
            return document.querySelector(`.error-message-box.${fieldName}`);
        },

        // Ensure accordion is open
        ensureAccordionOpen(errorBox) {
            const accordionSection = errorBox.closest(".white-box");
            if (accordionSection) {
                this.open = true;
            }
        },

        // Calculate scroll position
        calculateScrollPosition(errorBox) {
            const yOffset = -100; // Add some padding from the top
            return (
                errorBox.getBoundingClientRect().top + window.scrollY + yOffset
            );
        },

        // Focus error field
        focusErrorField(fieldName) {
            const input = document.querySelector(`[name="${fieldName}"]`);
            if (input) {
                input.focus();
            }
        },

        validateCommercialFields() {
            const requiredFields = [
                "email_1",
                "phone_1",
                "address",
                "zip_code",
                "country_id",
                "state",
                "city",
            ];
            this.validateCommonFields(requiredFields);

            // Validate name fields if provided (optional for commercial)
            this.validateOptionalNameFields();

            // Commercial name is required
            if (!this.commercial_pool_details?.trim()) {
                this.errors.commercial_pool_details = validation.required;
            }
        },

        validateResidentialFields() {
            const requiredFields = [
                "first_name",
                "last_name",
                "email_1",
                "phone_1",
                "address",
                "zip_code",
                "country_id",
                "state",
                "city",
            ];
            this.validateCommonFields(requiredFields);
        },

        validateCommonFields(requiredFields) {
            // Validate required fields
            requiredFields.forEach((field) => {
                const error = this.validateRequired(field);
                if (error) this.errors[field] = error;
            });

            // Validate zip code
            this.validateZipCodeField();
        },

        validateZipCodeField() {
            if (this.zip_code) {
                this.zip_code = this.zip_code.trim();
            }
            const zipError = this.validateZipCode(this.zip_code);
            if (zipError) this.errors.zip_code = zipError;
        },

        validateOptionalNameFields() {
            if (this.first_name?.trim()) {
                const nameError1 = this.validateName(this.first_name);
                if (nameError1) this.errors.first_name = nameError1;
            }
            if (this.last_name?.trim()) {
                const nameError2 = this.validateName(this.last_name);
                if (nameError2) this.errors.last_name = nameError2;
            }
        },

        handlePoolTypeChange() {
            // Set validation function based on pool type
            this.validateRequiredFields =
                this.pool_type == 2
                    ? this.validateCommercialFields
                    : this.validateResidentialFields;
        },
    }));
});
