<template>
    <div class="fixed w-full z-50 bg-white">
        <nav
            class="navbar block py-2 w-full max-w-full rounded-none px-2 lg:px-4 border-0 top-0 shadow-xl">
            <div class="container mx-auto w-full flex items-center justify-between">
                <router-link
                    :to="{name: 'home'}"
                    custom
                    v-slot="{ navigate }"
                >
                    <div @click="navigate" class="flex flex-row justify-between items-center gap-2 cursor-pointer">
                        <div class="avatar">
                            <div class="w-8 lg:w-12 rounded-xl">
                                <img src="../../../../public/logo.png"/>
                            </div>
                        </div>
                        <a class="antialiased font-sans text-md lg:text-lg font-bold">KeysCover</a>
                    </div>
                </router-link>

                <div class="w-full hidden md:block mx-0 md:mx-14">
                    <label class="input input-bordered items-center w-full flex gap-2">
                        <input type="text" @input="onSearchInput" :value="search" class="grow"
                               :placeholder="$route.query.categories?.length ? 'Пошук по категорії': 'Пошук на сайті'"
                               v-on:keyup.enter="onSearchInput"/>
                        <svg
                            v-if="!$route.query.search?.length"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 16 16"
                            fill="currentColor"
                            class="h-4 w-4 opacity-70">
                            <path
                                fill-rule="evenodd"
                                d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"
                                clip-rule="evenodd"/>
                        </svg>
                        <svg v-else @click="resetSearch" class="h-5 w-5 cursor-pointer" width="24" height="24"
                             viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z"/>
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </label>
                </div>
                <div class="items-center justify-end gap-2 flex">
                    <div class="auth flex flex-row justify-between items-center gap-2.5 md:gap-5">

                        <div ref="mobileSearch" class="block md:hidden cursor-pointer">
                            <div class="relative" :class="{'animate-pulse text-red-400': $route.query.search?.length}">
                                <svg class="h-6 w-6"
                                     @click="isShowMobileSearch = !isShowMobileSearch" viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"/>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                </svg>

                                <svg v-if="$route.query.search?.length" class="h-5 w-5 absolute -top-3 -right-3.5"
                                     width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                     fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z"/>
                                    <path d="M3 20l1.3 -3.9a9 8 0 1 1 3.4 2.9l-4.7 1"/>
                                    <line x1="12" y1="12" x2="12" y2="12.01"/>
                                    <line x1="8" y1="12" x2="8" y2="12.01"/>
                                    <line x1="16" y1="12" x2="16" y2="12.01"/>
                                </svg>
                            </div>
                            <div v-if="isShowMobileSearch"
                                 class="absolute left-1/2 transform -translate-x-1/2 w-full max-w-sm top-14 z-[999]">
                                <label class="input input-bordered items-center w-full flex gap-2">
                                    <input type="text" @input="onSearchInput" :value="search" class="grow"
                                           :placeholder="$route.query.categories?.length ? 'Пошук по категорії': 'Пошук на сайті'"
                                           v-on:keyup.enter="closeMobileSearch"/>
                                    <svg
                                        v-if="!$route.query.search?.length"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 16 16"
                                        fill="currentColor"
                                        class="h-4 w-4 opacity-70">
                                        <path
                                            fill-rule="evenodd"
                                            d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"
                                            clip-rule="evenodd"/>
                                    </svg>
                                    <svg v-else @click="resetSearch" class="h-5 w-5 cursor-pointer" width="24"
                                         height="24"
                                         viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z"/>
                                        <line x1="18" y1="6" x2="6" y2="18"/>
                                        <line x1="6" y1="6" x2="18" y2="18"/>
                                    </svg>
                                </label>
                            </div>
                        </div>

                        <div class="dropdown dropdown-end" ref="contactDropdown">
                            <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar"
                                 @click="openContactsModal">
                                <svg class="h-6 w-6" width="27" height="27" viewBox="0 0 24 24" stroke-width="2"
                                     stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z"/>
                                    <path
                                        d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2"/>
                                </svg>
                            </div>

                            <div class="menu bg-base-100 rounded-box z-[2] mt-3 w-36 p-2 shadow absolute right-0 top-10"
                                 v-if="isContactsOpen">
                                <div class="flex flex-col justify-center items-center w-full">
                                    <div class="flex flex-row justify-between items-center gap-3">
                                        <a href="https://wa.me/380968038462" target="_blank">
                                            <svg fill="#000000" height="24" width="24" version="1.1" id="Layer_1"
                                                 xmlns="http://www.w3.org/2000/svg"
                                                 xmlns:xlink="http://www.w3.org/1999/xlink"
                                                 viewBox="0 0 504.4 504.4" xml:space="preserve">
                            <g>
                                <g>
                                    <path d="M254.8,119.4c-71.6,0-129.6,57.6-129.6,128.8c0,28,9.2,54.4,24.8,75.6l-16.4,48l50-16c20.4,13.6,45.2,21.2,71.6,21.2
                                        c71.6,0,130-57.6,130-128.8C384.8,177,326.4,119.4,254.8,119.4z M330.8,301.4c-3.2,8.8-18.8,17.2-25.6,18s-6.8,5.6-45.6-9.2
                                        c-38.4-15.2-62.8-54-64.8-56.8c-2-2.4-15.6-20.4-15.6-38.8c0-18.4,9.6-27.6,13.2-31.2c3.6-3.6,7.6-4.8,10-4.8
                                        c2.4,0,5.2,0.4,7.2,0.4c2.4,0,5.2-1.2,8.4,6.4c3.2,7.6,10.8,26,11.6,28s1.6,4,0.4,6.4c-1.2,2.4-2,4-3.6,6.4c-2,2-4,4.8-5.6,6.4
                                        c-2,2-4,4-1.6,7.6c2.4,3.6,10,16,21.2,26c14.4,12.8,26.8,16.8,30.4,18.8c3.6,2,6,1.6,8-0.8c2.4-2.4,9.6-10.8,12-14.8
                                        c2.4-3.6,5.2-3.2,8.4-2c3.6,1.2,22,10.4,26,12.4c3.6,2,6.4,2.8,7.2,4.4C333.6,285,333.6,292.6,330.8,301.4z"/>
                                </g>
                            </g>
                                                <g>
                                <g>
                                    <path d="M377.6,0.2H126.4C56.8,0.2,0,57,0,126.6v251.6c0,69.2,56.8,126,126.4,126H378c69.6,0,126.4-56.8,126.4-126.4V126.6
                                        C504,57,447.2,0.2,377.6,0.2z M254.8,401.4c-27.2,0-52.4-6.8-74.8-19.2l-85.6,27.2l28-82c-14-23.2-22-50.4-22-79.2
                                        c0-84.8,69.2-153.2,154.4-153.2s154.4,68.4,154.4,153.2S340,401.4,254.8,401.4z"/>
                                </g>
                            </g>
                        </svg>
                                        </a>
                                        <a href="tg://resolve?domain=Yuro_o" target="_blank">
                                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="27"
                                                 height="27"
                                                 viewBox="0 0 50 50">
                                                <path
                                                    d="M25,2c12.703,0,23,10.297,23,23S37.703,48,25,48S2,37.703,2,25S12.297,2,25,2z M32.934,34.375	c0.423-1.298,2.405-14.234,2.65-16.783c0.074-0.772-0.17-1.285-0.648-1.514c-0.578-0.278-1.434-0.139-2.427,0.219	c-1.362,0.491-18.774,7.884-19.78,8.312c-0.954,0.405-1.856,0.847-1.856,1.487c0,0.45,0.267,0.703,1.003,0.966	c0.766,0.273,2.695,0.858,3.834,1.172c1.097,0.303,2.346,0.04,3.046-0.395c0.742-0.461,9.305-6.191,9.92-6.693	c0.614-0.502,1.104,0.141,0.602,0.644c-0.502,0.502-6.38,6.207-7.155,6.997c-0.941,0.959-0.273,1.953,0.358,2.351	c0.721,0.454,5.906,3.932,6.687,4.49c0.781,0.558,1.573,0.811,2.298,0.811C32.191,36.439,32.573,35.484,32.934,34.375z"></path>
                                            </svg>
                                        </a>
                                        <a href="viber://chat?number=%2B380968038462" target="_blank">
                                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="27"
                                                 height="27"
                                                 viewBox="0 0 50 50">
                                                <path
                                                    d="M 44.78125 13.15625 C 44 10.367188 42.453125 8.164063 40.1875 6.605469 C 37.328125 4.632813 34.039063 3.9375 31.199219 3.511719 C 27.269531 2.925781 23.710938 2.84375 20.316406 3.257813 C 17.136719 3.648438 14.742188 4.269531 12.558594 5.273438 C 8.277344 7.242188 5.707031 10.425781 4.921875 14.734375 C 4.539063 16.828125 4.28125 18.71875 4.132813 20.523438 C 3.789063 24.695313 4.101563 28.386719 5.085938 31.808594 C 6.046875 35.144531 7.722656 37.527344 10.210938 39.09375 C 10.84375 39.492188 11.65625 39.78125 12.441406 40.058594 C 12.886719 40.214844 13.320313 40.367188 13.675781 40.535156 C 14.003906 40.6875 14.003906 40.714844 14 40.988281 C 13.972656 43.359375 14 48.007813 14 48.007813 L 14.007813 49 L 15.789063 49 L 16.078125 48.71875 C 16.269531 48.539063 20.683594 44.273438 22.257813 42.554688 L 22.472656 42.316406 C 22.742188 42.003906 22.742188 42.003906 23.019531 42 C 25.144531 41.957031 27.316406 41.875 29.472656 41.757813 C 32.085938 41.617188 35.113281 41.363281 37.964844 40.175781 C 40.574219 39.085938 42.480469 37.355469 43.625 35.035156 C 44.820313 32.613281 45.527344 29.992188 45.792969 27.019531 C 46.261719 21.792969 45.929688 17.257813 44.78125 13.15625 Z M 35.382813 33.480469 C 34.726563 34.546875 33.75 35.289063 32.597656 35.769531 C 31.753906 36.121094 30.894531 36.046875 30.0625 35.695313 C 23.097656 32.746094 17.632813 28.101563 14.023438 21.421875 C 13.277344 20.046875 12.761719 18.546875 12.167969 17.09375 C 12.046875 16.796875 12.054688 16.445313 12 16.117188 C 12.050781 13.769531 13.851563 12.445313 15.671875 12.046875 C 16.367188 11.890625 16.984375 12.136719 17.5 12.632813 C 18.929688 13.992188 20.058594 15.574219 20.910156 17.347656 C 21.28125 18.125 21.113281 18.8125 20.480469 19.390625 C 20.347656 19.511719 20.210938 19.621094 20.066406 19.730469 C 18.621094 20.816406 18.410156 21.640625 19.179688 23.277344 C 20.492188 26.0625 22.671875 27.933594 25.488281 29.09375 C 26.230469 29.398438 26.929688 29.246094 27.496094 28.644531 C 27.574219 28.566406 27.660156 28.488281 27.714844 28.394531 C 28.824219 26.542969 30.4375 26.726563 31.925781 27.78125 C 32.902344 28.476563 33.851563 29.210938 34.816406 29.917969 C 36.289063 31 36.277344 32.015625 35.382813 33.480469 Z M 26.144531 15 C 25.816406 15 25.488281 15.027344 25.164063 15.082031 C 24.617188 15.171875 24.105469 14.804688 24.011719 14.257813 C 23.921875 13.714844 24.289063 13.199219 24.835938 13.109375 C 25.265625 13.035156 25.707031 13 26.144531 13 C 30.476563 13 34 16.523438 34 20.855469 C 34 21.296875 33.964844 21.738281 33.890625 22.164063 C 33.808594 22.652344 33.386719 23 32.90625 23 C 32.851563 23 32.796875 22.996094 32.738281 22.984375 C 32.195313 22.894531 31.828125 22.378906 31.917969 21.835938 C 31.972656 21.515625 32 21.1875 32 20.855469 C 32 17.628906 29.371094 15 26.144531 15 Z M 31 21 C 31 21.550781 30.550781 22 30 22 C 29.449219 22 29 21.550781 29 21 C 29 19.347656 27.652344 18 26 18 C 25.449219 18 25 17.550781 25 17 C 25 16.449219 25.449219 16 26 16 C 28.757813 16 31 18.242188 31 21 Z M 36.710938 23.222656 C 36.605469 23.6875 36.191406 24 35.734375 24 C 35.660156 24 35.585938 23.992188 35.511719 23.976563 C 34.972656 23.851563 34.636719 23.316406 34.757813 22.777344 C 34.902344 22.140625 34.976563 21.480469 34.976563 20.816406 C 34.976563 15.957031 31.019531 12 26.160156 12 C 25.496094 12 24.835938 12.074219 24.199219 12.21875 C 23.660156 12.34375 23.125 12.003906 23.003906 11.464844 C 22.878906 10.925781 23.21875 10.390625 23.757813 10.269531 C 24.539063 10.089844 25.347656 10 26.160156 10 C 32.125 10 36.976563 14.851563 36.976563 20.816406 C 36.976563 21.628906 36.886719 22.4375 36.710938 23.222656 Z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                    <a @click="copyNumber" class="cursor-pointer text-orange-500 mt-2 font-bold">
                                        0968038462
                                    </a>
                                </div>
                            </div>

                        </div>

                        <div v-if="$store.state.user !== null">
                            <button class="btn btn-outline rounded-2xl btn-sm text-xs"
                                    v-if="$route.name !== 'checkout'"
                                    onclick="cartModal.showModal()">
                                <span class="flex flex-row gap-1 whitespace-nowrap">
                                    <svg class="h-5 w-5 text-slate-500" width="24" height="24" viewBox="0 0 24 24"
                                         stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                                         stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z"/>
                                        <circle cx="9" cy="19" r="2"/>
                                        <circle cx="17" cy="19" r="2"/>
                                        <path d="M3 3h2l2 12a3 3 0 0 0 3 2h7a3 3 0 0 0 3 -2l1 -7h-15.2"/>
                                    </svg>
                                    <span class="mt-0.5">{{
                                            $store.state.cart?.total ? (($store.state.cart?.total ?? 0) - $store.state.cart?.discount_amount_uah ?? 0) : 0
                                        }} ₴</span>
                                </span>
                            </button>

                            <button v-else class="btn btn-outline rounded-2xl btn-sm md:w-32">
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

                        <div class="dropdown dropdown-end" ref="dropdown">
                            <div tabindex="0" role="button" class="btn  btn-circle btn-ghost avatar"
                                 @click="toggleDropdown">
                                <div class="relative" :class="{'animate-pulse text-red-400': notificationsCount > 0}">
                                    <svg class="h-8 w-8"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span v-if="notificationsCount > 0"
                                          class="indicator-item badge badge-xs badge-error absolute top-0 right-0"></span>
                                </div>
                            </div>

                            <ul v-if="isOpen"
                                tabindex="0"
                                class="menu dropdown-content bg-base-100 rounded-box z-[2] mt-3 w-52 p-2 shadow">
                                <li v-if="$store.state.user">
                                    <p>Вітаємо, {{ $store.state.user.name }}</p>
                                </li>
                                <li v-if="$store.state.user !== null">
                                    <router-link
                                        :to="{name: 'notifications'}"
                                        custom
                                        v-slot="{ navigate, href }"
                                    >
                                        <a @click="navigate" :class="{'link-error': $route.path === '/notifications'}"
                                           class="link">Сповіщень ({{ notificationsCount }})</a>
                                    </router-link>
                                </li>
                                <li>
                                    <a @click="goToProducts"
                                       :class="{'link-error': $route.path === '/'}"
                                       class="link">Каталог</a>
                                </li>
                                <li v-if="$store.state.user !== null">
                                    <router-link
                                        :to="{name: 'orders-list'}"
                                        custom
                                        v-slot="{ navigate, href }"
                                    >
                                        <a @click="navigate" :class="{'link-error': $route.path === '/orders/list'}"
                                           class="link">Історія замовлень</a>
                                    </router-link>
                                </li>
                                <li v-if="$store.state.user !== null">
                                    <router-link
                                        :to="{name: 'favorites'}"
                                        custom
                                        v-slot="{ navigate, href }"
                                    >
                                        <a @click="navigate" :class="{'link-error': $route.path === '/favorites'}"
                                           class="link">Улюблені товари</a>
                                    </router-link>
                                </li>
                                <li v-if="$store.state.user !== null && $store.state.user.role === 'admin'">
                                    <router-link
                                        :to="{name: 'admin.dashboard'}"
                                        custom
                                        v-slot="{ navigate, href }"
                                    >
                                        <a @click="navigate"
                                           class="link">Admin</a>
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
                <dialog v-if="$route.name !== 'checkout'" ref="cartModal" id="cartModal" class="modal">

                    <div class="modal-box w-11/12 max-w-3xl">
                        <form method="dialog">
                            <button class="btn btn-lg btn-circle btn-ghost absolute right-2 top-2">✕</button>
                        </form>
                        <Cart/>
                    </div>
                    <form method="dialog" class="modal-backdrop">
                        <button></button>
                    </form>
                </dialog>
            </div>
        </nav>
        <transition name="slide-fade">
            <nav
                v-if="isShowSubNavbar && header_message"
                class="w-full shadow-xl flex flex-row gap-4 items-center justify-center bg-base-200 notification"
            >
                <MdPreview class="w-full text-center" :modelValue="header_message"/>
            </nav>
        </transition>
    </div>
