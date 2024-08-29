<template>
    <div>
        <nav
            class="navbar block py-4 w-full max-w-full rounded-none px-4 border-0 top-0 shadow-xl">
            <div class="container mx-auto flex items-center justify-between">
                <router-link
                    :to="{name: 'admin.dashboard'}"
                    custom
                    v-slot="{ navigate }"
                >
                    <div class="flex flex-row justify-between items-center gap-2">
                        <div class="avatar">
                            <div class="w-8 lg:w-12 rounded-xl">
                                <img src="../../../../public/logo.png" />
                            </div>
                        </div>
                        <a @click="navigate"
                           class="block antialiased font-sans cursor-pointer text-md lg:text-lg font-bold">KeysCover</a>
                    </div>
                </router-link>
                <div class="hidden items-center gap-2 lg:flex">
                    <div class="auth flex flex-row justify-center items-center gap-5">
                        <router-link
                            :to="{name: 'admin.dashboard'}"
                            custom
                            v-slot="{ navigate, href }"
                        >
                            <a @click="navigate"
                               :class="{'link-error': $route.path === '/admin/dashboard'}"
                               class="link">Dashboard</a>
                        </router-link>
                        <router-link
                            :to="{name: 'admin.users'}"
                            custom
                            v-slot="{ navigate, href }"
                        >
                            <a @click="navigate"
                               :class="{'link-error': $route.path === '/admin/users'}"
                               class="link">Користувачі</a>
                        </router-link>
                        <router-link
                            :to="{name: 'admin.orders'}"
                            custom
                            v-slot="{ navigate, href }"
                        >
                            <a @click="navigate"
                               :class="{'link-error': $route.path === '/admin/orders'}"
                               class="link">Замовлення</a>
                        </router-link>
                        <router-link
                            :to="{name: 'admin.exports'}"
                            custom
                            v-slot="{ navigate, href }"
                        >
                            <a @click="navigate"
                               :class="{'link-error': $route.path === '/admin/exports'}"
                               class="link">Експорти</a>
                        </router-link>
                        <router-link
                            :to="{name: 'home'}"
                            custom
                            v-slot="{ navigate, href }"
                        >
                            <a @click="navigate"
                               class="link">На сайт</a>
                        </router-link>
                        <div class="flex flex-row gap-4" v-if="$store.state.user === null">
                            <router-link to="/login">
                                <a class="link">Вхід</a>
                            </router-link>
                            <router-link to="/sign-up">
                                <a class="link">Реєстрація</a>
                            </router-link>
                        </div>
                        <a @click="logout" v-if="$store.state.user !== null" class="link">Вийти</a>
                    </div>
                </div>
                <div class="lg:hidden flex flex-row justify-between items-center gap-2">
                    <span class="font-bold text-sm" v-if="usd">USD {{ usd }}</span>
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>

                        </div>
                        <ul
                            tabindex="0"
                            class="menu menu-sm dropdown-content bg-base-100 rounded-box z-[2] mt-3 w-52 p-2 shadow">
                            <li v-if="$store.state.user !== null">
                                <router-link
                                    :to="{name: 'admin.dashboard'}"
                                    custom
                                    v-slot="{ navigate, href }"
                                >
                                    <a @click="navigate"
                                       :class="{'link-error': $route.path === '/admin/dashboard'}"
                                       class="link">Dashboard</a>
                                </router-link>
                                <router-link
                                    :to="{name: 'admin.users'}"
                                    custom
                                    v-slot="{ navigate, href }"
                                >
                                    <a @click="navigate"
                                       :class="{'link-error': $route.path === '/admin/users'}"
                                       class="link">Користувачі</a>
                                </router-link>
                                <router-link
                                    :to="{name: 'admin.orders'}"
                                    custom
                                    v-slot="{ navigate, href }"
                                >
                                    <a @click="navigate"
                                       :class="{'link-error': $route.path === '/admin/orders'}"
                                       class="link">Замовлення</a>
                                </router-link>
                                <router-link
                                    :to="{name: 'admin.exports'}"
                                    custom
                                    v-slot="{ navigate, href }"
                                >
                                    <a @click="navigate"
                                       :class="{'link-error': $route.path === '/admin/exports'}"
                                       class="link">Експорти</a>
                                </router-link>
                                <router-link
                                    :to="{name: 'home'}"
                                    custom
                                    v-slot="{ navigate, href }"
                                >
                                    <a @click="navigate"
                                       class="link">На сайт</a>
                                </router-link>
                            </li>
                            <li v-if="$store.state.user == null">
                                <router-link to="/login">
                                    <a class="link">Вхід</a>
                                </router-link>
                            </li>
                            <li v-if="$store.state.user == null">
                                <router-link to="/sign-up">
                                    <a class="link">Реєстрація</a>
                                </router-link>
                            </li>
                            <li v-if="$store.state.user !== null">
                                <a @click="logout" class="link">Вийти</a>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </nav>
    </div>
</template>
<script>
import Auth from "../../api/Auth/Auth.js";

export default {
    data() {
        return {
            usd: null,
        }
    },
    methods: {
        logout() {
            Auth.logout()
                .then(() => {
                    this.$store.commit('clearUser')
                    this.$router.push({name: 'home'});
                })
        }
    }
}
</script>
<style>
.mobile-cart {
    position: absolute;
    right: -40px;
    top: 40px
}
</style>
