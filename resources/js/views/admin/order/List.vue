<template>
    <div class="border rounded-2xl shadow-xl p-4">
        <div class="flex flex-row justify-between items-center">
            <h3 class="font-bold text-lg mb-4">Замовлення <span v-if="data.meta?.total">({{ data.meta.total }})</span>
            </h3>
        </div>
        <TableSkeleton v-if="!isDataLoaded"/>
        <div class="overflow-x-auto" v-if="isDataLoaded && data.data.length">
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Номер</th>
                    <th>ПІБ</th>
                    <th>Номер телефону</th>
                    <th>Тип доставки</th>
                    <th>Тип оплати</th>
                    <th>Місто</th>
                    <th>Відділення</th>
                    <th>Вартість</th>
                    <th>Знижка</th>
                    <th>Створено</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="order in data.data" :key="order.id">
                    <td>{{ order.id }}</td>
                    <td>{{ order.number }}</td>
                    <td>{{ order.surname }} {{ order.name }} {{ order.patronymic }}</td>
                    <td>+38{{ order.phone }}</td>
                    <td>{{ order.delivery_type === 'self-pickup' ? 'Самовивіз' : 'Нова Пошта' }}</td>
                    <td>
                        {{
                            order.payment_type === 'by_requisites' ? 'Оплата за реквізитами' : 'Оплата на пошті при отриманні'
                        }}
                    </td>
                    <td>{{ order.city_name }}</td>
                    <td>{{ order.warehouse_name }}</td>
                    <td>{{ order.total_price_usd }}$ / {{ order.total_price_uah }} грн.</td>
                    <td>{{ order.discount_usd }}$ / {{ order.discount_uah }} грн.</td>
                    <td>{{ order.created_at }}</td>
                    <td>
                        <button class="btn btn-xs btn-success text-white" @click="showOrder(order)">
                            Переглянути
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div v-if="isDataLoaded && !data.data.length">
            <p class="text-center text-xl font-bold">Замовлення відсутні</p>
        </div>

        <dialog id="order_modal" class="modal" v-if="activeOrder">
            <div class="modal-box w-11/12 max-w-5xl">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <h3 class="text-lg font-bold">Замовлення {{activeOrder.number}}</h3>

                <div class="client-data flex flex-col justify-start items-start">
                    <span><span class="font-bold">ПІБ</span>: {{ activeOrder.surname }} {{ activeOrder.name }} {{ activeOrder.patronymic }}</span>
                    <span><span class="font-bold">Телефон</span>: {{ activeOrder.phone }}</span>
                    <span><span class="font-bold">Спосіб доставки</span>: {{ activeOrder.delivery_type === 'new-post' ? 'Нова Пошта' : 'Самовивіз' }}</span>
                    <span><span class="font-bold">Спосіб оплати</span>: {{ activeOrder.payment_type === 'by_requisites' ? 'Оплата за реквізитами' : 'Розрахунок на пошті при отриманні' }}</span>
                    <span v-if="activeOrder.city_name"><span class="font-bold">Місто</span>: {{ activeOrder.city_name }}</span>
                    <span v-if="activeOrder.warehouse_name"><span class="font-bold">Відділення</span>: {{ activeOrder.warehouse_name }}</span>
                    <span><span class="font-bold">Вартість</span>: {{ activeOrder.total_price_usd }} $ / {{ activeOrder.total_price_uah }} грн.</span>
                    <span v-if="activeOrder.discount_percent > 0"><span class="font-bold">Знижка ({{ activeOrder.discount_percent }}%)</span>: {{ activeOrder.discount_usd }} $ / {{activeOrder.discount_uah}} грн.</span>
                    <span v-if="activeOrder.discount_percent > 0"><span class="font-bold">Вартість зі знижкою</span>: {{ activeOrder.total_with_discount_usd }} $ / {{ activeOrder.total_with_discount_uah}} грн.</span>
                    <span v-if="activeOrder.comment"><span class="font-bold">Коментар</span>: {{ activeOrder.comment }}</span>
                </div>
                <div class="products-list w-full overflow-x-auto">
                    <table class="table min-w-full">
                        <thead>
                        <tr>
                            <th>Фото</th>
                            <th>Назва</th>
                            <th>Арт.</th>
                            <th>Ціна за шт, $</th>
                            <th>Кількість</th>
                            <th>Вартість, $</th>
                            <th>Вартість, грн</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(product, index) in activeOrder.products" :key="index">
                            <td>
                                <div class="avatar">
                                    <div class="w-24 rounded">
                                        <img :src="product.product.image"/>
                                    </div>
                                </div>
                            </td>
                            <td><span class="link cursor-pointer" @click="showProduct(product)">{{ product.product.name }}</span></td>
                            <td class="text-gray-400">{{ product.product.sku }}</td>
                            <td>{{product.product.usd_price}} $</td>
                            <td>{{product.quantity}}</td>
                            <td>{{product.total_price}} $</td>
                            <td>{{product.total_price_uah}} грн.</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </dialog>

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
            activeOrder: null,
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
            this.filters = {...this.filters, ...this.route.query};
        },
        showOrder(order) {
            this.activeOrder = order;
            this.$nextTick(() => {
                const dialog = document.getElementById('order_modal');
                dialog.showModal();
            });
        },
        showProduct(product) {
            // open in new tab
            const url = this.$router.resolve({
                name: 'product.show',
                params: { id: product.product.id }
            }).href;

            window.open(url, '_blank');
        },
        fetchOrders() {
            this.isDataLoaded = false;
            RouteHelper.updateQueryParams(this.queryParams);
            axios.get('/api/orders', {params: this.queryParams})
                .then(response => {
                    this.data = response.data;
                    this.isDataLoaded = true;
                })
        },
    }
}
</script>