</template>
<script>
import Auth from "../../api/Auth/Auth.js";
import Cart from "../../views/cart/Cart.vue";
import {toast} from "vue3-toastify";
import Modal from "../../components/Modal.vue";
import axios from "axios";
import {MdPreview} from "md-editor-v3";
import 'md-editor-v3/lib/preview.css';
import {debounce} from 'lodash';

export default {
    data() {
        return {
            notificationsCount: 0,
            isContactsOpen: false,
            isOpen: false,
            isDataLoaded: false,
            isShowSubNavbar: true,
            isShowMobileSearch: false,
            search: '',
            header_message: null,
        }
    },
    created() {
        this.fetchCart();
        this.emitter.on('show-cart', (evt) => {
            this.$refs?.cartModal?.showModal();
        })
    },
    mounted() {
        this.search = this.$route.query.search ?? '';
        this.debouncedDelaySearch = debounce(this.updateQuery, 500);

        document.addEventListener('click', this.handleClickOutside);
        document.addEventListener('click', this.handleClickOutsideMobileSearch);
        window.addEventListener("scroll", this.checkScrollPosition);
        this.getNotificationsCount();

        if (this.$store.state.user !== null) {
            setInterval(() => {
                this.getNotificationsCount();
            }, 60000);
        }
    },
    beforeDestroy() {
        document.removeEventListener('click', this.handleClickOutside);
        document.removeEventListener('click', this.handleClickOutsideMobileSearch);
        window.addEventListener("scroll", this.checkScrollPosition);
    },
    components: {
        MdPreview,
        Modal,
        Cart
    },
    watch: {
        $route(to, from) {
            this.$refs?.cartModal?.close();

            if (to.path !== from.path) {
                this.fetchCart();
            }

            this.search = to.query.search ?? '';
        },
        '$store.state.configs'(value) {
            this.header_message = value?.header_message;
        }
    },
    methods: {
        toggleDropdown() {
            this.isOpen = !this.isOpen;
        },
        closeDropdown() {
            this.isOpen = false;
        },
        openContactsModal() {
            this.isContactsOpen = !this.isContactsOpen;
        },
        handleClickOutside(event) {
            const dropdown = this.$refs.dropdown;
            if (dropdown && !dropdown.contains(event.target)) {
                this.closeDropdown();
            }

            const contactsDropdown = this.$refs.contactDropdown;

            if (contactsDropdown && !contactsDropdown.contains(event.target)) {
                this.isContactsOpen = false;
            }
        },
        handleClickOutsideMobileSearch(event) {

            if (!this.isShowMobileSearch) {
                return;
            }

            const search = this.$refs.mobileSearch;
            if (search && !search.contains(event.target)) {
                this.closeMobileSearch();
            }
        },
        onSearchInput(event) {
            this.search = event.target.value; // Оновлюємо локальний стан
            this.debouncedDelaySearch(this.search); // Викликаємо debounce для оновлення URL
        },

        updateQuery(value) {

            if (this.$route.name !== 'home') {
                this.$router.push({name: 'home', query: {search: value}});
                return
            }

            const query = {...this.$route.query, search: value};
            if (!value.length) delete query.search;
            this.$router.replace({query});
        },

        resetSearch() {
            this.search = '';
            const query = {...this.$route.query};
            delete query.search;
            this.$router.replace({query});
        },

        closeMobileSearch() {
            this.isShowMobileSearch = false;
            const query = {...this.$route.query, search: this.search};
            if (!this.search.length) delete query.search;
            this.$router.replace({query});
        },
        copyNumber() {
            navigator.clipboard.writeText('0968038462').then(() => {
                toast.success('Телефон скопійовано');
            }).catch(err => {
            })
        },
        fetchCart() {
            axios.get('/api/cart')
                .then(response => {
                    this.$store.commit('setCart', response.data.data)

                    if (response.data.data.is_changed == true) {
                        setTimeout(() => {
                            toast.warn('Було перераховано кількість товарів в кошику в звязку зменшенням залишків на складі!', {
                                timeout: 5000,
                                position: 'bottom-right'
                            });
                        }, 1000);

                    }
                })
        },
        logout() {
            Auth.logout()
                .then(() => {
                    this.$store.commit('clearUser')
                    this.$router.push({name: 'home'});
                })
        },
        async getNotificationsCount() {
            axios.get('/api/notifications/count')
                .then((response) => {
                    this.notificationsCount = response.data.count ?? 0;
                })
        },
        checkScrollPosition() {
            const currentScrollY = window.scrollY;
            if (currentScrollY > 200) {
                this.isShowSubNavbar = false;
            } else {
                this.isShowSubNavbar = true;
            }
        },
        goToProducts() {
            localStorage.removeItem('productListState');

            this.$router.push({name: 'home'});
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

.slide-fade-enter-active, .slide-fade-leave-active {
    transition: transform .2s ease, opacity .2s ease;
}

/* Enter transition - sliding in */
.slide-fade-enter, .slide-fade-leave-to {
    transform: translateY(-100%); /* Start off-screen */
    opacity: 0;
}

/* Enter transition - end state */
.slide-fade-enter-to {
    transform: translateY(0); /* End in view */
    opacity: 1;
}

/* Leave transition - sliding out */
.slide-fade-leave {
    transform: translateY(-100%); /* Move off-screen when leaving */
    opacity: 0;
}
</style>

<style>
.notification .md-editor-previewOnly {
    background: transparent !important;
}

.notification .md-editor-preview-wrapper {
    padding: 0 !important;
}
</style>
