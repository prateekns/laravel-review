// Constants for validation
const VALIDATION_RULES = {
    NAME_MAX_LENGTH: 150,
    DESCRIPTION_MAX_LENGTH: 1200,
    ADDITIONAL_TASK_MAX_LENGTH: 255
};

// Add validation messages
const validation = {
    ...(window?.validation ?? {}),
    required: 'This field is required.',
    name_max: 'Character limit exceeded.',
    description_max: 'Character limit exceeded.',
    additional_task_max: 'Character limit exceeded.',
    start_date_required: 'This field is required.',
    start_time_required: 'This field is required.',
    start_time_increment: 'Time must be in 15-minute increments (e.g., 09:00, 09:15, 09:30, 09:45).',
    start_date_future: 'Please select the future date from today.',
    end_date_after_start: 'End Date must be later than Preferred Start Date.',
    customer_required: 'This field is required.',
    template_required: 'This field is required.',
    inactive_confirm: 'Are you sure you want to mark this work order as inactive?',
    duplicate_task: 'Task already added',
    select_checklist_items_required: 'Please add or select minimum one checklist point.',
};

// Make validation available globally if needed
window.validation ??= validation;

// Ensure Alpine.js is available
if (typeof window.Alpine === 'undefined') {
    console.error('Alpine.js is not loaded. Please ensure it is loaded before this script.');
}

