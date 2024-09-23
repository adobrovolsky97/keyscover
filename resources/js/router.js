import {createRouter, createWebHistory} from 'vue-router';
import MainLayout from "./layouts/main/MainLayout.vue";
import Home from "./views/home/Home.vue";
import Login from "./views/auth/Login.vue";
import Register from "./views/auth/Register.vue";
import AuthLayout from "./layouts/full-page/AuthLayout.vue";
import store from "./store";
import middlewarePipeline from "./middleware/pipeline.js";
import auth from "./middleware/auth.js";
import guest from "./middleware/guest.js";
import admin from "./middleware/admin.js";
import Checkout from "./views/checkout/Checkout.vue";
import List from "./views/orders/List.vue";
import EditForm from "./views/admin/product/Edit.vue";
import UsersList from "./views/admin/user/List.vue";
import ConfigsList from "./views/admin/config/List.vue";
import ConfigEdit from "./views/admin/config/Edit.vue";
import ProductList from "./views/admin/product/List.vue";
import OrdersList from "./views/admin/order/List.vue";
import ExportsList from "./views/admin/export/List.vue";
import AdminLayout from "./layouts/admin/AdminLayout.vue";
import Dashboard from "./views/admin/Dashboard.vue";
import NotFound from "./views/errors/NotFound.vue";
import Detailed from "./views/product/Detailed.vue";
import ForgotPassword from "./views/auth/ForgotPassword.vue";

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            component: MainLayout,
            children: [
                {
                    path: '/',
                    name: 'home',
                    component: Home,
                },
                {
                    path: '/product/:id',
                    name: 'product.show',
                    component: Detailed,
                },
                {
                    path: '/checkout',
                    name: 'checkout',
                    component: Checkout,
                    meta: {
                        middleware: [auth]
                    }
                },
                {
                    path: '/orders/list',
                    name: 'orders-list',
                    component: List,
                    meta: {
                        middleware: [auth]
                    }
                },
            ]
        },
        {
            path: '/',
            component: AuthLayout,
            children: [
                {
                    path: '/login',
                    name: 'login',
                    component: Login,
                    meta: {
                        middleware: [guest]
                    }
                },
                {
                    path: '/forgot-password',
                    name: 'forgot-password',
                    component: ForgotPassword,
                    meta: {
                        middleware: [guest]
                    }
                },
                {
                    path: '/sign-up',
                    name: 'sign-up',
                    component: Register,
                    meta: {
                        middleware: [guest]
                    }
                },
                {
                    path: '/404',
                    name: '404',
                    component: NotFound,
                }
            ]
        },
        {
            path: '/admin',
            component: AdminLayout,
            children: [
                {
                    path: '',
                    name: 'admin.dashboard',
                    component: Dashboard,
                    meta: {
                        middleware: [admin]
                    }
                },
                {
                    path: 'products',
                    name: 'admin.products',
                    component: ProductList,
                    meta: {
                        middleware: [admin]
                    }
                },
                {
                    path: 'products/:id',
                    name: 'admin.products.edit',
                    component: EditForm,
                    meta: {
                        middleware: [admin]
                    }
                },
                {
                    path: 'users',
                    name: 'admin.users',
                    component: UsersList,
                    meta: {
                        middleware: [admin]
                    }
                },
                {
                    path: 'configs',
                    name: 'admin.configs',
                    component: ConfigsList,
                    meta: {
                        middleware: [admin]
                    }
                },
                {
                    path: 'configs/:id',
                    name: 'admin.configs.edit',
                    component: ConfigEdit,
                    meta: {
                        middleware: [admin]
                    }
                },
                {
                    path: 'orders',
                    name: 'admin.orders',
                    component: OrdersList,
                    meta: {
                        middleware: [admin]
                    }
                },
                {
                    path: 'exports',
                    name: 'admin.exports',
                    component: ExportsList,
                    meta: {
                        middleware: [admin]
                    }
                },
            ]
        },
        {
            path: '/404',
            name: '404',
            component: NotFound,
        },
        {
            path: '/:pathMatch(.*)*',
            redirect: '/404'
        },
    ],
});

router.beforeEach(async (to, from, next) => {

    /** Navigate to next if middleware is not applied */
    if (!to.meta.middleware && !to.meta.config && !to.meta.permissions) {
        return next()
    }
    const middleware = to.meta.middleware;
    const context = {
        to, from, next, store
    }

    return middleware[0]({
        ...context, next: middlewarePipeline(context, middleware, 1)
    })
});

let isTracked = false;

router.afterEach((to, from) => {
    const pathChanged = to.path !== from.path;
    const queryChanged = JSON.stringify(to.query) !== JSON.stringify(from.query);

    if ((pathChanged || queryChanged) && !to.path.startsWith('/admin') && !isTracked) {
        isTracked = true;
        axios.post('/api/track', {
            url: window.location.href,
        }).finally(() => {
            // Reset the flag after tracking is complete
            isTracked = false;
        });
    }
});


export default router;
