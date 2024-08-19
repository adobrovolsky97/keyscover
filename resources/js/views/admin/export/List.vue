<template>
    <div class="border rounded-2xl shadow-xl p-4">
        <div class="flex flex-row justify-between items-center">
            <h3 class="font-bold text-lg mb-4">Експорти</h3>
        </div>
        <TableSkeleton v-if="!isDataLoaded"/>
        <div class="overflow-x-auto" v-if="isDataLoaded && data.data.length">
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Назва</th>
                    <th>Статус</th>
                    <th>Помилка</th>
                    <th>Дата створення</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <!-- row 1 -->
                <tr v-for="exportItem in data.data" :key="user.id">
                    <th>{{ exportItem.id }}</th>
                    <th>{{ exportItem.name }}</th>
                    <th>
                        <span class="text-warning" v-if="exportItem.status === 'pending'">В процесі</span>
                        <span class="text-success" v-if="exportItem.status === 'completed'">Завершено</span>
                        <span class="text-error" v-if="exportItem.status === 'failed'">Помилка</span>
                    </th>
                    <th>{{ exportItem.error }}</th>
                    <th>{{ exportItem.created_at }}</th>
                    <th><span v-if="exportItem.status === 'completed'" @click="downloadExport(exportItem)" class="link cursor-pointer">Завантажити</span></th>
                </tr>
                </tbody>
            </table>
        </div>
        <div v-if="isDataLoaded && !data.data.length">
            <p class="text-center text-xl font-bold">Експорти відсутні</p>
        </div>

        <div class="mt-4 w-full"
             v-if="isDataLoaded && data?.meta?.last_page > 1">
            <Pagination
                :limit="5"
                :data="data"
                @pagination-change-page="updatePage"
            />
        </div>
    </div>
</template>

<script>
import {useRoute} from "vue-router";
import Pagination from "../../../components/pagination/Pagination.vue";
import TableSkeleton from "../../../components/skeleton/TableSkeleton.vue";
import RouteHelper from "../../../helpers/Route/RouteHelper.js";
import {toast} from "vue3-toastify";

export default {
    data() {
        return {
            isDataLoaded: false,
            isExportDisabled: false,
            data: {},
            filters: {
                page: 1,
            },
            route: useRoute()
        }
    },
    components: {
        Pagination,
        TableSkeleton
    },
    watch: {
        'filters': {
            handler: function () {
                this.fetchExports();
            },
            deep: true
        }
    },
    computed: {
        queryParams() {
            return this.filters;
        },
        user() {
            return this.$store.state.user;
        }
    },
    beforeRouteLeave() {
        this.isDataLoaded = false;
    },
    mounted() {
        this.resolveQueryParams();
    },
    methods: {
        updatePage(page) {
            this.filters.page = page;
        },
        resolveQueryParams() {
            this.filters = {...this.filters, ...this.route.query};
        },
        fetchExports() {
            this.isDataLoaded = false;
            RouteHelper.updateQueryParams(this.queryParams);
            axios.get('/api/exports', {params: this.queryParams})
                .then(response => {
                    this.data = response.data;
                    this.isDataLoaded = true;
                })
        },
        downloadExport(exportItem) {
            axios({
                url: `/api/exports/${exportItem.id}/download`,
                method: 'GET',
                responseType: 'blob'
            })
                .then(response => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', exportItem.name); // or any other file name
                    document.body.appendChild(link);
                    link.click();
                    link.remove();
                })
                .catch((e) => {
                    console.log(e)
                    toast.error('Помилка завантаження експорту');
                    this.isExportDisabled = false;
                })
        }
    }
}
</script>
