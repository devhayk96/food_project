<template>
    <div style="width: 100%; height: 100%">
        <v-container style="height: 400px;" v-if="$store.state.optionGroup.dataDetails.id > 1 && $store.state.optionGroup.dataDetails.name === ''">
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
                @click="$router.push({name:'optional-group-list'})"
            >
                <v-icon class="back-btn-arrow">
                    mdi-arrow-left
                </v-icon>
                Go Back
            </v-btn>

            <v-form  ref="form">
               <div class="form-send-container">
                   <div class="send-form">
                       <v-card-title class="text-capitalize">
                           {{ $store.state.optionGroup.dataDetails.id > 0 ? 'Edit': 'Add' }} Option Group
                       </v-card-title>
                       <v-card-text>
                           <v-tabs-items v-model="tab" class="form-content">
                               <v-tab-item>
                                   <v-row>
                                       <v-col class="pb-0" cols="12" md="12">
                                           <v-text-field
                                               label="Number"
                                               name="number"
                                               class="form-inp"
                                               type="text"
                                               v-model="$store.state.optionGroup.dataDetails.number"
                                           ></v-text-field>
                                           <div class="red--text" v-if="errors.number">{{ errors.number }}</div>
                                       </v-col>
                                       <v-col class="pt-0 pb-0" cols="12" md="12">
                                           <v-text-field
                                               label="Name"
                                               name="name"
                                               type="text"
                                               v-model="$store.state.optionGroup.dataDetails.name"
                                               :rules="textRules"
                                               required
                                           ></v-text-field>
                                           <div class="red--text" v-if="errors.name">{{ errors.name }}</div>
                                       </v-col>
                                       <v-col class="pt-0 pb-0" cols="12" md="12">
                                           <v-textarea
                                               label="Description"
                                               name="description"
                                               type="textarea"
                                               class="form-description"
                                               v-model="$store.state.optionGroup.dataDetails.description"
                                               :rules="textRules"
                                               required
                                           ></v-textarea>
                                           <div class="red--text" v-if="errors.description">{{ errors.description }}</div>
                                       </v-col>
                                       <v-col class="form-pd" cols="12" md="12">
                                           <v-row class="ma-0">
                                               <v-col cols="3" md="3">
                                                   <v-checkbox
                                                       v-model="$store.state.optionGroup.dataDetails.remarks"
                                                       label="Remarks"
                                                   ></v-checkbox>
                                                   <div class="red--text" v-if="errors.remarks">{{ errors.remarks }}</div>
                                               </v-col>
                                               <v-col cols="3" md="3">
                                                   <v-checkbox
                                                       v-model="$store.state.optionGroup.dataDetails.is_active"
                                                       label="Active"
                                                   ></v-checkbox>
                                                   <div class="red--text" v-if="errors.is_active">{{ errors.is_active }}</div>
                                               </v-col>
                                               <v-col cols="3" md="3">
                                                   <v-checkbox
                                                       v-model="$store.state.optionGroup.dataDetails.no_discount"
                                                       label="No Discount"
                                                   ></v-checkbox>
                                                   <div class="red--text" v-if="errors.no_discount">{{ errors.no_discount }}</div>
                                               </v-col>
                                               <v-col cols="3" md="3">
                                                   <v-checkbox
                                                       v-model="$store.state.optionGroup.dataDetails.is_optional"
                                                       label="Optional"
                                                   ></v-checkbox>
                                                   <div class="red--text" v-if="errors.is_optional">{{ errors.is_optional }}</div>
                                               </v-col>
                                           </v-row>
                                       </v-col>
                                       <v-col class="pt-0" cols="6" md="6">
                                           <v-select
                                               label="Type"
                                               name="type"
                                               @change="changeMaxNumbersSelectBox"
                                               :items="$store.state.optionGroup.types"
                                               v-model="$store.state.optionGroup.dataDetails.type"
                                               :rules="textRules"
                                               required
                                           ></v-select>
                                           <div class="red--text" v-if="errors.type">{{ errors.type }}</div>
                                       </v-col>
                                       <v-col class="pt-0" cols="6" md="6">
                                           <v-select
                                               label="Maximum number of selections"
                                               name="max_selections_number"
                                               prepend-icon="mdi-select"
                                               :items="$store.state.optionGroup.typeLimitNumbers"
                                               v-model="$store.state.optionGroup.dataDetails.type_limit"
                                               :rules="textRules"
                                               required
                                           ></v-select>
                                           <div class="red--text" v-if="errors.type_limit">{{ errors.type_limit }}</div>
                                       </v-col>
                                   </v-row>
                               </v-tab-item>
                           </v-tabs-items>
                       </v-card-text>
                   </div>
                   <div class="upload-image">
                       <v-col cols="12" md="12" v-if="$store.state.optionGroup.dataDetails.image !== null && $store.state.optionGroup.dataDetails.image.trim() !== ''">
                           <label class="text--secondary">
                               <v-icon>
                                   mdi-camera
                               </v-icon>
                               Image
                           </label>
                           <div class="d-flex">
                               <v-col class="pl-0" cols="4">
                                   <v-img
                                       max-height="160"
                                       max-width="300"
                                       :src="$store.state.optionGroup.dataDetails.image"
                                   />
                               </v-col>
                               <v-col cols="4">
                                   <v-btn
                                       class="mx-2"
                                       outlined
                                       color="red"
                                       @click="()=>{$store.state.optionGroup.dataDetails.image = null}">Remove</v-btn>
                               </v-col>
                           </div>
                       </v-col>
                       <v-col cols="12" md="12" v-else>
                           <v-img
                               max-width="300"
                               class="form-select-img"
                               src="/images/upload-image.svg"
                               @click="chooseFile"
                           ></v-img>
                           <v-file-input
                               label="Image"
                               name="image"
                               id="file-upload"
                               hide-input
                               :prepend-icon="null"
                               v-model="$store.state.optionGroup.dataDetails.image"
                               accept="image/png, image/jpeg, image/jpg"
                           ></v-file-input>
                       </v-col>
                       <div class="red--text" v-if="errors.image">{{ errors.image }}</div>
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
                            v-if="userCan(savePermission)"
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
        name: "OptionalGroupManage",
        props: ['id'],
        components: {
            // SnackBar,
        },
        data() {
            return {
                errors: {
                    name: null,
                    number: null,
                    description: null,
                    type: null,
                    type_limit: null,
                    remarks: null,
                    is_active: null,
                    is_optional: null,
                    no_discount: null,
                    image: null,
                },
                tab: 0,
                search: '',
                selectedProductIds: [],
                textRules: [
                    v => !!v || 'Field is required'
                ],
                savePermission: 'optional_groups-create'
            }
        },
        created() {
            if (this.id === undefined) {
                this.$store.state.optionGroup.dataDetails.id = this.$route.query.id;
            } else {
                this.$store.state.optionGroup.dataDetails.id = this.id;
            }
        },
        mounted() {
            if (this.$store.state.optionGroup.dataDetails.id > 0) {
                this.savePermission = 'optional_groups-update';
                this.fetchOptionGroupDetails();
            } else {
                this.setInitialDataDetails();
            }
            this.getOptionalGroupTypes();
        },

        beforeDestroy() {
        },

        methods: {
            ...mapActions('app', ['showSuccess', 'showError']),
            ...mapActions('optionGroup', ['fetchOptionGroupDetails', 'getOptionalGroupTypes']),
            ...mapMutations('optionGroup', ['setInitialDataDetails']),
            saveDetails() {
                if (this.$refs.form.validate()) {
                    if (this.$store.state.optionGroup.dataDetails.id > 0) {
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
                const formData = new FormData();
                Object.keys(this.$store.state.optionGroup.dataDetails).forEach(key => {
                    let value = this.$store.state.optionGroup.dataDetails[key] === null ? ""
                        : (this.$store.state.optionGroup.dataDetails[key] === true ? 1
                        : (this.$store.state.optionGroup.dataDetails[key] === false ? 0 : this.$store.state.optionGroup.dataDetails[key]));
                    formData.append(key, value);
                });
                axios.post('/web/v1/optional-group', formData)
                    .then( response => {
                        if (response.data) {
                            this.$store.state.optionGroup.dataDetails.id = response.data.id;
                             this.showSuccess('Data is updated.');
                             this.$refs.form.reset();
                             this.$refs.form.resetValidation();
                             this.$router.push({name: 'optional-group-list'});
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
                const formData = new FormData();
                Object.keys(this.$store.state.optionGroup.dataDetails).forEach(key => {
                    let value = this.$store.state.optionGroup.dataDetails[key] === null ? ""
                        : (this.$store.state.optionGroup.dataDetails[key] === true ? 1
                        : (this.$store.state.optionGroup.dataDetails[key] === false ? 0 : this.$store.state.optionGroup.dataDetails[key]));
                    formData.append(key, value);
                });
                // formData.append('_method', 'POST');
                axios.post('/web/v1/optional-group/' + this.$store.state.optionGroup.dataDetails.id, formData)
                    .then( response => {
                        if (response.data) {
                            this.showSuccess('Data is updated.');
                            this.$refs.form.reset();
                            this.$refs.form.resetValidation();
                            this.$router.push({name:'optional-group-list'});
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
            changeMaxNumbersSelectBox(first_time) {
                this.$store.state.optionGroup.typeLimitNumbers = this.$store.state.optionGroup.typeLimits[this.$store.state.optionGroup.dataDetails.type];
                this.$store.state.optionGroup.dataDetails.type_limit = first_time ? this.$store.state.optionGroup.dataDetails.type_limit : this.$store.state.optionGroup.typeLimitNumbers[0].value;
            },
            showValidationErrors(errors) {
                for (let [key, value] of Object.entries(errors)) {
                    this.errors[key] = value[0];
                }

                this.showError({ message: '', error: {message: "Please check the form below for errors" } });
            },
            chooseFile() {
                document.getElementById('file-upload').click();
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
