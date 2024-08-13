<template>
    <div>
        <TableSkeleton v-if="!isDataLoaded"/>
        <div class="flex md:flex-row flex-col p-1 justify-between items-start gap-4" v-else>

            <div class="hero bg-base-100 rounded-xl shadow-lg border">
                <div class="hero-content w-full">
                    <div class="card-body">
                        <h2 class="text-center">Замовлення</h2>
                        <div class="row">
                            <div class="flex flex-col gap-4">
                                <div class="form-group">
                                    <label>Прізвище</label>
                                    <input type="text" class="input input-bordered w-full" placeholder="Прізвище"
                                           v-model="form.surname">
                                    <error v-if="Object.keys(errors).length > 0" :errors="errors"
                                           attribute="surname"></error>
                                </div>
                                <div class="form-group">
                                    <label>Ім'я</label>
                                    <input type="text" class="input input-bordered w-full " placeholder="Ім'я"
                                           v-model="form.name">
                                    <error v-if="Object.keys(errors).length > 0" :errors="errors"
                                           attribute="name"></error>
                                </div>

                                <div class="form-group">
                                    <label>По-батькові</label>
                                    <input type="text" class="input input-bordered w-full " placeholder="По-батькові"
                                           v-model="form.patronymic">
                                    <error v-if="Object.keys(errors).length > 0" :errors="errors"
                                           attribute="patronymic"></error>
                                </div>


                                <div class="form-group">
                                    <label>Номер телефону</label>
                                    <input type="text" class="input input-bordered w-full " placeholder="Номер телефону"
                                           v-model="form.phone">
                                    <error v-if="Object.keys(errors).length > 0" :errors="errors"
                                           attribute="phone"></error>
                                </div>
                                <div class="form-group">
                                    <label for="">Коментар</label>
                                    <textarea v-model="form.comment"
                                              class="textarea input-bordered w-full "></textarea>
                                    <error v-if="Object.keys(errors).length > 0" :errors="errors"
                                           attribute="comment"></error>
                                </div>
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio"
                                               v-model="deliveryType" :value="'new-post'"
                                               name="delivery_subtype" id="delivery_subtype_1">
                                        <label class="form-check-label" for="delivery_subtype_1">
                                            Доставка у відділення або поштомат
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" v-model="deliveryType"
                                               :value="'self-pickup'" type="radio" name="delivery_subtype"
                                               id="delivery_subtype_2">
                                        <label class="form-check-label" for="delivery_subtype_2">
                                            Самовивіз
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <select v-model="form.paymentType" class="select select-bordered w-full">
                                        <option :value="null">Оплата</option>
                                        <option :value="'cash_on_delivery'">Розрахунок на пошті при отриманні</option>
                                        <option :value="'by_requisites'">Оплата по реквізитам</option>
                                    </select>
                                    <error v-if="Object.keys(errors).length > 0" :errors="errors"
                                           attribute="payment_type"></error>
                                </div>

                                <div class="form-group" v-if="deliveryType === 'new-post'">
                                    <label>Місто</label>
                                    <input type="text" class="input input-bordered w-full "
                                           @focusin="isCityFocused=true"
                                           v-model="form.city"
                                           @input="fetchCities">
                                    <div class="variants border p-3 border-t-0 rounded-lg max-h-56 overflow-y-auto"
                                         v-if="cityVariants.length > 0 && isCityFocused">
                                                    <span class="select2-results">
                                                        <ul>
                                                            <li
                                                                v-for="item in cityVariants" :key="item.id"
                                                                class="variant-item bg-base-100 mb-2 cursor-pointer hover:bg-gray-100"
                                                                role="treeitem"
                                                                @click="selectCity(item)"
                                                            >{{ item.name }}</li>
                                                           </ul></span>
                                    </div>
                                    <error v-if="Object.keys(errors).length > 0" :errors="errors"
                                           attribute="city_id"></error>
                                </div>

                                <div class="form-group"
                                     v-if="deliveryType === 'new-post' && (filteredWarehouses.length > 0 || form.warehouseId)">
                                    <label>Відділення</label>
                                    <input type="text" class="input input-bordered w-full "
                                           @focusin="isWarehouseFocused=true"
                                           v-model="form.warehouse"
                                           @input="filterWarehouses">
                                    <div class="variants border p-3 border-t-0 rounded-lg max-h-56 overflow-y-auto"
                                         v-if="warehouseVariants.length > 0 && isWarehouseFocused">
                                                    <span class="select2-results">
                                                        <ul>
                                                            <li
                                                                v-for="item in filteredWarehouses" :key="item.id"
                                                                class="variant-item bg-base-100 mb-2 cursor-pointer hover:bg-gray-100"
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
                        <button class="btn btn-block btn-outline mt-4"
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


export default {
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
            }
        };
    },
    components: {
        TableSkeleton,
        Cart,
        'error': Error
    },
    mounted() {
        this.cart = this.$store.state.cart;

        if ((this.cart?.total ?? 0) === 0) {
            this.$router.push({name: 'home'});
        }

        this.fetchUser()
            .then(() => {
                this.form.surname = this.$store.state.user?.last_order?.surname ?? null;
                this.form.name = this.$store.state.user?.last_order?.name ?? null;
                this.form.patronymic = this.$store.state.user?.last_order?.patronymic ?? null;
                this.form.phone = this.$store.state.user?.last_order?.phone ?? null;
                this.form.city = this.$store.state.user?.last_order?.city_name ?? null;
                this.form.cityId = this.$store.state.user?.last_order?.city_id ?? null;
                this.form.warehouse = this.$store.state.user?.last_order?.warehouse_name ?? null;
                this.form.warehouseId = this.$store.state.user?.last_order?.warehouse_id ?? null;
                this.form.paymentType = this.$store.state.user?.last_order?.payment_type ?? null;
                this.deliveryType = this.$store.state.user?.last_order?.delivery_type ?? null;

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
                axios
                    .get('/api/delivery/' + this.deliveryType + '/cities/' + this.form.cityId + '/warehouses')
                    .then((response) => {
                        this.warehouseVariants = response.data.data
                        this.filteredWarehouses = response.data.data

                    })
            }, 500)
        },

        selectWarehouse(item) {
            this.form.warehouseId = item.id;
            this.form.warehouse = item.name;
            this.isWarehouseFocused = false;
        },
        fetchCities() {
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
                    setTimeout(() => {
                        this.$router.push({name: 'home'});
                    }, 2000)
                })
                .catch((e) => {
                    this.errors = e.response.data.errors;
                    this.isDisabled = false;
                })

        }
    }
}
</script>
