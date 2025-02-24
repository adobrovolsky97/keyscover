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
                    <th>Статус</th>
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
                    <td>
                        <span
                            v-if="order.crm_status"
                            class="font-light"
                            :class="{'text-red-400': !order.is_paid, 'text-green-400': order.is_paid}">{{
                                order.crm_status
                            }}</span>
                    </td>
                    <td>{{ order.surname }} {{ order.name }} {{ order.patronymic }}</td>
                    <td>+38{{ order.phone }}</td>
                    <td>{{ order.delivery_type === 'self-pickup' ? 'Самовивіз' : 'Нова Пошта' }}</td>
                    <td>
                        <span v-if="order.payment_type === 'by_requisites'">Оплата за реквізитами IBAN</span>
                        <span v-if="order.payment_type === 'cash_on_delivery'">Розрахунок на пошті при отриманні</span>
                        <span
                            v-if="order.payment_type === 'online'">Посилання для онлайн оплати картою (+1,5% комісії)</span>
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
                    <h3 class="text-lg font-bold">Замовлення {{ activeOrder.number }}<span
                        v-if="activeOrder.crm_status"
                        class="font-light"
                        :class="{'text-red-400': !activeOrder.is_paid, 'text-green-400': activeOrder.is_paid}"> ({{
                            activeOrder.crm_status
                        }})</span></h3>

                    <div class="flex flex-row justify-between gap-2 items-center mr-2">
                        <svg class="cursor-pointer" @click="exportOrderToPdf(activeOrder)" height="30px" width="30px"
                             version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                             xmlns:xlink="http://www.w3.org/1999/xlink"
                             viewBox="0 0 309.267 309.267" xml:space="preserve">
                            <g>
                                <path style="fill:#E2574C;" d="M38.658,0h164.23l87.049,86.711v203.227c0,10.679-8.659,19.329-19.329,19.329H38.658
                                    c-10.67,0-19.329-8.65-19.329-19.329V19.329C19.329,8.65,27.989,0,38.658,0z"/>
                                <path style="fill:#B53629;" d="M289.658,86.981h-67.372c-10.67,0-19.329-8.659-19.329-19.329V0.193L289.658,86.981z"/>
                                <path style="fill:#FFFFFF;" d="M217.434,146.544c3.238,0,4.823-2.822,4.823-5.557c0-2.832-1.653-5.567-4.823-5.567h-18.44
                                    c-3.605,0-5.615,2.986-5.615,6.282v45.317c0,4.04,2.3,6.282,5.412,6.282c3.093,0,5.403-2.242,5.403-6.282v-12.438h11.153
                                    c3.46,0,5.19-2.832,5.19-5.644c0-2.754-1.73-5.49-5.19-5.49h-11.153v-16.903C204.194,146.544,217.434,146.544,217.434,146.544z
                                     M155.107,135.42h-13.492c-3.663,0-6.263,2.513-6.263,6.243v45.395c0,4.629,3.74,6.079,6.417,6.079h14.159
                                    c16.758,0,27.824-11.027,27.824-28.047C183.743,147.095,173.325,135.42,155.107,135.42z M155.755,181.946h-8.225v-35.334h7.413
                                    c11.221,0,16.101,7.529,16.101,17.918C171.044,174.253,166.25,181.946,155.755,181.946z M106.33,135.42H92.964
                                    c-3.779,0-5.886,2.493-5.886,6.282v45.317c0,4.04,2.416,6.282,5.663,6.282s5.663-2.242,5.663-6.282v-13.231h8.379
                                    c10.341,0,18.875-7.326,18.875-19.107C125.659,143.152,117.425,135.42,106.33,135.42z M106.108,163.158h-7.703v-17.097h7.703
                                    c4.755,0,7.78,3.711,7.78,8.553C113.878,159.447,110.863,163.158,106.108,163.158z"/>
                            </g>
                        </svg>

                        <svg @click="exportOrderToXls(activeOrder)" class="cursor-pointer" height="30px" width="30px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                             viewBox="0 0 512 512" xml:space="preserve">
                            <path style="fill:#E2E5E7;" d="M128,0c-17.6,0-32,14.4-32,32v448c0,17.6,14.4,32,32,32h320c17.6,0,32-14.4,32-32V128L352,0H128z"/>
                                                        <path style="fill:#B0B7BD;" d="M384,128h96L352,0v96C352,113.6,366.4,128,384,128z"/>
                                                        <polygon style="fill:#CAD1D8;" points="480,224 384,128 480,128 "/>
                                                        <path style="fill:#84BD5A;" d="M416,416c0,8.8-7.2,16-16,16H48c-8.8,0-16-7.2-16-16V256c0-8.8,7.2-16,16-16h352c8.8,0,16,7.2,16,16
                                V416z"/>
                                                        <g>
                                <path style="fill:#FFFFFF;" d="M144.336,326.192l22.256-27.888c6.656-8.704,19.584,2.416,12.288,10.736
                                    c-7.664,9.088-15.728,18.944-23.408,29.04l26.096,32.496c7.04,9.6-7.024,18.8-13.936,9.328l-23.552-30.192l-23.152,30.848
                                    c-6.528,9.328-20.992-1.152-13.696-9.856l25.712-32.624c-8.064-10.112-15.872-19.952-23.664-29.04
                                    c-8.048-9.6,6.912-19.44,12.8-10.464L144.336,326.192z"/>
                                                            <path style="fill:#FFFFFF;" d="M197.36,303.152c0-4.224,3.584-7.808,8.064-7.808c4.096,0,7.552,3.6,7.552,7.808v64.096h34.8
                                    c12.528,0,12.8,16.752,0,16.752H205.44c-4.48,0-8.064-3.184-8.064-7.792v-73.056H197.36z"/>
                                                            <path style="fill:#FFFFFF;" d="M272.032,314.672c2.944-24.832,40.416-29.296,58.08-15.728c8.704,7.024-0.512,18.16-8.192,12.528
                                    c-9.472-6-30.96-8.816-33.648,4.464c-3.456,20.992,52.192,8.976,51.296,43.008c-0.896,32.496-47.968,33.248-65.632,18.672
                                    c-4.24-3.456-4.096-9.072-1.792-12.544c3.328-3.312,7.024-4.464,11.392-0.88c10.48,7.152,37.488,12.528,39.392-5.648
                                    C321.28,339.632,268.064,351.008,272.032,314.672z"/>
                            </g>
                                                        <path style="fill:#CAD1D8;" d="M400,432H96v16h304c8.8,0,16-7.2,16-16v-16C416,424.8,408.8,432,400,432z"/>
                        </svg>
                    </div>
                </div>

                <div class="client-data flex flex-col justify-start items-start">
                    <span v-if="activeOrder.ttn"><span class="font-bold">ТТН</span>: {{ activeOrder.ttn }} <span
                        class="italic" v-if="activeOrder.nova_poshta_status"> ({{
                            activeOrder.nova_poshta_status
                        }})</span></span>
                    <span><span class="font-bold">ПІБ</span>: {{ activeOrder.surname }} {{
                            activeOrder.name
                        }} {{ activeOrder.patronymic }}</span>
                    <span><span class="font-bold">Телефон</span>: {{ activeOrder.phone }}</span>
                    <span><span class="font-bold">Спосіб доставки</span>: {{
                            activeOrder.delivery_type === 'new-post' ? 'Нова Пошта' : 'Самовивіз'
                        }}</span>
                    <span><span class="font-bold">Спосіб оплати</span>:
                    <span v-if="activeOrder.payment_type === 'by_requisites'">Оплата за реквізитами IBAN</span>
                    <span
                        v-if="activeOrder.payment_type === 'cash_on_delivery'">Розрахунок на пошті при отриманні</span>
                    <span v-if="activeOrder.payment_type === 'online'">Посилання для онлайн оплати картою (+1,5% комісії)</span>
                    </span>
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
                sort_by: 'created_at',
                order_dir: 'desc',
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
        exportOrderToPdf(order) {
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
        exportOrderToXls(order) {
            if (this.isExportDisabled) {
                return;
            }

            this.isExportDisabled = true;

            axios.get(`/api/export`, {
                params: {
                    name: `Замовлення_#${order.number}.xlsx`,
                    type: 'orders_xlsx',
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
