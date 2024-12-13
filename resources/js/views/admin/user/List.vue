<template>
    <div class="border rounded-2xl shadow-xl p-4">
        <div class="flex flex-row justify-between items-center">
            <h3 class="font-bold text-lg mb-4">Користувачі <span v-if="data.meta?.total">({{ data.meta.total }})</span>
            </h3>
            <button @click="exportUsers" :disabled="isExportDisabled" class="btn btn-success btn-outline">Експортувати
            </button>
        </div>
        <TableSkeleton v-if="!isDataLoaded"/>
        <div class="overflow-x-auto" v-if="isDataLoaded && data.data.length">
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Ім'я</th>
                    <th>Номер телефону</th>
                    <th>Email</th>
                    <th class="link">
                           <span @click="toggleOrder('orders_count')" class="cursor-pointer">
                            Кількість замовлень <span
                               v-if="filters.order_by === 'orders_count' && filters.order_dir === 'asc'">↑</span>
                             <span v-if="filters.order_by === 'orders_count' && filters.order_dir === 'desc'">↓</span>
                        </span>
                    </th>
                    <th class="link">
                        <span @click="toggleOrder('last_activity_at')" class="cursor-pointer">
                            Остання активність <span
                            v-if="filters.order_by === 'last_activity_at' && filters.order_dir === 'asc'">↑</span>
                             <span v-if="filters.order_by === 'last_activity_at' && filters.order_dir === 'desc'">↓</span>
                        </span>
                    </th>
                    <th class="link">
                        <span @click="toggleOrder('created_at')" class="cursor-pointer">
                            Дата реєстрації <span
                            v-if="filters.order_by === 'created_at' && filters.order_dir === 'asc'">↑</span>
                             <span v-if="filters.order_by === 'created_at' && filters.order_dir === 'desc'">↓</span>
                        </span>
                    </th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <!-- row 1 -->
                <tr v-for="user in data.data" :key="user.id">
                    <td>{{ user.id }}</td>
                    <td>{{user.surname}} {{ user.name }}</td>
                    <td>{{ user.phone }}</td>
                    <td>{{ user.email }}</td>
                    <td>{{ user.orders_count }}</td>
                    <td>{{ user.last_activity_at }}</td>
                    <td>{{ user.created_at }}</td>
                    <td>
                        <svg @click="deleteUser(user)" class="h-6 w-6 cursor-pointer text-red-500" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                            <line x1="10" y1="11" x2="10" y2="17"/>
                            <line x1="14" y1="11" x2="14" y2="17"/>
                        </svg>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div v-if="isDataLoaded && !data.data.length">
            <p class="text-center text-xl font-bold">Користувачі відсутні</p>
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
import {useHead} from "@vueuse/head";

export default {
    setup() {
        useHead({
            title: 'Користувачі',
        });
    },
    data() {
        return {
            isDataLoaded: false,
            isExportDisabled: false,
            data: {},
            filters: {
                page: 1,
                order_by: null,
                order_dir: null,
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
                this.fetchUsers();
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
        toggleOrder(orderBy) {
            this.filters.order_dir = this.filters.order_dir === 'asc' ? 'desc' : 'asc';
            this.filters.order_by = orderBy;
            this.filters.page = 1;
        },
        updatePage(page) {
            this.filters.page = page;
        },
        resolveQueryParams() {
            this.filters = {...this.filters, ...this.route.query};
        },
        fetchUsers() {
            this.isDataLoaded = false;
            RouteHelper.updateQueryParams(this.queryParams);
            axios.get('/api/users', {params: this.queryParams})
                .then(response => {
                    this.data = response.data;
                    this.isDataLoaded = true;
                })
        },
        exportUsers() {
            this.isExportDisabled = true;
            axios.get('/api/export', {params: {name: 'Users.xlsx', type: 'users'}})
                .then(response => {
                    toast.success("Експорт запущено, згодом він з'явиься на сторінці з експортами і буде доступним до завантаження", {
                        timeout: 10000,
                        position: 'bottom-right'
                    });
                })

            setTimeout(() => {
                this.isExportDisabled = false;
            }, 5000);
        },
        deleteUser(user) {
            if (confirm('Ви впевнені, що хочете видалити користувача?')) {
                axios.delete(`/api/users/${user.id}`)
                    .then(response => {
                        this.fetchUsers();
                        toast.success('Користувача видалено');
                    })
            }
        }
    }
}
</script>
