<template>
    <button aria-label="Add to favorites"
            @click="toggle(productId)"
            class="shadow btn btn-circle z-[10]"
            :class="classes">
        <svg class="h-6 w-6" viewBox="0 0 24 24" :fill="isFavorite ? 'red' : 'none'"
             stroke="currentColor"
             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path
                d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
    </button>
</template>
<script setup>
import {ref} from "vue";

const props = defineProps({
    productId: Number,
    isInFavorites: {
        type: Boolean,
        default: false
    },
    classes: {
        type: String,
        default: 'absolute top-2 right-2 ',
    },
});

const isFavorite = ref(props.isInFavorites);

const emit = defineEmits(['removed']);

/**
 * toggle
 * @param productId
 * @returns {Promise<unknown>}
 */
const toggle = (productId) => {
    axios.post('/api/favorites/' + productId + '/toggle').then(response => {
        isFavorite.value = !isFavorite.value;
        emit('removed', productId);
    }).catch(error => {
        console.log(error);
    });
}

</script>
