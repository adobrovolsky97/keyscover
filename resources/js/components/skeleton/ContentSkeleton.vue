<template>
    <div class="lg:grid-cols-4 sm:grid-cols-1 grid gap-4">
        <div v-for="(item, index) in items" :key="index" class="skeleton" :class="item"></div>
    </div>
</template>
<script>
export default {
    name: "ContentSkeleton",
    props:  {
        items : {
            type: Array,
            default: () => [
                'h-64',
                'h-64',
                'h-64',
                'h-64',
                'h-64',
                'h-64',
                'h-64',
                'h-64',
                'h-64',
                'h-64',
                'h-64',
                'h-64',
                'h-64',
                'h-64',
                'h-64',
                'h-64',
            ]
        }
    }
}
</script>
