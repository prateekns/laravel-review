import Dropzone from "dropzone";

export function dzUpload() {

    return {
        myDropzone: null,
        fileError: null,
        errors: {},
        imageData: null,

        uploadImage() {
            this.errors = {};
            const alpineContext = this;
            let myDropzone = new Dropzone(this.$refs.dzImageUpload, {
                url: "#",
                previewsContainer: false,
                acceptedFiles: "image/png,image/jpeg",
                autoProcessQueue: false,
                thumbnailWidth: 200,
                thumbnailHeight: 200,
                resizeQuality: 1,
                maxThumbnailFilesize: 2,
                maxFilesize: 2,
            });

            myDropzone.on("addedfile", file => {

            });

            myDropzone.on("thumbnail", function (file, dataURL) {
                const maxWidth = 1024;
                const maxHeight = 1024;

                if (file.width > maxWidth || file.height > maxHeight) {
                    file.rejectDimensions = true;
                    this.removeFile(file);
                    alpineContext.errors.fileError = window.validation.business_logo_size;
                } else if (file.status != 'error') {

                    alpineContext.errors.fileError = null;
                    if (document.getElementById("dz-upload-btn")) {
                        document.getElementById("dz-upload-btn").classList.add("hidden");
                    }
                    document.getElementById("dz-preview").src = dataURL;
                    alpineContext.imageData = dataURL;

                } else {
                    alpineContext.errors.fileError = window.validation.business_logo_error;
                }
            });

            myDropzone.on("error", function (file, errormessage, xhr) {
                if (errormessage) {
                    alpineContext.errors.fileError = null;
                    if (errormessage.indexOf("files of this type") != -1) {
                        alpineContext.errors.fileError = window.validation.business_logo;
                    } else if (errormessage.indexOf("File is too big") != -1) {
                        alpineContext.errors.fileError = window.validation.business_logo;
                    } else {
                        alpineContext.errors.fileError = errormessage;
                    }
                }
            });
        },
    }
}