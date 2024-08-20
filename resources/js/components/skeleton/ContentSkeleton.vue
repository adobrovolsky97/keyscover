<template>
    <div :class="classes">
        <div v-for="(item, index) in items" :key="index" class="skeleton" :class="item"></div>
    </div>
</template>
<script>
export default {
    name: "ContentSkeleton",
    props:  {
        classes: {
            type: String,
            default: 'lg:grid-cols-4 sm:grid-cols-1 grid gap-4'
        },
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
