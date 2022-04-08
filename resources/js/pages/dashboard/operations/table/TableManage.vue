<template>
    <v-container>
        <v-card height="100%" width="100%">
            <v-container style="height: 400px;" v-if="this.dataDetails.id >0 && dataDetails.name === ''">
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
                    {{ this.dataDetails.id >0? 'Edit': 'Add' }} Table
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
                                label="Number"
                                name="number"
                                prepend-icon="mdi-account-switch"
                                type="text"
                                v-model="dataDetails.number"
                                :rules="textRules"
                                required
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" md="12">
                            <v-select
                                label="Shop"
                                name="shop"
                                prepend-icon="mdi-select"
                                :loading="shopList === ''"
                                :items="_.map(shopList, ((cat)=>{ return {text:cat.name, value:cat.id}}))"
                                v-model="dataDetails.shop_id"
                            ></v-select>
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
        name: "TableManage",
        props: ['id'],
        components: {
        },
        data() {
            return {
                dataDetails: {
                    id:0,
                    name: '',
                    number: '',
                    shop_id: '',
                    is_blocked: false,
                },
                shopList: '',
                textRules: [
                    v => !!v || 'Field is required'
                ],
                savePermission: 'tables-create'
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
                this.savePermission = 'tables-update';
                this.fetchDetails()
            }
            this.fetchShopList();
        },

        beforeDestroy() {
        },

        methods: {
            ...mapActions('app', ['showSuccess', 'showError']),
            fetchShopList: function () {
                const _this = this;
                axios.get('/web/v1/shops')
                    .then(function (response) {
                        console.log(response);
                        _this.shopList = response.data.data;
                    })
                    .catch(function (resp) {
                        console.log(resp);
                    });
            },
            fetchDetails: function () {
                const _this = this;
                axios.get('/web/v1/table/'+this.dataDetails.id)
                    .then(function (response) {
                        console.log(response);
                        _this.dataDetails = response.data.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                        console.log(error);
                        _this.showError({message:'Error: ', error: error});
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
                axios.post('/web/v1/table', this.dataDetails)
                    .then(function (response) {
                        console.log(response);
                        if(response.data) {
                            _this.showSuccess('Data is updated.');
                            _this.$refs.form.reset();
                            _this.$refs.form.resetValidation();
                            _this.$router.push({name: 'table-list'});
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

                axios.put('/web/v1/table/' + this.dataDetails.id, this.dataDetails)
                    .then(function (response) {
                        console.log(response);
                        if(response.data) {
                            _this.showSuccess('Data is updated.');
                            _this.$refs.form.reset();
                            _this.$refs.form.resetValidation();
                            _this.$router.push({name:'table-list'});
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