// Register Alpine.js components
window.Alpine?.data('workOrderForm', () => ({
    // Form state
    customerType: 'existing',
    selectedCustomerId: null,
    selectedTemplateId: null,
    selectedCustomer: null,
    selectedTemplate: null,
    open: true,
    isCustomerFixed: false,
    jobName: '',
    jobDescription: '',
    additionalTask: '',
    preferredStartDate: document.getElementById('preferred_start_date')?.value || '',
    preferredStartTime: document.getElementById('preferred_start_time')?.value || '',
    endDate: '',
    minAllowedDate: '',
    maxAllowedDate: '',
    errors: {},
    isSubmitting: false,
    action: 'save',
    showConfirm: false,
    showUpdateConfirm: false,
    previousStatus: null,
    isActive: true,
    
    // Recurring service state
    isRecurring: false,
    frequency: 'weekly',
    repeatAfter: 1,
    selectedDays: ['wednesday', 'friday', 'saturday'],
    monthlyDayType: 'first',
    monthlyDayOfWeek: 'monday',

    // Format time for display
    formatTimeForDisplay(time) {
        if (!time) return '';
        
        try {
            // Parse the time string
            const [hours, minutes] = time.split(':');
            const date = new Date();
            date.setHours(parseInt(hours), parseInt(minutes), 0, 0);
            
            // Format in 12-hour format
            return date.toLocaleTimeString('en-US', { 
                hour: 'numeric',
                minute: '2-digit',
                hour12: true 
            });
        } catch (e) {
            return time;
        }
    },

    // Format time for input
    formatTimeForInput(time) {
        if (!time) return '';
        
        try {
            // If time is already in HH:mm format, return as is
            if (/^\d{2}:\d{2}$/.test(time)) {
                return time;
            }

            // Parse the time string and convert to 24-hour format
            const date = new Date(`2000-01-01 ${time}`);
            return date.toLocaleTimeString('en-GB', { 
                hour: '2-digit',
                minute: '2-digit',
                hour12: false 
            });
        } catch (e) {
            return time;
        }
    },

    // Initialize form data
    init() {
        this.initializeBasicFields();
        this.initializeDateTimeFields();
        this.initializeDateBounds();
        this.initializeStatus();
        this.initializeCustomerAndTemplate();
        this.initializeRecurringFields();
        this.setupFormSubmitListener();
    },

    // Initialize basic form fields
    initializeBasicFields() {
        this.jobName = this.initializeField('name');
        this.jobDescription = this.initializeField('description');
        this.additionalTask = this.initializeField('additional_task');
    },

    // Initialize date and time fields
    initializeDateTimeFields() {
        // Get values from form fields first
        const rawDate = this.initializeField('preferred_start_date') || this.preferredStartDate;
        const rawTime = this.initializeField('preferred_start_time') || this.preferredStartTime;
        const rawEndDate = this.initializeField('end_date');

        // Set date and time from form values or keep existing values
        if (rawDate) {
            this.preferredStartDate = this.formatDate(rawDate);
        }
        if (rawTime) {
            this.preferredStartTime = this.formatTimeForInput(rawTime);
            // Format again to handle any edge cases
            this.preferredStartTime = this.formatTimeForInput(this.preferredStartTime);
        }

        // Set end date if exists
        this.endDate = rawEndDate ? this.formatDate(rawEndDate) : '';
    },

    // Compute dynamic ±20-year bounds for the date picker
    initializeDateBounds() {
        const today = new Date();
        const toIsoDate = (d) => new Date(d.getTime() - d.getTimezoneOffset() * 60000)
            .toISOString()
            .slice(0, 10);

        const min = new Date(today);
        min.setFullYear(min.getFullYear() - 20);
        const max = new Date(today);
        max.setFullYear(max.getFullYear() + 20);

        this.minAllowedDate = toIsoDate(min);
        this.maxAllowedDate = toIsoDate(max);
    },

    // Initialize status fields
    initializeStatus() {
        this.isActive = window.oldWorkOrder ? Boolean(window.oldWorkOrder.is_active) : true;
        this.previousStatus = this.isActive;
    },

    // Initialize customer and template
    initializeCustomerAndTemplate() {
        this.initializeIds();
        this.initializeCustomer();
        this.initializeTemplate();
    },

    // Initialize recurring fields
    initializeRecurringFields() {
        // Check if we're in edit mode
        this.isEditMode = !!window.oldWorkOrder?.id;
        
        this.isRecurring = this.initializeField('is_recurring') === '1' || this.initializeField('is_recurring') === true;
        this.frequency = this.initializeField('frequency') || 'weekly';
        this.repeatAfter = parseInt(this.initializeField('repeat_after')) || 1;

        // Handle backend validation errors
        this.handleBackendErrors();
        
        // Initialize selected days
        const selectedDaysValue = this.initializeField('selected_days');
        if (selectedDaysValue) {
            if (Array.isArray(selectedDaysValue)) {
                this.selectedDays = selectedDaysValue;
            } else if (typeof selectedDaysValue === 'string') {
                try {
                    this.selectedDays = JSON.parse(selectedDaysValue);
                } catch (e) {
                    this.selectedDays = [];
                }
            } else {
                this.selectedDays = [];
            }
        } else {
            this.selectedDays = [];
        }
        
        this.monthlyDayType = this.initializeField('monthly_day_type') || 'first';
        this.monthlyDayOfWeek = this.initializeField('monthly_day_of_week') || 'monday';
    },

    // Handle frequency change to reset selected days for semi-monthly
    handleFrequencyChange() {
        // Clear irrelevant fields based on frequency
        this.clearIrrelevantFields();
        
        if (this.frequency === 'semi_monthly') {
            // For semi-monthly, ensure only one day is selected
            if (this.selectedDays.length > 1) {
                this.selectedDays = [this.selectedDays[0]];
            }
        }
        
        // Clear related errors when frequency changes
        this.errors.frequency = null;
        this.errors.repeat_after = null;
        this.errors.selected_days = null;
        this.errors.monthly_day_type = null;
        this.errors.monthly_day_of_week = null;
    },

    // Clear irrelevant fields based on frequency
    clearIrrelevantFields() {
        switch (this.frequency) {
            case 'daily':
                // Daily doesn't need selected_days, monthly_day_type, monthly_day_of_week
                this.selectedDays = [];
                this.monthlyDayType = 'first';
                this.monthlyDayOfWeek = 'monday';
                break;
                
            case 'weekly':
                // Weekly doesn't need monthly_day_type, monthly_day_of_week
                this.monthlyDayType = 'first';
                this.monthlyDayOfWeek = 'monday';
                break;
                
            case 'semi_monthly':
                // Semi-monthly doesn't need repeat_after, monthly_day_type, monthly_day_of_week
                this.repeatAfter = 1;
                this.monthlyDayType = 'first';
                this.monthlyDayOfWeek = 'monday';
                break;
                
            case 'monthly':
                // Monthly doesn't need selected_days
                this.selectedDays = [];
                break;
        }
    },

    // Handle recurring toggle to clear errors and fields
    handleRecurringToggle() {
        if (!this.isRecurring) {
            // Clear all recurring errors when recurring is disabled
            this.errors.frequency = null;
            this.errors.repeat_after = null;
            this.errors.selected_days = null;
            this.errors.monthly_day_type = null;
            this.errors.monthly_day_of_week = null;
            this.errors.end_date = null;
            
            // Clear all recurring field values when recurring is disabled
            this.frequency = 'weekly';
            this.repeatAfter = 1;
            this.selectedDays = [];
            this.monthlyDayType = 'first';
            this.monthlyDayOfWeek = 'monday';
            this.endDate = '';
        }
    },

    // Handle backend validation errors
    handleBackendErrors() {
        // Check if there are any Laravel validation errors
        if (window.laravelErrors) {
            Object.keys(window.laravelErrors).forEach(field => {
                this.errors[field] = window.laravelErrors[field][0];
            });
        }
    },

    // Initialize customer and template IDs
    initializeIds() {
        this.selectedCustomerId = this.selectedCustomerId && this.selectedCustomerId !== 'null' 
            ? Number(this.selectedCustomerId) 
            : null;
        
        this.selectedTemplateId = this.selectedTemplateId && this.selectedTemplateId !== 'null' 
            ? Number(this.selectedTemplateId) 
            : null;
    },

    // Initialize customer information
    initializeCustomer() {
        if (!this.selectedCustomerId) return;

        if (this.isCustomerFixed) {
            const customerInfo = document.querySelector('.customer-info');
            if (!customerInfo) return;

            this.selectedCustomer = {
                id: this.selectedCustomerId,
                name: customerInfo.textContent.trim()
            };
        } else {
            // In add mode, get customer info from the select option
            const customerOption = document.querySelector(`#customer_id option[value='${this.selectedCustomerId}']`);
            if (customerOption) {
                this.selectedCustomer = {
                    id: this.selectedCustomerId,
                    name: customerOption.textContent.trim()
                };
            }
        }

        // Always dispatch customer-selected to populate pool details
        Livewire.dispatch('customer-selected', [this.selectedCustomerId]);
    },

    // Initialize template information
    initializeTemplate() {
        if (!this.selectedTemplateId) return;

        const template = document.querySelector(`#template_id option[value='${this.selectedTemplateId}']`);
        if (!template) return;

        this.selectedTemplate = {
            id: this.selectedTemplateId,
            name: template.textContent.trim(),
            description: template.dataset.description
        };
        
        Livewire.dispatch('template-changed', [this.selectedTemplateId]);
        
        // Don't auto-generate job details during initialization in edit mode
        if (!this.isEditMode && this.selectedCustomer) {
            this.updateJobDetails(true);
        }
    },

    // Setup form submit listener
    setupFormSubmitListener() {
        const form = this.$refs.form;
        if (form) {
            form.addEventListener('submit', (e) => this.handleSubmit(e));
        }
    },

    // Initialize field value from old input or work order data
    initializeField(fieldName) {
        const oldValue = window.oldInput?.[fieldName];
        const workOrderValue = window.oldWorkOrder?.[fieldName];
        const refValue = this.$refs[fieldName]?.value;
        
        return oldValue ?? workOrderValue ?? refValue ?? '';
    },

    // Format date to YYYY-MM-DD
    formatDate(dateString) {
        if (!dateString) return '';
        
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return '';
        
        return date.toISOString().split('T')[0];
    },

    // Format time to HH:mm
    formatTime(timeString) {
        if (!timeString) return '';
        
        // Try parsing as ISO datetime string
        const date = new Date(timeString);
        if (!isNaN(date.getTime())) {
            return date.toTimeString().slice(0, 5); // Get HH:mm from time string
        }
        
        // If already in HH:mm format, return as is
        if (/^\d{2}:\d{2}$/.test(timeString)) {
            return timeString;
        }
        
        // If in HH:mm:ss format, truncate seconds
        if (/^\d{2}:\d{2}:\d{2}$/.test(timeString)) {
            return timeString.slice(0, 5);
        }
        
        return '';
    },

    // Handle form submission
    handleSubmit(event) {
        event.preventDefault();

        if (this.isSubmitting) {
            return;
        }

        // Trim input fields before validation
        this.trimInputFields();

        // Validate form before submission
        if (!this.validateForm()) {
            return;
        }

        this.isSubmitting = true;

        // Format time before submission
        const formData = new FormData(event.target);
        if (this.preferredStartTime) {
            formData.set('preferred_start_time', this.formatTimeForInput(this.preferredStartTime));
        }

        // Set delete_photo based on existing field value (set by markPhotoForDeletion if needed)
        const deletePhotoInput = this.$refs.form.querySelector('[name="delete_photo"]');
        const deletePhotoValue = deletePhotoInput ? deletePhotoInput.value : '0';
        formData.set('delete_photo', deletePhotoValue);

        // Submit form normally to allow Laravel validation to work
        event.target.submit();
    },

    // Trim input fields
    trimInputFields() {
        this.jobName = this.jobName.trim();
        this.jobDescription = this.jobDescription.trim();
        this.additionalTask = this.additionalTask.trim();
        
        // Update form field values
        const nameInput = this.$refs.name;
        const descriptionInput = this.$refs.description;
        const additionalTaskInput = this.$refs.additional_task;
        
        if (nameInput) nameInput.value = this.jobName;
        if (descriptionInput) descriptionInput.value = this.jobDescription;
        if (additionalTaskInput) additionalTaskInput.value = this.additionalTask;
    },

    // Submit form with specific action
    submitWithAction(action) {
        try {
            this.action = action;
            
            if (this.$refs.action) {
                this.$refs.action.value = action;
            }

            // Ensure delete_photo field exists (it will be set by markPhotoForDeletion if needed)
            let deleteInput = this.$refs.form.querySelector('[name="delete_photo"]');
            if (!deleteInput) {
                deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'delete_photo';
                deleteInput.value = '0';
                this.$refs.form.appendChild(deleteInput);
            }

            if (!this.validateForm()) {
                return;
            }

            // Check if this is an update operation (existing work order)
            if (window.oldWorkOrder?.id) {
                // Show confirmation modal for updates
                this.showUpdateConfirm = true;
                return;
            }

            const form = this.$refs.form;
            if (!form) {
                return;
            }

            this.isSubmitting = true;
            form.submit();
        } catch (error) {
            console.error('Error in submitWithAction:', error);
        }
    },

    // Handle customer selection change
    handleCustomerChange(event) {
        if (this.isCustomerFixed) {
            event.preventDefault();
            return false;
        }

        this.selectedCustomerId = event.target.value;
        const customer = document.querySelector(`#customer_id option[value='${event.target.value}']`);
        if (customer) {
            this.selectedCustomer = {
                id: event.target.value,
                name: customer.textContent.trim()
            };
            this.updateJobDetails(true);
            this.errors.customer_id = null;
        } else {
            this.selectedCustomer = null;
            this.jobName = '';
            if (this.customerType === 'existing') {
                this.errors.customer_id = validation.customer_required;
            }
        }
        Livewire.dispatch('customer-selected', [event.target.value]);
    },

    // Handle template selection change
    handleTemplateChange(event) {
        this.selectedTemplateId = event.target.value;
        const template = document.querySelector(`#template_id option[value='${event.target.value}']`);
        if (template) {
            this.selectedTemplate = {
                id: event.target.value,
                name: template.textContent.trim(),
                description: template.dataset.description
            };
            this.updateJobDetails(true);
            this.errors.template_id = null;
        } else {
            this.selectedTemplate = null;
            this.jobName = '';
            this.jobDescription = '';
            this.errors.template_id = validation.template_required;
        }
        Livewire.dispatch('template-changed', [event.target.value]);
    },

    // Update job details based on customer and template
    updateJobDetails(forceUpdate = false) {
        if (this.selectedCustomer && this.selectedTemplate) {
            if (forceUpdate || this.isCustomerFixed || !this.jobName) {
                const templateName = this.selectedTemplate.id ? this.selectedTemplate.name : '';
                this.jobName = `${this.selectedCustomer.name} ${templateName}`;
            }
            if (forceUpdate || this.isCustomerFixed || !this.jobDescription) {
                this.jobDescription = this.selectedTemplate.description || '';
            }
        }
    },

    // Validate form fields
    validateForm() {
        this.errors = {};
        
        const validations = [
            this.validateCustomerAndTemplate,
            this.validateJobDetails,
            this.validateOptionalFields,
            this.validateChecklistItemsPresent,
            this.validateDateAndTime,
            this.validateRecurringFields,
            this.validateEndDate,
        ];

        validations.forEach(validate => validate.call(this));

        // Debug: Log errors to console
        if (this.hasErrors()) {
            this.$nextTick(() => this.scrollToError());
            return false;
        }

        return true;
    },

    // Validate presence of at least one checklist item
    validateChecklistItemsPresent() {
        const anyTemplateItem = document.querySelector('input[name^="checklist_items["][name$="[item_text]"]');
        if (!anyTemplateItem) {
            this.errors.checklist_items = validation.required;
            return;
        }

        // Check if at least one checklist item is checked/visible
        const checkedItems = document.querySelectorAll('input[name^="checklist_items["][name$="[is_visible]"]:checked');
        if (checkedItems.length === 0) {
            this.errors.checklist_items = validation.select_checklist_items_required;
        }
    },

    // Check if there are any validation errors
    hasErrors() {
        return Object.keys(this.errors).length > 0;
    },

    // Validate customer and template selection
    validateCustomerAndTemplate() {
        if (this.customerType === 'existing' && !this.selectedCustomerId) {
            this.errors.customer_id = validation.customer_required;
        }

        if (!this.selectedTemplateId) {
            this.errors.template_id = validation.template_required;
        }
    },

    // Validate job name and description
    validateJobDetails() {
        if (!this.jobName) {
            this.errors.name = validation.required;
        } else if (this.jobName.length > VALIDATION_RULES.NAME_MAX_LENGTH) {
            this.errors.name = validation.name_max;
        }

        if (this.jobDescription && this.jobDescription.length > VALIDATION_RULES.DESCRIPTION_MAX_LENGTH) {
            this.errors.description = validation.description_max;
        }
    },

    // Validate optional fields
    validateOptionalFields() {
        if (this.additionalTask && this.additionalTask.length > VALIDATION_RULES.ADDITIONAL_TASK_MAX_LENGTH) {
            this.errors.additional_task = validation.additional_task_max;
        }
    },

    // Validate date and time fields
    validateDateAndTime() {
        let hasErrors = false;

        // Validate date
        if (!this.preferredStartDate) {
            this.errors.preferred_start_date = validation.start_date_required;
            hasErrors = true;
        } else {
            // Create dates in local timezone for comparison
            const [year, month, day] = this.preferredStartDate.split('-');
            const selectedDate = new Date(year, month - 1, day);
            selectedDate.setHours(0, 0, 0, 0);

            const today = new Date();
            today.setHours(0, 0, 0, 0);

            // In create mode, validate that date is not in the past
            if (!this.isEditMode && selectedDate < today) {
                this.errors.preferred_start_date = validation.start_date_future;
                hasErrors = true;
            }
        }

        // Validate time independently
        if (!this.preferredStartTime) {
            this.errors.preferred_start_time = validation.start_time_required;
            hasErrors = true;
            return !hasErrors;
        }

        // Normalize time to HH:mm if needed (accept HH:mm or HH:mm:ss or 12h)
        this.preferredStartTime = this.formatTimeForInput(this.preferredStartTime);

        // Validate normalized time format
        if (!/^\d{2}:\d{2}$/.test(this.preferredStartTime)) {
            this.errors.preferred_start_time = validation.start_time_required;
            hasErrors = true;
            return !hasErrors;
        }

        // Validate time value (only for create mode and today's date)
        if (!this.isEditMode) {
            const [year, month, day] = this.preferredStartDate.split('-');
            const selectedDate = new Date(year, month - 1, day);
            
            if (this.isSelectedDateToday(selectedDate) && !this.validateTime(this.preferredStartTime)) {
                hasErrors = true;
            }
        }

        return !hasErrors;
    },

    // Validate frequency selection
    validateFrequencyField() {
        if (!this.frequency) {
            this.errors.frequency = 'Frequency is required.';
            return false;
        }
        return true;
    },

    // Validate repeat after value and limits
    validateRepeatAfterField() {
        if (!['weekly', 'monthly'].includes(this.frequency)) {
            return true;
        }

        if (!this.repeatAfter || this.repeatAfter < 1) {
            this.errors.repeat_after = 'Repeat After is required.';
            return false;
        }

        const maxValues = {
            weekly: 4,
            monthly: 12
        };

        if (this.repeatAfter > maxValues[this.frequency]) {
            this.errors.repeat_after = `${this.frequency} frequency cannot exceed ${maxValues[this.frequency]} ${this.frequency === 'weekly' ? 'weeks' : 'months'}.`;
            return false;
        }

        return true;
    },

    // Validate selected days
    validateSelectedDaysField() {
        if (!['weekly', 'semi_monthly'].includes(this.frequency)) {
            return true;
        }

        if (!this.selectedDays?.length) {
            this.errors.selected_days = this.frequency === 'semi_monthly' 
                ? 'Please select a service day.'
                : 'Select at least one service day.';
            return false;
        }

        if (this.frequency === 'semi_monthly' && this.selectedDays.length > 1) {
            this.errors.selected_days = 'Please select only one day for semi-monthly frequency.';
            return false;
        }

        return true;
    },

    // Validate monthly day selection
    validateMonthlyDayField() {
        if (this.frequency !== 'monthly') {
            return true;
        }

        if (!this.monthlyDayType || !this.monthlyDayOfWeek) {
            this.errors.monthly_day_type = 'Please complete day selection.';
            return false;
        }

        return true;
    },

    // Main validation method for recurring fields
    validateRecurringFields() {
        if (!this.isRecurring) {
            return true;
        }

        const validations = [
            this.validateFrequencyField,
            this.validateRepeatAfterField,
            this.validateSelectedDaysField,
            this.validateMonthlyDayField
        ];

        return validations.every(validate => validate.call(this));
    },

    // Validate date is today or in the future
    validateDate(value) {
        if (!value) return false;

        // Create dates in local timezone
        const [year, month, day] = value.split('-');
        const selectedDate = new Date(year, month - 1, day);
        selectedDate.setHours(0, 0, 0, 0);

        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        return selectedDate >= today;
    },

    // Validate time for today's date
    validateTime(value) {
        if (!value || !this.preferredStartDate) return false;

        // Create date in local timezone
        const [year, month, day] = this.preferredStartDate.split('-');
        const selectedDate = new Date(year, month - 1, day);  // month is 0-based in JS
        
        // Only validate time if date is today
        if (this.isSelectedDateToday(selectedDate)) {
            return this.isTimeInFuture(selectedDate);
        }

        return true; // Time is valid for future dates
    },

    // Validate start date
    validateStartDate() {
        if (!this.preferredStartDate) {
            this.errors.preferred_start_date = validation.start_date_required;
            return;
        }

        if (!this.validateDate(this.preferredStartDate)) {
            this.errors.preferred_start_date = validation.start_date_future;
        }
    },

    // Validate start date
    validateEndDate() {
        if (!this.endDate) {
            return;
        }

        if (this.endDate < this.preferredStartDate) {
            this.errors.end_date = validation.end_date_after_start;
        }
    },

    // Validate start time
    validateStartTime() {
        if (!this.preferredStartTime) {
            this.errors.preferred_start_time = validation.start_time_required;
        }
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
        const accordionSection = errorBox.closest('.white-box');
        if (accordionSection) {
            this.open = true;
        }
    },

    // Calculate scroll position
    calculateScrollPosition(errorBox) {
        const yOffset = -100; // Add some padding from the top
        return errorBox.getBoundingClientRect().top + window.scrollY + yOffset;
    },

    // Focus error field
    focusErrorField(fieldName) {
        const input = document.querySelector(`[name="${fieldName}"]`);
        if (input) {
            input.focus();
        }
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
                behavior: 'smooth'
            });
            this.focusErrorField(firstErrorField);
        }, 100);
    },

    // Check if selected date is today
    isSelectedDateToday(selectedDate) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        // Create a new date object in local timezone
        const [year, month, day] = selectedDate.toISOString().split('T')[0].split('-');
        const localSelectedDate = new Date(year, month - 1, day);  // month is 0-based in JS
        
        return localSelectedDate.getTime() === today.getTime();
    },

    // Check if time is in the future
    isTimeInFuture(selectedDate) {
        const [hours, minutes] = this.preferredStartTime.split(':');
        // Use the same date object but set the time
        selectedDate.setHours(parseInt(hours), parseInt(minutes), 0, 0);
        
        const now = new Date();
        return selectedDate > now;
    },

    // Handle inactive status confirmation
    confirmInactive(event) {
        event.preventDefault();
        this.showConfirm= true;
    },

    // Cancel inactive status change
    cancelInactive() {
        this.showConfirm= false;
        const activeRadio = document.getElementById('status_active');
        if (activeRadio) {
            activeRadio.checked = true;
        }
    },

    // Confirm inactive status change
    confirmInactiveStatus() {
        this.showConfirm= false;
        const inactiveRadio = document.getElementById('status_inactive');
        if (inactiveRadio) {
            inactiveRadio.checked = true;
        }
    },

    handleStatusChange() {
        // If changing from active to inactive, show confirmation
        if (this.previousStatus && !this.isActive) {
            this.showConfirm = true;
        } else {
            // For any other change, just update the previous status
            this.previousStatus = this.isActive;
        }
    },

    handleCancel() {
        this.showConfirm = false;
        this.isActive = this.previousStatus;
    },

    handleConfirm() {
        this.showConfirm = false;
        this.previousStatus = this.isActive;
    },

    // Handle update confirmation modal
    handleUpdateConfirm() {
        this.showUpdateConfirm = false;
        const form = this.$refs.form;
        if (!form) {
            return;
        }
        this.isSubmitting = true;
        form.submit();
    },

    handleUpdateCancel() {
        this.showUpdateConfirm = false;
        // Stay on the form with all inputs preserved
    }
}));

