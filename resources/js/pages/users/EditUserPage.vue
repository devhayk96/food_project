<template>
    <div class="flex-grow-1">
        <div class="d-flex align-center py-3">
            <div>
                <div class="display-1">{{ actionText }} {{ userText }} {{ user.name && `- ${user.name}` }}</div>
                <v-breadcrumbs :items="breadcrumbs" class="pa-0 py-2"></v-breadcrumbs>
            </div>
            <v-spacer></v-spacer>
            <v-btn icon @click="fetchDetails()">
                <v-icon>mdi-refresh</v-icon>
            </v-btn>
        </div>

        <v-card class="my-2">
            <v-card-text>
                <v-form ref="form" v-model="isFormValid" lazy-validation>
                    <v-row>
                        <v-col cols="12" md="12">
                            <v-text-field
                                label="Name"
                                name="name"
                                v-model="user.name"
                                :rules="[rules.required, rules.maxlength(user.name, 255)]"
                                :validate-on-blur="false"
                                :error="error || typeof validationErrors.name != 'undefined'"
                                :error-messages="validationErrors.name"
                                @keyup="resetErrors"
                            ></v-text-field>
                            <v-text-field
                                label="Email"
                                name="email"
                                v-model="user.email"
                                :rules="[rules.required, rules.email, rules.maxlength(user.email, 255)]"
                                :validate-on-blur="false"
                                :error="error || typeof validationErrors.email != 'undefined'"
                                :error-messages="validationErrors.email"
                                @keyup="resetErrors"
                            ></v-text-field>
                            <v-text-field
                                :label="'Password' + passwordHelpText"
                                name="password"
                                :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                                :type="showPassword ? 'text' : 'password'"
                                @click:append="showPassword = !showPassword"
                                v-model="user.password"
                                :rules="user.id ? [rules.minlength(user.password, 6), rules.maxlength(user.password, 255)]
                                    : [rules.required, rules.minlength(user.password, 6), rules.maxlength(user.password, 255)]"
                                :validate-on-blur="false"
                                :error="error || typeof validationErrors.password != 'undefined'"
                                :error-messages="validationErrors.password"
                                @keyup="resetErrors"
                            ></v-text-field>
                            <div class="pin-code-area">
                                <v-radio-group v-model="user.pin_code_length" :label="'Pin Code Length'" class="d-flex" style="margin-right: 20px;">
                                    <v-radio
                                        v-for="n in $store.state.app.pinCodePossibleLengths"
                                        :key="n"
                                        :label="`${n}`"
                                        :value="n"
                                    ></v-radio>
                                </v-radio-group>
                                <v-text-field
                                    :label="'Pin Code' + pinCodeHelpText"
                                    name="pin_code"
                                    minlength="4"
                                    maxlength="6"
                                    @click:append="showPinCode = !showPinCode"
                                    v-model="user.pin_code"
                                    :validate-on-blur="false"
                                    :error="typeof validationErrors.pin_code != 'undefined'"
                                    :error-messages="validationErrors.pin_code"
                                    @keypress="checkValidCharacter($event)"
                                    @change="resetErrors"
                                ></v-text-field>
                                <v-btn
                                    dark
                                    color="primary"
                                    @click="generateNewPinCode"
                                >Generate
                                </v-btn>
                            </div>
                            <div class="select-shops-area">
                                <v-autocomplete
                                    v-model="user.shop_ids"
                                    :search-input.sync="filter.name"
                                    item-text="name"
                                    item-value="id"
                                    :eager="true"
                                    name="shop_ids"
                                    :items="$store.state.shop.shopsList"
                                    prepend-inner-icon="mdi-magnify"
                                    label="Shops"
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
                                                <div class="col-2 pt-0 pb-0"><span></span></div>
                                            </v-row>
                                        </v-list-item>
                                        <v-divider class="mt-2"></v-divider>
                                    </template>
                                    <template v-slot:item='{item}'>
                                        <v-row class="font-12">
                                            <div class="col-5 pt-0 pb-0">{{ item.name }}</div>
                                            <div class="col-5 pt-0 pb-0">{{ item.email }}</div>
                                            <div class="col-2 pt-0 pb-0">
                                                <v-img v-if="user.shop_ids > 0 && user.shop_ids.indexOf(item.id) !== -1"  src="/images/custom/add_success.svg" alt="" />
                                                <v-img v-else src="/images/custom/add.svg" alt="" />
                                            </div>
                                        </v-row>
                                    </template>
                                </v-autocomplete>
                            </div>
                            <v-text-field
                                label="Phone"
                                name="phone"
                                v-model="user.phone"
                                :rules="[rules.minlength(user.phone, 6), rules.maxlength(user.phone, 25)]"
                                :validate-on-blur="false"
                                :error="error || typeof validationErrors.phone != 'undefined'"
                                :error-messages="validationErrors.phone"
                                @keyup="resetErrors"
                            ></v-text-field>
                            <v-select
                                label="Country"
                                name="country_id"
                                :items="allCountries"
                                item-text="name"
                                item-value="id"
                                v-model="user.country_id"
                                menu-props="auto"
                                :error="typeof validationErrors.country_id != 'undefined'"
                                :error-messages="validationErrors.country_id"
                                @change="resetErrors"
                            ></v-select>
                            <v-text-field
                                label="City"
                                name="city"
                                v-model="user.city"
                                :rules="[rules.maxlength(user.city, 255)]"
                                :validate-on-blur="false"
                                :error="error || typeof validationErrors.city != 'undefined'"
                                :error-messages="validationErrors.city"
                                @keyup="resetErrors"
                            ></v-text-field>
                            <v-text-field
                                label="ZIP Code"
                                name="zip"
                                v-model="user.zip"
                                :rules="[rules.minlength(user.zip, 2), rules.maxlength(user.zip, 25)]"
                                :validate-on-blur="false"
                                :error="error || typeof validationErrors.zip != 'undefined'"
                                :error-messages="validationErrors.zip"
                                @keyup="resetErrors"
                            ></v-text-field>
                            <v-text-field
                                label="Address Line 1"
                                name="address_1"
                                v-model="user.address_1"
                                :rules="[rules.maxlength(user.address_1, 255)]"
                                :validate-on-blur="false"
                                :error="error || typeof validationErrors.address_1 != 'undefined'"
                                :error-messages="validationErrors.address_1"
                                @keyup="resetErrors"
                            ></v-text-field>
                            <v-text-field
                                label="Address Line 2"
                                name="address_2"
                                v-model="user.address_2"
                                :rules="[rules.maxlength(user.address_2, 255)]"
                                :validate-on-blur="false"
                                :error="error || typeof validationErrors.address_2 != 'undefined'"
                                :error-messages="validationErrors.address_2"
                                @keyup="resetErrors"
                            ></v-text-field>
                            <v-select
                                v-if="!profile && roles.length"
                                label="Role"
                                name="attached_roles"
                                :items="roles"
                                item-text="display_name"
                                item-value="name"
                                v-model="user.attached_roles"
                                menu-props="auto"
                                :rules="[rules.required]"
                                :validate-on-blur="false"
                                :error="error || typeof validationErrors.role != 'undefined'"
                                :error-messages="validationErrors.role"
                                @change="resetErrors"
                            ></v-select>
                            <v-select
                                label="Status"
                                name="status"
                                :items="allStatuses"
                                item-text="text"
                                item-value="status"
                                v-model="user.status"
                                menu-props="auto"
                                :rules="[rules.required]"
                                :validate-on-blur="false"
                                :error="error || typeof validationErrors.status != 'undefined'"
                                :error-messages="validationErrors.status"
                                @change="resetErrors"
                            ></v-select>
                        </v-col>
                    </v-row>

                    <div class="d-flex">
                        <v-btn @click="resetForm">Reset</v-btn>
                        <v-spacer></v-spacer>
                        <v-btn color="primary" @click="submit">Save</v-btn>
                    </div>
                </v-form>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import CopyLabel from '../../components/common/CopyLabel'
