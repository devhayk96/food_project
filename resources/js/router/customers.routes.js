export default [
    // {
    //     path: '/dashboard/customers',
    //     name: 'dashboard-customers',
    //     component: () => import(/* webpackChunkName: "dashboard-customers" */ '@/pages/dashboard/customers/CustomersTablePage.vue'),
    //     meta: {
    //         layout: 'default'
    //     },
    // },
    {
        path: '/customers',
        name: 'customers-view',
        component: () => import (/* webpackChunkName: "customers-view" */ '@/pages/customers/CustomersView.vue'),
        meta: {
            layout: 'default'
        }
    },

]
