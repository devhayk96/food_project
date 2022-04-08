<template>
    <div>
        <v-form
        ref="form"
        v-model="valid"
        lazy-validation
        @submit.prevent="submit(cardProgress.data.event)">
            <v-list-item>
                <v-text-field
                    required
                    :rules="p_codeRules"
                    v-model="p_code"
                    label="POS CODE"
                ></v-text-field>
                <div class="red--text" v-if="errors.code">{{ errors.code }}</div>
            </v-list-item>
            <v-list-item>
                <v-text-field
                    required
                    :rules="nameRules"
                    v-model="name"
                    label="NAME"
                ></v-text-field>
                <div class="red--text" v-if="errors.name">{{ errors.name }}</div>
            </v-list-item>
            <v-list-item>
                <v-text-field
                    required
                    :rules="client_class_rules"
                    v-model="client_class"
                    label="CLIENT"
                ></v-text-field>
                <div class="red--text" v-if="errors.client_class">{{ errors.client_class }}</div>
            </v-list-item>
            <v-list-item>
                <v-text-field
                    required
                    :rules="credentials_class_rules"
                    v-model="credentials_class"
                    label="CREDENTIALS"
                ></v-text-field>
                <div class="red--text" v-if="errors.credentials_class">{{ errors.credentials_class }}</div>
            </v-list-item>
            <v-list-item>
                <v-switch
                    inset
                    v-model="is_active"
                    label="Active"
                    hide-details
                ></v-switch>
                <div class="red--text" v-if="errors.is_active">{{ errors.is_active }}</div>
            </v-list-item>
            <v-list-item class="pt-3">
                <v-autocomplete
                    v-model="selectedShops"
                    @change="addShop"
                    :search-input.sync="filter.name"
                    item-text="name"
                    item-value="id"
                    :eager="true"
                    :items="shopsList"
                    prepend-inner-icon="mdi-magnify"
                    label="Shop"
                    multiple
                    outlined
                    dense
                    class="search-input"
                >
                    <template v-slot:prepend-item>
                        <v-list-item ripple>
                            <v-row class="mt-0 font-12">
                                <div class="col-5 pt-0 pb-0"><span>Name</span></div>
                                <div class="col-5 pt-0 pb-0"><span>Email</span></div>
                                <div class="col-2 pt-0 pb-0 pt-1"><span></span></div>
                            </v-row>
                        </v-list-item>
                        <v-divider class="mt-2"></v-divider>
                    </template>
                    <template v-slot:item='{item}'>
                        <v-row class="font-12">
                            <div class="col-5 pt-0 pb-0">{{ item.name }}</div>
                            <div class="col-5 pt-0 pb-0">{{ item.email }}</div>
                            <div class="col-2 pt-0 pb-0 pt-1">
                                <v-img v-if="!selectedShops.includes(item.id)"  src="/images/custom/add.svg" alt="" />
                                <v-img v-if="selectedShops.includes(item.id)"  src="/images/custom/add_success.svg" alt="" />
                            </div>
                        </v-row>
                    </template>
                </v-autocomplete>
                <div class="red--text" v-if="errors.shop_ids">{{ errors.shop_ids }}</div>
            </v-list-item>
            <v-list-item class="mt-2">
                <v-btn
                    type="submit"
                    color="success">
                    <v-icon
                        small
                        class="mr-1">
                        {{
                            cardProgress.data  &&
                            cardProgress.data.event == 'edit-source-type'? 'mdi-content-save-edit':
                            'mdi-content-save-all'
                        }}
                    </v-icon>
                    {{ BTNname }}
                </v-btn>
            </v-list-item>
        </v-form>
    </div>
</template>

<script>
import {mapActions} from "vuex";

