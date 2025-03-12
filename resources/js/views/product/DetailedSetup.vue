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
                    <FavoriteButton v-if="$store.state.user !== null"
                                    classes="absolute right-2 top-2" :product-id="product.id"
                                    :is-in-favorites="product.has_active_favorite"/>
                    <div class="w-full">
                        <h2 class="font-bold text-2xl">{{ product.name }}</h2>
                        <p class="text-lg">
                            {{ product.category.breadcrumbs }}
                            <br>
                            <span class="text-gray-400 hover:font-bold hover:text-black cursor-pointer">
                                <span @click="copyToClipboard(product.sku)">Арт. {{ product.sku }}</span>
                            </span>
                            <br>
                            <span class="text-error font-bold text-2xl">{{ product.usd_price }} $ / {{ product.price }} грн.</span>
                        </p>
                        <hr class="mt-2">
                        <div v-if="$store.state.user !== null && !product.is_hidden_price"
                             class="flex flex-row w-full gap-1 justify-center lg:justify-start mt-5">
                            <div class="flex flex-col items-center justify-center">
                                <div class="flex flex-row justify-between items-center gap-1 mb-4 md:mb-0">
                                    <div class="join">
                                        <button :disabled="product.left_in_stock <= 0" @click="decrementQuantity"
                                                class="btn btn-neutral join-item">
                                            -
                                        </button>
                                        <input :disabled="product.left_in_stock <= 0"
                                               class="input input-bordered text-center join-item w-16 focus:outline-none"
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
                            <button v-if="!cartProduct && product.left_in_stock <= 0"
                                    @click="subscribeToProduct(product)"
                                    :disabled="product.has_active_subscription"
                                    :class="{'!bg-green-100': product.has_active_subscription}"
                                    class="btn bg-gray-300 hover:bg-gray-500">
                                 <span v-if="!product.has_active_subscription">
                                    Повідомити коли зявиться
                                </span>
                                            <span v-else>
                                     Повідомимо коли зявиться
                                </span>
                            </button>
                            <button v-if="cartProduct && product.left_in_stock > 0" @click="updateProductQuantity"
                                    class="btn btn-success text-white">
                                Оновити кількість
                            </button>
                        </div>
                        <div v-if="product.is_hidden_price" class="mt-3.5">
                            Під замовлення
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
import {computed, ref, watch, watchEffect} from "vue";
import store from "../../store.js";
import {onMounted} from "vue";
import {useRoute} from "vue-router";
import {useHead} from "@vueuse/head";
import CartHelper from "../../helpers/CartHelper.js";
import FavoriteButton from "../../components/favorites/FavoriteButton.vue";

const isDataLoaded = ref(false);
const product = ref(null);
const productErrors = ref({});
const cartQty = ref(1);
const cart = ref(store.state.cart);
const cartProduct = ref(null);
const route = useRoute();

watch(() => route.params.id, async () => {
    await fetchProduct();
});

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

const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text).then(() => {
        toast.success('Артикул скопійовано');
    }).catch(err => {
    });
};

const subscribeToProduct = (product) => {

    if (product.has_active_subscription) {
        return;
    }

    axios.post('/api/products/' + product.id + '/subscribe')
        .then(() => {
            product.has_active_subscription = true;
            toast.success("Ви отримаєте сповіщення на Email, коли товар знову з’явиться");
        })
}

const fetchProduct = async () => {
    isDataLoaded.value = false;
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

const couldBeIncremented = () => {
    return product.value.left_in_stock >= cartQty.value + product.value.cart_increment_step;
};

const couldBeDecremented = () => {
    return cartQty.value > product.value.cart_increment_step;
};

const getCartQuantityForCurrentProduct = () => {
    if (!product.value) {
        return product.cart_increment_step;
    }

    let cartItem = cart.value?.products?.find(item => item.product_id === product.value.id);
    cartProduct.value = cartItem ? cartItem : null;
    return cartItem ? cartItem.quantity : 1;
};

watchEffect(() => {
    cart.value = store.state.cart;
    cartQty.value = getCartQuantityForCurrentProduct();
});

const timer = ref(null);

watch(() => cartQty.value, () => {
    if (timer.value) {
        clearTimeout(timer.value);
    }

    timer.value = setTimeout(() => {
        cartQty.value = CartHelper.updateCartQuantity(cartQty.value, product.value)
    }, 500);
});


const addItemToCart = (product) => {
    cartQty.value = CartHelper.updateCartQuantity(cartQty.value, product)

    axios.post('/api/cart/' + product.id, {quantity: cartQty.value})
        .then((response) => {
            toast.success("Товар додано до кошика!", {autoClose: 2000, position: 'bottom-right'});
            store.commit('setCart', response.data.data)
            cart.value = response.data.data;
            cartQty.value = getCartQuantityForCurrentProduct();
        })
        .catch((error) => {
            toast.error(error?.response?.data?.message ?? 'Помилка', {autoClose: 5000, position: 'bottom-right'});
        });
};

const incrementQuantity = () => {
    if (couldBeIncremented()) {
        cartQty.value += product.value.cart_increment_step;
    } else {
        cartQty.value = product.value.left_in_stock;
        productErrors.value[product.value.id] = "В наявності: " + product.value.left_in_stock + " шт.";
    }
};

const decrementQuantity = () => {
    if (!couldBeDecremented()) {
        return;
    }

    cartQty.value -= product.value.cart_increment_step;
};

const updateProductQuantity = () => {

    cartQty.value = CartHelper.updateCartQuantity(cartQty.value, product.value)

    if (product.value.left_in_stock <= cartQty.value) {
        cartQty.value = product.value.left_in_stock - (product.value.left_in_stock % product.value.cart_increment_step);
    }

    axios.patch(`/api/cart/${product.value.id}`, {quantity: cartQty.value})
        .then(response => {
            store.commit('setCart', response.data.data);
            cart.value = response.data.data;
            cartQty.value = getCartQuantityForCurrentProduct();
            toast.success("Кількість товару оновлено!", {autoClose: 5000, position: 'bottom-right'});
        })
        .catch(error => {
            toast.error(error?.response?.data?.message ?? 'Помилка', {autoClose: 5000, position: 'bottom-right'});
        });
}
</script>