window.Alpine?.data('photoUpload', () => ({
    file: null,
    dragover: false,
    fileInput: null,
    errorMessage: '',
    existingPhoto: false,
    existingPhotoUrl: '',
    existingPhotoThumbUrl: '',
    existingPhotoName: '',
    existingPhotoSize: '',
    shouldDeletePhoto: false,
    maxSize: 3 * 1024 * 1024, // 3MB in bytes
    allowedTypes: ['image/jpeg', 'image/png', 'image/jpg'],

    init() {
        this.fileInput = this.$refs.photo;
        
        // Initialize existing photo if available
        if (window.oldWorkOrder?.photo) {
            this.existingPhoto = true;
            // Use the photo URL directly
            this.existingPhotoUrl = window.oldWorkOrder.photo_url;
            this.existingPhotoThumbUrl = window.oldWorkOrder.photo_thumb_url;
            // Extract filename from URL
            this.existingPhotoName = window.oldWorkOrder.photo.split('/').pop();
        }

        // Watch for modal close to update state if needed
        window.addEventListener('close-modal', (event) => {
            if (event.detail.id === 'image-preview-modal' && this.shouldDeletePhoto) {
                this.file = null;
                this.existingPhoto = false;
                if (this.fileInput) this.fileInput.value = '';
            }
        });
    },

    handleFileSelect(event) {
        const files = event.target.files || event.dataTransfer.files;
        if (!files.length) return;
        
        this.validateAndSetFile(files[0]);
    },

    validateAndSetFile(selectedFile) {
        // Reset error
        this.errorMessage = '';

        // Validate file type
        if (!this.allowedTypes.includes(selectedFile.type)) {
            this.errorMessage = 'Only PNG, JPG images up to 3MB are supported.';
            if (this.fileInput) this.fileInput.value = '';
            return;
        }

        // Validate file size
        if (selectedFile.size > this.maxSize) {
            this.errorMessage = 'File size should not exceed 3MB.';
            if (this.fileInput) this.fileInput.value = '';
            return;
        }

        // Create preview URL
        const reader = new FileReader();
        reader.onload = (e) => {
            this.file = {
                file: selectedFile,
                preview: e.target.result,
                name: selectedFile.name
            };
            this.existingPhoto = false;
            this.shouldDeletePhoto = false;
            // Reset delete_photo field when new file is selected
            const form = this.$el.closest('form');
            if (form) {
                let deleteInput = form.querySelector('[name="delete_photo"]');
                if (!deleteInput) {
                    deleteInput = document.createElement('input');
                    deleteInput.type = 'hidden';
                    deleteInput.name = 'delete_photo';
                    form.appendChild(deleteInput);
                }
                deleteInput.value = '0';
            }
        };
        reader.readAsDataURL(selectedFile);
    },

    removeFile() {
        this.file = null;
        this.errorMessage = '';
        this.existingPhoto = false;
        if (this.fileInput) this.fileInput.value = '';
        
        // Find or create delete_photo input and reset it
        const form = this.$el.closest('form');
        if (form) {
            let deleteInput = form.querySelector('[name="delete_photo"]');
            if (!deleteInput) {
                deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'delete_photo';
                form.appendChild(deleteInput);
            }
            deleteInput.value = '0';
        }
    },

    markPhotoForDeletion() {
        this.shouldDeletePhoto = true;
        this.file = null;
        this.existingPhoto = false;
        if (this.fileInput) this.fileInput.value = '';

        // Find the form
        const form = this.$el.closest('form');
        if (!form) {
            return;
        }

        // Find or create delete_photo input
        let deleteInput = form.querySelector('[name="delete_photo"]');
        if (!deleteInput) {
            deleteInput = document.createElement('input');
            deleteInput.type = 'hidden';
            deleteInput.name = 'delete_photo';
            form.appendChild(deleteInput);
        }
        deleteInput.value = '1';
    },

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
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
    }
}));