export default {
    name: 'CreateEditSourceType',

    props:{
        cardProgress:{
            type: Object,
            required: true
        }
    },

    data:() => ({
        filter: {
            per_page: 25,
            name: '',
        },
        errors: {},
        initialErrors: {
            code: null,
            name: null,
            client_class: null,
            credentials_class: null,
            is_active: null,
            shop_ids: null,
        },
        valid: true,
        p_code: '',
        p_codeRules: [
            i => !!i || 'Code field is required'
        ],
        name: '',
        nameRules: [
            i => !!i || 'Name field is required'
        ],
        client_class: '',
        client_class_rules: [
            i => !!i || 'Client field is required'
        ],
        credentials_class: '',
        credentials_class_rules: [
            i => !!i || 'Credentials field is required'
        ],
        selectedShops: [],
        shopRules: [
            i => !!i || 'Shop field is required'
        ],
        shopsList: [],
        BTNname: 'CREATE',
        is_active: false
    }),
    mounted() {
        this.getShopsList();
        this.errors = {...this.initialErrors};
    },
    methods: {
        ...mapActions('app', ['showSuccess', 'showError']),
        getShopsList() {
            axios.get('/web/v1/shops').then( ({data}) => {
                this.shopsList = data.data;
            })
        },
        showValidationErrors(errors) {
            for (let [key, value] of Object.entries(errors)) {
                this.errors[key] = value[0];
            }

            this.showError({ message: '', error: {message: "Please check the form below for errors" } });
        },
        submit(e) {
            this.$refs.form.validate();
            this.errors = {...this.initialErrors};

            let data = {
                progress: e,
                name: this.name,
                code: this.p_code,
                shop_ids: this.selectedShops,
                is_active: this.is_active,
                client_class: this.client_class,
                credentials_class: this.credentials_class
            };

            if (e === 'edit-source-type') {
                data.id = this.cardProgress.data.editable.id;
            }

            axios.post('/web/v1/create-edit-order-source-type', data)
                .then(response => {
                    if (response.data.success) {
                        if (e === 'create-source-type') {
                            this.clearData();
                            this.$refs.form.reset();
                            this.$refs.form.resetValidation();
                            this.$emit('refreshData', {e: 'create'});
                        } else if (e === 'edit-source-type') {
                            this.$refs.form.resetValidation();
                            this.$emit('refreshData', {e: 'edit'})
                        }
                    }
                }).catch( error => {
                    if (error.response.status === 422) {
                        this.showValidationErrors(error.response.data.errors);
                    } else {
                        this.showError({message: "Error : ", error: {message: "Something Went Wrong."}});
                    }
                    console.log(error);
                });
        },

        clearData() {
            this.p_code = '';
            this.name = '';
            this.client_class = '';
            this.credentials_class = '';
            this.is_active = false;
        },

        addShop(shops) {
            this.selectedShops = shops;
        }
    },

watch: {
    selectedShops(val) {
        console.log(val);
    },
    cardProgress() {
        this.errors = {...this.initialErrors};
        if(this.cardProgress?.data?.event == 'edit-source-type') {
            this.BTNname = 'EDIT';
            const editable = this.cardProgress.data.editable;
            this.id = editable.id;
            this.p_code = editable.code;
            this.credentials_class = editable.credentials_class;
            this.is_active = editable.is_active == 1 ? Boolean(1) : Boolean(0);
            this.name = editable.name;
            this.selectedShops = this.cardProgress.data.editable.shop_ids;
            this.client_class = editable.client_class;
        }else if(this.cardProgress?.data?.event == 'create-source-type') {
            this.$refs.form.resetValidation();
            this.BTNname = 'CREATE';
            this.p_code = '';
            this.name = '';
            this.selectedShops = [];
            this.client_class = '';
            this.credentials_class = '';
            this.is_active = false;
        }
    }
}

}
</script>

<style lang="css" scoped>

.v-text-field >>> input {
    font-size: 0.9em;
    font-weight: 100;
}
.v-text-field >>> label {
    font-size: 0.9em;
}

.v-form .v-list-item {
    display: block;
}
.v-autocomplete {
    border: none;
}

.v-autocomplete .v-input__slot {
    position: absolute;
    top: -33px;
    width: 464px;
    max-height: 40px;
    min-height: 40px;
}

.v-autocomplete .v-input__control .v-text-field__details {
    display: none;
}

.v-autocomplete .v-select__slot .v-label.theme--light {
    top: 10px;
}

.v-autocomplete .v-input__slot .v-input__prepend-inner,
.v-autocomplete .v-select__slot .v-input__append-inner {
    margin-top: 10px;
}

.v-autocomplete__content .theme--light.v-list-item:not(.v-list-item--active):not(.v-list-item--disabled) {
    color: rgb(0, 0, 0) !important;
    font-weight: 600;
}

</style>
