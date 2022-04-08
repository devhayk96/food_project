export default [
    {
        path: '/menu/category/list',
        name: 'menu-category-list',
        component: () => import(/* webpackChunkName: "products" */ '@/pages/menu/menuCategory/MenuCategoryList.vue'),
        meta: {
            layout: 'default',
            permission: 'menu_categories-read'
        }
    },
    {
        path: '/menu/category/manage',
        name: 'menu-category-manage',
        component: () => import(/* webpackChunkName: "product-manage" */ '@/pages/menu/menuCategory/MenuCategoryManage.vue'),
        meta: {
            layout: 'default',
            permission: 'menu_categories-read'
        },
        props: true
    },
    {
        path: '/menu/list',
        name: 'menu-list',
        component: () => import(/* webpackChunkName: "products" */ '@/pages/menu/menu/MenuList.vue'),
        meta: {
            layout: 'default',
            permission: 'menus-read'
        }
    },
    {
        path: '/menu/manage',
        name: 'menu-manage',
        component: () => import(/* webpackChunkName: "product-manage" */ '@/pages/menu/menu/MenuManage.vue'),
        meta: {
            layout: 'default',
            permission: 'menus-read'
        },
        props: true
    },
    {
        path: '/menu/details',
        name: 'menu-details',
        component: () => import(/* webpackChunkName: "product-manage" */ '@/pages/menu/menu/MenuDetails.vue'),
        meta: {
            layout: 'default',
            permission: 'menus-read'
        },
        props: true
    },
    {
        path: '/menu/categories/:id',
        name:'menu-categories',
        component: () => import(/* webpackChunkName: "menu-categories" */ '@/pages/menu/menu/MenuCategories.vue'),
        meta: {
            layout: 'default',
            parmissions: 'menu_categories-read'
        }
    },
    {
        path: '/menu/category/products',
        name: 'menu-category-products',
        component: () => import(/* webpackChunkName: "menu-category-products" */  '@/pages/menu/menuCategory/MenuCategoryProducts.vue'),
        meta: {
            layout: 'default',
            permissions: 'menus-read'
        }
    }
]
