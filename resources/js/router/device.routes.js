export default [
    {
        path: '/device',
        name: 'device-list',
        component: () => import(/* webpackChunkName: "device" */ '@/pages/device/DeviceList.vue'),
        meta: {
            layout: 'default',
            permission: 'device-read'
        }
    },
    {
        path: '/device/manage',
        name: 'device-manage',
        component: () => import(/* webpackChunkName: "device-manage" */ '@/pages/device/DeviceManage.vue'),
        meta: {
            layout: 'default',
            permission: 'device-update'
        },
        props: true
    },
]
