import { resetForm } from '../common/common.js';

/**
 * Template form validation component
 */
export function templateForm() {
    return {
        name: this.$wire?.name || '',
        description: this.$wire?.description || '',
        type: this.$wire?.type || 'WO',
        is_active: this.$wire?.is_active ?? true,
        processing: false,
        errors: {},

        init() {
            resetForm(this.$refs.form);
            
            // Store original wire:model bindings
            const inputs = this.$refs.form.querySelectorAll('[wire\\:model], [wire\\:model\\.defer]');
            inputs.forEach(input => {
                const hasDefer = input.hasAttribute('wire:model.defer');
                input.dataset.originalModel = hasDefer ? 'defer' : 'live';
            });
        },

        clearForm() {
            // Store original wire model bindings
            const inputs = this.$refs.form.querySelectorAll('[wire\\:model], [wire\\:model\\.defer]');
            const originalBindings = new Map();
            
            inputs.forEach(input => {
                if (input.hasAttribute('wire:model.defer')) {
                    originalBindings.set(input, {
                        attr: 'wire:model.defer',
                        value: input.getAttribute('wire:model.defer')
                    });
                } else if (input.hasAttribute('wire:model')) {
                    originalBindings.set(input, {
                        attr: 'wire:model',
                        value: input.getAttribute('wire:model')
                    });
                }
            });

            // Reset form state
            this.name = '';
            this.description = '';
            this.type = 'WO';
            this.errors = {};
            
            // Reset radio buttons
            document.getElementById('type_work_order').checked = true;
            document.getElementById('type_maintenance').checked = false;
            
            // Reset validation state
            this.$refs.form.reset();
            this.resetValidation();

            // Call Livewire reset once
            this.$wire.clearForm();

            // Restore original wire model bindings
            setTimeout(() => {
                originalBindings.forEach((binding, input) => {
                    input.setAttribute(binding.attr, binding.value);
                });
            }, 0);
        },

        resetValidation() {
            this.errors = {};
        },

        validateName() {
            const trimmedValue = this.name.trim();
            if (!trimmedValue) {
                this.errors.name = window.validation.required;
                return false;
            }
            if (trimmedValue.length > 100) {
                this.errors.name = window.validation.max_100;
                return false;
            }
            delete this.errors.name;
            return true;
        },

        validateDescription() {
            const trimmedValue = this.description.trim();
            if (!trimmedValue) {
                this.errors.description = window.validation.required;
                return false;
            }
            if (trimmedValue.length > 1200) {
                this.errors.description = window.validation.max_length;
                return false;
            }
            delete this.errors.description;
            return true;
        },

        validateType() {
            if (!['WO', 'MO'].includes(this.type)) {
                this.errors.type = window.validation.invalid_type;
                return false;
            }
            delete this.errors.type;
            return true;
        },

        validate(isSubmit = false) {
            this.errors = {};
            const isNameValid = this.validateName();
            const isDescriptionValid = this.validateDescription();
            
            // Only validate type on form submission
            const isTypeValid = isSubmit ? this.validateType() : true;

            return isNameValid && isDescriptionValid && isTypeValid;
        },

        submitForm($wire) {
            if (this.processing) return;
            
            this.processing = true;
            if (this.validate(true)) {
                // Update Livewire component properties
                $wire.set('name', this.name);
                $wire.set('description', this.description);
                $wire.set('type', this.type);
                
                // Call Livewire method
                $wire.addTemplate().then(() => {
                    this.processing = false;
                }).catch(() => {
                    this.processing = false;
                });
            } else {
                this.processing = false;
                const firstError = Object.keys(this.errors)[0];
                if (firstError) {
                    this.$refs[firstError]?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        },

        submitEditForm($wire) {
            if (this.processing) return;
            
            this.processing = true;
            if (this.validate(true)) {
                // Update Livewire component properties
                $wire.set('name', this.name);
                $wire.set('description', this.description);
                
                // Call Livewire method
                $wire.updateTemplate().then(() => {
                    this.processing = false;
                }).catch(() => {
                    this.processing = false;
                });
            } else {
                this.processing = false;
                const firstError = Object.keys(this.errors)[0];
                if (firstError) {
                    this.$refs[firstError]?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        }
    };
}
