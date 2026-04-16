document.addEventListener('alpine:init', () => {
    Alpine.data('importCustomers', () => ({
        selectedFile: null,
        isDragOver: false,
        isUploading: false,
        validationErrors: [],

        selectFile() {
            this.$refs.fileUpload.click();
        },

        handleFileSelect(event) {
            const file = event.target.files[0];
            this.selectedFile = file;
            this.validationErrors = [];
        },

        handleDrop(event) {
            this.isDragOver = false;
            const file = event.dataTransfer.files[0];
            this.selectedFile = file;
            this.validationErrors = [];
        },

        validateFile() {
            this.validationErrors = [];

            if (!this.selectedFile) {
                this.validationErrors.push('This field is required.');
                return false;
            }

            // Check file type
            if (!this.selectedFile.name.toLowerCase().endsWith('.csv')) {
                this.validationErrors.push('Only CSV files are supported.');
                return false;
            }

            // Check file size (5MB limit)
            const maxSize = 5 * 1024 * 1024; // 5MB in bytes
            if (this.selectedFile.size > maxSize) {
                this.validationErrors.push('File size should not exceed 5MB.');
                return false;
            }

            return true;
        },

        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';

            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));

            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },

        submitForm(event) {
            if (!this.validateFile()) {
                return;
            }

            // Double check that the file is in the input
            if (!this.$refs.fileUpload?.files?.length) {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(this.selectedFile);
                this.$refs.fileUpload.files = dataTransfer.files;
            }

            this.isUploading = true;
            // Submit the form
            this.$refs.form.submit();
        }
    }));
}); 
