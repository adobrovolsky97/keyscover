<template>
    <div class="relative w-full images-carousel rounded-lg select-none" ref="target">
        <swiper
            v-if="isVisible"
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
                <router-link
                    v-if="id"
                    :to="{ name: 'product.show', params: { id: id } }"
                    @click="linkClicked($event)"
                >
                    <img loading="lazy" class="w-full object-cover" :src="image.url" alt=""/>
                </router-link>
                <img loading="lazy" v-else class="w-full object-cover" :src="image.url" alt=""/>
            </swiper-slide>
        </swiper>

        <img v-else loading="lazy" class="w-full object-cover" :src="media[0].url" alt=""/>

        <swiper
            v-if="isVisible && media.length > 1 && showThumbs"
            @swiper="setThumbsSwiper"
            :spaceBetween="10"
            :slidesPerView="4"
            :freeMode="true"
            :watchSlidesProgress="true"
            :modules="modules"
        >
            <swiper-slide v-for="(image, index) in media" :key="index">
                <img loading="lazy" class="w-full border rounded-lg object-cover cursor-pointer" :src="image.url"
                     alt=""/>
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
import {useInViewport} from '../../composables/useInViewport.js';

const modules = [FreeMode, Navigation, Thumbs];

const emit = defineEmits(['link-clicked']);

const props = defineProps({
    media: Array,
    id: Number,
    showThumbs: {type: Boolean, default: true},
    arrowsSize: {type: String, default: '25px'},
    showArrows: {type: Boolean, default: true},
});

const thumbsSwiper = ref(null);
const setThumbsSwiper = (swiper) => thumbsSwiper.value = swiper;

const linkClicked = (event) => {
    emit('link-clicked', event);
};

// Handle lazy loading when in view
const {target, isVisible} = useInViewport();
</script>
