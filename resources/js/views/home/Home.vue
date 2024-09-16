<template>
    <div class="content flex flex-row justify-between items-start gap-4 p-1">
        <div class="filters hidden md:block border rounded-2xl shadow-xl fixed bg-white z-50 w-72">
            <div class="flex flex-row justify-between items-center px-6 mt-4">
                <p class="text-lg font-bold">Категорії</p>
                <button @click="clearFilter" class="badge badge-error text-white badge-sm p-2">Скинути фільтр</button>
            </div>
            <ul class="menu w-72">
                <category-item
                    :selected-categories="categoriesArray"
                    v-on:category-clicked="handleCategoryClicked"
                    v-for="category in categories"
                    :key="category.id"
                    :category="category"
                ></category-item>
            </ul>
        </div>
        <div class="products-data bg-base-100 w-full ml-0 md:ml-80">
            <div class="mobile md:hidden flex flex-row px-1 md:px-0 justify-between items-center mb-4 gap-1">
                <div class="z-[1]">
                    <input id="my-drawer" type="checkbox" class="drawer-toggle"/>
                    <div class="drawer-content">
                        <label for="my-drawer"
                               class="btn btn-sm font-medium border-gray-300 drawer-button btn-outline md:text-md text-xs w-full">Категорії</label>
                    </div>
                    <div class="drawer-side">
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
                <select v-model="filters.only_available"
                        class="select select-sm text-xs md:text-md select-bordered w-full">
                    <option :value="0">Всі товари</option>
                    <option :value="1">Лише в наявності</option>
                </select>
                <div class="form-control w-64">
                    <label class="label cursor-pointer">
                        <select :value="filters.per_page" @change="changePerPage"
                                class="select text-xs md:text-md select-bordered select-sm w-full">
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </label>
                </div>
                <button class="btn btn-sm btn-outline font-medium border-gray-300 w-10" @click="toggleMode">
                    <svg class="h-6 w-6" v-if="mode === '1-row'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    <svg v-else class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </button>
            </div>
            <div class="flex flex-row md:flex-col px-1 md:px-0 justify-between gap-2">
                <label class="input input-bordered text-sm input-sm md:input-md w-full flex items-center mb-2">
                    <input @input="delaySearch" v-model="search" type="text" class="grow"
                           :placeholder="filters.categories.length > 0 ? 'Пошук по категорії' : 'Загальний пошук'"/>
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
                <div class="flex flex-row justify-center flex-wrap md:justify-end items-center mb-6 w-full gap-4">
                    <div class="form-control hidden md:block">
                        <label class="label cursor-pointer">
                            <span v-if="filters.only_available == 0" class="label-text mr-2">Показувати все</span>
                            <span v-if="filters.only_available == 1" class="label-text mr-2">Лише в наявності</span>
                            <input type="checkbox" true-value="1" false-value="0" v-model="filters.only_available"
                                   class="toggle"/>
                            <select :value="filters.per_page" @change="changePerPage"
                                    class="select select-bordered ml-3">
                                <option value="20">20 товарів</option>
                                <option value="50">50 товарів</option>
                                <option value="100">100 товарів</option>
                            </select>
                        </label>
                    </div>
                    <select v-model="filters.order_by"
                            class="select select-sm md:select-md w-full md:w-36 select-bordered">
                        <option :value="'name_asc'">За назвою</option>
                        <option :value="'id_desc'">За новизною</option>
                        <option :value="'popularity_desc'">За популярністю</option>
                        <option :value="'price_asc'">Ціна ↑</option>
                        <option :value="'price_desc'">Ціна ↓</option>
                    </select>
                </div>
            </div>

            <ContentSkeleton v-if="!isDataLoaded"/>
            <div class="products lg:grid-cols-4 grid gap-3" :class="classes" v-if="isDataLoaded && data.data.length">
                <Item :id="product.id" v-for="product in data.data" :product="product" :key="product.id"/>
            </div>
            <div v-if="isDataLoaded && !data.data.length">
                <p class="text-center text-lg font-bold">Товари не знайдені</p>
            </div>
            <div
                class="mt-4 w-full border shadow-xl rounded-2xl p-2 flex lg:flex-row flex-col gap-2 justify-between items-center"
                v-if="isDataLoaded && data?.meta?.last_page > 1">
                <Pagination
                    :limit="5"
                    :data="data"
                    @pagination-change-page="updatePage"
                />
            </div>
        </div>
        <button
            v-show="showScrollToTop"
            @click="scrollToTop"
            class="fixed bottom-5 right-4 bg-neutral text-white p-3 rounded-full shadow-lg hover:bg-neutral-focus transition-opacity duration-300"
            aria-label="Scroll to top"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                 class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
            </svg>
        </button>
    </div>
