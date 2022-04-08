<template>
    <v-container>
        <v-card height="100%" width="100%">
            <v-container style="height: 400px;" v-if="$route.query.action === 'edit' && dataDetails.name === ''">
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
            <v-form ref="form" v-else>
                <v-card-title class="text-capitalize">
                    <v-btn
                        class="mx-2"
                        dark
                        outlined
                        color="primary"
                        @click="$router.go(-1)"
                    >
                        <v-icon small dark>
                            mdi-arrow-left
                        </v-icon>
                        &nbsp;
                        Go Back
                    </v-btn>
                    {{ $route.query.action }} Product Group
                </v-card-title>

                <v-card-text>
                    <v-row>
                        <v-col cols="12" md="12">
                            <v-text-field
                                label="Number"
                                name="number"
                                prepend-icon="mdi-account-switch"
                                type="text"
                                v-model="dataDetails.number"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="12">
                            <v-text-field
                                label="Name"
                                name="name"
                                prepend-icon="mdi-account-switch"
                                type="text"
                                v-model="dataDetails.name"
                                :rules="textRules"
                                required
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" md="12">
                            <v-textarea
                                label="Description"
                                name="description"
                                prepend-icon="mdi-web"
                                type="textarea"
                                v-model="dataDetails.description"
                                :rules="textRules"
                                required
                            ></v-textarea>
                        </v-col>

                        <v-col cols="12" md="12">
                            <v-text-field
                                label="Kitchen 1"
                                name="kitchen_1"
                                prepend-icon="mdi-web"
                                type="textarea"
                                v-model="dataDetails.kitchen_1_id"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" md="12">
                            <v-text-field
                                label="Kitchen 2"
                                name="kitchen_2"
                                prepend-icon="mdi-web"
                                type="textarea"
                                v-model="dataDetails.kitchen_2_id"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" md="12">
                            <v-text-field
                                label="Kitchen 3"
                                name="kitchen_3"
                                prepend-icon="mdi-web"
                                type="textarea"
                                v-model="dataDetails.kitchen_3_id"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="3" md="3">
                            <v-switch
                                v-model="dataDetails.is_blocked"
                                inset
                                :label="`Blocked`"
                            ></v-switch>
                        </v-col>
                        <v-col cols="3" md="3">
                            <v-switch
                                v-model="dataDetails.is_food"
                                inset
                                :label="`Food`"
                            ></v-switch>
                        </v-col>
                        <v-col cols="3" md="3">
                            <v-switch
                                v-model="dataDetails.is_drink"
                                inset
                                :label="`Drink`"
                            ></v-switch>
                        </v-col>

                        <v-col cols="12" md="12">
                            <v-text-field
                                label="Weight"
                                name="weight"
                                prepend-icon="mdi-web"
                                type="number"
                                v-model="dataDetails.weight"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="12" v-if="dataDetails.image !== null && dataDetails.image.trim() !== ''">
                            <label class="text--secondary">
                                <v-icon>
                                    mdi-camera
                                </v-icon>
                                Image
                            </label>
                            <div class="d-flex">
                                <v-col cols="4">
                                    <v-img
                                        max-height="160"
                                        max-width="300"
                                        :src="dataDetails.image"
                                    />
                                </v-col>
                                <v-col cols="4">
                                    <v-btn
                                        class="mx-2"
                                        outlined
                                        color="red"
                                        @click="()=>{dataDetails.image = null}">Remove</v-btn>
                                </v-col>
                            </div>
                        </v-col>
                        <v-col cols="12" md="12" v-else>
                            <v-file-input
                                label="Image"
                                name="image"
                                v-model="dataDetails.image"
                                accept="image/png, image/jpeg, image/jpg"
                                prepend-icon="mdi-camera"
                            ></v-file-input>
                        </v-col>

                    </v-row>
                </v-card-text>

                <v-card-actions class="float-right mt-4">
                    <v-btn
                        v-if="userCan(savePermission)"
                        absolute
                        dark
                        bottom
                        right
                        large
                        color="success"
                        @click="saveDetails"
                    >
                        SAVE
                    </v-btn>
                </v-card-actions>

            </v-form>
        </v-card>
    </v-container>
</template>

<script>

    import {mapActions} from "vuex";

    export default {
        name: "ProductGroupManage",
        props: ['id'],
        components: {
        },
        data() {
            return {
                dataDetails: {
                    id:0,
                    number: '',
                    name: '',
                    description: '',
                    kitchen_1_id: '',
                    kitchen_2_id: '',
                    kitchen_3_id: '',
                    is_blocked: 0,
                    is_food: 0,
                    is_drink: 0,
                    weight: 0,
                    image: null,
                },
                textRules: [
                    v => !!v || 'Field is required'
                ],
                savePermission: 'product_groups-create'
            }
        },
        created() {
            if(this.$route.query.action === 'add' || (this.$route.query.action === 'edit' && this.$route.query.id)) {
                console.log('working');
            }
            else{
                this.showError({message:"Error : ", error: {message:"The query params are incorrect."}});
                this.$router.go(-1);
            }
        },
        mounted() {
            if(this.$route.query.action === 'edit'){
                this.savePermission = 'product_groups-update';
                this.fetchDetails();
            }
        },

        beforeDestroy() {
        },

        methods: {
            ...mapActions('app', ['showSuccess', 'showError']),
            fetchDetails: function () {
                const _this = this;
                axios.get('/web/v1/group/'+this.$route.query.id)
                    .then(function (response) {
                        console.log(response);
                        _this.dataDetails = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                        _this.showError({message:"Error : ", error: error});
                    });
            },
            saveDetails: function () {
                const _this = this;
                if (this.$refs.form.validate()) {
                    if(_this.$route.query.action === 'edit'){
                        this.updateData();
                    }
                    else{
                        this.addData()
                    }
                }
                else{
                    _this.showError({message:"Error : ", error: {message:"Please fill the mandatory fields."}});
                }
            },
            addData: function () {
                const _this = this;
                const formData = new FormData();
                Object.keys(this.dataDetails).forEach(key => {
                    formData.append(key, this.dataDetails[key] ===null? "":this.dataDetails[key]===true?1:this.dataDetails[key]);
                });
                axios.post('/web/v1/group', formData)
                    .then(function (response) {
                        console.log(response);
                        if(response.data) {
                            _this.showSuccess('Data is updated.');
                            _this.$refs.form.reset();
                            _this.$refs.form.resetValidation();
                            _this.$router.push({name: 'product-group-list'});
                        }
                        else{
                            _this.showError({message:"Error : ", error: {message:"Something Went Wrong."}});
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                        _this.showError({message:"Error : ", error: error});
                    });
            },
            updateData: function () {
                const _this = this;
                const formData = new FormData();
                Object.keys(this.dataDetails).forEach(key => {
                    formData.append(key, this.dataDetails[key] ===null? "":this.dataDetails[key]===true?1:this.dataDetails[key]);
                });
                // formData.append('_method', 'POST');
                axios.post('/web/v1/group/' + this.dataDetails.id, formData)
                    .then(function (response) {
                        console.log(response);
                        if(response.data) {
                            _this.showSuccess('Data is updated.');
                            _this.$refs.form.reset();
                            _this.$refs.form.resetValidation();
                            _this.$router.push({name:'product-group-list'});
                        }
                        else{
                            _this.showError({message:"Error : ", error: {message:"Something Went Wrong."}});
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                        _this.showError({message:"Error : ", error: error});
                    });
            }
        }
    }
</script>
