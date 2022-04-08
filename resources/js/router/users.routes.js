export default [
    {
        path: '/profile',
        name: 'profile',
        meta: {permission: 'profile-read'},
        component: () => import(/* webpackChunkName: "users-show" */ '@/pages/users/ShowUserPage.vue')
    }, {
        path: '/profile/edit',
        name: 'profile-edit',
        meta: {permission: 'profile-update'},
        component: () => import(/* webpackChunkName: "users-edit" */ '@/pages/users/EditUserPage.vue')
    }, {
        path: '/users',
        name: 'users-list',
        meta: {permission: 'users-read'},
        component: () => import(/* webpackChunkName: "users-list" */ '@/pages/users/UsersPage.vue')
    }, {
        path: '/users/create',
        name: 'users-create',
        meta: {permission: 'users-create'},
        component: () => import(/* webpackChunkName: "users-edit" */ '@/pages/users/EditUserPage.vue')
    }, {
        path: '/users/show',
        name: 'users-show',
        meta: {permission: 'users-read'},
        component: () => import(/* webpackChunkName: "users-show" */ '@/pages/users/ShowUserPage.vue')
    }, {
        path: '/users/edit',
        name: 'users-edit',
        meta: {permission: 'users-update'},
        component: () => import(/* webpackChunkName: "users-edit" */ '@/pages/users/EditUserPage.vue')
    }
]
