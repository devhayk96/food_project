export default [
    {
        path: '/roles',
        name: 'roles-list',
        meta: {permission: 'roles-read'},
        component: () => import(/* webpackChunkName: "roles-list" */ '@/pages/roles/RolesPage.vue')
    }, {
        path: '/roles/create',
        name: 'roles-create',
        meta: {permission: 'roles-create'},
        component: () => import(/* webpackChunkName: "roles-edit" */ '@/pages/roles/EditRolePage.vue')
    }, {
        path: '/roles/show',
        name: 'roles-show',
        meta: {permission: 'roles-read'},
        component: () => import(/* webpackChunkName: "roles-show" */ '@/pages/roles/ShowRolePage.vue')
    }, {
        path: '/roles/edit',
        name: 'roles-edit',
        meta: {permission: 'roles-update'},
        component: () => import(/* webpackChunkName: "roles-edit" */ '@/pages/roles/EditRolePage.vue')
    }, {
        path: '/permissions',
        name: 'permissions-list',
        meta: {permission: 'permissions-read'},
        component: () => import(/* webpackChunkName: "permissions-list" */ '@/pages/permissions/PermissionsPage.vue')
    }
]
