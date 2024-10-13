<template>
    <ContentSkeleton v-if="!isDataLoaded" :items="['h-80 w-full']"
                     :classes="'flex flex-row justify-between items-center gap-4'"/>
    <div class="border rounded-2xl shadow-xl" v-else>
        <div class="card lg:card-side">
            <div class="flex flex-col justify-start items-center w-full lg:w-1/2 p-4">
                <Carousel :media="media"/>
            </div>
            <div class="card-body">
                <div class="flex flex-col lg:flex-row justify-between items-start gap-4">
                    <div class="w-full">
                        <h2 class="font-bold text-2xl">{{ product.name }}</h2>
                        <p class="text-lg">
                            {{ product.category.breadcrumbs }}
                            <br>
                            <span class="text-gray-400 hover:font-bold hover:text-black cursor-pointer">Арт.: {{ product.sku }}</span>
                            <br>
                            <span class="text-error font-bold text-2xl">{{ product.usd_price }} $ / {{ product.price }} грн.</span>
                        </p>
                        <hr class="mt-2">
                        <div v-if="$store.state.user !== null"
                             class="flex flex-row w-full gap-1 justify-center lg:justify-start mt-5">
                            <div class="flex flex-col items-center justify-center">
                                <div class="flex flex-row justify-between items-center gap-1 mb-4 md:mb-0">
                                    <div class="join">
                                        <button :disabled="product.left_in_stock <= 0" @click="decrementQuantity"
                                                class="btn btn-neutral join-item">
                                            -
                                        </button>
                                        <input :disabled="product.left_in_stock <= 0"
                                               class="input input-bordered text-center join-item w-16"
                                               placeholder="" v-model="cartQty"/>
                                        <button :disabled="product.left_in_stock <= 0" @click="incrementQuantity"
                                                class="btn btn-neutral join-item">
                                            +
                                        </button>
                                    </div>
                                </div>
                                <span class="text-error"
                                      v-if="productErrors[product.id]">{{ productErrors[product.id] }}</span>
                            </div>
                            <button v-if="!cartProduct && product.left_in_stock > 0" @click="addItemToCart(product)"
                                    class="btn btn-neutral">
                                Додати до кошика
                            </button>
                            <button v-if="!cartProduct && product.left_in_stock <= 0" disabled
                                    class="btn btn-neutral">
                                Немає в наявності
                            </button>
                            <button v-if="cartProduct && product.left_in_stock > 0" @click="updateProductQuantity"
                                    class="btn btn-success text-white">
                                Оновити кількість
                            </button>
                        </div>
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

                        <div class="related-products w-full mt-4" v-if="activeRelatedProducts.length">
                            <hr>
                            <p class="font-bold text-lg mt-4">
                                Додаткові товари:
                            </p>
                            <div class="grid grid-cols-1 mt-2">
                                <RelatedProducts :key="'related'" :products="activeRelatedProducts"/>
                            </div>
                        </div>

                        <div class="related-products w-full mt-4" v-if="activeSimilarProducts.length">
                            <hr>
                            <p class="font-bold text-lg mt-4">
                                Схожі товари:
                            </p>
                            <div class="grid grid-cols-1 mt-2">
                                <RelatedProducts :key="'similar'" :products="activeSimilarProducts"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="description px-6" v-if="parsedDescription">
            <hr>
            <p class="font-bold text-lg mt-4">Опис</p>
            <MdPreview :modelValue="product.description"/>
        </div>
    </div>
</template>
<script setup>
import {marked} from 'marked';
import ContentSkeleton from "../../components/skeleton/ContentSkeleton.vue";
import {toast} from "vue3-toastify";
import Carousel from "./Carousel.vue";
import RelatedProducts from "./RelatedProducts.vue";
import 'md-editor-v3/lib/preview.css';
import {MdPreview} from "md-editor-v3";
import {computed, ref} from "vue";
import store from "../../store.js";
import {onMounted} from "vue";
import {useRoute} from "vue-router";
import {useHead} from "@vueuse/head";

