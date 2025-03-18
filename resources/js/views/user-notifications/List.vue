<template>
    <div class="flex flex-col gap-2">
        <h3 class="text-center font-bold text-2xl">Сповіщення</h3>

        <div class="flex flex-row justify-end mb-4">
            <button @click="readAll" class="btn btn-xs rounded-full">Прочитати всі</button>
        </div>

        <div class="relative bg-base-100 w-full">
            <ContentSkeleton classes="w-full flex flex-col gap-2"
                             :items="['h-16', 'h-16', 'h-16','h-16','h-16','h-16','h-16','h-16','h-16','h-16','h-16','h-16','h-16','h-16','h-16']"
                             v-if="!isDataLoaded"/>
            <div class="w-full flex flex-col gap-2"
                 v-if="isDataLoaded && data.data.length">

                <div v-for="notification in data.data" role="alert" class="alert w-full relative"
                     :class="{'!alert-warning': notification.is_admin_notification}" :key="notification.id">
                    <svg
                        v-if="!notification.is_read"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        class="stroke-info h-6 w-6 shrink-0">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span v-else></span>
                    <span class="w-full">
                        <MdPreview class="w-full" :modelValue="notification.text"/>
                    </span>
                    <div>
                        <span>{{ notification.created_at }}</span>
                    </div>

                    <div @click="deleteNotification(notification)" v-if="notification.is_admin_notification && $store?.state?.user?.role === 'admin'" class="absolute top-2 right-2 cursor-pointer">
                        <svg class="h-5 w-5 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                            <line x1="10" y1="11" x2="10" y2="17"/>
                            <line x1="14" y1="11" x2="14" y2="17"/>
                        </svg>
                    </div>
                </div>

            </div>
            <div v-if="isDataLoaded && !data.data.length">
                <p class="text-center text-lg font-bold">Сповіщення не знайдені</p>
            </div>
            <div
                class="mt-4 w-full border shadow-xl rounded-2xl p-2 flex lg:flex-row flex-col gap-2 justify-between items-center"
                v-if="isDataLoaded && data?.meta?.last_page > 1">
                <Pagination
                    :limit="5"
                    :data="data"
                    @pagination-change-page="updatePage"
                />
            </div>
        </div>
    </div>
</template>

<script>
import Pagination from "../../components/pagination/Pagination.vue";
import TableSkeleton from "../../components/skeleton/TableSkeleton.vue";
import RouteHelper from "../../helpers/Route/RouteHelper.js";
import {useRoute} from "vue-router";
import Item from "../product/Item.vue";
import CategoryItem from "../../components/CategoryItem.vue";
import ContentSkeleton from "../../components/skeleton/ContentSkeleton.vue";
import {useHead} from "@vueuse/head";
import axios from "axios";
import 'md-editor-v3/lib/preview.css';
import {MdPreview} from "md-editor-v3";
import store from "../../store.js";
import {toast} from "vue3-toastify";

export default {
    setup() {
        // This is where you set the page title and meta tags
        useHead({
            title: 'Сповіщення',
            meta: [
                {
                    name: 'description',
                    content: 'Оптовий продаж автомобільних ключів та все що з ними повязано. Співпраця виключно з майстрами!'
                },
            ]
        });
    },
    name: "List",
    data() {
        return {
            isDataLoaded: false,
            data: {},
            filters: {
                page: 1,
                per_page: 20
            },
            route: useRoute()
        };
    },
    components: {
        MdPreview,
        ContentSkeleton,
        CategoryItem,
        Item,
        Pagination,
        TableSkeleton
    },
    watch: {
        'filters': {
            handler: function () {
                this.fetchData(false);
            },
            deep: true
        },
    },
    created() {
        this.resolveQueryParams();
    },
    computed: {
        queryParams() {
            return this.filters;
        },
        user() {
            return this.$store.state.user;
        }
    },
    methods: {
        readAll() {
            axios.post('/api/notifications/read-all')
                .then((response) => {
                    window.location.reload();
                });
        },
        deleteNotification(notification) {
            if (confirm('Підтвердіть видалення')) {
                axios.delete('/api/notifications/' + notification.id)
                    .then(() => {
                        this.fetchData();
                        toast.success('Сповіщення успішно видалено');
                    })
                    .catch(error => {
                        toast.error('Помилка видалення сповіщення');
                    })
            }
        },
        updatePage(page) {
            this.filters.page = page;
        },
        fetchData() {
            this.isDataLoaded = false;
            RouteHelper.updateQueryParams(this.queryParams);
            this.isDataLoaded = false;
            return axios.get('/api/notifications', {params: this.filters})
                .then((response) => {
                    this.data = response.data;
                    this.isDataLoaded = true;
                });
        },
        resolveQueryParams() {
            this.filters = {...this.filters, ...this.route.query};
        },
    }
};
</script>

<style>
.md-editor-previewOnly {
    background: transparent !important;
}
</style>
