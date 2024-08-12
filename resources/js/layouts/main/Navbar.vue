<template>
    <nav
        class="navbar block py-4 w-full max-w-full rounded-none px-4 border-0 top-0 shadow-xl">
        <div class="container mx-auto flex items-center justify-between">
            <router-link
                :to="{name: 'home'}"
                custom
                v-slot="{ navigate }"
            >
                <a @click="navigate" class="block antialiased font-sans cursor-pointer text-lg font-bold">KeysCover</a>
            </router-link>
            <div class="hidden items-center gap-2 lg:flex">
                <div class="auth flex flex-row justify-center items-center gap-5">
                    <router-link
                        :to="{name: 'home'}"
                        custom
                        v-slot="{ navigate, href }"
                    >
                        <a @click="navigate"
                           :class="{'link-error': $route.path === '/'}"
                           class="link">Каталог</a>
                    </router-link>
                    <router-link
                        v-if="$store.state.user !== null"
                        :to="{name: 'orders-list'}"
                        custom
                        v-slot="{ navigate, href }"
                    >
                        <a @click="navigate" :class="{'link-error': $route.path === '/orders/list'}" class="link">Історія
                            замовлень</a>
                    </router-link>
                    <p class="mr-5 font-bold" v-if="usd">USD {{ usd }}</p>
                    <div v-if="$store.state.user !== null">
                        <button class="btn btn-outline rounded-2xl btn-sm" v-if="$route.name !== 'checkout'"
                                onclick="cartModal.showModal()">
                            <svg class="h-6 w-6 text-slate-500" width="24" height="24" viewBox="0 0 24 24"
                                 stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z"/>
                                <circle cx="9" cy="19" r="2"/>
                                <circle cx="17" cy="19" r="2"/>
                                <path d="M3 3h2l2 12a3 3 0 0 0 3 2h7a3 3 0 0 0 3 -2l1 -7h-15.2"/>
                            </svg>
                            <span class="mt-0.5">{{
                                    $store.state.cart?.total ? (($store.state.cart?.total ?? 0) - $store.state.cart?.discount_amount_uah ?? 0) : 0
                                }} грн.</span>
                        </button>

                        <button v-else class="btn btn-outline rounded-2xl btn-sm">
                            <svg class="h-6 w-6 text-slate-500" width="24" height="24" viewBox="0 0 24 24"
                                 stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z"/>
                                <circle cx="9" cy="19" r="2"/>
                                <circle cx="17" cy="19" r="2"/>
                                <path d="M3 3h2l2 12a3 3 0 0 0 3 2h7a3 3 0 0 0 3 -2l1 -7h-15.2"/>
                            </svg>
                            <span class="mt-0.5">{{ $store.state.cart?.total ?? 0 }} грн.</span>
                        </button>
                    </div>
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
                <button class="btn btn-ghost btn-sm" v-if="$route.name !== 'checkout'"
                        onclick="cartModal.showModal()">
                    <svg class="h-5 w-5 text-slate-500" width="24" height="24" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"/>
                        <circle cx="9" cy="19" r="2"/>
                        <circle cx="17" cy="19" r="2"/>
                        <path d="M3 3h2l2 12a3 3 0 0 0 3 2h7a3 3 0 0 0 3 -2l1 -7h-15.2"/>
                    </svg>
                    <span class="mt-0.5">{{ $store.state.cart?.total ?? 0 }} грн.</span>
                </button>
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="12" x2="21" y2="12"/>
                            <line x1="3" y1="6" x2="21" y2="6"/>
                            <line x1="3" y1="18" x2="21" y2="18"/>
                        </svg>
                    </div>
                    <ul
                        tabindex="0"
                        class="menu menu-sm dropdown-content bg-base-100 rounded-box z-[2] mt-3 w-52 p-2 shadow">
                        <li>
                            <router-link
                                :to="{name: 'home'}"
                                custom
                                v-slot="{ navigate, href }"
                            >
                                <a @click="navigate"
                                   :class="{'link-error': $route.path === '/'}"
                                   class="link">Каталог</a>
                            </router-link>
                        </li>
                        <li>
                            <router-link
                                v-if="$store.state.user !== null"
                                :to="{name: 'orders-list'}"
                                custom
                                v-slot="{ navigate, href }"
                            >
                                <a @click="navigate" :class="{'link-error': $route.path === '/orders/list'}"
                                   class="link">Історія замовлень</a>
                            </router-link>
                        </li>
                        <li>
                            <a @click="logout" class="link">Вийти</a>
                        </li>
                    </ul>
                </div>

            </div>
            <dialog v-if="$route.name !== 'checkout'" ref="cartModal" id="cartModal" class="modal">

                <div class="modal-box w-11/12 max-w-3xl">
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                    </form>
                    <Cart/>
                </div>
            </dialog>
        </div>
    </nav>
</template>
<script>
import Auth from "../../api/Auth/Auth.js";
import Cart from "../../views/cart/Cart.vue";

export default {
    data() {
        return {
            usd: null,
        }
    },
    mounted() {
        this.fetchConfigs();
        this.fetchCart();
    },
    components: {
        Cart
    },
    watch: {
        $route(to, from) {
            this.$refs?.cartModal?.close();
        }
    },
    methods: {

        fetchCart() {
            axios.get('/api/cart')
                .then(response => {
                    this.$store.commit('setCart', response.data.data)
                })
        },
        fetchConfigs() {
            axios.get('/api/configs')
                .then(response => {
                    this.$store.commit('setConfigs', response.data)
                    this.usd = response.data.usd;
                })
        },
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
