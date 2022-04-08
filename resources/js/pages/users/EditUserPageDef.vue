<template>
    <div class="flex-grow-1">
        <div class="d-flex align-center py-3">
            <div>
                <div class="display-1">Edit User {{ user.name && `- ${user.name}` }}</div>
                <v-breadcrumbs :items="breadcrumbs" class="pa-0 py-2"></v-breadcrumbs>
            </div>
            <v-spacer></v-spacer>
            <v-btn icon @click>
                <v-icon>mdi-refresh</v-icon>
            </v-btn>
        </div>

        <div
            v-if="user.role === 'ADMIN'"
            class="d-flex align-center font-weight-bold primary--text my-2"
        >
            <v-icon small color="primary">mdi-security</v-icon>
            <span class="ma-1">Administrator</span>
        </div>

        <div class="mb-4">
            <div class="d-flex">
                <span class="font-weight-bold">Email</span>
                <span class="mx-1">
          <copy-label :text="user.email"/>
        </span>
            </div>
            <div class="d-flex">
                <span class="font-weight-bold">ID</span>
                <span class="mx-1">
          <copy-label :text="user.id + ''"/>
        </span>
            </div>
        </div>

        <v-tabs v-model="tab" :show-arrows="false" background-color="transparent">
            <v-tab to="#tabs-account">Account</v-tab>
            <v-tab to="#tabs-information">Information</v-tab>
        </v-tabs>

        <v-tabs-items v-model="tab">
            <v-tab-item value="tabs-account">
                <account-tab ref="tabs-account" :user="user"></account-tab>
            </v-tab-item>

            <v-tab-item value="tabs-information">
                <information-tab ref="tabs-information" :user="user"></information-tab>
            </v-tab-item>
        </v-tabs-items>
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
        return {
            user: {
                'id': 32,
                'email': 'bfitchew0@ezinearticles.com',
                'name': 'Bartel Fitchew',
                'verified': false,
                'created': '2019-08-09T03:14:12Z',
                'lastSignIn': '2019-08-14T20:00:53Z',
                'disabled': true,
                'role': 'ADMIN',
                'avatar': '/images/avatars/avatar1.svg'
            },
            tab: null,
            breadcrumbs: [
                {
                    text: 'Users',
                    to: '/users/list',
                    exact: true
                },
                {
                    text: 'Edit User'
                }
            ]
        }
    },
    mounted() {
        this.fetchDetails();
    },
    methods: {
        ...mapActions('app', ['showSuccess', 'showError']),

        fetchDetails: function () {
            const _this = this;
            axios.get('/web/v1/user/get/me')
                .then(function (response) {
                    console.log(response);
                    _this.user.id = btoa(response.data.id);
                    _this.user.name = response.data.name;
                    _this.user.email = response.data.email;
                })
                .catch(function (error) {
                    console.log(error);
                    _this.showError({message:'Something Went Wrong!', error: error})
                });
        }
    }
}
</script>
