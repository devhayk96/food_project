export default [
    {
        path: '/dashboard/addresses',
        name: 'dashboard-addresses',
        component: () => import(/* webpackChunkName: "addresses" */ '@/pages/dashboard/addresses/AddressesTablePage.vue'),
        meta: {
            layout: 'default'
        }
    },
    {
        path: '/dashboard/addresses/create',
        name: 'dashboard-addresses-create',
        component: () => import(/* webpackChunkName: "addresses-create" */ '@/pages/dashboard/addresses/AddressesEntity.vue'),
        meta: {
            layout: 'default'
        }
    },
    {
        path: '/dashboard/addresses/view',
        name: 'dashboard-addresses-view',
        component: () => import(/* webpackChunkName: "addresses-view" */ '@/pages/dashboard/addresses/AddressesEntity.vue'),
        meta: {
            layout: 'default'
        },
        props: true
    },
    {
        path: '/dashboard/addresses/edit',
        name: 'dashboard-addresses-edit',
        component: () => import(/* webpackChunkName: "addresses-edit" */ '@/pages/dashboard/addresses/AddressesEntity.vue'),
        meta: {
            layout: 'default'
        },
        props: true
    }
]
