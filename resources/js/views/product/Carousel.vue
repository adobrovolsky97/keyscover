<template>
    <!-- Main Image with Transition -->
    <div class="relative w-full images-carousel rounded-lg">
        <swiper
            :spaceBetween="10"
            :navigation="showArrows"
            :thumbs="{ swiper: thumbsSwiper }"
            :loop="true"
            :modules="modules"
            :style="{
              '--swiper-navigation-size': arrowsSize,
              '--swiper-navigation-color': '#1f2937',
            }"
        >
            <swiper-slide v-for="(image, index) in media" :key="index">
                <img class="w-full object-cover" :src="image.url" alt=""/>
            </swiper-slide>
        </swiper>
        <swiper
            v-if="media.length > 1 && showThumbs"
            @swiper="setThumbsSwiper"
            :spaceBetween="10"
            :slidesPerView="4"
            :freeMode="true"
            :watchSlidesProgress="true"
            :modules="modules"
        >
            <swiper-slide v-for="(image, index) in media" :key="index">
                <img class="w-full border rounded-lg object-cover cursor-pointer" :src="image.url" alt=""/>
            </swiper-slide>
        </swiper>
    </div>
</template>

<script setup>
import {ref, watch} from 'vue';
import {Swiper, SwiperSlide} from 'swiper/vue';
import {FreeMode, Navigation, Thumbs} from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/free-mode';
import 'swiper/css/navigation';
import 'swiper/css/thumbs';

const modules = [FreeMode, Navigation, Thumbs];

const props = defineProps({
    media: {
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
    showArrows: {
        type: Boolean,
        default: true,
    },
});

const thumbsSwiper = ref(null);

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
.images-carousel .swiper-button-next::after, .images-carousel .swiper-button-prev::after {
    color: #1f2937 !important;
    border: 1px solid #f2f2f2 !important;
    border-radius: 50% !important;
    padding: 8px !important;
    background-color: #f2f2f2 !important;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 70% !important;
}


.images-carousel .swiper-slide-thumb-active {
    border: 2px solid #e17c7c;
    border-radius: 5px;
}


</style>
