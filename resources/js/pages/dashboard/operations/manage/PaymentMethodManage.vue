<template>
    <v-container>
        <v-card height="100%" width="100%">
            <v-form ref="form">
                <v-card-title class="text-capitalize">
                    {{ this.dataDetails.id >0? 'Edit': 'Add' }} Payment Method
                </v-card-title>

                <v-card-text>
                    <v-row>
                        <v-col cols="12" md="12">
                            <v-text-field
                                label="Code"
                                name="code"
                                prepend-icon="mdi-account-switch"
                                type="text"
                                v-model="dataDetails.code"
                                :disabled="dataDetails.id > 0 && dataDetails.parent_id === null && dataDetails.source_type_id === 1"
                                required
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
                            <v-select
                                label="Source Type"
                                name="sourceType"
                                prepend-icon="mdi-select"
                                :loading="sourceTypeList === ''"
                                :items="_.map(sourceTypeList, ((cat)=>{ return {text: cat.name,value: cat.id}}))"
                                v-model="dataDetails.source_type_id"
                                :rules="textRules"
                                required
                                :disabled="dataDetails.id > 0 && dataDetails.parent_id === null && dataDetails.source_type_id === 1"
                            ></v-select>
                        </v-col>

                        <v-col cols="12" md="12">
                            <v-select
                                label="Parent"
                                name="parent"
                                prepend-icon="mdi-select"
                                :loading="paymentMethodList === ''"
                                :items="_.map(paymentMethodList, ((cat)=>{ return cat.parent_id === null?{text: cat.name,value: cat.id}: {text:'None', value:null}}))"
                                v-model="dataDetails.parent_id"
                                :disabled="dataDetails.id > 0 && dataDetails.parent_id === null && dataDetails.source_type_id === 1"
                            ></v-select>
                        </v-col>

                        <v-col cols="3" md="3">
                            <v-switch
                                v-model="dataDetails.is_active"
                                inset
                                :label="`Active`"
                                :disabled="dataDetails.id > 0 && dataDetails.parent_id === null && dataDetails.source_type_id === 1"
                            ></v-switch>
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
        name: "PaymentMethodManage",
        props: ['id'],
        components: {
        },
        data() {
            return {
                dataDetails: {
                    id:0,
                    code: '',
                    source_type_id:'',
                    name: '',
                    parent_id: null,
                    is_active: 1,
                },
                sourceTypeList: '',
                paymentMethodList: '',
                textRules: [
                    v => !!v || 'Field is required'
                ],
                savePermission: 'payment_methods-create'
            }
        },
        created() {
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
                this.savePermission = 'payment_methods-update';
                this.fetchDetails();
            }
            this.fetchSourceTypeList();
            this.fetchPaymentMethodList();
        },

        beforeDestroy() {
        },

        methods: {
            ...mapActions('app', ['showSuccess', 'showError']),
            fetchSourceTypeList: function () {
                const _this = this;
                axios.get('/web/v1/order-source-types')
                    .then(function (response) {
                        console.log(response);
                        _this.sourceTypeList = response.data.data;
                    })
                    .catch(function (resp) {
                        console.log(resp);
                    });
            },
            fetchPaymentMethodList: function () {
                const _this = this;
                axios.get('/web/v1/payment-methods')
                    .then(function (response) {
                        console.log(response);
                        _this.paymentMethodList = response.data.data;
                    })
                    .catch(function (resp) {
                        console.log(resp);
                    });
            },
            fetchDetails: function () {
                const _this = this;
                axios.get('/web/v1/payment-methods/'+this.dataDetails.id)
                    .then(function (response) {
                        console.log(response);
                        _this.dataDetails = response.data.data;
                        _this.dataDetails.source_type_id = _this.dataDetails.source_type.id;
                        _this.dataDetails.parent_id = _this.dataDetails.parent?_this.dataDetails.parent.id: null;
                    })
                    .catch(function (error) {
                        console.log(error);
                        _this.showError({message:error.message, error: error});
                    });
            },
            saveDetails: function () {
                const _this = this;
                if (this.$refs.form.validate()) {
                    if(_this.dataDetails.id>0){
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
                // const formData = new FormData();
                Object.keys(this.dataDetails).forEach(key => {
                    this.dataDetails[key] = this.dataDetails[key] ===null? "":this.dataDetails[key]===true?1:this.dataDetails[key];
                    // formData.append(key, this.dataDetails[key] ===null? "":this.dataDetails[key]===true?1:this.dataDetails[key]);
                });
                axios.post('/web/v1/payment-methods', this.dataDetails)
                    .then(function (response) {
                        console.log(response);
                        if(response.data) {
                            _this.showSuccess('Data is updated.');
                            _this.$refs.form.reset();
                            _this.$refs.form.resetValidation();
                            _this.$router.push({name:'dashboard-operations-payment-methods'});
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
                // const formData = new FormData();
                Object.keys(this.dataDetails).forEach(key => {
                    // formData.append(key, this.dataDetails[key] ===null? "":this.dataDetails[key]===true?1:this.dataDetails[key]);
                    this.dataDetails[key] = this.dataDetails[key] ===null? "":this.dataDetails[key]===true?1:this.dataDetails[key];
                });
                axios.put('/web/v1/payment-methods/' + this.dataDetails.id, this.dataDetails)
                    .then(function (response) {
                        console.log(response);
                        if(response.data) {
                            _this.showSuccess('Data is updated.');
                            _this.$refs.form.reset();
                            _this.$refs.form.resetValidation();
                            _this.$router.push({name:'dashboard-operations-payment-methods'});
                        }
                        else{
                            _this.showError({message:"Error : ", error: {message:"Something Went Wrong."}});
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                        _this.showError({message:"Error: ", error: error});
                    });
            }
        }
    }
</script>
