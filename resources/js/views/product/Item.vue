<template>
    <div class="card border bg-base-100 shadow-xl" :class="{'opacity-20': product.is_hidden, '!shadow-none': noShadow}">
        <FavoriteButton @removed="handleFavoriteRemoved" v-if="$store.state.user !== null && !minified"
                        classes="absolute right-2 top-2" :product-id="product.id"
                        :is-in-favorites="product.has_active_favorite"/>
        <figure class="w-full overflow-hidden p-1">
            <Carousel v-if="product.media.length > 1" :arrows-size="'15px'" :show-thumbs="false"
                      :id="product.id"
                      :show-arrows="showArrows"
                      @link-clicked="handleClick"
                      :media="product.media.length ? product.media : [{id: null, url: product.image}]"/>
            <router-link
                v-else
                :to="{ name: 'product.show', params: { id: product.id } }"
                @click="handleClick($event)"
                class="w-full h-full object-cover cursor-pointer"
            >
                <img
                    v-show="!loading"
                    @load="loading = false"
                    :src="product.image"
                    :alt="product.name"
                    class="w-full h-full object-cover cursor-pointer"
                />
            </router-link>
        </figure>

        <svg v-if="product.is_hidden" class="h-24 w-24 absolute z-[100] m-auto left-0 right-0 top-0 bottom-0"
             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round">
            <path
                d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
            <line x1="1" y1="1" x2="23" y2="23"/>
        </svg>
        <div class="card-body p-2 lg:p-4" :class="{'!p-4 lg:!p-2': minified}">

            <p v-if="!minified" class="text-xs text-gray-400 hover:font-bold cursor-pointer hover:text-black">
                <span @click="copyToClipboard(product.sku)">Арт. {{ product.sku }}</span>
            </p>
            <p v-if="!minified" class="text-xs text-gray-400">{{ product.category.breadcrumbs }}</p>

            <router-link
                :to="{ name: 'product.show', params: { id: product.id } }"
                @click="handleClick($event)"
                class="font-bold lg:text-lg text-xs product-name hover:text-gray-400 cursor-pointer text-start"
                :data-tip="product.name"
                :class="{'!text-xs': minified, 'tooltip': isTextTrimmed && !minified}"
            >
                <p
                    ref="productName"
                    class="w-full overflow-hidden text-ellipsis lg:line-clamp-2 line-clamp-1"
                >
                    {{ product.name }}
                </p>
            </router-link>

            <div class="flex flex-row justify-between items-center">
                <div class="badge badge-outline badge-neutral badge-sm xl:badge-md" :class="{'!badge-sm': minified}">
                    {{ product.usd_price }}$
                </div>
                <div class="badge badge-outline badge-neutral badge-sm xl:badge-md" :class="{'!badge-sm': minified}">
                    {{ product.price }} грн.
                </div>
            </div>
            <div v-if="$store.state.user !== null && !minified" class="card-actions justify-center">

                <div v-if="!product.is_hidden_price" class="flex flex-col items-center justify-center mb-2 relative">
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

                <span v-if="product.is_hidden_price">
                    Під замовлення
                </span>

                <button
                    v-if="!cartProduct && product.left_in_stock >= product.cart_increment_step && !product.is_hidden_price"
                    @click="addItemToCart(product)"
                    class="btn btn-neutral btn-block btn-sm lg:btn-md" :class="{'!btn-xs': minified}">
                    Додати до кошика
                </button>

                <button v-if="!cartProduct && product.left_in_stock < product.cart_increment_step"
                        @click="subscribeToProduct(product)"
                        :disabled="product.has_active_subscription"
                        :class="{'!bg-green-100': product.has_active_subscription, '!btn-xs': minified}"
                        class="btn bg-gray-300 hover:bg-gray-500 btn-block btn-sm lg:btn-md">
                    <span v-if="!product.has_active_subscription">
                        Повідомити коли зявиться
                    </span>
                    <span v-else>
                         Повідомимо коли зявиться
                    </span>
                </button>
                <button v-if="cartProduct && product.left_in_stock >= product.cart_increment_step"
                        @click="updateProductQuantity"
                        class="btn btn-success btn-block btn-sm lg:btn-md text-white" :class="{'!btn-xs': minified}">
                    Оновити кількість
                </button>
            </div>

            <div v-if="$store.state.user !== null && minified" class="card-actions justify-center">

                <div v-if="!product.is_hidden_price" class="flex flex-row justify-between items-center gap-1">
                    <div class="flex flex-row justify-between items-center gap-1 mb-0 md:mb-0">
                        <div class="join">
                            <button :disabled="!couldBeDecremented()" @click="decrementQuantity"
                                    class="btn btn-neutral join-item btn-sm lg:btn-md"
                                    :class="{'btn-sm lg:!btn-xs': minified}">
                                -
                            </button>
                            <input :disabled="product.left_in_stock <= product.cart_increment_step"
                                   class="input input-bordered text-center input-sm lg:input-md join-item"
                                   placeholder="" v-model="cartQty"
                                   :class="{'input-sm lg:!input-xs w-12 lg:w-8': minified}"/>
                            <button :disabled="!couldBeIncremented()" @click="incrementQuantity"
                                    class="btn btn-neutral join-item btn-sm lg:btn-md"
                                    :class="{'btn-sm lg:!btn-xs': minified}">
                                +
                            </button>
                        </div>
                    </div>

                    <button v-if="!cartProduct && product.left_in_stock >= product.cart_increment_step"
                            @click="addItemToCart(product)"
                            class="btn btn-neutral btn-sm lg:btn-md" :class="{'btn-sm lg:!btn-xs': minified}">
                        Додати
                    </button>

                    <button v-if="!cartProduct && product.left_in_stock < product.cart_increment_step" disabled
                            class="btn btn-neutral btn-sm lg:btn-md" :class="{'btn-sm lg:!btn-xs': minified}">
                        Немає
                    </button>
                    <button v-if="cartProduct && product.left_in_stock > 0" @click="updateProductQuantity"
                            class="btn btn-success btn-sm lg:btn-md text-white"
                            :class="{'btn-sm lg:!btn-xs': minified}">
                        Оновити
                    </button>
                </div>
                <div class="text-xs p-1" v-else>
                    Під замовлення
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {toast} from "vue3-toastify";
import Carousel from "./Carousel.vue";
import CartHelper from "../../helpers/CartHelper.js";
import FavoriteButton from "../../components/favorites/FavoriteButton.vue";