</template>

<script>
import Pagination from "../../components/pagination/Pagination.vue";
import TableSkeleton from "../../components/skeleton/TableSkeleton.vue";
import RouteHelper from "../../helpers/Route/RouteHelper.js";
import {useRoute} from "vue-router";
import Item from "../product/Item.vue";
import CategoryItem from "../../components/CategoryItem.vue";
import ContentSkeleton from "../../components/skeleton/ContentSkeleton.vue";

export default {
    name: "Home",
    data() {
        return {
            data: {},
            categories: [],
            search: '',
            mode: this.$store.state.mode,
            isDataLoaded: false,
            isInitialLoad: true,
            showScrollToTop: false,
            filters: {
                only_available: 1,
                order_by: 'id_desc',
                page: 1,
                search: '',
                categories: '',
                per_page: 20
            },
            route: useRoute()
        };
    },
    components: {
        ContentSkeleton,
        CategoryItem,
        Item,
        Pagination,
        TableSkeleton
    },
    watch: {
        'filters': {
            handler: function () {
                this.fetchData();
            },
            deep: true
        }
    },
    created() {
        this.fetchCategories();
        this.resolveQueryParams();

        this.search = this.filters.search;
    },
    mounted() {
        window.addEventListener("scroll", this.checkScrollPosition);
    },
    beforeDestroy() {
        window.removeEventListener("scroll", this.checkScrollPosition);
    },
    computed: {
        classes() {
            return {
                'grid-cols-1': this.mode === '1-row',
                'grid-cols-2': this.mode === '2-row',
            };
        },
        categoriesArray() {
            return this.filters.categories.split(',');
        },
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
    methods: {
        changePerPage(e) {
            this.filters.per_page = e.target.value;
            this.filters.page = 1;
        },
        clearSearch() {
            this.search = '';
            this.filters.search = '';
            this.filters.page = 1;
        },
        toggleMode() {
            if (this.mode === '1-row') {
                this.mode = '2-row';
            } else {
                this.mode = '1-row';
            }

            this.$store.commit('setMode', this.mode);
        },
        fetchCategories() {
            axios.get('/api/categories')
                .then((response) => {
                    this.categories = response.data.data;
                })
        },
        updatePage(page) {
            this.filters.page = page;
        },
        scrollToTop() {
            window.scrollTo({top: 0, behavior: 'smooth'});
        },
        checkScrollPosition() {
            const currentScrollY = window.scrollY;
            if (currentScrollY > 1000 && currentScrollY < this.lastScrollY) {
                this.showScrollToTop = true;
            } else {
                this.showScrollToTop = false;
            }
            this.lastScrollY = currentScrollY;
        },
        showCart() {
            this.emitter.emit('show-cart');
        },
        fetchData() {
            RouteHelper.updateQueryParams(this.queryParams);
            this.isDataLoaded = false;
            return axios.get('/api/products', {params: this.filters})
                .then((response) => {
                    this.data = response.data;
                })
                .then(() => {
                    this.isDataLoaded = true;

                    if (!this.isInitialLoad) {
                        window.scrollTo({top: 0, behavior: 'instant'});
                    } else {
                        const savedState = localStorage.getItem('productListState');
                        if (savedState) {
                            const {productId} = JSON.parse(savedState);
                            this.$nextTick(() => {
                                const element = document.getElementById(productId);
                                if (element) {
                                    element.scrollIntoView({behavior: 'instant', block: 'center'});
                                }
                            });
                        }
                        // Clear the saved state
                        localStorage.removeItem('productListState');
                    }
                })
                .finally(() => {
                    this.isInitialLoad = false;
                });
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
        clearFilter() {
            this.filters.categories = '';
            this.filters.page = 1;
        }
    }
};
</script>
