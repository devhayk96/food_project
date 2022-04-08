export default [
    {
        path: '/kitchens',
        redirect: 'kitchen/list'
    },
    {
        path: '/tables',
        redirect: 'table/list'
    },
    {
        path: '/kitchen/list',
        name: 'kitchen-list',
        meta: {permission: 'kitchens-read'},
        component: () => import(/* webpackChunkName: "users-list" */ '@/pages/dashboard/operations/kitchen/KitchenList.vue')
    },
    {
        path: '/kitchen/manage',
        name: 'kitchen-manage',
        meta: {permission: 'kitchens-read'},
        component: () => import(/* webpackChunkName: "users-edit" */ '@/pages/dashboard/operations/kitchen/KitchenManage.vue')
    },
    {
        path: '/table/list',
        name: 'table-list',
        meta: {permission: 'tables-read'},
        component: () => import(/* webpackChunkName: "users-list" */ '@/pages/dashboard/operations/table/TableList.vue')
    },
    {
        path: '/table/manage',
        name: 'table-manage',
        meta: {permission: 'tables-read'},
        component: () => import(/* webpackChunkName: "users-edit" */ '@/pages/dashboard/operations/table/TableManage.vue')
    },
    {
        path: '/cancellation-reason/list',
        name: 'cancellation-reason-list',
        meta: {permission: 'cancellation_reasons-read'},
        component: () => import(/* webpackChunkName: "users-list" */ '@/pages/dashboard/operations/cancellationReason/CancellationReasonList.vue')
    },
    {
        path: '/cancellation-reason/manage',
        name: 'cancellation-reason-manage',
        meta: {permission: 'cancellation_reasons-read'},
        component: () => import(/* webpackChunkName: "users-edit" */ '@/pages/dashboard/operations/cancellationReason/CancellationReasonManage.vue')
    },
    {
        path: '/account/settings',
        name: 'account-settings',
        meta: {permission: 'cancellation_reasons-read'},
        component: () => import(/* webpackChunkName: "users-edit" */ '@/pages/dashboard/operations/accountSettings/AccountSettings.vue')
    },
]
