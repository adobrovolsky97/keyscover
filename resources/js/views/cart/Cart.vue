<template>
    <div class="w-full">
        <h3 class="text-lg font-bold mb-4">Ваш кошик</h3>
        <p class="font-bold" v-if="usd">Курс USD {{ usd }}</p>
        <button v-if="($store.state.cart?.products ?? []).length"
                class="btn btn-warning btn-outline rounded-full float-start mb-4 btn-sm"
                @click="clearCart">Очистити кошик
        </button>
        <div v-if="($store.state.cart?.products ?? []).length"
             class="flex flex-col container w-full mx-auto px-4 border items-center justify-center rounded-lg shadow">
            <ul class="flex flex-col divide-y w-full">
                <li v-for="product in $store.state.cart?.products ?? []" :key="product.id"
                    class="flex flex-col xl:flex-row relative">
                    <div
                        class="select-none cursor-pointer flex flex-col xl:flex-row items-center xl:p-4 p-0 w-full overflow-hidden break-all">
                        <div class="flex flex-col w-24 h-24 justify-center items-center mb-4 xl:mb-0 xl:mr-4">
                            <div class="avatar">
                                <div class="w-24 rounded">
                                    <img :src="product.image"/>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 pl-3 mb-4 xl:mb-0 xl:mr-16 text-center xl:text-start">
                            <div class="text-sm text-gray-400">{{ product.sku }}</div>
                            <div class="font-medium">
                                <router-link
                                    :to="{ name: 'product.show', params: { id: product.product_id } }"
                                    @click="handleClick($event)"
                                    class="w-full h-full object-cover cursor-pointer"
                                >
                                    {{ product.name }}
                                </router-link>
                            </div>
                            <div class="text-sm flex flex-row xl:mt-0 mt-2 justify-center xl:justify-start gap-1">
                                <span>{{ product.price }}$</span>
                                <span>~{{ product.uah_price }}грн. за шт.</span>
                            </div>
                        </div>
                        <div class="flex flex-col justify-center items-center xl:gap-2 mb-2 xl:mb-0">
                            <div class="text-sm font-bold flex flex-row pb-2 md:pb-0">
                                <span>{{ product.total_price_usd }}$</span>
                                <span> ~{{ product.total_price_uah }}грн.</span>
                            </div>
                            <div class="flex flex-row justify-between items-center gap-1 mb-4 xl:mb-0 xl:mr-4">
                                <div class="join">
                                    <button
                                        :disabled="product.quantity <= product.cart_increment_step"
                                        @click="decrementQuantity(product)"
                                        class="btn btn-success text-white join-item btn-md rounded-l-full">
                                        -
                                    </button>
                                    <input @input="updateProductQuantity(product)"
                                           class="input input-bordered text-center input-md join-item w-14 focus:outline-none"
                                           placeholder="" v-model="product.quantity"/>
                                    <button @click="incrementQuantity(product)"
                                            :disabled="product.left_in_stock <= product.quantity"
                                            class="btn btn-success text-white join-item btn-md rounded-r-full">
                                        +
                                    </button>
                                </div>
                            </div>
                            <span v-if="productErrors[product.product_id]" class="text-error text-md absolute bottom-0">
                                {{ productErrors[product.product_id] }}
                            </span>
                        </div>
                        <!-- Delete Button Container -->
                        <div
                            @click="deleteProduct(product)"
                            class="absolute top-0 left-0 mt-4 ml-2 xl:relative xl:mt-0 xl:ml-0"
                        >
                            <svg class="h-6 w-6 text-red-500" width="24" height="24"
                                 viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                 fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z"/>
                                <line x1="4" y1="7" x2="20" y2="7"/>
                                <line x1="10" y1="11" x2="10" y2="17"/>
                                <line x1="14" y1="11" x2="14" y2="17"/>
                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                            </svg>
                        </div>
                    </div>
                </li>

            </ul>

            <div class="totals w-full flex flex-col xl:justify-end xl:items-end justify-start items-start mb-4 text-sm">
                                    <span>Сума замовлення: <span class="font-bold">{{
                                            $store.state.cart.total_usd ?? 0
                                        }} $ / {{ $store.state.cart.total ?? 0 }} грн.</span></span>
                <span v-if="$store.state.cart.discount_percent > 0">
                                        Сума знижки (<span
                    class="text-success font-bold">{{ $store.state.cart.discount_percent }}%</span>): <span
                    class="font-bold">{{
                        $store.state.cart.discount_amount
                    }} $ / {{ $store.state.cart.discount_amount_uah }} грн.</span>
                                    </span>
                <span v-if="$store.state.cart.discount_amount > 0">
                                        До сплати: <span class="font-bold">{{
                        ($store.state.cart?.total_usd - $store.state.cart.discount_amount).toFixed(2) ?? 0
                    }} $ / {{
                        ($store.state.cart?.total - $store.state.cart.discount_amount_uah).toFixed(0) ?? 0
                    }} грн.</span>
                                    </span>
                <span class="text-success font-bold" v-if="freeDeliveryRemaining <= 0">Безкоштовна доставка.</span>
            </div>

            <div class="discount-text mb-4">
                <p v-if="freeDeliveryRemaining > 0">Для безкоштовної доставки додайте товарів ще на <span
                    class="text-success font-bold">{{ freeDeliveryRemaining }} $</span></p>
                <p v-if="fivePercentDiscountRemaining > 0">Для знижки в <span
                    class="text-success font-bold">5%</span> додайте товарів ще на <span
                    class="text-success font-bold">{{ fivePercentDiscountRemaining }} $ </span>
                </p>
                <p v-if="tenPercentDiscountRemaining > 0">Для знижки в <span
                    class="text-success font-bold">10%</span> додайте товарів ще на <span
                    class="text-success font-bold">{{ tenPercentDiscountRemaining }} $</span></p>
            </div>

            <button v-if="$route.name !== 'checkout'" @click="goToCheckout"
                    class="btn btn-outline btn-success btn-block rounded-full mb-4">Оформити
                замовлення
            </button>
        </div>
        <p class="p-4 text-center text-lg" v-else>Кошик порожній</p>
    </div>
