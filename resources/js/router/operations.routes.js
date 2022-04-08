export default [
    {
        path: '/dashboard/operations/order-statuses',
        name: 'dashboard-operations-order-statuses',
        component: () => import(/* webpackChunkName: "dashboard-operations-order-statuses" */ '@/pages/dashboard/operations/OrderStatusesTablePage.vue'),
        meta: {
            layout: 'default',
            permission: 'order_statuses-read'
        }
    },
    {
        path: '/dashboard/operations/order-types',
        name: 'dashboard-operations-order-types',
        component: () => import(/* webpackChunkName: "dashboard-operations-orders-types" */ '@/pages/dashboard/operations/OrderTypesTablePage.vue'),
        meta: {
            layout: 'default',
            permission: 'order_types-read'
        }
    },
    {
        path: '/dashboard/operations/order-source-types',
        name: 'dashboard-operations-order-source-types',
        component: () => import(/* webpackChunkName: "dashboard-operations-order-source-types" */ '@/pages/dashboard/operations/OrderSourceTypesTablePage.vue'),
        meta: {
            layout: 'default',
            permission: 'order_source_types-read'
        }
    },
    {
        path: '/dashboard/operations/payment-methods',
        name: 'dashboard-operations-payment-methods',
        component: () => import(/* webpackChunkName: "dashboard-operations-payment-methods" */ '@/pages/dashboard/operations/PaymentMethodsTablePage.vue'),
        meta: {
            layout: 'default',
            permission: 'payment_methods-read'
        }
    },
    {
        path: '/dashboard/operations/courier-types',
        name: 'dashboard-operations-courier-types',
        component: () => import(/* webpackChunkName: "dashboard-operations-courier-types" */ '@/pages/dashboard/operations/CourierTypesTablePage.vue'),
        meta: {
            layout: 'default',
            permission: 'courier_types-read'
        }
    },
    {
        path: '/dashboard/operations/courier-types/manage',
        name: 'dashboard-operations-courier-type-manage',
        component: () => import(/* webpackChunkName: "dashboard-operations-courier-type-manage" */ '@/pages/dashboard/operations/manage/CourierTypeManage.vue'),
        meta: {
            layout: 'default',
            permission: 'courier_types-read'
        }
    },
    {
        path: '/dashboard/operations/order-statuses/manage',
        name: 'dashboard-operations-order-status-manage',
        component: () => import(/* webpackChunkName: "dashboard-operations-order-statuses" */ '@/pages/dashboard/operations/manage/OrderStatusManage.vue'),
        meta: {
            layout: 'default',
            permission: 'order_statuses-read'
        }
    },
    {
        path: '/dashboard/operations/order-types/manage',
        name: 'dashboard-operations-order-type-manage',
        component: () => import(/* webpackChunkName: "dashboard-operations-orders-types" */ '@/pages/dashboard/operations/manage/OrderTypeManage.vue'),
        meta: {
            layout: 'default',
            permission: 'order_types-read'
        }
    },
    {
        path: '/dashboard/operations/payment-methods/manage',
        name: 'dashboard-operations-payment-method-manage',
        component: () => import(/* webpackChunkName: "dashboard-operations-payment-methods" */ '@/pages/dashboard/operations/manage/PaymentMethodManage.vue'),
        meta: {
            layout: 'default',
            permission: 'payment_methods-read'
        }
    },
]
