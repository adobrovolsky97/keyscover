<template>
    <div class="rounded-lg related-products w-full">
        <swiper
            v-if="products.length >= 1 && showThumbs"
            @swiper="setThumbsSwiper"
            :space-between="5"
            :breakpoints="breakpoints"
            :freeMode="false"
            :navigation="true"
            :watchSlidesProgress="true"
            :modules="modules"
            :style="{
              '--swiper-navigation-size': arrowsSize,
              '--swiper-navigation-color': '#1f2937',
            }"
        >
            <swiper-slide v-for="(product, index) in products" :key="index">
                <Item :no-shadow="true" :show-arrows="false" :product="product" :minified="true" :key="product.id"/>
            </swiper-slide>
        </swiper>
    </div>
</template>

<script setup>
import {ref, watch} from 'vue';
import {Swiper, SwiperSlide} from 'swiper/vue';
import {FreeMode, Navigation, Thumbs} from 'swiper/modules';
import {onMounted, onBeforeUnmount} from "vue";
import 'swiper/css';
import 'swiper/css/free-mode';
// import 'swiper/css/navigation';
import 'swiper/css/thumbs';
import Item from "./Item.vue";

const modules = [FreeMode, Navigation, Thumbs];

const props = defineProps({
    products: {
        type: Array,
        required: true,
        default: () => [],
    },
    showThumbs: {
        type: Boolean,
        default: true,
    },
    arrowsSize: {
        type: String,
        default: '25px',
    },
});
const thumbsSwiper = ref(null);

const breakpoints = {
    640: {
        slidesPerView: 1, // Show 1 slide
    },
    768: {
        slidesPerView: 2, // Show 2 slides
    },
    1280: {
        slidesPerView: 3, // Show 4 slides
    },
    1550: {
        slidesPerView: 4, // Show 4 slides
    },
};

/**
 * Set the thumbs swiper.
 * @param swiper
 */
const setThumbsSwiper = (swiper) => {
    thumbsSwiper.value = swiper;
};

// Watch for changes in thumbsSwiper.value to ensure it updates properly
watch(() => thumbsSwiper.value, (newSwiper) => {
    // Handle any necessary updates when thumbsSwiper changes
});
</script>
<style>

.related-products .swiper-button-next,
.related-products .swiper-button-prev {
    background-position: center;
    background-size: 40px;
    background-repeat: no-repeat;
    padding: 16px 16px;
    border-radius: 100%;
    border: 2px solid black;
}

.related-products .swiper-button-next {
    background-image: url("../../../../public/arr-right.png") !important;
}

.related-products .swiper-button-prev {
    background-image: url("../../../../public/arr-left.png") !important;
}

.related-products .swiper-button-next::after, .related-products .swiper-button-prev::after {
    content: "";
}


</style>

