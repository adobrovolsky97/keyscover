<template>
    <div class="rounded-lg w-full">
        <swiper
            v-if="products.length > 1 && showThumbs"
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
    1024: {
        slidesPerView: 3, // Show 4 slides
    },
    1200: {
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

.swiper-wrapper {
    background: white !important;
}

.swiper-slide-thumb-active {
    border: 2px solid #e17c7c;
    border-radius: 5px;
}

</style>

