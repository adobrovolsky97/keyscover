<template>
    <div class="card border bg-base-100 shadow-xl">
        <figure>
            <img
                class=" w-full object-cover"
                :src="product.image"
                :alt="product.name"/>
        </figure>
        <div class="card-body p-2 lg:p-4">
            <p class="text-xs text-gray-400">Арт. {{ product.sku }}</p>
            <p class="text-xs text-gray-400">{{ product.category.name }}</p>
            <h3 class="font-bold lg:text-lg text-xs product-name">
                {{ product.name }}
            </h3>
            <div class="flex flex-row justify-between items-center">
                <div class="badge badge-outline badge-neutral badge-sm lg:badge-md">{{ product.usd_price }}$</div>
                <div class="badge badge-outline badge-neutral badge-sm lg:badge-md">{{ product.price }} грн.</div>
            </div>
            <div v-if="$store.state.user !== null" class="card-actions justify-center">

                <div class="flex flex-col items-center justify-center">
                    <div class="flex flex-row justify-between items-center gap-1 mb-4 md:mb-0">
                        <div class="join">
                            <button :disabled="product.left_in_stock <= 0" @click="decrementQuantity"
                                    class="btn btn-neutral join-item btn-sm lg:btn-md">
                                -
                            </button>
                            <input :disabled="product.left_in_stock <= 0"
                                   class="input input-bordered text-center input-sm lg:input-md join-item w-16"
                                   placeholder="" v-model="cartQty"/>
                            <button :disabled="product.left_in_stock <= 0" @click="incrementQuantity"
                                    class="btn btn-neutral join-item btn-sm lg:btn-md">
                                +
                            </button>
                        </div>
                    </div>
                    <span class="text-error" v-if="productErrors[product.id]">{{productErrors[product.id]}}</span>
                </div>

                <button v-if="!cartProduct && product.left_in_stock > 0" @click="addItemToCart(product)"
                        class="btn btn-neutral btn-block btn-sm lg:btn-md">
                    Додати до кошика
                </button>

                <button v-if="!cartProduct && product.left_in_stock <= 0" disabled
                        class="btn btn-neutral btn-block btn-sm lg:btn-md">
                    Немає в наявності
                </button>
                <button v-if="cartProduct && product.left_in_stock > 0" @click="updateProductQuantity"
                        class="btn btn-success btn-block btn-sm lg:btn-md">
                    Оновити кількість
                </button>
            </div>
        </div>
    </div>
</template>
<script>
import {toast} from "vue3-toastify";

export default {
    name: 'Item',
    props: ['product'],
    data() {
        return {
            productErrors: {},
            cart: this.$store.state.cart,
            cartQty: 0,
            cartProduct: null,
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
            return cartItem ? cartItem.quantity : 1;
        },
        addItemToCart(product) {

            if (this.cartQty < 1) {
                toast.error("Невірна кількість товару", {
                    position:'bottom-right'
                })
                return;
            }

            if (product.left_in_stock < this.cartQty) {
                this.cartQty = product.left_in_stock;
                toast.error("Недостатня кількість товарів на складі. Залишок: " + product.left_in_stock + " штук.", {
                    position:'bottom-right'
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

            if (this.product.left_in_stock > this.cartQty) {
                this.cartQty++;
            }else{
                this.cartQty = this.product.left_in_stock;
                this.productErrors[this.product.id] = "В наявності: " + this.product.left_in_stock + " шт.";
            }
        },
        decrementQuantity() {
            if (this.cartQty <= 1) {
                return;
            }

            this.cartQty--;
        },
        updateProductQuantity() {

            if (this.product.left_in_stock < this.cartQty) {
                this.cartQty = this.product.left_in_stock;
                toast.error("Недостатня кількість товарів на складі. Залишок: " + this.product.left_in_stock + " штук.", {
                    position:'bottom-right'
                });
                return;
            }

            axios.patch(`/api/cart/${this.product.id}`, {quantity: this.cartQty})
                .then(response => {
                    this.$store.commit('setCart', response.data.data);
                    this.cart = response.data.data;
                    this.cartQty = this.getCartQuantityForCurrentProduct();
                })
        }
    }
}
</script>
<style>
.product-name {
    white-space: break-spaces;
}
</style>
