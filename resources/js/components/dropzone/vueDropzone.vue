<template>
  <div class="z-x-1">
    <vue-dropzone
      ref="myVueDropzone"
      @vdropzone-total-upload-progress="
        (totaluploadprogress, totalBytes, totalBytesSent) =>
          progress(totaluploadprogress, totalBytes, totalBytesSent)
      "
      @vdropzone-drag-over="(event) => over(event)"
      @vdropzone-drag-start="(event) => start(event)"
      @vdropzone-drag-end="(event) => end(event)"
      @vdropzone-drag-drop="(event, file) => dragDrop(event, file)"
      @vdropzone-file-added="(file) => fileAdded(file)"
      @vdropzone-removed-file="(file, error, xhr) => removed(file, error, xhr)"
      @vdropzone-success="(file, response) => success(file, response)"
      id="dz1"
      :options="dropzoneOptions"
      :useCustomSlot="true"
    >
      <div class="dropzone-custom-content">
        <img :src="dropzoneImage" alt="dropzone image">
        <h3 class="dropzone-custom-title">Drop your image here or browse</h3>
        <div class="subtitle">
            Supports: jpg, png
        </div>
      </div>
    </vue-dropzone>
  </div>
</template>

<script>
import vue2Dropzone from "vue2-dropzone";
import "vue2-dropzone/dist/vue2Dropzone.min.css";
export default {
  name: "app",
  components: {
    vueDropzone: vue2Dropzone,
  },

    props: {
        manuallfyAddedFile:{required:false},
        dropzoneImage:{
            required:false,
            default: "/images/image_drag_drop.svg"
        }
    },

  mounted() {
    this.readFile(this.manuallfyAddedFile);
  },

  computed: {},

  data: () => ({
    dropzoneOptions: {
      maxFiles: 1,
      url: "https://httpbin.org/post",
      maxFilesize: 5,
      thumbnailWidth: 300,
      thumbnailHeight: 200,
      addRemoveLinks: true,
      headers: { "My-Awesome-Header": "header value" },
    },
  }),

  methods: {
    over(event) {
      this.$emit("over", event);
    },

    start(event) {
      this.$emit("start", event);
    },

    end(event) {
      this.$emit("end", event);
    },

    progress(totaluploadprogress, totalBytes, totalBytesSent) {
      this.$emit("progress", {
        totaluploadprogress,
        totalBytes,
        totalBytesSent,
      });
    },

    dragDrop(event, file) {
      console.log(file, event);
      this.$emit("dragdrop", { event: event, file: file });
    },

    fileAdded(file) {
      this.$emit("added", file);
    },

    removed(file, error, xhr) {
      this.$emit("removed", { file: file, error: error, xhr: xhr });
    },

    success(file, response) {
      this.$emit("success", { file: file, response: response });
    },

    async readFile(param) {
      console.log(param);
      if (param !== null) {
        console.log(this.$refs.myVueDropzone);
        const reader = new FileReader();
        let blob = await fetch(`${window.origin + param}`).then((r) =>
          r.blob()
        );
        reader.readAsDataURL(blob);
        this.$refs.myVueDropzone.manuallyAddFile(
          blob,
          `${window.origin + param}`
        );
      }
    },
  },

  watch: {
    manuallfyAddedFile() {
      console.log(this.manuallfyAddedFile);
    },
  },
};
</script>

<style scoped>
@import url("https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
.dropzone {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 !important;
  min-width: 300px !important;
  min-height: 200px !important;
}

#dropzone > .dz-preview {
  margin: 0 !important;
}

.z-x-1 {
  z-index: 1;
}

.dropzone-custom-content {
  text-align: center;
}

.dropzone-custom-title {
  margin-top: 0;
  color: #00315B;
}

.subtitle {
  color: #949494;
}
</style>