import AccountTab from './EditUser/AccountTab'
import InformationTab from './EditUser/InformationTab'
import {mapActions} from "vuex";

export default {
    components: {
        CopyLabel,
        AccountTab,
        InformationTab
    },
    data() {
        let profile = false;
        let userText = '';
        if ((typeof localStorage.authUser != 'undefined' && JSON.parse(localStorage.authUser).id == this.$route.query.id)
            || typeof this.$route.query.id == 'undefined') {
            userText = 'Profile';
        } else {
            userText = 'User';
        }

        let userUrl = '';
        let putUserUrl = '';
        let redirectUrl = '';
        let actionText = '';
        let passwordHelpText = '';
        let pinCodeHelpText = '';
        let breadcrumbs = '';
        if (this.$route.query.id == 0) {
            userUrl = '/web/v1/users/create';
            actionText = 'Create';
            passwordHelpText = '';
            pinCodeHelpText = '';
        } else {
            if (typeof this.$route.query.id == 'undefined') {
                userUrl = '/web/v1/user/profile-edit';
            } else {
                userUrl = '/web/v1/users/' + this.$route.query.id + '/edit';
            }
            actionText = 'Edit';
            passwordHelpText = ' (if you don\'t want to change the password, leave the field empty)';
            pinCodeHelpText = ' (you can generate a new unique pin code by clicking the generate button)';
        }

        if (typeof this.$route.query.id == 'undefined') {
            profile = true;
            breadcrumbs = [{
                text: 'Edit ' + userText
            }];
            putUserUrl = '/web/v1/user/profile';
            redirectUrl = {name: 'profile'};
        } else {
            breadcrumbs = [
                {
                    text: 'Users',
                    to: '/users',
                    exact: true
                },
                {
                    text: actionText + ' ' + userText
                }
            ];
            putUserUrl = '/web/v1/users/' + this.$route.query.id;
            redirectUrl = {name: 'users-show', query: {id: this.$route.query.id}};
        }

        return {
            filter: {
                per_page: 25,
                name: '',
            },
            isFormValid: true,
            actionText: actionText,
            userText: userText,
            passwordHelpText: passwordHelpText,
            pinCodeHelpText: pinCodeHelpText,
            profile: profile,
            userUrl: userUrl,
            putUserUrl: putUserUrl,
            redirectUrl: redirectUrl,
            user: {},
            userDefault: {},
            rules: {
                required: (value) => (value && Boolean(value)) || 'The field is required',
                maxlength: (value, length) => (!value || (value && value.length <= length)) || 'The field length couldn\'t be greater than ' + length,
                email: (value) => {
                    const reg = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                    return (value && reg.test(value.toLowerCase())) || 'The email address isn\'t valid';
                },
                minlength: (value, length) => (!value || (value && value.length >= length)) || 'The field length couldn\'t be less than ' + length,
            },
            error: false,
            validationErrors: {},
            showPassword: false,
            showPinCode: false,
            allCountries: [],
            roles: [],
            allStatuses: [],
            tab: null,
            breadcrumbs: breadcrumbs,
        }
    },
    created() {
        this.user.id = this.$route.query.id;
    },
    mounted() {
        this.fetchDetails();
        this.getUserPinCodePossibleLengths();
    },
    methods: {
        ...mapActions('app', ['showSuccess', 'showError', 'getUserPinCodePossibleLengths']),

        fetchDetails() {
            axios.get(this.userUrl)
                .then( response => {
                    this.user = response.data.user;
                    this.userDefault = Object.assign({}, response.data.user);
                    this.roles = response.data.roles;
                    this.allCountries = response.data.countries;
                    this.allStatuses = response.data.all_statuses;
                })
                .catch(function (error) {
                    if (error.response && error.response.status === 404) {
                        this.$router.push({name: 'error-not-found'});
                    } else {
                        this.showError({message:'Something Went Wrong!', error: error})
                    }
                });
        },
        resetErrors() {
            this.error = false
            this.validationErrors = {};
        },
        resetForm: function () {
            this.user = Object.assign({}, this.userDefault);
        },
        showErrorMessage: function (error) {
            if (error.response.status === 422) {
                this.validationErrors = error.response.data.errors;
                this.showError({message: 'Please fix validation errors.', error: error});
            } else {
                const helpMessage = 'We couldn\'t save the user, please try again, then try after refreshing of the page or contact the dev team if that doesn\'t help.';
                this.showError({message: helpMessage, error: error});
            }
        },
        async submit() {
            if (this.$refs.form.validate()) {
                this.user.role = this.user.attached_roles;
                if (this.user.id) {
                    await axios.put(
                        this.putUserUrl,
                        this.user
                    ).then(response => {
                        if (response.status === 200) {
                            this.showSuccess(this.userText + ' has been updated successfully.');
                            setTimeout(() => this.$router.push(this.redirectUrl), 700);
                        } else {
                            this.showError({message: 'Something went wrong.'});
                        }
                    }).catch(error => {
                        this.showErrorMessage(error);
                    });
                } else {
                    await axios.post(
                        '/web/v1/users',
                        this.user
                    ).then(response => {
                        if (response.status === 200 && response.data.id) {
                            this.showSuccess('User has been created successfully.');
                            setTimeout(() => this.$router.push({name: 'users-show', query: {id: response.data.id}}), 700);
                        } else {
                            this.showError({message: 'Something went wrong.'});
                        }
                    }).catch(error => {
                        this.showErrorMessage(error);
                    });
                }
            }
        },
        generateNewPinCode() {
            this.resetErrors();
            axios.get(`/web/v1/user/generate-pin-code?pin_code_length=${this.user.pin_code_length}`)
                .then( response => {
                    this.user.pin_code = `${response.data}`;
                })
                .catch( error => {
                    let errorMessage = {
                        message: 'Generate pin code issue.',
                        error: {
                            message: 'Something went wrong.'
                        }
                    };
                    this.showError(errorMessage)
                });
        },
        checkValidCharacter(event) {
            event = (event) ? event : Window.event;
            let charCode = (event.which) ? event.which : event.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                console.log('tar e');
                event.preventDefault();
            } else {
                console.log('tar che');
                return true;
            }
        },
    }
}
</script>

<style lang="scss">
    .pin-code-area {
        width: 660px;
        display: flex;
        align-items: center;

        .v-btn {
            margin-left: 15px;
        }
    }

    .v-menu__content  .v-list-item--link .row  {
        align-items: center !important;
        padding: 0 15px;
        justify-content: space-between;
        margin-bottom: 3px;
        margin-top: 3px;
        font-size: 12px;
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

    .select-shops-area {
        padding: 50px 0 20px;
    }
</style>
