import Vue from 'vue'
import Router from 'vue-router'
import store from "../store";

// Routes
import PagesRoutes from './pages.routes'
import OrdersRoutes from './orders.routes'
import AddressesRoutes from './addresses.routes'
import UsersRoutes from './users.routes'
import RolesRoutes from './roles.routes'
import OperationsRoutes from './operations.routes'
import AdministrationsRoutes from './administrations.routes'
import ProductsRoutes from './products.routes'
import OperationBaseRoutes from './operationBase.routes'
import MenuRoutes from './menus.routes'
import OptionalGroupRoutes from './optionalGroup.routes'
import clientRoutes from './clients.routes'
import customersRoutes from './customers.routes'
import DeviceRoutes from './device.routes'

Vue.use(Router)

export const routes = [
    {
        path: '/',
        redirect: '/auth/signin'
    },
    {
        path: '/dashboard/analytics',
        name: 'dashboard-analytics',
        component: () => import(/* webpackChunkName: "dashboard" */ '@/pages/dashboard/DashboardPage.vue')
    },
    ...PagesRoutes,
    ...OrdersRoutes,
    ...AddressesRoutes,
    ...UsersRoutes,
    ...RolesRoutes,
    ...OperationsRoutes,
    ...AdministrationsRoutes,
    ...ProductsRoutes,
    ...OperationBaseRoutes,
    ...MenuRoutes,
    ...OptionalGroupRoutes,
    ...clientRoutes,
    ...customersRoutes,
    ...DeviceRoutes,
    ...customersRoutes,
    ...DeviceRoutes,
    {
        path: '/blank',
        name: 'blank',
        component: () => import(/* webpackChunkName: "blank" */ '@/pages/BlankPage.vue')
    },

    {
        path: '*',
        name: 'error',
        component: () => import(/* webpackChunkName: "error" */ '@/pages/error/NotFoundPage.vue'),
        meta: {
            layout: 'error'
        }
    }]

const router = new Router({
    mode: 'history',
    base: process.env.BASE_URL || '/',
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) return savedPosition

        return {x: 0, y: 0}
    },
    routes
})

/**
 * Before each route update
 */
router.beforeEach((to, from, next) => {
    if (to.name !== 'auth-signin') {
        if (!to.matched.some(record => record.meta.noAuth)) {
            if (localStorage.isLoggedIn === 'true' && !store.state.app.isLoggedIn) {
                store.state.app.isLoggedIn = true;
            }
            // this route requires auth, check if logged in
            // if not, redirect to login page.
            if (!store.state.app.isLoggedIn) {
                next({name: 'auth-signin'})
            } else {
                if (!Vue.prototype.userCan(to.meta.permission)) {
                    next({name: 'error-not-found'});
                } else {
                    next() // go to wherever I'm going
                }
            }
        } else {
            next() // does not require auth, make sure to always call next()!
        }

    } else {
        next();
    }
})

/**
 * After each route update
 */
router.afterEach((to, from) => {
})

export default router
