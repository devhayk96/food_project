<template>
    <v-card width="100%" height="100%" class="user--profile">
        <v-card-title class="text-capitalize">
            <v-btn class="mx-2" dark outlined color="primary" @click="$router.go(-1)">
                <v-icon small dark> mdi-arrow-left </v-icon>
                &nbsp; Go Back
            </v-btn>
        </v-card-title>
        <div class="size mt-4 mb-5">
            <div class="edit--view-user--profile pb-5">
                <div class="w-90 mx-auto">
                    <div class="contain">
                        <div class="heading">
                            <div class="d-flex align-items-center gap-15">
                                <img v-bind:src="avatar" alt="Avatar" class="avatar" />
                                <div class="d-flex hi-text">
                                    <p>Hello,</p>
                                    <h2>{{ user.name }}</h2>
                                </div>
                            </div>
                        </div>
                        <v-form
                            ref="form"
                            class="mt-3"
                            v-model="isFormValid"
                            lazy-validation
                        >
                            <div class="d-flex gap-10">
                                <div class="label">
                                    <v-subheader>ID</v-subheader>
                                </div>
                                <div class="field">
                                    <v-text-field
                                        outlined
                                        name="id"
                                        label="ID"
                                        readonly
                                        single-line
                                        v-model="user.id"
                                        :rules="[rules.required]"
                                        :validate-on-blur="false"
                                        :error="error || typeof validationErrors.id != 'undefined'"
                                        :error-messages="validationErrors.id"
                                        @keyup="resetErrors"
                                    ></v-text-field>
                                </div>
                            </div>
                            <div class="d-flex gap-10">
                                <div class="label">
                                    <v-subheader>Name</v-subheader>
                                </div>
                                <div class="field">
                                    <v-text-field
                                        outlined
                                        single-line
                                        label="Name"
                                        name="name"
                                        v-model="user.name"
                                        @click:append="copy(user.name)"
                                        append-icon="mdi-content-copy"
                                        :rules="[rules.required, rules.maxlength(user.name, 255)]"
                                        :validate-on-blur="false"
                                        :error="
                      error || typeof validationErrors.name != 'undefined'
                    "
                                        :error-messages="validationErrors.name"
                                        @keyup="resetErrors"
                                    ></v-text-field>
                                </div>
                            </div>
                            <div class="d-flex gap-10">
                                <div class="label">
                                    <v-subheader>Email</v-subheader>
                                </div>
                                <div class="field">
                                    <v-text-field
                                        outlined
                                        single-line
                                        label="Email"
                                        name="email"
                                        v-model="user.email"
                                        :rules="[
                      rules.required,
                      rules.email,
                      rules.maxlength(user.email, 255),
                    ]"
                                        :validate-on-blur="false"
                                        :error="
                      error || typeof validationErrors.email != 'undefined'
                    "
                                        :error-messages="validationErrors.email"
                                        @keyup="resetErrors"
                                    ></v-text-field>
                                </div>
                            </div>
                            <div class="d-flex gap-10">
                                <div class="label">
                                    <v-subheader>Phone</v-subheader>
                                </div>
                                <div class="field phone-field-style position-relative">
                                    <v-text-field
                                        outlined
                                        single-line
                                        label="Phone"
                                        name="phone"
                                        class="phone"
                                        v-model="user.phone"
                                        :rules="[
                      rules.minlength(user.phone, 6),
                      rules.maxlength(user.phone, 25),
                    ]"
                                        :validate-on-blur="false"
                                        :error="
                      error || typeof validationErrors.phone != 'undefined'
                    "
                                        :error-messages="validationErrors.phone"
                                        @keyup="resetErrors"
                                    >
                                    </v-text-field>
                                </div>
                            </div>
                            <div class="d-flex gap-10">
                                <div class="label">
                                    <v-subheader>Pin Code</v-subheader>
                                </div>
                                <div class="field">
                                    <v-text-field
                                        outlined
                                        single-line
                                        label="Pin Code"
                                        name="pin_code"
                                        v-model="user.pin_code"
                                        @click:append="copy(user.pin_code)"
                                        append-icon="mdi-content-copy"
                                        @keypress="disablePinCodeChange($event)"
                                    ></v-text-field>
                                </div>
                            </div>
                            <div class="d-flex gap-10">
                                <div class="label">
                                    <v-subheader>Address</v-subheader>
                                </div>
                                <div class="field">
                                    <v-text-field
                                        outlined
                                        single-line
                                        label="Address Line 1"
                                        name="address_1"
                                        v-model="user.address_1"
                                        :rules="[rules.maxlength(user.address_1, 255)]"
                                        :validate-on-blur="false"
                                        :error="
                      error || typeof validationErrors.address_1 != 'undefined'
                    "
                                        :error-messages="validationErrors.address_1"
                                        @keyup="resetErrors"
                                    ></v-text-field>
                                </div>
                            </div>
                            <div class="d-flex gap-10">
                                <div class="label">
                                    <v-subheader>Address 2</v-subheader>
                                </div>
                                <div class="field">
                                    <v-text-field
                                        outlined
                                        single-line
                                        label="Address Line 2"
                                        name="address_2"
                                        v-model="user.address_2"
                                        :rules="[rules.maxlength(user.address_2, 255)]"
                                        :validate-on-blur="false"
                                        :error="
                      error || typeof validationErrors.address_2 != 'undefined'
                    "
                                        :error-messages="validationErrors.address_2"
                                        @keyup="resetErrors"
                                    ></v-text-field>
                                </div>
                            </div>
                            <div class="d-flex gap-10">
                                <div class="label">
                                    <v-subheader>Postcode</v-subheader>
                                </div>
                                <div class="field">
                                    <v-text-field
                                        outlined
                                        single-line
                                        label="ZIP Code"
                                        name="zip"
                                        v-model="user.zip"
                                        :rules="[
                      rules.minlength(user.zip, 2),
                      rules.maxlength(user.zip, 25),
                    ]"
                                        :validate-on-blur="false"
                                        :error="error || typeof validationErrors.zip != 'undefined'"
                                        :error-messages="validationErrors.zip"
                                        @keyup="resetErrors"
                                    ></v-text-field>
                                </div>
                            </div>
                            <div class="d-flex gap-10">
                                <div class="label">
                                    <v-subheader>City</v-subheader>
                                </div>
                                <div class="field">
                                    <v-text-field
                                        outlined
                                        single-line
                                        label="City"
                                        name="city"
                                        v-model="user.city"
                                        :rules="[rules.maxlength(user.city, 255)]"
                                        :validate-on-blur="false"
                                        :error="
                      error || typeof validationErrors.city != 'undefined'
                    "
                                        :error-messages="validationErrors.city"
                                        @keyup="resetErrors"
                                    ></v-text-field>
                                </div>
                            </div>

                            <div class="d-flex gap-10">
                                <div class="label">
                                    <v-subheader>Country</v-subheader>
                                </div>
                                <div class="field">
                                    <v-select
                                        outlined
                                        single-line
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
                                </div>
                            </div>
                            <div class="d-flex gap-10">
                                <div class="label">
                                    <v-subheader>Role</v-subheader>
                                </div>
                                <div class="field">
                                    <v-select
                                        outlined
                                        readonly
                                        single-line
                                        label="Role"
                                        name="attached_roles"
                                        :items="roles"
                                        item-text="display_name"
                                        item-value="name"
                                        v-model="user.attached_roles"
                                        menu-props="auto"
                                    ></v-select>
                                </div>
                            </div>
                            <!-- Status -->
                            <div class="d-flex gap-10">
                                <div class="label">
                                    <v-subheader>Status</v-subheader>
                                </div>
                                <div class="field">
                                    <v-select
                                        outlined
                                        single-line
                                        label="Status"
                                        name="status"
                                        :items="allStatuses"
                                        item-text="text"
                                        item-value="status"
                                        v-model="user.status"
                                        menu-props="auto"
                                        :rules="[rules.required]"
                                        :validate-on-blur="false"
                                        :error="
                      error || typeof validationErrors.status != 'undefined'
                    "
                                        :error-messages="validationErrors.status"
                                        @change="resetErrors"
                                    ></v-select>
                                </div>
                            </div>

                            <div class="d-flex gap-10">
                                <div class="label">
                                    <v-subheader>Last Login IP</v-subheader>
                                </div>
                                <div class="field">
                                    <v-text-field
                                        outlined
                                        readonly
                                        single-line
                                        label="Last Login IP"
                                        name="ip"
                                        item-text="text"
                                        item-value="ip"
                                        v-model="user.last_login_ip"
                                        menu-props="auto"
                                    ></v-text-field>
                                </div>
                            </div>

                            <div class="d-flex gap-10">
                                <div class="label">
                                    <v-subheader>Last Login Time</v-subheader>
                                </div>
                                <div class="field">
                                    <v-text-field
                                        outlined
                                        readonly
                                        single-line
                                        label="Last Login Time"
                                        name="lasLogin"
                                        item-text="text"
                                        item-value="lastLogin"
                                        v-model="user.last_login_at"
                                        menu-props="auto"
                                    ></v-text-field>
                                </div>
                            </div>

                            <div class="d-flex gap-10">
                                <div class="label">
                                    <v-subheader>New Password</v-subheader>
                                </div>
                                <div class="field">
                                    <v-text-field
                                        outlined
                                        single-line
                                        :label="'Password' + passwordHelpText"
                                        name="password"
                                        :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                                        :type="showPassword ? 'text' : 'password'"
                                        @click:append="showPassword = !showPassword"
                                        v-model="user.password"
                                        :rules="
                      user.id
                        ? [
                            rules.minlength(this.user.password, 6),
                            rules.maxlength(this.user.password, 255),
                          ]
                        : [
                            rules.required,
                            rules.minlength(this.user.password, 6),
                            rules.maxlength(this.user.password, 255),
                          ]
                    "
                                        :validate-on-blur="false"
                                        :error="
                      error || typeof validationErrors.password != 'undefined'
                    "
                                        :error-messages="validationErrors.password"
                                        @keyup="resetErrors"
                                    ></v-text-field>
                                </div>
                            </div>
                            <div class="d-flex gap-10">
                                <div class="label">
                                    <v-subheader>Repeat Password</v-subheader>
                                </div>
                                <div class="field">
                                    <v-text-field
                                        outlined
                                        single-line
                                        label="Repeat Password"
                                        name="repeatpassword"
                                        :append-icon="showRPassword ? 'mdi-eye' : 'mdi-eye-off'"
                                        :type="showRPassword ? 'text' : 'password'"
                                        @click:append="showRPassword = !showRPassword"
                                        v-model="repeatpassword"
                                        :rules="
                      user.id
                        ? [
                            rules.minlength(this.user.password, 6),
                            rules.maxlength(this.user.password, 255),
                            rules.confirmation(
                              this.user.password,
                              this.repeatpassword
                            ),
                          ]
                        : [
                            rules.required,
                            rules.minlength(this.user.password, 6),
                            rules.maxlength(this.user.password, 255),
                            rules.confirmation(
                              this.user.password,
                              this.repeatpassword
                            ),
                          ]
                    "
                                        :validate-on-blur="true"
                                        :error="
                      error ||
                      typeof validationErrors.repeatpassword != 'undefined'
                    "
                                        :error-messages="validationErrors.repeatpassword"
                                        @keyup="resetErrors"
                                    ></v-text-field>
                                </div>
                            </div>
                            <div class="d-flex gap-10">
                                <div class="label">
                                    <v-subheader>Language</v-subheader>
                                </div>
                                <div class="field position-relative lang-field-style">
                                    <v-select
                                        outlined
                                        single-line
                                        name="language"
                                        item-value="last"
                                        v-model="language"
                                        :items="availableLocales"
                                    >
                                        <template slot-scope="data" slot="selection">
                                            <div class="flag-ico-contain">
                                                <flag-icon
                                                    :class="[$vuetify.rtl ? 'ml-1' : 'mr-1']"
                                                    :flag="data.item.flag"
                                                ></flag-icon>
                                            </div>
                                            <span>{{ data.item.label }}</span>
                                        </template>
                                        <template slot="item" slot-scope="data">
                                            <flag-icon
                                                :class="[$vuetify.rtl ? 'ml-1' : 'mr-1']"
                                                :flag="data.item.flag"
                                            ></flag-icon>
                                            <span>{{ data.item.label }}</span>
                                        </template>
                                    </v-select>
                                </div>
                            </div>
                            <div class="d-flex gap-10">
                                <div class="label">
                                    <v-subheader>Number of Lines</v-subheader>
                                </div>
                                <div class="field">
                                    <v-select
                                        outlined
                                        single-line
                                        :items="linesSet"
                                        name="number_of_lines"
                                        v-model="user.number_of_lines"
                                        label="Number of Lines"
                                        @change="modifyStorage(user.number_of_lines)"
                                    ></v-select>
                                </div>
                            </div>

                            <div class="d-flex gap-12 profile-page-actions">
                                <v-btn rounded large class="white-bordered" @click="resetForm"
                                >Cancel</v-btn
                                >
                                <v-btn rounded large color="primary" @click="submit"
                                >Save</v-btn
                                >
                            </div>
                        </v-form>
                    </div>
                </div>
            </div>
        </div>
    </v-card>
