<template>
    <div>
        <form ref="dropzoneForm" class="dropzone">
            <div class="fallback">
                <input type="file" multiple />
            </div>
        </form>
    </div>
</template>

<script>
import Dropzone from "dropzone";
import "dropzone/dist/dropzone.css";

export default {
    mounted() {
        const dropzoneForm = this.$refs.dropzoneForm;
        const uploadUrl = "/upload-chunk";

        const dropzoneConfig = {
            url: uploadUrl,
            paramName: "file",
            chunking: true,
            forceChunking: true,
            chunkSize: 10 * 1024 * 1024,
            parallelUploads: 1,
            retryChunks: true,
            retryChunksLimit: 3,
            autoProcessQueue: true,
            maxFilesize: 10240,
            init: function () {
                this.on("error", function (file, errorMessage, xhr) {
                    console.error(
                        "An error occurred while uploading:",
                        errorMessage
                    );
                });

                this.on("totaluploadprogress", function (progress) {
                    console.log("Total progress:", progress);
                });
            },
        };

        new Dropzone(dropzoneForm, dropzoneConfig);
    },
};
</script>
