<template>
    <v-snackbar
        v-model="status"
        :color="color"
        multi-line
        top
        right
        :timeout="timeoutSnackBar"
    >
        {{ snackBarText }}
        <v-btn
            dark
            text
            @click="status = false"
        >
            Close
        </v-btn>
    </v-snackbar>
</template>

<script>
    export default {
        name: "SnackBar",
        data() {
            return {
                status: false
            }
        },
        props: {
            snackBar: {
                default: false
            },
            color: {
                type: String,
                default: "success"
            },
            timeoutSnackBar: {
                type: Number,
                default: 4000
            },
            snackBarText: {
                type: String,
                default: ''
            }
        },

        watch: {
            snackBar: function(newVal, oldVal) { // watch it
                this.status = newVal !== oldVal;
            }
        }
    }
</script>

