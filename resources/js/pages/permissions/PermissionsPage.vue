<template>
    <div class="d-flex flex-column flex-grow-1">
        <div class="d-flex align-center py-3">
            <div>
                <div class="display-1">Permissions</div>
                <v-breadcrumbs :items="breadcrumbs" class="pa-0 py-2"></v-breadcrumbs>
            </div>
            <v-spacer></v-spacer>
        </div>

        <v-card>
            <v-row dense class="pa-2 align-center">
                <v-col cols="6" class="d-flex text-right align-center">
                    <v-btn
                        :loading="isLoading"
                        icon
                        small
                        class="ml-2"
                        @click="initialize()"
                    >
                        <v-icon>mdi-refresh</v-icon>
                    </v-btn>
                    <v-btn
                        v-if="userCan('permissions-update') && !editPermissions"
                        color="info" @click="editPermissions = true"
                        class="ml-2"
                    >
                        <v-icon left small>mdi-pencil</v-icon>
                        Edit Permissions
                    </v-btn>
                    <div v-if="userCan('permissions-update') && editPermissions">
                        <v-btn class="ml-2" @click="resetForm">
                            <v-icon left small>mdi-skip-backward</v-icon>
                            Reset
                        </v-btn>
                        <v-btn color="secondary" class="ml-2" @click="cancelPermissions">
                            <v-icon left small>mdi-cancel</v-icon>
                            Cancel
                        </v-btn>
                        <v-btn color="info" class="ml-2" @click="savePermissions">
                            <v-icon left small>mdi-content-save</v-icon>
                            Save
                        </v-btn>
                    </div>
                </v-col>
            </v-row>
            <div class="ml-5">
                <div
                    v-for="(role, index) in roles"
                >
                    <h2>{{ role.display_name }}</h2>
                    <div
                        v-for="(object, objIndex) in role.grouppedPermissions"
                    >
                        <h4 class="ml-4">{{ object.display_name }}</h4>
                        <div class="ml-4 mb-2 d-flex">
                            <div class="ml-3 d-flex"
                                v-for="(permission, permissionIndex) in object.data"
                            >
                                {{ permission.name | capitalize }}
                                <v-icon v-if="!editPermissions" :color="permission.value ? 'success' : 'error'"
                                >{{ permission.value ? 'mdi-check' : 'mdi-close' }}</v-icon>
                                <v-checkbox v-else
                                    class="ml-1 mt--4"
                                    v-model="roles[index].grouppedPermissions[objIndex].data[permissionIndex].value"
                                ></v-checkbox>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <v-row dense class="pa-2 align-center">
                <v-col cols="6" class="d-flex text-right align-center">
                    <v-btn
                        :loading="isLoading"
                        icon
                        small
                        class="ml-2"
                        @click="initialize()"
                    >
                        <v-icon>mdi-refresh</v-icon>
                    </v-btn>
                    <v-btn
                        v-if="userCan('permissions-update') && !editPermissions"
                        color="info" @click="editPermissions = true"
                        class="ml-2"
                    >
                        <v-icon left small>mdi-pencil</v-icon>
                        Edit Permissions
                    </v-btn>
                    <div v-if="userCan('permissions-update') && editPermissions">
                        <v-btn class="ml-2" @click="resetForm">
                            <v-icon left small>mdi-skip-backward</v-icon>
                            Reset
                        </v-btn>
                        <v-btn color="secondary" class="ml-2" @click="cancelPermissions">
                            <v-icon left small>mdi-cancel</v-icon>
                            Cancel
                        </v-btn>
                        <v-btn color="info" class="ml-2" @click="savePermissions">
                            <v-icon left small>mdi-content-save</v-icon>
                            Save
                        </v-btn>
                    </div>
                </v-col>
            </v-row>
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
        return {
            isLoading: false,
            breadcrumbs: [{
                text: 'Permissions'
            }],
            editPermissions: false,
            roles: {},
            rolesDefault: {},
            deleteDialog: false,
            deletableId: null,
        }
    },
    watch: {
        selectedRoles(val) {

        }
    },
    created () {
        this.initialize()
    },
    methods: {
        ...mapActions('app', ['showSuccess', 'showError']),

        initialize() {
            let self = this;
            axios.get('/web/v1/permissions')
                .then(function (response) {
                    self.roles = response.data.roles;
                    self.rolesDefault = _.cloneDeep(response.data.roles);
                })
                .catch(function (err) {
                    self.showError({message: 'We couldn\'t get roles.', error: error});
                });
        },
        searchRole() {
        },
        open() {
        },
        savePermissions() {
            let self = this;
            axios.put(
                '/web/v1/permissions',
                self.roles
            ).then(response => {
                if (response.status == 200) {
                    self.showSuccess('Permissions have been updated successfully.');
                    setTimeout(() => {
                        self.editPermissions = false;
                        self.rolesDefault = _.cloneDeep(self.roles);
                    }, 700);
                } else {
                    self.showError({message: 'Something went wrong.'});
                }
            }).catch(error => {
                self.showErrorMessage(error);
            });
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
        cancelPermissions: function() {
            this.roles = _.cloneDeep(this.rolesDefault);
            this.editPermissions = false;
        },
        resetForm: function () {
            this.roles = _.cloneDeep(this.rolesDefault);
        },
        deleteRole(id) {
            this.deleteDialog = true
            this.deletableId = id;
        },
        confirmDeleteRole() {
            let self = this;
            axios.delete('/web/v1/roles/' + this.deletableId)
                .then(function (response) {
                    self.showSuccess('Role has been deleted successfully.');
                    setTimeout(() => self.initialize(), 700);
                    self.deletableId = null;
                })
                .catch(function (err) {
                    self.showError({message: 'We couldn\'t delete the role.', error: err});
                    self.deletableId = null;
                });
            this.deleteDialog = false;
        },
        cancelDeleteRole() {
            this.deletableId = null;
            this.deleteDialog = false;
        }
    }
}
</script>

<style lang="scss" scoped>
.slide-fade-enter-active {
    transition: all 0.3s ease;
}

.slide-fade-leave-active {
    transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter,
.slide-fade-leave-to {
    transform: translateX(10px);
    opacity: 0;
}

.mt--4 {
    margin-top: -4px !important;
}
</style>
