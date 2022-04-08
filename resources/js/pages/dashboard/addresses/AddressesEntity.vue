<template>
    <v-container>
        <v-card height="100%" width="100%">
            <v-form ref="form">
                <v-card-title class="text-capitalize">
                    {{ this.action }} ADDRESSES
                </v-card-title>

                <HistoryCard v-if="this.action === 'edit'"/>

                <v-card-text>
                    <v-row>
                        <v-col cols="12" md="6">
                            <v-text-field
                                label="Street"
                                name="street"
                                prepend-icon="mdi-account-switch"
                                type="text"
                                v-model="dataForm.street"
                                :rules="textRules"
                                required
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" md="6">
                            <v-text-field
                                label="Postcode"
                                name="postcode"
                                prepend-icon="mdi-web"
                                type="text"
                                v-model="dataForm.postcode"
                                :rules="textRules"
                                required
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" md="6">
                            <v-text-field
                                label="City"
                                name="city"
                                prepend-icon="mdi-key"
                                type="text"
                                :rules="textRules"
                                v-model="dataForm.city"
                                required
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" md="6">
                            <v-text-field
                                label="extra"
                                name="extra"
                                prepend-icon="mdi-key"
                                type="text"
                                v-model="dataForm.extra"
                            ></v-text-field>
                        </v-col>

                    </v-row>
                </v-card-text>

                <v-card-actions class="float-right mt-4">
                    <v-btn
                        absolute
                        dark
                        bottom
                        right
                        large
                        color="success"
                        @click="createAddress"
                    >
                        SAVE
                    </v-btn>
                </v-card-actions>

            </v-form>
        </v-card>

        <SnackBar :color=color :snack-bar=snackBar :snack-bar-text=snackBarText :timeout-snack-bar=timeoutSnackBar />

    </v-container>
</template>

<script>
    import HistoryCard from "./../../../components/dashboard/EditHistoryCard";
    import SnackBar from "../../../components/common/SnackBar";

    export default {
        name: "AddressesEntity",
        components: { HistoryCard, SnackBar },

        data: () => ({
            createBy: '',
            creationDate: '',
            lastEditBy: '',
            lastEditDate: '',

            id: null,
            dataForm: {
                street: null,
                postcode: null,
                city: null,
                extra: null
            },
            snackBar: false,
            snackBarText: '',
            color: "success",
            timeoutSnackBar: 4000,

            textRules: [
                v => !!v || 'Field is required'
            ],
        }),

        props: {
            action: {
                type: String,
                default: "create"
            },
            addressId: {
                type: Number,
                default: null
            }
        },

        created() {
            // if (this.action === 'edit' && this.addressId > 0) {
            //     axios
            //         .get('/json/crm' + this.addressId + '.json')
            //         .then(response => {
            //             let data = response.data;
            //             for (let d in data) {
            //                 this[d] = data[d];
            //             }
            //             this.setInfoBox(data);
            //         });
            // }
        },

        methods: {
            createAddress() {
                if (this.$refs.form.validate()) {
                    let self = this;
                    const formData = new FormData();
                    formData.append('street', this.dataForm.street);
                    formData.append('postcode', this.dataForm.postcode);
                    formData.append('city', this.dataForm.city);
                    formData.append('extra', this.dataForm.extra);

                    axios.post('/web/v1/addresses', formData).then(function (response) {
                        let data = response.data;
                        self.snackBarText = data.test + " -> ADDRESS Added Successfully";
                        self.snackBar = Math.random();

                        self.$refs.form.reset();
                        self.$refs.form.resetValidation();

                    }).catch(function (error) {
                        console.log(error);
                    });
                }
            }
        }
    }
</script>
