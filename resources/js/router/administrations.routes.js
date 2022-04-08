export default [
    {
        path: '/dashboard/administrations/order-sources',
        name: 'dashboard-administrations-order-sources',
        component: () => import(/* webpackChunkName: "administrations-order-sources" */ '@/pages/dashboard/administrations/OrderSourcesTablePage.vue'),
        meta: {
            layout: 'default',
            permission: 'order_sources-read'
        }
    },
    {
        path: '/dashboard/administrations/shops',
        name: 'dashboard-administrations-shops',
        component: () => import(/* webpackChunkName: "administrations-shops" */ '@/pages/dashboard/administrations/ShopsTablePage.vue'),
        meta: {
            layout: 'default',
            permission: 'shops-read'
        }
    },
    {
        path: '/dashboard/administrations/shop-details',
        name: 'dashboard-administrations-shop-details',
        component: () => import(/* webpackChunkName: "administrations-shops" */ '@/pages/dashboard/administrations/ShopDetails.vue'),
        meta: {
            layout: 'default',
            permission: 'shops-read'
        },
        props: true
    },
    {
        path: '/dashboard/administrations/shop-add',
        name: 'dashboard-administrations-shop-add',
        component: () => import(/* webpackChunkName: "administrations-shops" */ '@/pages/dashboard/administrations/ShopDetailsAdd.vue'),
        meta: {
            layout: 'default',
            permission: 'shops-create'
        },
        props: true
    }
]
