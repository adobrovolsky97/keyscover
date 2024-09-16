<template>
    <div class="border rounded-2xl shadow-xl p-4">
        <div class="flex flex-row justify-between items-center">
            <h3 class="font-bold text-lg mb-4">Товари <span v-if="data.meta?.total">({{ data.meta.total }})</span></h3>
        </div>
        <TableSkeleton v-if="!isDataLoaded"/>
        <div class="flex flex-row md:flex-col px-1 md:px-0 justify-between gap-2">
            <label class="input input-bordered text-sm input-sm md:input-md w-full flex items-center mb-2">
                <input @input="delaySearch" v-model="search" type="text" class="grow"
                       placeholder="Пошук"/>
                <svg v-if="search !== ''" @click="clearSearch" class="h-5 w-5 cursor-pointer" width="24" height="24"
                     viewBox="0 0 24 24" stroke-width="2"
                     stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z"/>
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
                <svg
                    v-else
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 16 16"
                    fill="currentColor"
                    class="h-4 w-4 opacity-70">
                    <path
                        fill-rule="evenodd"
                        d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"
                        clip-rule="evenodd"/>
                </svg>
            </label>
        </div>
        <div class="overflow-x-auto" v-if="isDataLoaded && data.data.length">
            <table class="table">
                <thead>
                <tr>
                    <th></th>
                    <th>ID</th>
                    <th>Назва</th>
                    <th>SKU</th>
                    <th>CRM синхр. припинено</th>
                    <th>Прихований</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <!-- row 1 -->
                <tr v-for="product in data.data" :key="product.id">
                    <th><img :src="product.image" class="w-52" alt=""></th>
                    <th>{{ product.id }}</th>
                    <th>{{ product.name }}</th>
                    <th>{{ product.sku }}</th>
                    <th>
                        <span v-if="product.is_stop_crm_update" class="badge badge-error text-white badge-sm">Так</span>
                        <span v-else class="badge badge-success text-white badge-sm">Ні</span>
                    </th>
                    <th>
                        <span v-if="product.is_hidden" class="badge badge-error text-white badge-sm">Так</span>
                        <span v-else class="badge badge-success text-white badge-sm">Ні</span>
                    </th>
                    <th>
                        <router-link
                            :to="{name: 'admin.products.edit', params: {id: product.id}}"
                            class="btn btn-ghost"
                        >
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>

                        </router-link>
                    </th>
                </tr>
                </tbody>
            </table>
        </div>
        <div v-if="isDataLoaded && !data.data.length">
            <p class="text-center text-xl font-bold">Товари відсутні</p>
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
            search: '',
            filters: {
                page: 1,
                search: '',
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
                this.fetchProducts();
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
        delaySearch() {
            clearTimeout(this.timer);
            this.timer = setTimeout(() => {
                this.filters.search = this.search;
                this.filters.page = 1;
            }, 800);
        },
        fetchProducts() {
            this.isDataLoaded = false;
            RouteHelper.updateQueryParams(this.queryParams);
            axios.get('/api/products', {params: this.queryParams})
                .then(response => {
                    this.data = response.data;
                    this.isDataLoaded = true;
                })
        },
    }
}
</script>