</template>
<script>
import {toast} from "vue3-toastify";

export default {
    data() {
        return {
            fivePercentDiscountRemaining: 0,
            tenPercentDiscountRemaining: 0,
            freeDeliveryRemaining: null,
            timer: null,
            productErrors: {},
            usd: null,
        }
    },
    mounted() {
        this.calculateRemainingDiscounts();
        this.fetchConfigs();
    },
    watch: {
        '$store.state.cart.total': function () {
            this.calculateRemainingDiscounts();

            if (this.$store.state.cart.total === 0 && this.$route.name === 'checkout') {
                this.$router.push({name: 'home'})
            }
        }
    },
    methods: {
        fetchConfigs() {
            axios.get('/api/configs')
                .then(response => {
                    this.$store.commit('setConfigs', response.data)
                    this.usd = response.data.usd;
                })
                .catch((error) => {
                    toast.error(error?.response?.data?.message ?? 'Помилка', {
                        autoClose: 5000,
                        position: 'bottom-right'
                    });
                })
        },
        deleteProduct(product) {
            axios.delete(`/api/cart/${product.product_id}`)
                .then(response => {
                    this.$store.commit('setCart', response.data.data);
                })
                .catch((error) => {
                    toast.error(error?.response?.data?.message ?? 'Помилка', {
                        autoClose: 5000,
                        position: 'bottom-right'
                    });
                })
        },
        clearCart() {
            axios.delete('/api/cart')
                .then(() => {
                    this.$store.commit('setCart', {})
                })
                .catch((error) => {
                    toast.error(error?.response?.data?.message ?? 'Помилка', {
                        autoClose: 5000,
                        position: 'bottom-right'
                    });
                })
        },
        goToCheckout() {
            this.$router.push({name: 'checkout'})
        },
        incrementQuantity(product) {

            this.productErrors = {};
            axios.patch(`/api/cart/${product.product_id}`, {quantity: (parseInt(product.quantity) + parseInt(product.cart_increment_step))})
                .then(response => {
                    this.$store.commit('setCart', response.data.data);
                })
                .catch((error) => {

                    if (error.response.status === 404) {
                        this.fetchCart();
                        return;
                    }

                    this.productErrors[product.product_id] = error.response.data.message;
                })
        },
        decrementQuantity(product) {
            if (product.quantity <= product.cart_increment_step) {
                return;
            }

            this.productErrors = {};

            axios.patch(`/api/cart/${product.product_id}`, {quantity: (parseInt(product.quantity) - parseInt(product.cart_increment_step))})
                .then(response => {
                    this.$store.commit('setCart', response.data.data);
                })
                .catch((error) => {

                    if (error.response.status === 404) {
                        this.fetchCart();
                        return;
                    }

                    toast.error(error?.response?.data?.message ?? 'Помилка', {
                        autoClose: 5000,
                        position: 'bottom-right'
                    });
                })
        },
        calculateRemainingDiscounts() {
            this.fivePercentDiscountRemaining = ((this.$store.state.configs.five_percent_discount - this.$store.state.cart.total_usd)).toFixed(2);
            this.tenPercentDiscountRemaining = ((this.$store.state.configs.ten_percent_discount - this.$store.state.cart.total_usd)).toFixed(2);
            this.freeDeliveryRemaining = (this.$store.state.configs.free_delivery_sum - this.$store.state.cart.total_usd).toFixed(2);
        },
        handleClick(event) {
            // Handle left-click only (event.button === 0) and let middle-click, Ctrl+click work as normal
            if (event.button === 0 && !event.ctrlKey && !event.metaKey) {
                event.preventDefault();
                this.showProduct();
            }
        },
        updateProductQuantity(product) {

            this.productErrors = {};
            clearTimeout(this.timer);
            this.timer = setTimeout(() => {

                if (product.quantity < product.cart_increment_step) {
                    product.quantity = product.cart_increment_step;
                }

                if (product.quantity % product.cart_increment_step !== 0) {
                    product.quantity = parseInt(product.quantity) + parseInt(product.cart_increment_step - (product.quantity % product.cart_increment_step));
                }

                if (product.left_in_stock <= product.quantity) {
                    product.quantity = product.left_in_stock - (product.left_in_stock % product.cart_increment_step);
                }

                axios.patch(`/api/cart/${product.product_id}`, {quantity: product.quantity})
                    .then(response => {
                        this.$store.commit('setCart', response.data.data);
                        this.cart = response.data.data;
                    })
                    .catch((error) => {

                        if (error.response.status === 404) {
                            this.fetchCart();
                            return;
                        }

                        this.productErrors[product.product_id] = error.response.data.message;
                    })
            }, 800);
        },
        fetchCart() {
            axios.get('/api/cart')
                .then(response => {
                    this.$store.commit('setCart', response.data.data)

                    toast.warn('Було перераховано кількість товарів в кошику', {
                        timeout: 15000,
                        position: 'bottom-right'
                    });
                })
        },
    },
}
</script>
