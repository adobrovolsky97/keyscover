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
            <h3 class="font-bold lg:text-lg text-xs">
                {{ product.name }}
            </h3>
            <div class="flex flex-row justify-between items-center">
                <div class="badge badge-outline badge-neutral badge-sm lg:badge-md">{{ product.usd_price }}$</div>
                <div class="badge badge-outline badge-neutral badge-sm lg:badge-md">{{ product.price }} грн.</div>
            </div>
            <div v-if="$store.state.user !== null" class="card-actions justify-center">

                <div class="flex flex-row justify-between items-center gap-1 mb-4 md:mb-0 md:mr-4">
                    <div class="join">
                        <button @click="decrementQuantity"
                                class="btn btn-neutral join-item btn-sm lg:btn-md">
                            -
                        </button>
                        <input class="input input-bordered text-center input-sm lg:input-md join-item w-24"
                               placeholder="" v-model="cartQty"/>
                        <button @click="incrementQuantity"
                                class="btn btn-neutral join-item btn-sm lg:btn-md">
                            +
                        </button>
                    </div>
                </div>

                <button v-if="!cartProduct" @click="addItemToCart(product)" class="btn btn-neutral btn-block btn-sm lg:btn-md">
                    Додати до кошика
                </button>
                <button v-else @click="updateProductQuantity" class="btn btn-success btn-block btn-sm lg:btn-md">
                    Оновити товар
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

            if(this.cartQty < 1){
                toast.error("Невірна кількість товару")
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
            this.cartQty++;
        },
        decrementQuantity() {
            if (this.cartProduct.quantity <= 1) {
                return;
            }

            this.cartQty--;
        },
        updateProductQuantity() {
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
