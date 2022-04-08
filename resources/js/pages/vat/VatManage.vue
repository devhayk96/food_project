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
                    {{ $route.query.action }} Vat
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
                            <v-text-field
                                label="Code"
                                name="code"
                                prepend-icon="mdi-account-switch"
                                type="text"
                                v-model="dataDetails.vat_code"
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
                            <v-switch
                                v-model="dataDetails.is_sales"
                                inset
                                :label="`Sales`"
                            ></v-switch>
                        </v-col>

                        <v-col cols="3" md="3">
                            <v-switch
                                v-model="dataDetails.is_purchase"
                                inset
                                :label="`Purchase`"
                            ></v-switch>
                        </v-col>
                        <v-col cols="3" md="3">
                            <v-switch
                                v-model="dataDetails.is_blocked"
                                inset
                                :label="`Blocked`"
                            ></v-switch>
                        </v-col>

                        <v-col cols="12" md="12">
                            <v-text-field
                                label="Percentage"
                                name="percentage"
                                prepend-icon="mdi-account-switch"
                                type="number"
                                v-model="dataDetails.percentage"
                                :rules="textRules"
                                required
                            ></v-text-field>
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
        name: "VatManage",
        props: ['id'],
        components: {
        },
        data() {
            return {
                dataDetails: {
                    id:0,
                    name: '',
                    vat_code: '',
                    description: '',
                    percentage: 0,
                    is_sales: 0,
                    is_purchase: 0,
                    is_blocked: 0,
                },
                textRules: [
                    v => !!v || 'Field is required'
                ],
                savePermission: 'vats-create'
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
                this.savePermission = 'vats-update';
                this.fetchDetails();
            }
        },

        beforeDestroy() {
        },

        methods: {
            ...mapActions('app', ['showSuccess', 'showError']),
            fetchDetails: function () {
                const _this = this;
                axios.get('/web/v1/vat/'+this.$route.query.id)
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
                axios.post('/web/v1/vat', this.dataDetails)
                    .then(function (response) {
                        console.log(response);
                        if(response.data) {
                            _this.showSuccess('Data is updated.');
                            _this.$refs.form.reset();
                            _this.$refs.form.resetValidation();
                            _this.$router.push({name: 'vats'});
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

                axios.put('/web/v1/vat/' + this.dataDetails.id, this.dataDetails)
                    .then(function (response) {
                        console.log(response);
                        if(response.data) {
                            _this.showSuccess('Data is updated.');
                            _this.$refs.form.reset();
                            _this.$refs.form.resetValidation();
                            _this.$router.push({name:'vats'});
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
