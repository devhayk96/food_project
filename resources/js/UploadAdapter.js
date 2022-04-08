export default class UploadAdapter {
    constructor( loader ) {
        // The file loader instance to use during the upload.
        this.loader = loader;

    }

    // Starts the upload process.
    async upload() {
        return this.loader.file.then((file) => {
            const data = new FormData()
            data.append("file", file)
            const genericError = `Couldn't upload file: ${file.name}.`

            return axios({
                data,
                method: "POST",
                url: "/uploads",
                headers: {
                    "Content-Type": "multipart/form-data",
                },
                onUploadProgress: (progressEvent) => {
                    this.loader.uploadTotal = progressEvent.total
                    this.loader.uploaded = progressEvent.loaded
                    const uploadPercentage = parseInt(
                        Math.round((progressEvent.loaded / progressEvent.total) * 100)
                    )
                },
            })
                .then(({ data }) => ({ default: data.url }))
                .catch(({ error }) => Promise.reject(error?.message ?? genericError))
        })
    }

    // Aborts the upload process.
    abort() {
        return Promise.reject()
    }

}