</template>

<script>
import AccountTab from "./EditUser/AccountTab";
import InformationTab from "./EditUser/InformationTab";
import { mapActions } from "vuex";
import FlagIcon from "../../components/common/FlagIcon";
import CopyLabel from "../../components/common/CopyLabel";

export default {
    components: {
        FlagIcon,
        CopyLabel,
        AccountTab,
        InformationTab,
    },

    props: {
        avatar: {
            type: String,
            default: require("../../assets/images/avatar-default-min.png"),
        },
        toastText: {
            type: String,
            default: "Copied to clipboard!",
        },
    },

    computed: {
        availableLocales() {
            return this.$i18n.locales;
        },
    },

    data() {
        let profile = false;
        let userUrl = "";
        let userText = "";
        let getLink = "";
        let editLink = "";
        let putUserUrl = "";
        let redirectUrl = "";
        let deleteLink = "";
        let breadcrumbs = "";
        let repeatpassword = "";
        let deleteUserText = "";
        let passwordHelpText = "";

        if (
            (typeof localStorage.authUser != "undefined" &&
                JSON.parse(localStorage.authUser).id == this.$route.query.id) ||
            typeof this.$route.query.id == "undefined"
        ) {
            userText = "Profile";
            deleteUserText = "your profile";
        } else {
            userText = "User";
            deleteUserText = "this user";
        }

        if (typeof this.$route.query.id == "undefined") {
            profile = true;
            userUrl = "/web/v1/user/profile-edit";
            getLink = "web/v1/user/profile";
            editLink = { name: "profile-edit" };
            deleteLink = "/web/v1/user/profile";
            putUserUrl = "/web/v1/user/profile";
            redirectUrl = { name: "profile" };
            breadcrumbs = [
                {
                    text: "Show " + userText,
                },
            ];
        } else {
            userUrl = "/web/v1/users/" + this.$route.query.id + "/edit";
            putUserUrl = "/web/v1/users/" + this.$route.query.id;
            redirectUrl = { name: "users-show", query: { id: this.$route.query.id } };
            getLink = "/web/v1/users/" + this.$route.query.id;
            editLink = {
                name: "users-edit",
                query: { id: this.$route.query.id || this.user.id },
            };
            deleteLink = "/web/v1/users/" + this.$route.query.id;
            breadcrumbs = [
                {
                    text: "Users",
                    to: "/users",
                    exact: true,
                },
                {
                    text: "Show " + userText,
                },
            ];
        }

        return {
            tab: null,
            roles: [],
            language: [],
            error: false,
            userUrl: userUrl,
            allStatuses: [],
            profile: profile,
            userText: userText,
            allCountries: [],
            linesSet: [
                '10',
                '20',
                '50',
                "All"
            ],
            numberOfLines: "",
            validationErrors: {},
            getLink: getLink,
            isFormValid: true,
            editLink: editLink,
            showPassword: false,
            showRPassword: false,
            deleteDialog: false,
            putUserUrl: putUserUrl,
            redirectUrl: redirectUrl,
            deleteLink: deleteLink,
            breadcrumbs: breadcrumbs,
            user: { attached_roles: "" },
            repeatpassword:repeatpassword,
            deleteUserText: deleteUserText,
            passwordHelpText: passwordHelpText,
            rules: {
                required: (value) =>
                    (value && Boolean(value)) || "The field is required",
                maxlength: (value, length) =>
                    !value ||
                    (value && value.length <= length) ||
                    "The field length couldn't be greater than " + length,
                email: (value) => {
                    const reg =
                        /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                    return (
                        (value && reg.test(value.toLowerCase())) ||
                        "The email address isn't valid"
                    );
                },
                minlength: (value, length) =>
                    !value ||
                    (value && value.length >= length) ||
                    "The field length couldn't be less than " + length,
                confirmation: (pass, repass) => {
                    return(
                        !pass ||
                        (pass === repass) ||
                        "Passwords do not match"
                    );
                }
            },
        };
    },
    created() {
        this.user.id =
            typeof this.$route.query.id != "undefined"
                ? this.$route.query.id
                : JSON.parse(localStorage.authUser).id;
    },
    mounted() {
        this.fetchData();
        this.fetchDetails();
        this.currentLocale();
    },
    methods: {
        ...mapActions("app", ["showSuccess", "showError"]),

        modifyStorage(value) {
            let userData = localStorage.authUser;
            userData = JSON.parse(userData);
            userData.number_of_lines = value;
            localStorage.authUser = JSON.stringify(userData);
        },

        setLocale(locale) {
            this.$i18n.locale = locale
            this.$vuetify.lang.current = locale
        },

        currentLocale() {
            const current = this.$i18n.locales.find(
                (i) => i.code === this.$i18n.locale
            );
            this.language = current;
            return current;
        },

        submit() {
            if (this.$refs.form.validate()) {
                let self = this;
                self.user.role = self.user.attached_roles;
                if (self.user.id) {
                    self.user.language = self.language.code;
                    axios
                        .put(self.putUserUrl, self.user)
                        .then((response) => {
                            if (response.status == 200) {
                                self.showSuccess(
                                    self.userText + " has been updated successfully."
                                );
                            } else {
                                self.showError({ message: "Something went wrong." });
                            }
                        })
                        .catch((error) => {
                            self.showErrorMessage(error);
                        });
                } else {
                    axios
                        .post("/web/v1/users", self.user)
                        .then((response) => {
                            if (response.status == 200 && response.data.id) {
                                self.showSuccess("User has been created successfully.");
                                setTimeout(
                                    () =>
                                        self.$router.push({
                                            name: "users-show",
                                            query: { id: response.data.id },
                                        }),
                                    700
                                );
                            } else {
                                self.showError({ message: "Something went wrong." });
                            }
                        })
                        .catch((error) => {
                            self.showErrorMessage(error);
                        });
                }
            }
        },

        copy: function (name) {
            this.$clipboard(name, this.toastText);
        },

        disablePinCodeChange(event) {
            event.preventDefault();
        },

        fetchData: function () {
            const self = this;
            axios
                .get(self.userUrl)
                .then(function (response) {
                    self.userDefault = Object.assign({}, response.data.user);
                    self.numberOfLines = response.data.user.number_of_lines+'' !='' ?
                        response.data.user.number_of_lines : "";
                    self.roles = response.data.user.roles;
                    self.allCountries = response.data.countries;
                    self.allStatuses = response.data.all_statuses;
                })
                .catch(function (error) {
                    if (error.response && error.response.status == 404) {
                        self.$router.push({ name: "error-not-found" });
                    } else {
                        self.showError({ message: "Something Went Wrong!", error: error });
                    }
                });
        },

        fetchDetails: function () {
            const self = this;
            axios
                .get(self.getLink)
                .then(function (response) {
                    self.user = response.data.user;
                })
                .catch(function (error) {
                    if (error.response.status == 404) {
                        self.$router.push({ name: "error-not-found" });
                    } else {
                        self.showError({ message: "Something Went Wrong!", error: error });
                    }
                });
        },
        confirmDeleteUser() {
            let self = this;
            axios
                .delete(self.deleteLink)
                .then(function (response) {
                    self.showSuccess(self.userText + " has been deleted successfully.");
                    setTimeout(() => {
                        if (self.profile) {
                            localStorage.removeItem("authUser");
                            localStorage.removeItem("authUserRoles");
                            localStorage.removeItem("authUserPermissions");
                            router.push({ name: "auth-signin" });
                        } else {
                            self.$router.push({ name: "users-list" });
                        }
                    }, 700);
                })
                .catch(function (err) {
                    self.showError({
                        message: "We couldn't delete the user.",
                        error: err,
                    });
                });
            this.deleteDialog = false;
        },

        resetErrors: function () {
            this.error = false;
            this.validationErrors = {};
        },
        resetForm: function () {
            this.user = Object.assign({}, this.userDefault);
            this.$router.go(-1)
        },
        showErrorMessage: function (error) {
            if (error.response.status == 422) {
                this.validationErrors = error.response.data.errors;
                this.showError({
                    message: "Please fix validation errors.",
                    error: error,
                });
            } else {
                const helpMessage =
                    "We couldn't save the user, please try again, then try after refreshing of the page or contact the dev team if that doesn't help.";
                this.showError({ message: helpMessage, error: error });
            }
        },
    },

    watch: {
        language() {
            this.setLocale(this.language.code)
        }
    }
};
</script>
