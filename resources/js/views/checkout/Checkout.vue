<template>
    <div>
        <TableSkeleton v-if="!isDataLoaded"/>
        <div class="flex md:flex-row flex-col p-1 justify-between items-start gap-4" v-else>

            <div class="hero bg-base-100 rounded-xl shadow-lg border lg:sticky top-24 w-full md:w-2/5">
                <div class="content px-8 py-2 w-full">
                    <div class="py-2">
                        <h2 class="text-start text-lg mb-2 font-bold">Замовлення</h2>
                        <div class="row">
                            <div class="flex flex-col gap-2">
                                <div class="form-group">
                                    <input type="text" class="input input-sm input-bordered w-full" placeholder="Прізвище"
                                           v-model="form.surname">
                                    <error v-if="Object.keys(errors).length > 0" :errors="errors"
                                           attribute="surname"></error>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="input input-sm input-bordered w-full " placeholder="Ім'я"
                                           v-model="form.name">
                                    <error v-if="Object.keys(errors).length > 0" :errors="errors"
                                           attribute="name"></error>
                                </div>

                                <div class="form-group">
                                    <input type="text" class="input input-sm input-bordered w-full " placeholder="По-батькові"
                                           v-model="form.patronymic">
                                    <error v-if="Object.keys(errors).length > 0" :errors="errors"
                                           attribute="patronymic"></error>
                                </div>


                                <div class="form-group">
                                    <label class="form-control w-full">
                                        <label class="input input-bordered input-sm flex text-sm items-center gap-2">
                                            +38
                                            <input type="tel" class="text-sm left-0" autocomplete="off" v-model="form.phone"
                                                   placeholder="975231231"/>
                                        </label>
                                        <error v-if="Object.keys(errors).length > 0" :errors="errors"
                                               attribute="phone"></error>
                                    </label>
                                </div>
                                <textarea v-model="form.comment"
                                          class="textarea textarea-bordered textarea-xs w-full" rows="3" placeholder="Коментар"></textarea>
                                <error v-if="Object.keys(errors).length > 0" :errors="errors"
                                       attribute="comment"></error>
                                <div class="form-group -pt-4">
                                    <hr>
                                    <p>Спосіб доставки</p>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio"
                                               v-model="deliveryType" :value="'new-post'"
                                               name="delivery_subtype" id="delivery_subtype_1">
                                        <label class="form-check-label text-sm" for="delivery_subtype_1">
                                            Доставка у відділення або поштомат
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" v-model="deliveryType"
                                               :value="'self-pickup'" type="radio" name="delivery_subtype"
                                               id="delivery_subtype_2">
                                        <label class="form-check-label text-sm" for="delivery_subtype_2">
                                            Самовивіз
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group max-w-sm" v-if="deliveryType === 'new-post'">
                                    <input type="text" placeholder="Місто" class="input input-sm input-bordered w-full "
                                           @focusin="isCityFocused=true"
                                           v-model="form.city"
                                           @input="fetchCities">
                                    <div class="variants border p-3 border-t-0 rounded-lg max-h-56 overflow-y-auto"
                                         v-if="cityVariants.length > 0 && isCityFocused">
                                                    <span class="select2-results">
                                                        <ul>
                                                            <li
                                                                v-for="item in cityVariants" :key="item.id"
                                                                class="variant-item text-sm bg-base-100 mb-2 cursor-pointer hover:bg-gray-100"
                                                                role="treeitem"
                                                                @click="selectCity(item)"
                                                            >{{ item.name }}</li>
                                                           </ul></span>
                                    </div>
                                    <error v-if="Object.keys(errors).length > 0" :errors="errors"
                                           attribute="city_id"></error>
                                </div>

                                <div class="form-group flex flex-col gap-2"
                                     v-if="deliveryType === 'new-post' && isWarehouseLoading">
                                    <span class="loading loading-spinner mx-auto loading-md"></span>
                                </div>

                                <div class="form-group max-w-sm"
                                     v-if="deliveryType === 'new-post' && !isWarehouseLoading">
                                    <input type="text" placeholder="Відділення" class="input input-sm max-w-sm input-bordered w-full "
                                           @focusin="isWarehouseFocused=true"
                                           v-model="form.warehouse"
                                           @input="filterWarehouses">
                                    <div class="variants border p-3 border-t-0 rounded-lg max-h-56 overflow-y-auto"
                                         v-if="warehouseVariants.length > 0 && isWarehouseFocused">
                                                    <span class="select2-results">
                                                        <ul>
                                                            <li
                                                                v-for="item in filteredWarehouses" :key="item.id"
                                                                class="variant-item bg-base-100 mb-2 text-sm cursor-pointer hover:bg-gray-100"
                                                                role="treeitem"
                                                                @click="selectWarehouse(item)"
                                                            >{{ item.name }}</li>
                                                           </ul></span>
                                    </div>
                                    <error v-if="Object.keys(errors).length > 0" :errors="errors"
                                           attribute="warehouse_id"></error>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-2">
                            <hr>
                            <p>Спосіб оплати</p>
                            <div v-if="deliveryType ==='new-post'" class="form-check">
                                <input class="form-check-input" type="radio"
                                       v-model="form.paymentType" :value="'cash_on_delivery'"
                                       name="payment_type" id="payment_type_1">
                                <label class="form-check-label text-sm" for="payment_type_1">
                                    Розрахунок на пошті при отриманні
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio"
                                       v-model="form.paymentType" :value="'by_requisites'"
                                       name="payment_type" id="payment_type_2">
                                <label class="form-check-label text-sm" for="payment_type_2">
                                    Оплата за реквізитами IBAN
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio"
                                       v-model="form.paymentType" :value="'online'"
                                       name="payment_type" id="payment_type_3">
                                <label class="form-check-label text-sm" for="payment_type_3">
                                    Посилання для онлайн оплати картою
                                </label>
                                <br>
                                <span v-if="form.paymentType === 'online'" class="text-xs text-gray-400">
                                    * Банківська комісія +1,5% до вартості, але без плати за кредитні кошти.
                                </span>
                            </div>
                            <error v-if="Object.keys(errors).length > 0" :errors="errors"
                                   attribute="payment_type"></error>
                        </div>
                        <button class="btn btn-block btn-success text-white mt-4"
                                :disabled="isDisabled || $store.state.cart?.total <= 0"
                                @click="createOrder">Створити замовлення
                        </button>
                    </div>
                </div>
            </div>
            <div class="hero bg-base-100 rounded-xl shadow-lg p-4 border">
                <Cart/>
            </div>

        </div>
    </div>
