<template>
    <div style="width: 100%; height: 100%">
        <v-container style="height: 400px;" v-if="$store.state.device.dataDetails.id > 1 && $store.state.device.dataDetails.name === ''">
            <v-row
                class="fill-height"
                align-content="center"
                justify="center"
            >
                <v-col
                    class="subtitle-1 text-center"
                    cols="12"
                >
                    Fetching data...
                </v-col>
                <v-col cols="6">
                    <v-progress-linear
                        color="primary accent-4"
                        indeterminate
                        rounded
                        height="6"
                    ></v-progress-linear>
                </v-col>
            </v-row>
        </v-container>

        <v-card v-else height="100%" width="100%">
            <v-btn
                dark
                large
                class="back-btn"
                color="custom"
                @click="$router.push({ name:'device-list' })"
            >
                <v-icon class="back-btn-arrow">
                    mdi-arrow-left
                </v-icon>
                Go Back
            </v-btn>

            <v-form ref="form">
               <div class="form-send-container">
                   <div class="send-form">
                       <v-card-title class="text-capitalize">
                           {{ $store.state.device.dataDetails.id > 0 ? 'Edit': 'Add' }} Device
                       </v-card-title>
                       <v-card-text>
                           <v-row>
                               <v-col cols="8" md="8">
                                   <v-text-field
                                       label="Name"
                                       name="name"
                                       type="text"
                                       v-model="$store.state.device.dataDetails.name"
                                       :rules="textRules"
                                       required
                                   ></v-text-field>
                                   <div class="red--text" v-if="errors.name">{{ errors.name }}</div>
                               </v-col>
                           </v-row>

                           <v-row v-if="$store.state.device.dataDetails.id > 0" class="d-flex align-center">
                               <v-col cols="5" md="5">
                                   <v-text-field
                                       label="Code"
                                       name="code"
                                       type="text"
                                       v-model="$store.state.device.dataDetails.code"
                                       :rules="textRules"
                                       required
                                   ></v-text-field>
                                   <div class="red--text" v-if="errors.code">{{ errors.code }}</div>
                               </v-col>
                               <v-col cols="3" md="3">
                                   <v-btn
                                       dark
                                       color="primary"
                                       @click="generateNewCode"
                                   >Generate
                                   </v-btn>
                               </v-col>
                           </v-row>

                           <v-row>
                               <v-col cols="8" md="8">
                                   <v-select
                                       label="Shop"
                                       name="shop_id"
                                       prepend-icon="mdi-select"
                                       :items="$store.state.shop.shopsList"
                                       item-text="name"
                                       item-value="id"
                                       v-model="$store.state.device.dataDetails.shop_id"
                                       required
                                   ></v-select>
                                   <div class="red--text" v-if="errors.shop_id">{{ errors.shop_id }}</div>
                               </v-col>
                           </v-row>

                           <v-row>
                               <v-col cols="8" md="8">
                                   <v-select
                                       label="Orders Auto Refresh Time"
                                       name="orders_refresh_time"
                                       prepend-icon="mdi-select"
                                       :items="refreshTimeOptions"
                                       v-model="$store.state.device.dataDetails.orders_auto_refresh_time"
                                   ></v-select>
                                   <div class="red--text" v-if="errors.orders_auto_refresh_time">{{ errors.orders_auto_refresh_time }}</div>
                               </v-col>
                           </v-row>

                           <v-row>
                               <v-col cols="8" md="8">
                                   <v-select
                                       label="Finished Orders Delay Time"
                                       name="finished_orders_delay_time"
                                       prepend-icon="mdi-select"
                                       :items="refreshTimeOptions"
                                       v-model="$store.state.device.dataDetails.finished_orders_delay_time"
                                   ></v-select>
                                   <div class="red--text" v-if="errors.finished_orders_delay_time">{{ errors.finished_orders_delay_time }}</div>
                               </v-col>
                           </v-row>
                       </v-card-text>
                   </div>
               </div>
                <div class="save-button">
                    <v-col cols="12" class="pt-5 pl-0 pb-3">
                        <v-btn
                            class="chancel-btn"
                            color="#4C4C4C"
                            rounded
                            text
                            x-large
                        >CANCEL</v-btn>
                        <v-btn
                            class="save-btn"
                            rounded
                            x-large
                            v-if="userCan('device-create')"
                            @click="saveDetails"
                        >SAVE</v-btn>
                    </v-col>
                </div>
            </v-form>
        </v-card>
    </div>
</template>

