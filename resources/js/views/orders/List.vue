<template>
    <div class="card border rounded-2xl shadow-lg p-4">
        <div class="card-title">
            <h2 class="ml-2">Історія Замовлень</h2>
        </div>
        <div class="container">
            <TableSkeleton v-if="!isDataLoaded"/>
            <div class="orders-list mt-4" v-if="isDataLoaded && data.data.length > 0">
                <div class="collapse collapse-arrow border shadow-xl mb-4 w-full" v-for="order in data.data" :key="order.id">
                    <input type="checkbox"/>
                    <div class="collapse-title md:text-xl text-sm font-medium flex md:flex-row flex-col md:justify-between justify-start md:items-center items-start">
                        <span>Замовлення #{{ order.id }}</span>
                        <span>{{ order.created_at }}</span>
                    </div>
                    <div class="collapse-content  overflow-x-auto">
                        <div class="client-data flex flex-col justify-start items-start">
                            <span><span class="font-bold">ПІБ</span>: {{ order.surname }} {{ order.name }} {{ order.patronymic }}</span>
                            <span><span class="font-bold">Телефон</span>: {{ order.phone }}</span>
                            <span><span class="font-bold">Спосіб доставки</span>: {{ order.delivery_type === 'new-post' ? 'Нова Пошта' : 'Самовивіз' }}</span>
                            <span v-if="order.city_name"><span class="font-bold">Місто</span>: {{ order.city_name }}</span>
                            <span v-if="order.warehouse_name"><span class="font-bold">Відділення</span>: {{ order.warehouse_name }}</span>
                            <span><span class="font-bold">Вартість</span>: {{ order.total_price_usd }} $ / {{ order.total_price_uah }} грн.</span>
                            <span v-if="order.discount_percent > 0"><span class="font-bold">Знижка ({{ order.discount_percent }}%)</span>: {{ order.discount_usd }} $ / {{order.discount_uah}} грн.</span>
                            <span v-if="order.discount_percent > 0"><span class="font-bold">Вартість зі знижкою</span>: {{ order.total_price_usd - order.discount_usd }} $ / {{ order.total_price_uah - order.discount_uah }} грн.</span>
                        </div>
                        <div class="products-list w-full overflow-x-auto">
                            <table class="table min-w-full">
                                <thead>
                                <tr>
                                    <th>Фото</th>
                                    <th>Назва</th>
                                    <th>SKU</th>
                                    <th>Кількість</th>
                                    <th>Вартість, $</th>
                                    <th>Вартість, грн</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(product, index) in order.products" :key="index">
                                    <td>
                                        <div class="avatar">
                                            <div class="w-24 rounded">
                                                <img :src="product.product.image"/>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ product.product.name }}</td>
                                    <td class="text-gray-400">{{ product.product.sku }}</td>
                                    <td>{{product.quantity}}</td>
                                    <td>{{product.total_price}} $</td>
                                    <td>{{product.total_price_uah}} грн.</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="mt-6 w-full" v-if="isDataLoaded && data?.meta?.last_page > 1">
                    <Pagination :limit="5" :data="data" @pagination-change-page="updatePage"/>
                </div>
            </div>
            <div class="no-orders" v-if="isDataLoaded && !data.data?.length">
                <p class="text-center text-xl">Історія замовлень пуста</p>
            </div>
        </div>
    </div>
</template>

<script>
import { useRoute } from "vue-router";
import Pagination from "../../components/pagination/Pagination.vue";
import TableSkeleton from "../../components/skeleton/TableSkeleton.vue";
import RouteHelper from "../../helpers/Route/RouteHelper.js";

export default {
    data() {
        return {
            isDataLoaded: false,
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
                this.fetchOrders();
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
            this.filters = { ...this.filters, ...this.route.query };
        },
        fetchOrders() {
            this.isDataLoaded = false;
            RouteHelper.updateQueryParams(this.queryParams);
            axios.get('/api/orders', { params: this.queryParams })
                .then(response => {
                    this.data = response.data;
                    this.isDataLoaded = true;
                })
        }
    }
}
</script>
