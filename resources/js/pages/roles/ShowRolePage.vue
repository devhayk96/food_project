<template>
    <div class="flex-grow-1">
        <div class="d-flex align-center py-3">
            <div>
                <div class="display-1">Show Role {{ role.name && `- ${role.name}` }}</div>
                <v-breadcrumbs :items="breadcrumbs" class="pa-0 py-2"></v-breadcrumbs>
            </div>
            <v-spacer></v-spacer>
            <v-btn icon @click="fetchDetails()">
                <v-icon>mdi-refresh</v-icon>
            </v-btn>
        </div>

        <div class="mb-4">
            <div class="d-flex">
                <span class="font-weight-bold">ID</span>
                <span class="mx-1">
                    <copy-label :text="role.id + ''"/>
                </span>
            </div>
            <div class="d-flex mt-2">
                <span class="font-weight-bold">Name</span>
                <span class="mx-1">
                    <copy-label :text="role.name"/>
                </span>
            </div>
            <div class="d-flex mt-2">
                <span class="font-weight-bold">Display Name</span>
                <span class="mx-1">
                    <copy-label :text="role.display_name"/>
                </span>
            </div>
            <div class="d-flex mt-2">
                <span class="font-weight-bold">Description</span>
                <span class="mx-1">
                    <copy-label :text="role.description"/>
                </span>
            </div>
            <div class="d-flex mt-2">
                <span class="font-weight-bold">Created By</span>
                <span class="mx-1">
                    {{ role.created_by_user ? '#' + role.created_by_user.id + ' ' : '' }}
                    <copy-label :text="role.created_by_user ? role.created_by_user.email : ''"/>
                </span>
            </div>
            <div class="d-flex mt-2">
                <span class="font-weight-bold">Updated By</span>
                <span class="mx-1">
                    {{ role.updated_by_user ? '#' + role.updated_by_user.id + ' ' : '' }}
                    <copy-label :text="role.updated_by_user ? role.updated_by_user.email : ''"/>
                </span>
            </div>
            <div class="d-flex mt-2">
                <span class="font-weight-bold">Created At</span>
                <span class="mx-1">
                    <copy-label :text="role.created_at | formatDate('ll')"/>
                </span>
            </div>
            <div class="d-flex mt-2">
                <span class="font-weight-bold">Updated At</span>
                <span class="mx-1">
                    <copy-label :text="role.updated_at | formatDate('ll')"/>
                </span>
            </div>
            <div class="d-flex mt-5">
                <v-btn
                    v-if="userCan('roles-update') && role.name != 'superadmin'"
                    color="info mr-2"
                    :to="{name: 'roles-edit', query: {id: this.$route.query.id}}"
                >
                    <v-icon left small>mdi-pencil</v-icon>
                    Edit Role
                </v-btn>
                <v-btn
                    v-if="userCan('roles-delete') && role.name != 'superadmin'"
                    color="error" @click="deleteDialog = true"
                >
                    <v-icon left small>mdi-delete</v-icon>
                    Delete Role
                </v-btn>
            </div>
        </div>

        <v-dialog v-model="deleteDialog" max-width="290">
            <v-card>
                <v-card-title class="headline">Delete Role</v-card-title>
                <v-card-text>Are you sure you want to delete this role?</v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn @click="deleteDialog = false">Cancel</v-btn>
                    <v-btn color="error" @click="confirmDeleteRole()">Delete</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
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
            role: {},
            tab: null,
            deleteDialog: false,
            breadcrumbs: [
                 {
                     text: 'Roles',
                     to: '/roles',
                     exact: true
                 },
                 {
                     text: 'Show Role'
                 }
             ]
        }
    },
    created() {
        this.role.id = typeof this.$route.query.id != 'undefined' ? this.$route.query.id : 0;
    },
    mounted() {
        this.fetchDetails();
    },
    methods: {
        ...mapActions('app', ['showSuccess', 'showError']),

        fetchDetails: function () {
            const self = this;
            axios.get('/web/v1/roles/' + self.role.id)
                .then(function (response) {
                    self.role = response.data.role;
                })
                .catch(function (error) {
                    if (error.response.status == 404) {
                        self.$router.push({name: 'error-not-found'});
                    } else {
                        self.showError({message:'Something Went Wrong!', error: error})
                    }
                });
        },
        confirmDeleteRole() {
            let self = this;
            axios.delete('/web/v1/roles/' + self.role.id)
                .then(function (response) {
                    self.showSuccess('Role has been deleted successfully.');
                    setTimeout(() => {self.$router.push({name: 'roles-list'})}, 700);
                })
                .catch(function (err) {
                    self.showError({message: 'We couldn\'t delete the role.', error: err});
                });
            this.deleteDialog = false;
        }
    }
}
</script>