<script>
    import {mapActions, mapMutations } from "vuex";

    export default {
        name: "DeviceManage",
        props: ['id'],
        components: {

        },
        data() {
            return {
                errors: {
                    name: null,
                },
                search: '',
                textRules: [
                    v => !!v || 'Field is required'
                ],
                refreshTimeOptions: [
                    { 'text': '5 sec', 'value': 5000 },
                    { 'text': '10 sec', 'value': 10000 },
                    { 'text': '15 sec', 'value': 15000 },
                    { 'text': '20 sec', 'value': 20000 },
                    { 'text': '25 sec', 'value': 25000 },
                    { 'text': '30 sec', 'value': 30000 },
                    { 'text': '35 sec', 'value': 35000 },
                    { 'text': '40 sec', 'value': 40000 },
                    { 'text': '45 sec', 'value': 45000 },
                    { 'text': '50 sec', 'value': 50000 },
                    { 'text': '55 sec', 'value': 55000 },
                    { 'text': '60 sec', 'value': 60000 },
                ],
            }
        },
        created() {
            if (this.id === undefined) {
                this.$store.state.device.dataDetails.id = this.$route.query.id;
            } else {
                this.$store.state.device.dataDetails.id = this.id;
            }
        },
        mounted() {
            if (this.$store.state.device.dataDetails.id > 0) {
                this.savePermission = 'device-update';
                this.fetchDeviceDetails();
            } else {
                this.setInitialDataDetails();
            }
        },

        beforeDestroy() {
        },

        methods: {
            ...mapActions('app', ['showSuccess', 'showError']),
            ...mapActions('device', ['fetchDeviceDetails']),
            ...mapMutations('device', ['setInitialDataDetails']),
            saveDetails() {
                if (this.$refs.form.validate()) {
                    if (this.$store.state.device.dataDetails.id > 0) {
                        this.updateData();
                    } else {
                        this.addData()
                    }
                } else {
                    this.showError({message: "Error : ", error: {message: "Please fill the mandatory fields."}});
                }
            },
            addData() {
                this.errors = [];
                axios.post('/web/v1/device', this.$store.state.device.dataDetails)
                    .then( response => {
                        if (response.data.status === 'success') {
                            this.setInitialDataDetails();
                            this.showSuccess('Device is created.');
                            this.$refs.form.reset();
                            this.$refs.form.resetValidation();
                            this.$router.push({name:'device-list'});
                        } else {
                            this.showError({ message: "Error : ", error: { message: "Something Went Wrong." } });
                        }
                    })
                    .catch( error => {
                        if (error.response.status === 422) {
                            this.showValidationErrors(error.response.data.errors);
                        } else {
                            this.showError({ message:'Error: ', error: error });
                        }
                    });
            },
            updateData() {
                this.errors = [];
                axios.put('/web/v1/device/' + this.$store.state.device.dataDetails.id, this.$store.state.device.dataDetails)
                    .then( response => {
                        if (response.data.status === 'success') {
                            this.showSuccess('Data is updated.');
                            this.$refs.form.reset();
                            this.$refs.form.resetValidation();
                            this.$router.push({name:'device-list'});
                        } else {
                            this.showError({message: "Error : ", error: {message: "Something Went Wrong."}});
                        }
                    })
                    .catch( error => {
                        if (error.response.status === 422) {
                            this.showValidationErrors(error.response.data.errors);
                        } else {
                            this.showError({ message:'Error: ', error: error });
                        }
                    });
            },
            showValidationErrors(errors) {
                let errs = [];
                for (let [key, value] of Object.entries(errors)) {
                    errs[key] = value[0];
                }
                this.errors = errs;
                this.showError({ message: '', error: {message: "Please check the form below for errors" } });
            },
            generateNewCode() {
                this.errors = [];
                axios.get('/web/v1/device/generate-code')
                    .then( response => {
                        this.$store.state.device.dataDetails.code = response.data;
                    })
                    .catch( error => {
                        let errorMessage = {
                            message: 'Generate code issue.',
                            error: {
                                message: 'Something went wrong.'
                            }
                        };
                        this.showError(errorMessage)
                    });
            }
        }
    }
</script>

<style>
    .back-btn {
        width: 133px;
        height: 64px;
        margin: 30px;
        background: transparent !important;
        color: #0096C7 !important;
        border: 1px solid #0096C7;
    }
    .form-send-container {
        margin-left: 230px;
        display: flex;
        align-items: center;
    }
    .text-capitalize {
        margin: 0 0 20px;
    }
    .send-form{
        width: 48%;
    }
    .upload-image {
        width: 50%;
    }
    #input-170 {
        line-height: 0;
    }
    .mdi-camera::before {
        /*content: url(http://name.png);*/
    }
    .save-button {
        margin-left: 230px;
    }
    .form-pd {
        padding: 0;
    }
    .v-input--selection-controls__ripple {
        display: none;
    }
    #input-163 {
        height: 50px !important;
    }
    .save-btn {
        width: 160px;
        height: 42px !important;
        font: normal normal 600 14px/18px Quicksand;
        background: #0096C7 !important;
        color: #ffffff !important;
        letter-spacing: 1.4px;
    }
    .chancel-btn {
        margin-right: 30px;
        font: normal normal bold 14px/18px Quicksand;
        letter-spacing: 1.4px;
        color: #4C4C4C;
        opacity: 1;
    }
    .form-select-img {
        cursor: pointer;
        margin-left: 100px;
    }
    #input-157,
    #input-160,
    #input-182 {
        min-height: 37px;
    }

    .back-btn-arrow {
        font-size: 16px !important;
        margin-right: 10px;
    }
    .form-description textarea {
        height: 48px !important;
    }
</style>
