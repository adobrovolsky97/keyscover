<template>
    <div class="card border bg-base-100 shadow-xl" :class="{'opacity-20': product.is_hidden, '!shadow-none': noShadow}">
        <figure class="w-full overflow-hidden p-1">
            <Carousel :arrows-size="'15px'" :show-thumbs="false"
                      :id="product.id"
                      :show-arrows="showArrows"
                      @link-clicked="handleClick"
                      :media="product.media.length ? product.media : [{id: null, url: product.image}]"/>
        </figure>

        <svg v-if="product.is_hidden" class="h-24 w-24 absolute z-[100] m-auto left-0 right-0 top-0 bottom-0"
             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round">
            <path
                d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
            <line x1="1" y1="1" x2="23" y2="23"/>
        </svg>
        <div class="card-body p-2 lg:p-4" :class="{'!p-4 lg:!p-2': minified}">

            <p v-if="!minified" class="text-xs text-gray-400 hover:font-bold cursor-pointer hover:text-black">Арт. {{ product.sku }}</p>
            <p v-if="!minified" class="text-xs text-gray-400">{{ product.category.breadcrumbs }}</p>

            <!-- Product name -->
            <router-link
                :to="{ name: 'product.show', params: { id: product.id } }"
                @click="handleClick($event)"
                class="font-bold lg:text-lg text-xs product-name hover:text-gray-400 cursor-pointer overflow-x-auto"
                :class="{'!text-xs !overflow-hidden !text-ellipsis !whitespace-nowrap': minified}"
            >
                {{ product.name }}
            </router-link>

            <div class="flex flex-row justify-between items-center">
                <div class="badge badge-outline badge-neutral badge-sm lg:badge-md" :class="{'!badge-sm': minified}">
                    {{ product.usd_price }}$
                </div>
                <div class="badge badge-outline badge-neutral badge-sm lg:badge-md" :class="{'!badge-sm': minified}">
                    {{ product.price }} грн.
                </div>
            </div>
            <div v-if="$store.state.user !== null && !minified" class="card-actions justify-center">

                <div class="flex flex-col items-center justify-center mb-2 relative">
                    <div class="flex flex-row justify-between items-center gap-1 mb-0 md:mb-0">
                        <div class="join">
                            <button :disabled="!couldBeDecremented()" @click="decrementQuantity"
                                    class="btn btn-neutral join-item btn-sm lg:btn-md" :class="{'!btn-xs': minified}">
                                -
                            </button>
                            <input :disabled="product.left_in_stock <= product.cart_increment_step"
                                   class="input input-bordered text-center input-sm lg:input-md join-item w-16 focus:outline-none"
                                   placeholder="" v-model="cartQty" :class="{'!input-xs': minified}"/>
                            <button :disabled="!couldBeIncremented()" @click="incrementQuantity"
                                    class="btn btn-neutral join-item btn-sm lg:btn-md" :class="{'!btn-xs': minified}">
                                +
                            </button>
                        </div>
                    </div>
                    <span class="text-error text-xs absolute -bottom-4" v-if="product.left_in_stock <= cartQty">В наявності: {{
                            product.left_in_stock
                        }} шт.</span>
                </div>

                <button v-if="!cartProduct && product.left_in_stock >= product.cart_increment_step" @click="addItemToCart(product)"
                        class="btn btn-neutral btn-block btn-sm lg:btn-md" :class="{'!btn-xs': minified}">
                    Додати до кошика
                </button>

                <button v-if="!cartProduct && product.left_in_stock < product.cart_increment_step" disabled
                        class="btn btn-neutral btn-block btn-sm lg:btn-md" :class="{'!btn-xs': minified}">
                    Немає в наявності
                </button>
                <button v-if="cartProduct && product.left_in_stock >= product.cart_increment_step" @click="updateProductQuantity"
                        class="btn btn-success btn-block btn-sm lg:btn-md text-white" :class="{'!btn-xs': minified}">
                    Оновити кількість
                </button>
            </div>

            <div v-if="$store.state.user !== null && minified" class="card-actions justify-center">

                <div class="flex flex-row justify-between items-center gap-1">
                    <div class="flex flex-row justify-between items-center gap-1 mb-0 md:mb-0">
                        <div class="join">
                            <button :disabled="!couldBeDecremented()" @click="decrementQuantity"
                                    class="btn btn-neutral join-item btn-sm lg:btn-md" :class="{'btn-sm lg:!btn-xs': minified}">
                                -
                            </button>
                            <input :disabled="product.left_in_stock <= product.cart_increment_step"
                                   class="input input-bordered text-center input-sm lg:input-md join-item"
                                   placeholder="" v-model="cartQty" :class="{'input-sm lg:!input-xs w-12 lg:w-8': minified}"/>
                            <button :disabled="!couldBeIncremented()" @click="incrementQuantity"
                                    class="btn btn-neutral join-item btn-sm lg:btn-md" :class="{'btn-sm lg:!btn-xs': minified}">
                                +
                            </button>
                        </div>
                    </div>

                    <button v-if="!cartProduct && product.left_in_stock >= product.cart_increment_step" @click="addItemToCart(product)"
                            class="btn btn-neutral btn-sm lg:btn-md" :class="{'btn-sm lg:!btn-xs': minified}">
                        Додати
                    </button>

                    <button v-if="!cartProduct && product.left_in_stock <= product.cart_increment_step" disabled
                            class="btn btn-neutral btn-sm lg:btn-md" :class="{'btn-sm lg:!btn-xs': minified}">
                        Немає
                    </button>
                    <button v-if="cartProduct && product.left_in_stock > 0" @click="updateProductQuantity"
                            class="btn btn-success btn-sm lg:btn-md text-white" :class="{'btn-sm lg:!btn-xs': minified}">
                        Оновити
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {toast} from "vue3-toastify";
import Carousel from "./Carousel.vue";

