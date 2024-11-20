<template>
    <div class="border rounded-2xl shadow-xl p-4">
        <div class="flex flex-row justify-between items-center">
            <h3 class="font-bold text-lg mb-4">Замовлення <span v-if="data.meta?.total">({{ data.meta.total }})</span>
            </h3>
        </div>
        <TableSkeleton v-if="!isDataLoaded"/>

        <div>
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
                    <th>ID</th>
                    <th>Номер</th>
                    <th>ПІБ</th>
                    <th>Номер телефону</th>
                    <th>Тип доставки</th>
                    <th>Тип оплати</th>
                    <th>Місто</th>
                    <th>Відділення</th>
                    <th>
                        <span @click="toggleOrder('total_price_uah')" class="cursor-pointer link">
                            Вартість <span
                            v-if="filters.sort_by === 'total_price_uah' && filters.order_dir === 'asc'">↑</span>
                             <span v-if="filters.sort_by === 'total_price_uah' && filters.order_dir === 'desc'">↓</span>
                        </span>
                    </th>
                    <th>Знижка</th>
                    <th>
                        <span @click="toggleOrder('created_at')" class="cursor-pointer link">
                            Створено <span
                            v-if="filters.sort_by === 'created_at' && filters.order_dir === 'asc'">↑</span>
                             <span v-if="filters.sort_by === 'created_at' && filters.order_dir === 'desc'">↓</span>
                        </span>
                    </th>
                    <th>Синхр.?</th>
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
                        <span v-if="order.is_crm_synced" class="badge badge-sm badge-success text-white">Так</span>
                        <span v-else class="badge cursor-pointer badge-error badge-sm whitespace-nowrap text-white"
                              @click="syncOrder(order)">Ні, синхронізувати</span>
                    </td>
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
                <div class="flex flex-row justify-between items-start">
                    <h3 class="text-lg font-bold">Замовлення {{ activeOrder.number }}</h3>
                    <svg class="h-8 w-8 cursor-pointer text-green-700 float-right mr-4"
                         @click="exportAndDownloadOrder(activeOrder)" width="24" height="24" viewBox="0 0 24 24"
                         stroke-width="2"
                         stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"/>
                        <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                        <line x1="12" y1="11" x2="12" y2="17"/>
                        <polyline points="9 14 12 17 15 14"/>
                    </svg>
                </div>

                <div class="client-data flex flex-col justify-start items-start">
                    <span><span class="font-bold">ПІБ</span>: {{ activeOrder.surname }} {{
                            activeOrder.name
                        }} {{ activeOrder.patronymic }}</span>
                    <span><span class="font-bold">Телефон</span>: {{ activeOrder.phone }}</span>
                    <span><span class="font-bold">Спосіб доставки</span>: {{
                            activeOrder.delivery_type === 'new-post' ? 'Нова Пошта' : 'Самовивіз'
                        }}</span>
                    <span><span class="font-bold">Спосіб оплати</span>: {{
                            activeOrder.payment_type === 'by_requisites' ? 'Оплата за реквізитами' : 'Розрахунок на пошті при отриманні'
                        }}</span>
                    <span v-if="activeOrder.city_name"><span class="font-bold">Місто</span>: {{ activeOrder.city_name }}</span>
                    <span v-if="activeOrder.warehouse_name"><span
                        class="font-bold">Відділення</span>: {{ activeOrder.warehouse_name }}</span>
                    <span><span class="font-bold">Вартість</span>: {{
                            activeOrder.total_price_usd
                        }} $ / {{ activeOrder.total_price_uah }} грн.</span>
                    <span v-if="activeOrder.discount_percent > 0"><span class="font-bold">Знижка ({{
                            activeOrder.discount_percent
                        }}%)</span>: {{ activeOrder.discount_usd }} $ / {{ activeOrder.discount_uah }} грн.</span>
                    <span v-if="activeOrder.discount_percent > 0"><span class="font-bold">Вартість зі знижкою</span>: {{
                            activeOrder.total_with_discount_usd
                        }} $ / {{ activeOrder.total_with_discount_uah }} грн.</span>
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
                            <td><span class="link cursor-pointer" @click="showProduct(product)">{{
                                    product.product.name
                                }}</span></td>
                            <td class="text-gray-400">{{ product.product.sku }}</td>
                            <td>{{ product.product.usd_price }} $</td>
                            <td>{{ product.quantity }}</td>
                            <td>{{ product.total_price }} $</td>
                            <td>{{ product.total_price_uah }} грн.</td>
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
import {useHead} from "@vueuse/head";
import axios from "axios";

export default {
    setup() {
        useHead({
            title: 'Замовлення',
        });
    },
    data() {
        return {
            isDataLoaded: false,
            isExportDisabled: false,
            activeOrder: null,
            data: {},
            search: '',
            filters: {
                search: '',
                page: 1,
                sort_by: null,
                order_dir: 'asc',
            },
            route: useRoute(),
            isSyncPending: false
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
        delaySearch() {
            clearTimeout(this.timer);
            this.timer = setTimeout(() => {
                this.filters.search = this.search;
                this.filters.page = 1;
            }, 800);
        },
        updatePage(page) {
            this.filters.page = page;
        },
        resolveQueryParams() {
            this.filters = {...this.filters, ...this.route.query};

            this.search = this.filters.search;
        },
        showOrder(order) {
            this.activeOrder = order;
            this.$nextTick(() => {
                const dialog = document.getElementById('order_modal');
                dialog.showModal();
            });
        },
        toggleOrder(orderBy) {
            this.filters.order_dir = this.filters.order_dir === 'asc' ? 'desc' : 'asc';
            this.filters.sort_by = orderBy;
            this.filters.page = 1;
        },
        showProduct(product) {
            // open in new tab
            const url = this.$router.resolve({
                name: 'product.show',
                params: {id: product.product.id}
            }).href;

            window.open(url, '_blank');
        },
        syncOrder(order) {

            if (this.isSyncPending) {
                return;
            }

            this.isSyncPending = true;

            axios.post(`/api/orders/${order.id}/sync`)
                .then(response => {
                    this.isSyncPending = false;
                    toast.success('Замовлення успішно синхронізовано');
                    this.fetchOrders();
                })
                .catch(error => {
                    this.isSyncPending = false;
                    toast.error(error?.response?.data?.message ?? 'Помилка синхронізації замовлення');
                });
        },
        exportAndDownloadOrder(order) {
            if (this.isExportDisabled) {
                return;
            }

            this.isExportDisabled = true;

            axios.get(`/api/export`, {
                params: {
                    name: `Замовлення_#${order.number}.pdf`,
                    type: 'orders',
                    params: {
                        order_id: order.id
                    }
                }
            })
                .then(response => {
                    this.isExportDisabled = false;
                    toast.success('Замовлення успішно експортовано');
                })
                .catch(error => {
                    this.isExportDisabled = false;
                    toast.error(error?.response?.data?.message ?? 'Помилка експорту замовлення');
                });
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
