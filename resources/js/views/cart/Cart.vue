<template>
    <div class="w-full">
        <h3 class="text-lg font-bold mb-4">Ваш кошик</h3>
        <button v-if="($store.state.cart?.products ?? []).length"
                class="btn btn-warning btn-outline rounded-full float-start mb-4 btn-sm"
                @click="clearCart">Очистити кошик
        </button>
        <div v-if="($store.state.cart?.products ?? []).length"
             class="flex flex-col container w-full mx-auto px-4 border items-center justify-center rounded-lg shadow">
            <ul class="flex flex-col divide-y w-full">
                <li v-for="product in $store.state.cart?.products ?? []" :key="product.id"
                    class="flex flex-col md:flex-row relative">
                    <div class="select-none cursor-pointer flex flex-col md:flex-row items-center md:p-4 p-0 w-full">
                        <div class="flex flex-col w-24 h-24 justify-center items-center mb-4 md:mb-0 md:mr-4">
                            <div class="avatar">
                                <div class="w-24 rounded">
                                    <img :src="product.image"/>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 pl-3 mb-4 md:mb-0 md:mr-16 text-center lg:text-start">
                            <div class="text-sm text-gray-400">{{ product.sku }}</div>
                            <div class="font-medium">{{ product.name }}</div>
                            <div class="text-sm flex flex-col lg:flex-row lg:mt-0 mt-2 justify-between lg:justify-start gap-2">
                                <span>{{ product.total_price_usd }} $</span>
                                <span> ~{{ product.total_price_uah }} грн.</span>
                            </div>
                        </div>
                        <div class="flex flex-col justify-center items-center lg:gap-2 mb-2 lg:mb-0">
                            <div class="flex flex-row justify-between items-center gap-1 mb-4 md:mb-0 md:mr-4">
                                <div class="join">
                                    <button @click="decrementQuantity(product)"
                                            class="btn btn-success join-item btn-md rounded-l-full">
                                        -
                                    </button>
                                    <input @input="updateProductQuantity(product)"
                                           class="input input-bordered text-center input-md join-item w-14"
                                           placeholder="" v-model="product.quantity"/>
                                    <button @click="incrementQuantity(product)"
                                            class="btn btn-success join-item btn-md rounded-r-full">
                                        +
                                    </button>
                                </div>
                            </div>
                            <div v-if="productErrors[product.product_id]" class="text-error text-md">
                                {{ productErrors[product.product_id]}}
                            </div>
                        </div>
                        <!-- Delete Button Container -->
                        <div
                            @click="deleteProduct(product)"
                            class="absolute top-0 left-0 mt-4 ml-2 md:relative md:mt-0 md:ml-0"
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

            <div class="totals w-full flex flex-col md:justify-end md:items-end justify-start items-start mb-4 text-sm">
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
                <p v-if="freeDeliveryRemaining > 0">Для безкоштовної доставки додайте товарів на <span
                    class="text-success font-bold">{{ freeDeliveryRemaining }} $</span></p>
                <p v-if="fivePercentDiscountRemaining > 0">Для знижки в <span
                    class="text-success font-bold">5%</span> додайте товарів на <span
                    class="text-success font-bold">{{ fivePercentDiscountRemaining }} $ </span>
                </p>
                <p v-if="tenPercentDiscountRemaining > 0">Для знижки в <span
                    class="text-success font-bold">10%</span> додайте товарів на <span
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
        }
    },
    mounted() {
        this.calculateRemainingDiscounts();
    },
    watch: {
        '$store.state.cart.total': function () {
            this.calculateRemainingDiscounts();

            if (this.$store.state.cart.total === 0) {
                this.$router.push({name: 'home'})
            }
        }
    },
    methods: {
        deleteProduct(product) {
            axios.delete(`/api/cart/${product.product_id}`)
                .then(response => {
                    this.$store.commit('setCart', response.data.data);
                })
        },
        clearCart() {
            axios.delete('/api/cart')
                .then(() => {
                    this.$store.commit('setCart', {})
                })
        },
        goToCheckout() {
            this.$router.push({name: 'checkout'})
        },
        incrementQuantity(product) {

            this.productErrors = {};
            axios.patch(`/api/cart/${product.product_id}`, {quantity: product.quantity + 1})
                .then(response => {
                    this.$store.commit('setCart', response.data.data);
                })
                .catch((error) => {
                    this.productErrors[product.product_id] = error.response.data.message;
                })
        },
        decrementQuantity(product) {
            if (product.quantity <= 1) {
                return;
            }

            this.productErrors = {};

            axios.patch(`/api/cart/${product.product_id}`, {quantity: product.quantity - 1})
                .then(response => {
                    this.$store.commit('setCart', response.data.data);
                })
        },
        calculateRemainingDiscounts() {
            this.fivePercentDiscountRemaining = ((this.$store.state.configs.five_percent_discount - this.$store.state.cart.total_usd)).toFixed(2);
            this.tenPercentDiscountRemaining = ((this.$store.state.configs.ten_percent_discount - this.$store.state.cart.total_usd)).toFixed(2);
            this.freeDeliveryRemaining = (this.$store.state.configs.free_delivery_sum - this.$store.state.cart.total_usd).toFixed(2);
        },
        updateProductQuantity(product) {
            this.productErrors = {};
            clearTimeout(this.timer);
            this.timer = setTimeout(() => {
                axios.patch(`/api/cart/${product.product_id}`, {quantity: product.quantity})
                    .then(response => {
                        this.$store.commit('setCart', response.data.data);
                        this.cart = response.data.data;
                        this.cartQty = this.getCartQuantityForCurrentProduct();
                    })
                    .catch((error) => {
                        this.productErrors[product.product_id] = error.response.data.message;
                    })
            }, 800);
        }
    },
}
</script>
