<template>
    <dialog :id="id" class="modal">
        <div class="modal-box w-11/12 max-w-5xl overflow-auto" :class="classes">
            <h3 v-if="title" class="text-lg font-bold">{{ title }}</h3>
            <div class="content">
                <slot></slot>
            </div>
            <div class="modal-action">
                <form method="dialog">
                    <!-- if there is a button in form, it will close the modal -->
                    <button class="btn btn-circle btn-sm btn-ghost absolute right-2 top-2">x</button>
                </form>
            </div>
        </div>
    </dialog>
</template>
<script setup>
const props = defineProps({
    id: {
        type: String,
        default: 'modalWindow'
    },
    title: {
        type: String,
        default: null,
    },
    classes: {
        type: String,
        default: ''
    }
});
</script>
