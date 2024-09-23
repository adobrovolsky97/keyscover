<template>
    <section>
        <Navbar/>
        <div class="w-full container min-h-screen mx-auto py-2 bg-base-50">
            <div class="content mt-32">
                <router-view :key="$route.fullPath" v-slot="{ Component }">
                    <transition name="fade" mode="out-in">
                        <component :is="Component"/>
                    </transition>
                </router-view>
            </div>
        </div>
    </section>

</template>

<script>
import Navbar from "./Navbar.vue";
import {defineComponent} from "vue";

export default defineComponent({
    components: {Navbar}
})

</script>

<style>
</style>