export default {
    name: 'Item',
    components: {FavoriteButton, Carousel},
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
            isTextTrimmed: false,
            productErrors: {},
            cart: this.$store.state.cart,
            cartQty: 1,
            cartProduct: null,
            fallbackImage: '../../../../public/no-image.png',
            loading: true,
            timer: null
        }
    },
    watch: {
        '$store.state.cart': {
            handler: function () {
                this.cart = this.$store.state.cart;
                this.cartQty = this.getCartQuantityForCurrentProduct();
            },
            deep: true
        },
        cartQty: {
            handler: function () {
                if (this.timer) {
                    clearTimeout(this.timer);
                }

                this.timer = setTimeout(() => {
                    this.cartQty = CartHelper.updateCartQuantity(this.cartQty, this.product)
                }, 500);
            },
            deep: true
        }
    },
    mounted() {
        this.checkIfTextTrimmed();

        if (this.$store.state.user !== null) {
            this.cartQty = this.getCartQuantityForCurrentProduct();
        }
    },
    methods: {
        checkIfTextTrimmed() {
            const productNameElement = this.$refs.productName;
            if (productNameElement) {
                this.isTextTrimmed = productNameElement.scrollWidth > productNameElement.clientWidth;
            }
        },
        getCartQuantityForCurrentProduct() {
            let cartItem = this.cart?.products?.find(item => item.product_id === this.product.id);
            this.cartProduct = cartItem ? cartItem : null;
            return cartItem ? cartItem.quantity : this.product.cart_increment_step;
        },
        copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                toast.success('Артикул скопійовано');
            }).catch(err => {
            });
        },
        subscribeToProduct(product) {

            if (product.has_active_subscription) {
                return;
            }

            axios.post('/api/products/' + product.id + '/subscribe')
                .then(() => {
                    product.has_active_subscription = true;
                    toast.success("Ви отримаєте сповіщення на Email, коли товар знову з’явиться");
                })
        },
        addItemToCart(product) {
            this.cartQty = CartHelper.updateCartQuantity(this.cartQty, product)

            axios.post('/api/cart/' + product.id, {quantity: this.cartQty})
                .then((response) => {
                    toast.success("Товар додано до кошика!", {autoClose: 2000, position: 'bottom-right'});
                    this.$store.commit('setCart', response.data.data)
                    this.cart = response.data.data;
                    this.cartQty = this.getCartQuantityForCurrentProduct();
                })
                .catch((error) => {
                    toast.error(error?.response?.data?.message ?? 'Помилка', {
                        autoClose: 5000,
                        position: 'bottom-right'
                    });
                });
        },
        incrementQuantity() {

            if (this.couldBeIncremented()) {
                this.cartQty += this.product.cart_increment_step;
            } else {
                this.cartQty = this.product.left_in_stock;
                this.productErrors[this.product.id] = "В наявності: " + this.product.left_in_stock + " шт.";
            }
        },
        decrementQuantity() {
            if (!this.couldBeDecremented()) {
                return;
            }

            this.cartQty -= this.product.cart_increment_step;
        },
        couldBeIncremented() {
            return this.product.left_in_stock >= this.cartQty + this.product.cart_increment_step;
        },
        couldBeDecremented() {
            return this.cartQty > this.product.cart_increment_step;
        },
        handleFavoriteRemoved(productId) {
            this.$emit('favorite-removed', productId);
        },
        updateProductQuantity() {
            this.cartQty = CartHelper.updateCartQuantity(this.cartQty, this.product)

            if (this.product.left_in_stock <= this.cartQty) {
                this.cartQty = this.product.left_in_stock - (this.product.left_in_stock % this.product.cart_increment_step);
            }

            axios.patch(`/api/cart/${this.product.id}`, {quantity: this.cartQty})
                .then(response => {
                    this.$store.commit('setCart', response.data.data);
                    this.cart = response.data.data;
                    this.cartQty = this.getCartQuantityForCurrentProduct();
                    toast.success("Кількість товару оновлено!", {autoClose: 5000, position: 'bottom-right'});
                })
                .catch(error => {
                    toast.error(error?.response?.data?.message ?? 'Помилка', {
                        autoClose: 5000,
                        position: 'bottom-right'
                    });
                });
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
    }
}
</script>
<style>
.product-name {
    white-space: break-spaces;
}
</style>
