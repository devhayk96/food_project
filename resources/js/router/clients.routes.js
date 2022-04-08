export default [
    {
        path: '/client/settings/ubereats',
        name: 'client-setting-ubereats',
        component: () => import(/* webpackChunkName: "products" */ '@/pages/clients/UberEats.vue'),
        meta: {
            layout: 'default',
            permission: 'clients-read'
        }
    },
]
