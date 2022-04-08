<template>
    <v-container>
        <v-card height="100%" width="100%">
            <v-form ref="form">
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
                    {{ this.dataDetails.id >0? 'Edit': 'Add' }} Menu
                </v-card-title>

                <v-card-text>
                    <v-row>
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

                        <v-col cols="3" md="3">
                            <v-menu
                                :close-on-content-click="true"
                                :nudge-right="40"
                                transition="scale-transition"
                                offset-y
                                min-width="auto"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="dataDetails.start_date"
                                        label="Start Date"
                                        prepend-icon="mdi-calendar"
                                        readonly
                                        v-bind="attrs"
                                        v-on="on"
                                        clearable
                                    ></v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="dataDetails.start_date"
                                ></v-date-picker>
                            </v-menu>
                        </v-col>
                        <v-col cols="3" md="3">
                            <v-menu
                                :close-on-content-click="true"
                                :nudge-right="40"
                                transition="scale-transition"
                                offset-y
                                min-width="auto"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="dataDetails.end_date"
                                        label="End Date"
                                        prepend-icon="mdi-calendar"
                                        readonly
                                        v-bind="attrs"
                                        v-on="on"
                                        clearable
                                    ></v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="dataDetails.end_date"
                                    :min="dataDetails.start_date"
                                ></v-date-picker>
                            </v-menu>
                        </v-col>
                        <v-col cols="3" md="3">
                            <v-switch
                                v-model="dataDetails.is_blocked"
                                inset
                                :label="`Blocked`"
                            ></v-switch>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-col cols="12" md="12">
                    <shops-source
                    :getSources="getSources"
                    :saveEvent="saveEvent"
                    :cancelEvent="Math.random()"
                    />
                </v-col>
                <v-card-actions class="float-right mt-5">
                    <v-btn
                        dark
                        bottom
                        left
                        large
                        color="error"
                        class="mr-5"
                        @click="$router.go(-1)"
                    >
                        &nbsp;
                        Cancel
                    </v-btn>
                    <v-btn
                        v-if="userCan(savePermission)"
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

    import moment from 'moment';
    import {mapActions} from "vuex";
    import ShopsSource from '../../../components/menuconfig/ShopsSource.vue';

    export default {
        name: "MenuManage",
        props: ['id'],
        components: {
            ShopsSource
        },
        data() {
            return {
                dataDetails: {
                    id:0,
                    name: '',
                    description: '',
                    is_blocked: 0,
                    start_date: null,
                    end_date: null,
                },
                saveEvent:0,
                getSources:false,

                textRules: [
                    v => !!v || 'Field is required'
                ],
                savePermission: 'menus-create'
            }
        },
        created() {
            this.getSources = true;
            if(this.id === undefined) {
                this.dataDetails.id = this.$route.query.id;
            }
            else{
                this.dataDetails.id = this.id;
            }
        },
        mounted() {
            console.log(this.dataDetails.id);
            if(this.dataDetails.id>0){
                this.savePermission = 'menus-update';
                this.fetchDetails()
            }
        },

        beforeDestroy() {
        },

        methods: {
            ...mapActions('app', ['showSuccess', 'showError']),
            fetchDetails: function () {
                const _this = this;
                axios.get('/web/v1/menu/'+this.dataDetails.id)
                    .then(function (response) {
                        console.log(response);
                        _this.dataDetails = response.data;
                        _this.dataDetails.start_date = response.data.start_date?moment(response.data.start_date).format('YYYY-MM-DD'):response.data.start_date;
                        _this.dataDetails.end_date = response.data.end_date?moment(response.data.end_date).format('YYYY-MM-DD'):response.data.end_date;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            saveDetails: function () {
                const _this = this;
                if (this.$refs.form.validate()) {
                    if(_this.dataDetails.id>0){
                        this.updateData();
                        this.saveEvent = Math.random();
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
                axios.post('/web/v1/menu', formData)
                    .then(function (response) {
                        console.log(response);
                        if(response.data) {
                            _this.showSuccess('Data is updated.');
                            _this.$refs.form.reset();
                            _this.$refs.form.resetValidation();
                            _this.saveEvent = response.data.id;
                            _this.$router.push({name:'menu-list'});
                        }
                        else{
                            _this.showError({message:"Error : ", error: {message:"Something Went Wrong."}});
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                        _this.showError({message:'Error: ', error: error});
                    });
            },
            updateData: function () {
                const _this = this;
                const formData = new FormData();
                Object.keys(this.dataDetails).forEach(key => {
                    formData.append(key, this.dataDetails[key] ===null? "":this.dataDetails[key]===true?1:this.dataDetails[key]);
                });
                // formData.append('_method', 'POST');
                axios.put('/web/v1/menu/' + this.dataDetails.id, this.dataDetails)
                    .then(function (response) {
                        console.log(response);
                        if(response.data) {
                            _this.showSuccess('Data is updated.');
                            _this.$refs.form.reset();
                            _this.$refs.form.resetValidation();
                            _this.$router.push({name:'menu-list'});
                        }
                        else{
                            _this.showError({message:"Error : ", error: {message:"Something Went Wrong."}});
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                        _this.showError({message:'Error: ', error: error});
                    });
            }
        }
    }
</script>
