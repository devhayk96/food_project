<template>
    <div class="flex-grow-1">
        <div class="d-flex align-center py-3">
            <div>
                <div class="display-1">{{ actionText }} Role {{ role.name && `- ${role.name}` }}</div>
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
                                v-model="role.name"
                                :rules="[rules.required, rules.maxlength(role.name, 255)]"
                                :validate-on-blur="false"
                                :error="error || typeof validationErrors.name != 'undefined'"
                                :error-messages="validationErrors.name"
                                @keyup="resetErrors"
                            ></v-text-field>
                            <v-text-field
                                label="Display Name"
                                name="display_name"
                                v-model="role.display_name"
                                :rules="[rules.required, rules.maxlength(role.display_name, 255)]"
                                :validate-on-blur="false"
                                :error="error || typeof validationErrors.display_name != 'undefined'"
                                :error-messages="validationErrors.display_name"
                                @keyup="resetErrors"
                            ></v-text-field>
                            <v-text-field
                                label="Description"
                                name="description"
                                v-model="role.description"
                                :rules="[rules.required, rules.maxlength(role.description, 25)]"
                                :validate-on-blur="false"
                                :error="error || typeof validationErrors.description != 'undefined'"
                                :error-messages="validationErrors.description"
                                @keyup="resetErrors"
                            ></v-text-field>
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
import {mapActions} from "vuex";

export default {
    components: {
        CopyLabel
    },
    data() {

        let roleUrl = '';
        let actionText = '';
        if (this.$route.query.id == 0) {
            roleUrl = '/web/v1/roles/create';
            actionText = 'Create';
        } else {
            roleUrl = '/web/v1/roles/' + this.$route.query.id + '/edit';
            actionText = 'Edit';
        }

        return {
            isFormValid: true,
            actionText: actionText,
            roleUrl: roleUrl,
            role: {},
            roleDefault: {},
            rules: {
                required: (value) => (value && Boolean(value)) || 'The field is required',
                maxlength: (value, length) => (!value || (value && value.length <= length)) || 'The field length couldn\'t be greater than ' + length,
                minlength: (value, length) => (!value || (value && value.length >= length)) || 'The field length couldn\'t be less than ' + length,
            },
            error: false,
            validationErrors: {},
            tab: null,
            breadcrumbs: [
                 {
                     text: 'Roles',
                     to: '/roles',
                     exact: true
                 },
                 {
                     text: actionText + ' Role'
                 }
             ]
        }
    },
    created() {
        this.role.id = this.$route.query.id;
    },
    mounted() {
        this.fetchDetails();
    },
    methods: {
        ...mapActions('app', ['showSuccess', 'showError']),

        fetchDetails: function () {
            const self = this;
            axios.get(self.roleUrl)
                .then(function (response) {
                    self.role = response.data.role;
                    self.roleDefault = Object.assign({}, response.data.role);
                })
                .catch(function (error) {
                    if (error.response.status == 404) {
                        self.$router.push({name: 'error-not-found'});
                    } else {
                        self.showError({message:'Something Went Wrong!', error: error})
                    }
                });
        },
        resetErrors: function () {
            this.error = false
            this.validationErrors = {};
        },
        resetForm: function () {
            this.role = Object.assign({}, this.roleDefault);
        },
        showErrorMessage: function (error) {
            if (error.response.status == 422) {
                this.validationErrors = error.response.data.errors;
                this.showError({message: 'Please fix validation errors.', error: error});
            } else {
                const helpMessage = 'We couldn\'t save the role, please try again, then try after refreshing of the page or contact the dev team if that doesn\'t help.';
                this.showError({message: helpMessage, error: error});
            }
        },
        submit() {
            if (this.$refs.form.validate()) {
                let self = this;
                let $method = 'get';
                if (self.role.id) {
                    axios.put(
                        '/web/v1/roles/' + self.role.id,
                        self.role
                    ).then(response => {
                        if (response.status == 200) {
                            self.showSuccess('Role has been updated successfully.');
                            setTimeout(() => self.$router.push({name: 'roles-show', query: {id: self.role.id}}), 700);
                        } else {
                            self.showError({message: 'Something went wrong.'});
                        }
                    }).catch(error => {
                        self.showErrorMessage(error);
                    });
                } else {
                    axios.post(
                        '/web/v1/roles',
                        {
                            name: self.role.name,
                            display_name: self.role.display_name,
                            description: self.role.description
                        }
                    ).then(response => {
                        if (response.status == 200 && response.data.id) {
                            self.showSuccess('Role has been created successfully.');
                            setTimeout(() => self.$router.push({name: 'roles-show', query: {id: response.data.id}}), 700);
                        } else {
                            self.showError({message: 'Something went wrong.'});
                        }
                    }).catch(error => {
                        self.showErrorMessage(error);
                    });
                }
            }
        },
    }
}
</script>
