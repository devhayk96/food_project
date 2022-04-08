<template>
    <div class="d-flex flex-grow-1 flex-column">
        <v-row class="flex-grow-0" dense>
            <v-col cols="12">
                <v-card>
                    <v-card-title>
                        {{ $t('shop.shop_details') }} - {{ getShop.name }}
                    </v-card-title>
                    <v-card-text>
                        <v-tabs v-model="tabs" grow>
                            <v-tabs-slider></v-tabs-slider>
                            <v-tab href="#home"><v-icon class="mr-3">mdi-home</v-icon>{{ $t('shop.home') }}</v-tab>
                            <v-tab href="#opening-hours" disabled><v-icon class="mr-3">mdi-clock-time-eight</v-icon>{{ $t('shop.opening_hours') }}</v-tab>
                            <v-tab href="#delivery-hours" disabled><v-icon class="mr-3">mdi-clock-time-four</v-icon>{{ $t('shop.delivery_hours') }}</v-tab>
                            <v-tab href="#api" disabled><v-icon class="mr-3">mdi-cloud-tags</v-icon>API</v-tab>
                        </v-tabs>

                        <v-tabs-items v-model="tabs">
                            <v-tab-item :value="'home'">
                                <v-card flat class="mt-7">
                                    <v-card-text>
                                        <v-form ref="homeForm">
                                            <v-row>
                                                <v-col md="4" cols="12">
                                                    <v-text-field
                                                        :label="$t('shop.name') | capitalize"
                                                        name="name"
                                                        prepend-icon="mdi-account"
                                                        type="text"
                                                        v-model="getShop.name"
                                                        :rules="textRules"
                                                        required
                                                    ></v-text-field>
                                                </v-col>
                                                <v-col md="4" cols="12">
                                                    <v-text-field
                                                        :label="$t('shop.address') | capitalize"
                                                        name="address"
                                                        prepend-icon="mdi-map-marker"
                                                        type="text"
                                                        v-model="getShop.address.street"
                                                        :rules="textRules"
                                                        required
                                                    ></v-text-field>
                                                </v-col>
                                                <v-col md="4" cols="12">
                                                    <v-text-field
                                                        :label="$t('shop.address') + ' 2' | capitalize"
                                                        name="address2"
                                                        prepend-icon="mdi-map-marker"
                                                        type="text"
                                                        v-model="getShop.address.street_extra"
                                                    ></v-text-field>
                                                </v-col>
                                            </v-row>

                                            <v-row>
                                                <v-col md="4" cols="12">
                                                    <v-text-field
                                                        :label="$t('shop.post_code') | capitalize"
                                                        name="postalcode"
                                                        prepend-icon="mdi-map-marker"
                                                        type="text"
                                                        v-model="getShop.address.postcode"
                                                        :rules="textRules"
                                                        required
                                                    ></v-text-field>
                                                </v-col>
                                                <v-col md="4" cols="12">
                                                    <v-text-field
                                                        :label="$t('shop.city') | capitalize"
                                                        name="city"
                                                        prepend-icon="mdi-map-marker"
                                                        type="text"
                                                        v-model="getShop.address.city"
                                                        :rules="textRules"
                                                        required
                                                    ></v-text-field>
                                                </v-col>
                                                <v-col md="4" cols="12">
                                                    <v-text-field
                                                        :label="$t('shop.country') | capitalize"
                                                        name="country"
                                                        prepend-icon="mdi-map-marker"
                                                        type="text"
                                                        v-model="getShop.address.country"
                                                        :rules="textRules"
                                                        required
                                                    ></v-text-field>
                                                </v-col>
                                            </v-row>

                                            <v-row>
                                                <v-col md="4" cols="12">
                                                    <v-text-field
                                                        :label="$t('shop.telephone') | capitalize"
                                                        name="telephone"
                                                        prepend-icon="mdi-phone"
                                                        type="text"
                                                        v-model="getShop.phone"
                                                    ></v-text-field>
                                                </v-col>
                                                <v-col md="4" cols="12">
                                                    <v-text-field
                                                        :label="$t('shop.email') | capitalize"
                                                        name="email"
                                                        prepend-icon="mdi-email"
                                                        type="text"
                                                        v-model="getShop.email"
                                                        :rules="emailRules"
                                                    ></v-text-field>
                                                </v-col>
                                                <v-col md="4" cols="12">
                                                    <v-text-field
                                                        :label="$t('shop.chamber_commerce') | capitalize"
                                                        name="chamber"
                                                        prepend-icon="mdi-receipt"
                                                        type="text"
                                                        v-model="getShop.company_number"
                                                    ></v-text-field>
                                                </v-col>
                                            </v-row>

                                            <v-row>
                                                <v-col md="4" cols="12">
                                                    <v-text-field
                                                        :label="$t('shop.vat') | capitalize"
                                                        name="vat"
                                                        prepend-icon="mdi-receipt"
                                                        type="text"
                                                        v-model="getShop.vat"
                                                    ></v-text-field>
                                                </v-col>
                                                <v-col md="4" cols="12">
                                                    <v-text-field
                                                        :label="$t('shop.iban')"
                                                        name="iban"
                                                        prepend-icon="mdi-bank"
                                                        type="text"
                                                        v-model="getShop.iban"
                                                        :rules="ibanRules"
                                                    ></v-text-field>
                                                </v-col>
                                                <v-col md="2" cols="6" class="d-flex justify-center">
                                                    <v-switch
                                                        dense
                                                        inset
                                                        v-model="getShop.is_active"
                                                        :label="getShop.is_active
                                                        ? $options.filters.capitalize($t('shop.active'))
                                                        : $options.filters.capitalize($t('shop.inactive'))"
                                                    ></v-switch>
                                                </v-col>
                                                <v-col md="2" cols="6" class="d-flex justify-center">
                                                    <v-switch
                                                        dense
                                                        inset
                                                        v-model="getShop.is_visible"
                                                        :label="getShop.is_visible
                                                        ? $options.filters.capitalize($t('shop.visible'))
                                                            :$options.filters.capitalize($t('shop.hidden'))"
                                                    ></v-switch>
                                                </v-col>
                                            </v-row>

                                        </v-form>
                                    </v-card-text>

                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn :loading="loadingSave"
                                               :disabled="loadingSave"
                                               depressed
                                               color="primary"
                                               @click="saveHomeForm">
                                            {{ $t('common.save') | uppercase }}
                                        </v-btn>
                                    </v-card-actions>

                                </v-card>
                            </v-tab-item>
                        </v-tabs-items>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script>
    import iban from "iban";
    import { mapState, mapActions } from "vuex";

    export default {
        name: "ShopDetails",
        props: {

        },
        data () {
            return {
                snackBar: false,
                snackBarText: '',
                color: "success",
                timeoutSnackBar: 4000,
                tabs: null,
                loadingSave: null,
                forceShop: false,
                getShop: {
                    name: "",
                    phone: "",
                    email: "",
                    company_number: "",
                    vat: "",
                    iban: null,
                    address: {
                        city: "",
                        street: "",
                        country: "",
                        postcode: "",
                        street_extra: ""
                    },
                    is_open: 0,
                    is_delivering: 0,
                    is_active: 1,
                    is_visible: 1
                },
                // validation
                textRules: [v => !!v || this.$t('validations.field_required')],
                ibanRules: [
                    v => !v ? true : iban.isValid(v) || this.$t('validations.invalid_iban')
                ],
                emailRules: [
                    v => !v ? true : /.+@.+\..+/.test(v) || this.$t('validations.invalid_email'),
                ],
            }
        },

        created() {

        },

        watch: {

        },

        computed: {
            ...mapState('app', ['shop']),
        },

        methods: {
            ...mapActions('app', ['showSuccess', 'showError']),

            saveHomeForm () {
                if (this.$refs.homeForm.validate()) {
                    let dataUpdate = {
                        name: this.getShop.name,
                        address_street: this.getShop.address.street,
                        address_street_extra: this.getShop.address.street_extra,
                        address_postcode: this.getShop.address.postcode,
                        address_city: this.getShop.address.city,
                        address_country: this.getShop.address.country,
                        phone: this.getShop.phone,
                        email: this.getShop.email,
                        company_number: this.getShop.company_number,
                        vat: this.getShop.vat,
                        iban: this.getShop.iban,
                        is_open: this.getShop.is_open,
                        is_delivering: this.getShop.is_delivering,
                        is_active: this.getShop.is_active,
                        is_visible: this.getShop.is_visible
                    }
                    this.loadingSave = true;
                    let self = this;
                    axios.post('/web/v1/shops', dataUpdate).then(function (response) {
                        console.log(response);
                        // self.loadingSave = false;
                        self.showSuccess('Save completed successfully');
                        self.$router.push({ name: 'dashboard-administrations-shop-details', params: { shopFromTable: response.data.data }});
                    })
                        .catch(function (error) {
                            // console.log(error.message);
                            self.loadingSave = false;
                            self.showError({message:'Failed!', error: error});
                        });

                } else {
                    this.$nextTick(() => {
                        const el = this.$refs.homeForm.$el.querySelector(".v-messages.error--text:first-of-type");
                        this.$vuetify.goTo(el, {offset: 75});
                    });
                }
            },
        }
    }
</script>

<style scoped>
    .theme--light.v-sheet--outlined {
        border: thin solid rgba(0,0,0,.15);
    }

    .pc-24-22 {
        padding-right: 22px !important;
        padding-left: 24px !important;
        padding-bottom: 80px;
    }

    .action-card-api {
        position: absolute;
        bottom: 0;
        right: 0;
    }
</style>
