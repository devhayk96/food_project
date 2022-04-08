export default [
    {
        path: '/products',
        name: 'products',
        component: () => import(/* webpackChunkName: "products" */ '@/pages/product/ProductList.vue'),
        meta: {
            layout: 'default',
            permission: 'products-read'
        }
    },
    {
        path: '/products/manage',
        name: 'product-manage',
        component: () => import(/* webpackChunkName: "product-manage" */ '@/pages/product/ProductManage.vue'),
        meta: {
            layout: 'default',
            permission: 'products-read'
        },
        props: true
    },
    {
        path: '/vats',
        name: 'vats',
        component: () => import(/* webpackChunkName: "products" */ '@/pages/vat/VatList.vue'),
        meta: {
            layout: 'default',
            permission: 'vats-read'
        }
    },
    {
        path: '/vats/manage',
        name: 'vat-manage',
        component: () => import(/* webpackChunkName: "product-manage" */ '@/pages/vat/VatManage.vue'),
        meta: {
            layout: 'default',
            permission: 'vats-read'
        },
        props: true
    },
    {
        path: '/product/group/list',
        name: 'product-group-list',
        component: () => import(/* webpackChunkName: "categories" */ '@/pages/productGroup/ProductGroupList.vue'),
        meta: {
            layout: 'default',
            permission: 'product_groups-read'
        }
    },
    {
        path: '/product/groups/manage',
        name: 'product-group-manage',
        component: () => import(/* webpackChunkName: "category-manage" */ '@/pages/productGroup/ProductGroupManage.vue'),
        meta: {
            layout: 'default',
            permission: 'product_groups-read'

        },
        props: true
    },
    {
        path: '/product/subgroup/list',
        name: 'product-subgroup-list',
        component: () => import(/* webpackChunkName: "subcategories" */ '@/pages/productSubgroup/ProductSubgroupList.vue'),
        meta: {
            layout: 'default',
            permission: 'product_subgroups-read'
        }
    },
    {
        path: '/product/subgroup/manage',
        name: 'product-subgroup-manage',
        component: () => import(/* webpackChunkName: "subcategory-manage" */ '@/pages/productSubgroup/ProductSubgroupManage.vue'),
        meta: {
            layout: 'default',
            permission: 'product_subgroups-read'
        },
        props: true
    },
]
