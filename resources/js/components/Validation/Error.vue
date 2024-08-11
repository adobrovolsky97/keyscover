<template>
    <p class="text-error text-left text-sm" v-if="error.length > 0">{{ error }}</p>
</template>

<script>

export default {
    props: ['errors', 'attribute'],
    data() {
        return {
            error: ''
        }
    },
    mounted() {
        this.error = this.errors[this.attribute] ? this.errors[this.attribute][0] : '';
    }
}

</script>

<style scoped lang="scss">

</style>
