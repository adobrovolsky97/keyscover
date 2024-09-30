<template>
    <div class="border rounded-2xl shadow-xl p-4">
        <div class="flex flex-row justify-between items-center mb-4">
            <h3 class="font-bold text-lg">Товари <span v-if="data.meta?.total">({{ data.meta.total }})</span></h3>
            <div v-if="selectedProducts.length" class="flex flex-row justify-end items-center gap-2">
                <select v-model="action" class="select select-sm select-bordered">
                    <option :value="null">Обрати дію</option>
                    <option value="delete">Видалити</option>
                    <option value="hide">Приховати</option>
                    <option value="show">Відображати</option>
                </select>
                <button @click="runMassAction" class="btn btn-sm text-white btn-success">Виконати</button>
            </div>
        </div>
        <TableSkeleton v-if="!isDataLoaded"/>
        <div class="flex md:flex-row flex-col px-1 md:px-0 justify-between gap-2">
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
            <div>
                <input id="my-drawer" type="checkbox" class="drawer-toggle"/>
                <div class="drawer-content">
                    <label for="my-drawer"
                           class="btn font-medium md:btn-md btn-sm border-gray-300 drawer-button btn-outline  w-full z-[5]">Категорії</label>
                </div>
                <div class="drawer-side z-[100]">
                    <label for="my-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
                    <div class="menu bg-base-100 text-base-content border shadow-xl min-h-full w-80 p-2">
                        <div class="flex flex-row justify-between items-center">
                            <p class="px-4 py-2 text-lg font-bold">Категорії</p>
                            <button @click="clearFilter" class="badge text-white badge-error badge-sm p-2">Скинути
                                фільтр
                            </button>
                        </div>
                        <category-item
                            :selected-categories="categoriesArray"
                            v-on:category-clicked="handleCategoryClicked"
                            v-for="category in categories"
                            :key="category.id"
                            :category="category"
                        ></category-item>
                    </div>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto" v-if="isDataLoaded && data.data.length">
            <table class="table">
                <thead>
                <tr>
                    <th>
                        <input @click="selectAll" type="checkbox" class="checkbox checkbox-primary">
                    </th>
                    <th></th>
                    <th>ID</th>
                    <th>
                        <span @click="toggleOrder('name')" class="cursor-pointer">
                            Назва <span v-if="filters.sort_by === 'name' && filters.order_dir === 'asc'">↑</span>
                             <span v-if="filters.sort_by === 'name' && filters.order_dir === 'desc'">↓</span>
                        </span>
                    </th>
                    <th>
                        <span @click="toggleOrder('sku')" class="cursor-pointer">
                            SKU <span v-if="filters.sort_by === 'sku' && filters.order_dir === 'asc'">↑</span>
                             <span v-if="filters.sort_by === 'sku' && filters.order_dir === 'desc'">↓</span>
                        </span>
                    </th>
                    <th>
                        <span @click="toggleOrder('last_sync_at')" class="cursor-pointer">
                            Синхронізовано в
                            <span v-if="filters.sort_by === 'last_sync_at' && filters.order_dir === 'asc'">↑</span>
                             <span v-if="filters.sort_by === 'last_sync_at' && filters.order_dir === 'desc'">↓</span>
                        </span>
                        <div class="tooltip tooltip-bottom z-[99999999]"
                             data-tip="Час останної синхронізації позиції з СРМ">
                            <button class="btn btn-circle btn-xs">?</button>
                        </div>
                    </th>
                    <th>
                         <span @click="toggleOrder('left_in_stock')" class="cursor-pointer">
                            Залишок <span
                             v-if="filters.sort_by === 'left_in_stock' && filters.order_dir === 'asc'">↑</span>
                             <span v-if="filters.sort_by === 'left_in_stock' && filters.order_dir === 'desc'">↓</span>
                        </span>
                        <div class="tooltip tooltip-bottom z-[99999999]"
                             data-tip="Кількість товарів синхронізованих з СРМ">
                            <button class="btn btn-circle btn-xs">?</button>
                        </div>
                    </th>
                    <th>
                        <span @click="toggleOrder('is_stop_crm_update')" class="cursor-pointer">
                            CRM синхр. припинено
                            <span
                                v-if="filters.sort_by === 'is_stop_crm_update' && filters.order_dir === 'asc'">↑</span>
                             <span
                                 v-if="filters.sort_by === 'is_stop_crm_update' && filters.order_dir === 'desc'">↓</span>
                        </span>
                        <div class="tooltip tooltip-bottom z-[99999999]"
                             data-tip="Чи припинена синхронізація товару з СРМ">
                            <button class="btn btn-circle btn-xs">?</button>
                        </div>
                    </th>
                    <th>
                         <span @click="toggleOrder('is_hidden')" class="cursor-pointer">
                            Прихований
                            <span v-if="filters.sort_by === 'is_hidden' && filters.order_dir === 'asc'">↑</span>
                             <span v-if="filters.sort_by === 'is_hidden' && filters.order_dir === 'desc'">↓</span>
                        </span>
                    </th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <!-- row 1 -->
                <tr v-for="product in data.data" :key="product.id">
                    <td>
                        <input @click="toggleCheckbox(product.id)" :checked="selectedProducts.includes(product.id)"
                               type="checkbox" class="checkbox checkbox-primary">
                    </td>
                    <td>
                        <div class="avatar">
                            <div class="mask mask-squircle h-32 w-32">
                                <img
                                    :src="product.image"
                                />
                            </div>
                        </div>
                    </td>
                    <td>{{ product.id }}</td>
                    <td>
                        <router-link
                            :to="{ name: 'product.show', params: { id: product.id } }"
                            class="font-bold product-name hover:text-gray-400 cursor-pointer overflow-x-auto"
                        >
                            {{ product.name }}
                        </router-link>
                    </td>
                    <td>{{ product.sku }}</td>
                    <td>{{ product.last_sync_at }}</td>
                    <td>{{ product.left_in_stock }}</td>
                    <td>
                        <span v-if="product.is_stop_crm_update" class="badge badge-error text-white badge-sm">Так</span>
                        <span v-else class="badge badge-success text-white badge-sm">Ні</span>
                    </td>
                    <td>
                        <span v-if="product.is_hidden" class="badge badge-error text-white badge-sm">Так</span>
                        <span v-else class="badge badge-success text-white badge-sm">Ні</span>
                    </td>
                    <td>
                        <div class="flex flex-row justify-between items-center">
                            <router-link
                                :to="{name: 'admin.products.edit', params: {id: product.id}}"
                                class="btn btn-ghost"
                            >
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>

                            </router-link>

                            <svg @click="destroyProduct(product.id)" class="h-6 w-6 text-red-500 cursor-pointer"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"/>
                                <path
                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                <line x1="10" y1="11" x2="10" y2="17"/>
                                <line x1="14" y1="11" x2="14" y2="17"/>
                            </svg>
                        </div>
                    </td>
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
import CategoryItem from "../../../components/CategoryItem.vue";