</template>
<script>
import TableSkeleton from "../../components/skeleton/TableSkeleton.vue";
import Error from "../../components/Validation/Error.vue";
import {toast} from "vue3-toastify";
import 'vue3-toastify/dist/index.css';
import Cart from "../cart/Cart.vue";
import {useHead} from "@vueuse/head";


export default {
    setup() {
        // This is where you set the page title and meta tags
        useHead({
            title: 'Замовлення',
            meta: [
                { name: 'description', content: 'Оптовий продаж автомобільних ключів та все що з ними повязано. Співпраця виключно з майстрами!' },
            ]
        });
    },
    name: "Checkout",
    data() {
        return {
            isDataLoaded: false,
            cart: {},
            errors: {},
            isDisabled: false,

            deliveryType: 'new-post',

            cityVariants: [],
            filteredWarehouses: [],
            warehouseVariants: [],

            isWarehouseLoading: false,
            isWarehouseFocused: false,
            isAddressFocused: false,
            isCityFocused: false,

            form: {
                surname: null,
                name: null,
                patronymic: null,
                phone: null,
                apartment: null,
                flat: null,
                city: null,
                cityId: null,
                warehouse: null,
                warehouseId: null,
                comment: null,
                paymentType: null
            },
            orderTimeout: null,  // To store the timeout ID
        };
    },
    components: {
        TableSkeleton,
        Cart,
        'error': Error
    },
    beforeRouteLeave(to, from, next) {
        if (this.orderTimeout) {
            clearTimeout(this.orderTimeout);
        }
        next(); // continue with the navigation
    },
    watch: {
        'form.phone': function (val) {
            // Remove any non-digit characters
            val = val.replace(/\D/g, '');

            // Ensure the length does not exceed 10 digits
            if (val.length > 10) {
                val = val.slice(0, 10);
            }

            // Update the form.phone value
            this.form.phone = val;
        },
        'deliveryType': {
            handler: function (val) {
                this.form.paymentType = 'by_requisites';
            }
        }
    },
    mounted() {
        this.cart = this.$store.state.cart;

        if ((this.cart?.total ?? 0) === 0) {
            this.$router.push({name: 'home'});
        }

        this.fetchUser()
            .then(() => {
                this.form.surname = this.$store.state.user?.last_order?.surname ?? this.$store.state.user?.surname ?? null;
                this.form.name = this.$store.state.user?.last_order?.name ?? this.$store.state.user?.name ?? null;;
                this.form.patronymic = this.$store.state.user?.last_order?.patronymic ?? null;
                this.form.phone = this.$store.state.user?.last_order?.phone ?? this.$store.state.user.phone;
                this.form.city = this.$store.state.user?.last_order?.city_name ?? null;
                this.form.cityId = this.$store.state.user?.last_order?.city_id ?? null;
                this.form.warehouse = this.$store.state.user?.last_order?.warehouse_name ?? null;
                this.form.warehouseId = this.$store.state.user?.last_order?.warehouse_id ?? null;
                this.form.paymentType = this.$store.state.user?.last_order?.payment_type ?? 'by_requisites';
                this.deliveryType = this.$store.state.user?.last_order?.delivery_type ?? 'new-post';

                if (this.form.cityId) {
                    this.fetchCities();
                    this.fetchWarehouses();
                }
            });

        this.isDataLoaded = true;
    },
    methods: {
        selectCity(item) {
            this.warehouseVariants = [];
            this.filteredWarehouses = [];
            this.form.cityId = item.id;
            this.form.city = item.name;
            this.form.warehouseId = null;
            this.form.warehouse = null;
            this.isCityFocused = false;
            this.fetchWarehouses();
        },
        fetchUser() {
            return axios.get('/api/users/me')
                .then(response => {
                    this.$store.commit('setUser', response.data.data)
                })
        },
        filterWarehouses() {
            this.filteredWarehouses = this.warehouseVariants.filter((item) => {
                return item.name.includes(this.form.warehouse);
            })
        },
        fetchWarehouses() {
            if (!this.form.cityId) {
                return false;
            }

            if (this.warehouseTimer) {
                clearTimeout(this.warehouseTimer);
                this.warehouseTimer = null;
            }
            this.warehouseTimer = setTimeout(() => {
                this.isWarehouseLoading = true;
                axios
                    .get('/api/delivery/' + this.deliveryType + '/cities/' + this.form.cityId + '/warehouses')
                    .then((response) => {
                        this.warehouseVariants = response.data.data
                        this.filteredWarehouses = response.data.data

                    })
                    .finally(() => {
                        this.isWarehouseLoading = false;
                    })
            }, 500)
        },

        selectWarehouse(item) {
            this.form.warehouseId = item.id;
            this.form.warehouse = item.name;
            this.isWarehouseFocused = false;
        },
        fetchCities() {

            if(!this.form.city){
                this.cityVariants = [];
                this.warehouseVariants = [];
                this.form.warehouse = null;
                this.form.warehouseId = null;
                return false;
            }

            if (this.citiesTimer) {
                clearTimeout(this.citiesTimer);
                this.citiesTimer = null;
            }
            this.citiesTimer = setTimeout(() => {
                axios
                    .get('/api/delivery/' + this.deliveryType + '/cities?search=' + this.form.city)
                    .then((response) => {
                        this.cityVariants = response.data.data

                    })
            }, 500)
        },
        /**
         * Fetch data
         */
        fetchCart() {
            axios.get('/api/cart')
                .then(response => {
                    this.$store.commit('setCart', response.data.data)

                    if (response.data.data.is_changed) {
                        toast.warn('Було перераховано кількість товарів в кошику в звязку зменшенням залишків на складі!', {
                            timeout: 15000,
                            position: 'bottom-right'
                        });
                    }
                })
        },
        createOrder() {
            this.isDisabled = true;
            this.errors = [];
            axios
                .post('/api/orders', {
                    surname: this.form.surname,
                    name: this.form.name,
                    phone: this.form.phone,
                    patronymic: this.form.patronymic,
                    delivery_type: this.deliveryType,
                    comment: this.form.comment,
                    city_id: this.form.cityId,
                    city_name: this.form.city,
                    warehouse_id: this.form.warehouseId,
                    warehouse_name: this.form.warehouse,
                    payment_type: this.form.paymentType,
                })
                .then(() => {
                    this.fetchCart();
                    toast.success("Замовлення успішно створене! Передано на комплектацію!", {
                        autoClose: 5000,
                        position: 'bottom-right'
                    });
                    this.orderTimeout = setTimeout(() => {
                        this.$router.push({name: 'orders-list'});
                    }, 3000);
                })
                .catch((e) => {
                    this.errors = e.response.data.errors;
                    this.isDisabled = false;
                })

        }
    }
}
</script>
