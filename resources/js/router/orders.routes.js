export default [
    /*{
        path: '/dashboard/orders/accepted',
        name: 'dashboard-orders-accepted',
        component: () => import(/!* webpackChunkName: "orders-accepted" *!/ '@/pages/dashboard/orders/OrdersAcceptedPage.vue'),
        meta: {
            layout: 'default'
        }
    },*/
   /* {
        path: '/dashboard/orders/list',
        name: 'order-list',
        component: () => import(/!* webpackChunkName: "orders-accepted" *!/ '@/pages/dashboard/orders/OrderList.vue'),
        meta: {
            layout: 'default'
        }
    },*/
    {
        path: '/dashboard/orders/new',
        name: 'order-new',
        component: () => import(/* webpackChunkName: "orders-accepted" */ '@/pages/dashboard/orders/OrderList.vue'),
        meta: {
            layout: 'default',
            permission: 'orders-read'
        }
    },
    {
        path: '/dashboard/orders/accepted',
        name: 'order-accepted',
        component: () => import(/* webpackChunkName: "orders-accepted" */ '@/pages/dashboard/orders/OrderList.vue'),
        meta: {
            layout: 'default',
            permission: 'orders-read'
        }
    },
    {
        path: '/dashboard/orders/denied',
        name: 'order-denied',
        component: () => import(/* webpackChunkName: "orders-accepted" */ '@/pages/dashboard/orders/OrderList.vue'),
        meta: {
            layout: 'default',
            permission: 'orders-read'
        }
    },
    {
        path: '/dashboard/orders/details',
        name: 'dashboard-orders-details',
        component: () => import(/* webpackChunkName: "orders-accepted" */ '@/pages/dashboard/orders/OrderDetailsPage.vue'),
        meta: {
            layout: 'default',
            permission: 'orders-read'
        },
        props: true
    },
    {
        path: '/dashboard/orders/error',
        name: 'dashboard-orders-error',
        component: () => import(/* webpackChunkName: "orders-error" */ '@/pages/dashboard/orders/ErrorOrderList.vue'),
        meta: {
            layout: 'default',
            permission: 'orders-read'
        }
    },
]
