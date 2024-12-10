<template>
    <div class="border rounded-2xl shadow-xl p-4">
        <div class="flex flex-row justify-between items-center">
            <h3 class="font-bold text-lg mb-4">Налаштування <span v-if="data.meta?.total">({{ data.meta.total }})</span>
            </h3>
        </div>
        <TableSkeleton v-if="!isDataLoaded"/>
        <div class="overflow-x-auto" v-if="isDataLoaded">
            <table class="table">
                <thead>
                <tr>
                    <th>Key</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Description</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <!-- row 1 -->
                <tr v-for="config in data" :key="config.id">
                    <td>{{ config.key }}</td>
                    <td>{{ config.type }}</td>
                    <td>{{ config.value }}</td>
                    <td><span v-html="config.description"></span></td>
                    <td>
                        <router-link
                            :to="{name: 'admin.configs.edit', params: {id: config.id}}"
                            class="btn btn-ghost"
                        >
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>

                        </router-link>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div v-if="isDataLoaded && !data.length">
            <p class="text-center text-xl font-bold">Налаштування відсутні</p>
        </div>
    </div>
</template>

<script>
import {useRoute} from "vue-router";
import Pagination from "../../../components/pagination/Pagination.vue";
import TableSkeleton from "../../../components/skeleton/TableSkeleton.vue";
import RouteHelper from "../../../helpers/Route/RouteHelper.js";
import {toast} from "vue3-toastify";
import {useHead} from "@vueuse/head";

export default {
    setup() {
        useHead({
            title: 'Налаштування',
        });
    },
    data() {
        return {
            isDataLoaded: false,
            isExportDisabled: false,
            data: [],
            route: useRoute()
        }
    },
    components: {
        Pagination,
        TableSkeleton
    },
    computed: {
        user() {
            return this.$store.state.user;
        }
    },
    mounted() {
        this.fetchConfigs();
    },
    methods: {
        fetchConfigs() {
            this.isDataLoaded = false;
            axios.get('/api/configs/list')
                .then(response => {
                    this.data = response.data.data;
                    this.isDataLoaded = true;
                })
        },
    }
}
</script>