export default {
    data() {
        return {
            isDataLoaded: false,
            isExportDisabled: false,
            data: {},
            action: null,
            selectedProducts: [],
            categories: [],
            search: '',
            filters: {
                page: 1,
                per_page: 100,
                search: '',
                sort_by: null,
                order_dir: 'asc',
                categories: '',
            },
            route: useRoute()
        }
    },
    components: {
        CategoryItem,
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
        },
        categoriesArray() {
            return this.filters.categories.split(',');
        },
    },
    beforeRouteLeave() {
        this.isDataLoaded = false;
    },
    created() {
        this.fetchCategories();
        this.resolveQueryParams();

        this.search = this.filters.search;
    },
    methods: {
        toggleOrder(orderBy) {
            this.filters.order_dir = this.filters.order_dir === 'asc' ? 'desc' : 'asc';
            this.filters.sort_by = orderBy;
            this.filters.page = 1;
        },
        fetchCategories() {
            axios.get('/api/categories')
                .then((response) => {
                    this.categories = response.data.data;
                })
        },
        selectAll() {
            if (this.selectedProducts.length === this.data.data.length) {
                this.selectedProducts = [];
            } else {
                this.selectedProducts = this.data.data.map(item => item.id);
            }
        },
        toggleCheckbox(id) {
            if (this.selectedProducts.includes(id)) {
                this.selectedProducts = this.selectedProducts.filter(item => item !== id);
            } else {
                this.selectedProducts.push(id);
            }
        },
        handleCategoryClicked(category) {
            let categoriesArray = this.filters.categories.split(',');

            if (categoriesArray.includes(category.slug)) {
                categoriesArray = this.processAllChildCategories(category, categoriesArray, 'remove');
            } else {
                categoriesArray = this.processAllChildCategories(category, categoriesArray, 'add');
            }

            categoriesArray = this.updateParentCategories(this.categories, categoriesArray);

            this.filters.categories = categoriesArray.filter((category) => category !== '').join(',');
            this.filters.page = 1;
        },
        processAllChildCategories(category, categoriesArray, operation) {
            const processCategory = (category, categoriesArray, operation) => {
                if (operation === 'remove') {
                    categoriesArray = categoriesArray.filter(cat => cat !== category.slug);
                } else {
                    categoriesArray.push(category.slug);
                }

                if (category.children && category.children.length) {
                    category.children.forEach((child) => {
                        categoriesArray = processCategory(child, categoriesArray, operation);
                    });
                }

                return categoriesArray;
            };

            return processCategory(category, categoriesArray, operation);
        },
        updateParentCategories(categories, selectedCategories) {
            const processParentCategories = (categories, selectedCategories) => {
                categories.forEach(category => {
                    if (category.children && category.children.length) {
                        const allChildrenSelected = category.children.every(child => selectedCategories.includes(child.slug));

                        if (allChildrenSelected) {
                            if (!selectedCategories.includes(category.slug)) {
                                selectedCategories.push(category.slug);
                            }
                        } else {
                            selectedCategories = selectedCategories.filter(cat => cat !== category.slug);
                        }

                        selectedCategories = processParentCategories(category.children, selectedCategories);
                    }
                });

                return selectedCategories;
            };

            return processParentCategories(categories, selectedCategories);
        },
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
        clearSearch() {
            this.search = '';
            this.filters.search = '';
            this.filters.page = 1;
        },
        runMassAction() {

            if (!this.action) {
                toast.error('Оберіть дію');
                return;
            }

            axios.post('/api/products/mass-actions', {
                action: this.action,
                ids: this.selectedProducts
            })
                .then(response => {
                    this.selectedProducts = [];
                    this.action = null;
                    this.fetchProducts();
                    toast.success('Дія успішно виконана');
                })
                .catch(error => {
                    toast.error('Помилка виконання дії');
                })
        },
        clearFilter() {
            this.filters.categories = '';
            this.filters.page = 1;
        },
        destroyProduct(id) {
            if (confirm('Підтвердіть видалення')) {
                axios.delete('/api/products/' + id)
                    .then(response => {
                        this.fetchProducts();
                        toast.success('Товар успішно видалено');
                    })
                    .catch(error => {
                        toast.error('Помилка видалення товару');
                    })
            }
        },
    }
}
</script>