export default {
    name: 'Item',
    components: {Carousel},
    props: {
        product: {
            type: Object,
            required: true
        },
        minified: {
            type: Boolean,
            default: false
        },
        showArrows: {
            type: Boolean,
            default: true
        },
        noShadow: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            productErrors: {},
            cart: this.$store.state.cart,
            cartQty: 1,
            cartProduct: null,
            fallbackImage: '../../../../public/no-image.png',
            loading: true,
        }
    },
    watch: {
        '$store.state.cart': {
            handler: function () {
                this.cart = this.$store.state.cart;
                this.cartQty = this.getCartQuantityForCurrentProduct();
            },
            deep: true
        }
    },
    mounted() {
        if (this.$store.state.user !== null) {
            this.cartQty = this.getCartQuantityForCurrentProduct();
        }
    },
    methods: {
        getCartQuantityForCurrentProduct() {
            let cartItem = this.cart?.products?.find(item => item.product_id === this.product.id);
            this.cartProduct = cartItem ? cartItem : null;
            return cartItem ? cartItem.quantity : this.product.cart_increment_step;
        },
        addItemToCart(product) {

            if (this.cartQty < this.product.cart_increment_step) {
                toast.error("Невірна кількість товару", {
                    position: 'bottom-right'
                })
                return;
            }

            if (product.left_in_stock < this.cartQty) {
                this.cartQty = product.left_in_stock;
                toast.error("Недостатня кількість товарів на складі. Залишок: " + product.left_in_stock + " штук.", {
                    position: 'bottom-right'
                });
                return;
            }

            axios.post('/api/cart/' + product.id, {quantity: this.cartQty})
                .then((response) => {
                    toast.success("Товар додано до кошика!", {autoClose: 2000, position: 'bottom-right'});
                    this.$store.commit('setCart', response.data.data)
                    this.cart = response.data.data;
                    this.cartQty = this.getCartQuantityForCurrentProduct();
                })
        },
        incrementQuantity() {

            if (this.couldBeIncremented()) {
                this.cartQty+= this.product.cart_increment_step;
            } else {
                this.cartQty = this.product.left_in_stock;
                this.productErrors[this.product.id] = "В наявності: " + this.product.left_in_stock + " шт.";
            }
        },
        decrementQuantity() {
            if (!this.couldBeDecremented()) {
                return;
            }

            this.cartQty-= this.product.cart_increment_step;
        },
        couldBeIncremented() {
            return this.product.left_in_stock >= this.cartQty + this.product.cart_increment_step;
        },
        couldBeDecremented() {
            return this.cartQty > this.product.cart_increment_step;
        },
        updateProductQuantity() {
            if (this.cartQty < this.product.cart_increment_step) {
                this.cartQty = this.product.cart_increment_step;
            }

            if (this.cartQty % this.product.cart_increment_step !== 0) {
                this.cartQty = parseInt(this.cartQty) +  parseInt(this.product.cart_increment_step - (this.cartQty % this.product.cart_increment_step));
            }

            if (this.product.left_in_stock < this.cartQty) {
                this.cartQty = this.product.left_in_stock;
                toast.error("Недостатня кількість товарів на складі. Залишок: " + this.product.left_in_stock + " штук.", {
                    position: 'bottom-right'
                });
                return;
            }

            axios.patch(`/api/cart/${this.product.id}`, {quantity: this.cartQty})
                .then(response => {
                    this.$store.commit('setCart', response.data.data);
                    this.cart = response.data.data;
                    this.cartQty = this.getCartQuantityForCurrentProduct();
                    toast.success("Кількість товару оновлено!", {autoClose: 5000, position: 'bottom-right'});
                })
        },
        handleClick(event) {
            // Handle left-click only (event.button === 0) and let middle-click, Ctrl+click work as normal
            if (event.button === 0 && !event.ctrlKey && !event.metaKey) {
                event.preventDefault();
                this.saveScrollPosition();
                this.showProduct();
            }
        },
        saveScrollPosition() {
            this.$nextTick(() => {
                localStorage.setItem('productListState', JSON.stringify({
                    scrollPosition: window.scrollY,
                    productId: this.product.id
                }));
            });
        },
        showProduct() {
            return this.$router.push({name: 'product.show', params: {id: this.product.id}});
        },
        onImageError(event) {
            event.target.src = this.fallbackImage;
            this.loading = false;
        }
    }
}
</script>
<style>
.product-name {
    white-space: break-spaces;
}
</style>