const isDataLoaded = ref(false);
const product = ref(null);
const productErrors = ref({});
const cartQty = ref(1);
const cart = ref(store.state.cart);
const cartProduct = ref(null);
const route = useRoute();

onMounted(async () => {
    await fetchProduct();
});

const productTitle = computed(() => {
    return product.value?.name || 'Завантаження...';
});

const productDescription = computed(() => {
    return product.value?.description || 'Оптовий продаж автомобільних ключів та все що з ними повязано. Співпраця виключно з майстрами!'
});

const media = computed(() => {
    return product.value?.media?.length ? product.value.media : [{url: product.value.image}];
});


const activeRelatedProducts = computed(() => {
    return product.value?.related_products?.filter(product => !product.is_hidden);
});

const activeSimilarProducts = computed(() => {
    return product.value?.similar_products?.filter(product => !product.is_hidden);
});

const parsedDescription = computed(() => {
    return marked(product.value.description || '');
});

const fetchProduct = async () => {
    axios.get('/api/products/' + route.params.id)
        .then((response) => {
            product.value = response.data.data;

            useHead({
                title: productTitle,
                meta: [
                    {name: 'description', content: productDescription},
                    {property: 'og:title', content: productTitle},
                    {property: 'og:description', productDescription},
                    {property: 'og:image', content: media.value[0]?.url},
                    {property: 'og:url', content: window.location.href},
                    {property: 'og:type', content: 'product'},
                    {property: 'og:site_name', content: 'KeysCover'},
                    {property: 'og:locale', content: 'ua_UA'},
                ]
            });
        })
        .then(() => {
            if (store.state.user !== null) {
                cartQty.value = getCartQuantityForCurrentProduct();
            }
        })
        .then(() => {
            // scroll to top
            window.scrollTo(0, 0);
            isDataLoaded.value = true;
        })
        .catch((error) => {
            console.log(error);
        });
};

const getCartQuantityForCurrentProduct = () => {
    if (!product.value) {
        return 1;
    }

    let cartItem = cart.value?.products?.find(item => item.product_id === product.value.id);
    cartProduct.value = cartItem ? cartItem : null;
    return cartItem ? cartItem.quantity : 1;
};

const addItemToCart = (product) => {
    if (cartQty.value < 1) {
        toast.error("Невірна кількість товару", {
            position: 'bottom-right'
        })
        return;
    }

    if (product.left_in_stock < cartQty.value) {
        cartQty.value = product.left_in_stock;
        toast.error("Недостатня кількість товарів на складі. Залишок: " + product.left_in_stock + " штук.", {
            position: 'bottom-right'
        });
        return;
    }

    axios.post('/api/cart/' + product.id, {quantity: cartQty.value})
        .then((response) => {
            toast.success("Товар додано до кошика!", {autoClose: 2000, position: 'bottom-right'});
            store.commit('setCart', response.data.data)
            cart.value = response.data.data;
            cartQty.value = getCartQuantityForCurrentProduct();
        })
};

const incrementQuantity = () => {
    if (product.value.left_in_stock > cartQty.value) {
        cartQty.value++;
    } else {
        cartQty.value = product.value.left_in_stock;
        productErrors.value[product.value.id] = "В наявності: " + product.value.left_in_stock + " шт.";
    }
};

const decrementQuantity = () => {
    if (cartQty.value <= 1) {
        return;
    }

    cartQty.value--;
};

const updateProductQuantity = () => {
    if (product.value.left_in_stock < cartQty.value) {
        cartQty.value = product.value.left_in_stock;
        toast.error("Недостатня кількість товарів на складі. Залишок: " + product.value.left_in_stock + " штук.", {
            position: 'bottom-right'
        });
        return;
    }

    axios.patch(`/api/cart/${product.value.id}`, {quantity: cartQty.value})
        .then(response => {
            store.commit('setCart', response.data.data);
            cart.value = response.data.data;
            cartQty.value = getCartQuantityForCurrentProduct();
            toast.success("Кількість товару оновлено!", {autoClose: 5000, position: 'bottom-right'});
        })
}
</script>
