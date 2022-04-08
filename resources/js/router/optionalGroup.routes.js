export default [
    {
        path: '/optional-group/list',
        name: 'optional-group-list',
        component: () => import(/* webpackChunkName: "products" */ '@/pages/optionalGroup/OptionalGroupList.vue'),
        meta: {
            layout: 'default',
            permission: 'optional_groups-read'
        }
    },
    {
        path: '/optional-group/manage',
        name: 'optional-group-manage',
        component: () => import(/* webpackChunkName: "product-manage" */ '@/pages/optionalGroup/OptionalGroupManage.vue'),
        meta: {
            layout: 'default',
            permission: 'optional_groups-update'
        },
        props: true
    },
    {
        path: '/optional-group/view',
        name: 'optional-group-view',
        component: () => import(/* webpackChunkName: "product-view" */ '@/pages/optionalGroup/OptionalGroupView.vue'),
        meta: {
            layout: 'default',
            permission: 'optional_groups-read'
        },
        props: true
    },
]
