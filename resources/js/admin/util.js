export default function businessTable() {
    return {
        businesses: [],
        pagination: {},
        searchTerm: '',
        controller: null,
        fetchBusinesses(url) {
            // Cancel previous request if it exists
            if (this.controller) {
                this.controller.abort();
            }

            // Create a new AbortController for the current request
            this.controller = new AbortController();

            axios.get(url, {
                params: {
                    search: this.searchTerm
                },
                signal: this.controller.signal
            }).then(response => {
                this.businesses = response.data.data;
                this.pagination = response.data.pagination;
            }).catch(err => {
                // Ignore abort errors
                if (axios.isCancel(error)) {
                    console.log('Previous request cancelled');
                } else {
                    console.error('Fetch error:', err);
                }
            });
        }
    }
}

export function videoUploadComponent() {
    return {
        video: null,
        error: null,
        maxSize: 12 * 1024 * 1024, // 12MB in bytes
        allowedTypes: ['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-matroska'],
        isValid: false,
        uploadProgress: 0,
        isUploading: false,
        
        init() {
            console.log('Video upload component initialized');
            
            // Listen for upload progress events
            this.$wire.on('upload-progress', (event) => {
                console.log('Upload progress:', event.detail.progress);
                this.uploadProgress = event.detail.progress;
                this.isUploading = true;
            });

            // Listen for upload complete event
            this.$wire.on('upload-complete', () => {
                console.log('Upload complete');
                this.isUploading = false;
                this.uploadProgress = 0;
            });
        },
        
        validateVideo(file) {
            console.log('Validating file:', file.name, 'Type:', file.type, 'Size:', file.size);
            this.error = null;
            this.isValid = false;
            
            // Check file type
            if (!this.allowedTypes.includes(file.type)) {
                this.error = `Invalid file type: ${file.type}. Please upload MP4, MOV, AVI, or MKV files only.`;
                console.log('Validation failed: Invalid file type');
                return false;
            }
            
            // Check file size
            if (file.size > this.maxSize) {
                const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
                this.error = `File size (${sizeInMB}MB) exceeds 12MB limit.`;
                console.log('Validation failed: File too large');
                return false;
            }
            
            console.log('Validation passed');
            this.isValid = true;
            return true;
        },
        
        handleFileChange(event) {
            console.log('File input changed');
            const file = event.target.files[0];
            if (!file) {
                console.log('No file selected');
                this.video = null;
                this.isValid = false;
                return;
            }
            
            if (this.validateVideo(file)) {
                this.video = file;
                console.log('File validated');
            } else {
                console.log('File validation failed, clearing input');
                event.target.value = ''; // Clear the input
                this.video = null;
                this.isValid = false;
            }
        },
        
        handleDrop(event) {
            event.preventDefault();
            console.log('File dropped');
            const file = event.dataTransfer.files[0];
            if (!file) {
                console.log('No file in drop event');
                this.video = null;
                this.isValid = false;
                return;
            }
            
            if (this.validateVideo(file)) {
                this.video = file;
                console.log('Dropped file validated');
            } else {
                this.video = null;
                this.isValid = false;
            }
        },
        
        handleDragOver(event) {
            event.preventDefault();
        },

        // Handle form submission
        handleSubmit(event) {
            console.log('Form submission attempted');
            console.log('Validation status:', this.isValid);
            console.log('Error status:', this.error);
            
            if (!this.isValid || this.error || !this.video) {
                console.log('Preventing form submission due to validation error');
                event.preventDefault();
                event.stopPropagation();
                return false;
            }
            
            console.log('Form submission allowed, triggering Livewire save');
            this.isUploading = true;
            this.uploadProgress = 0;
            
            // Dispatch the file to Livewire with progress tracking
            this.$wire.upload(
                'video', 
                this.video, 
                (uploadedFilename) => {
                    console.log('File uploaded:', uploadedFilename);
                    this.isUploading = false;
                    // After successful upload, trigger the save method
                    this.$wire.save();
                },
                (event) => {
                    // Update progress
                    this.uploadProgress = Math.round((event.detail.progress * 100));
                    console.log('Upload progress:', this.uploadProgress + '%');
                }
            );
            
            return true;
        }
    }
}