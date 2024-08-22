<template>
    <ContentSkeleton v-if="!isDataLoaded" :items="['h-80 w-full']"
                     :classes="'flex flex-row justify-between items-center gap-4'"/>
    <div v-else>
        <div class="card lg:card-side border rounded-2xl shadow-xl">
            <div class="flex flex-col justify-center items-center w-full lg:w-2/6">
                <figure>
                    <div class="carousel overflow-hidden relative">
                        <div class="flex transition-transform duration-300"
                             :style="{ transform: `translateX(-${currentSlide * 100}%)` }">
                            <div v-for="(image, index) in media"
                                 :key="index"
                                 class="carousel-item w-full flex-shrink-0">
                                <img :src="image.url" class="w-full"/>
                            </div>
                        </div>
                        <div v-if="media.length > 1"
                             class="absolute left-5 opacity-50 right-5 top-1/2 flex -translate-y-1/2 transform justify-between">
                            <button @click="prevSlide" class="btn btn-circle">❮</button>
                            <button @click="nextSlide" class="btn btn-circle">❯</button>
                        </div>
                    </div>
                </figure>
                <div v-if="media.length > 1" class="flex gap-2 mt-4 overflow-x-auto mb-4">
                    <div v-for="(image, index) in thumbnailMedia" :key="index" @click="currentSlide = index"
                         class="cursor-pointer border rounded-lg overflow-hidden">
                        <img :src="image.url" class="w-24 h-24 object-cover"
                             :class="{ 'border-blue-500': currentSlide === index }"/>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="flex flex-col lg:flex-row justify-between items-start gap-4">
                    <div class="w-full">
                        <h2 class="card-title text-2xl">{{ product.name }}</h2>
                        <p>
                            Категорія: {{ product.category.name }}
                            <br>
                            <span class="text-gray-400">Арт.: {{ product.sku }}</span>
                            <br>
                            {{ product.usd_price }} $ / {{ product.price }} грн.
                        </p>
                        <div class="overflow-x-auto rounded-lg mt-4" v-if="product.custom_fields.length > 0">
                            <hr>
                            <table class="table">
                                <tbody>
                                <tr v-for="(customField, index) in product.custom_fields" :key="index">
                                    <td>{{ customField.key }}</td>
                                    <td>{{ customField.value }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
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
                            <span class="text-error"
                                  v-if="productErrors[product.id]">{{ productErrors[product.id] }}</span>
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
        </div>
    </div>
</template>
<script>
import ContentSkeleton from "../../components/skeleton/ContentSkeleton.vue";
import {toast} from "vue3-toastify";

export default {
    name: 'Detailed',
    components: {ContentSkeleton},
    data() {
        return {
            isDataLoaded: false,
            product: null,
            productErrors: {},
            cart: this.$store.state.cart,
            cartQty: 0,
            cartProduct: null,
            currentSlide: 0, // Track the current slide
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
    computed: {
        media() {
            return this.product?.media?.length ? this.product.media : [{url: this.product.image}];
        },
        thumbnailMedia() {
            const length = this.media.length;
            const numThumbnails = 4; // Number of thumbnails to show
            let start = 0;
            let end = numThumbnails - 1;

            // Calculate the range of thumbnails to show
            if (length <= numThumbnails) {
                // Show all thumbnails if there are fewer than or equal to numThumbnails
                return this.media.filter((_, index) => index !== this.currentSlide);
            } else {
                if (this.currentSlide < numThumbnails - 1) {
                    // If currentSlide is less than the number of thumbnails to show, start from the beginning
                    start = 0;
                    end = numThumbnails;
                } else if (this.currentSlide > length - numThumbnails) {
                    // If currentSlide is near the end, adjust the start to show the last slides
                    start = length - numThumbnails;
                    end = length;
                } else {
                    // Normal case where currentSlide is in the middle
                    start = this.currentSlide - Math.floor(numThumbnails / 2);
                    end = this.currentSlide + Math.floor(numThumbnails / 2);
                }

                // Ensure start and end are within bounds
                start = Math.max(start, 0);
                end = Math.min(end, length - 1);

                // Exclude the current slide from the thumbnails
                return this.media.filter((_, index) => index !== this.currentSlide && index >= start && index <= end);
            }
        }
    },
    mounted() {
        this.fetchProduct();
    },
    methods: {
        fetchProduct() {
            axios.get('/api/products/' + this.$route.params.id)
                .then((response) => {
                    this.product = response.data.data;
                })
                .then(() => {
                    if (this.$store.state.user !== null) {
                        this.cartQty = this.getCartQuantityForCurrentProduct();
                    }
                    this.isDataLoaded = true;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        prevSlide() {
            this.currentSlide = (this.currentSlide === 0) ? this.media.length - 1 : this.currentSlide - 1;
        },
        nextSlide() {
            this.currentSlide = (this.currentSlide === this.media.length - 1) ? 0 : this.currentSlide + 1;
        },
        getCartQuantityForCurrentProduct() {
            if (!this.product) {
                return 1;
            }

            let cartItem = this.cart?.products?.find(item => item.product_id === this.product.id);
            this.cartProduct = cartItem ? cartItem : null;
            return cartItem ? cartItem.quantity : 1;
        },
        addItemToCart(product) {
            if (this.cartQty < 1) {
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
            if (this.product.left_in_stock > this.cartQty) {
                this.cartQty++;
            } else {
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
    }
}
</script>
