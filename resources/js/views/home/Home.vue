<template>
    <div class="content flex flex-row justify-between items-start gap-4 p-1">
        <div class="filters hidden lg:block">
            <ul class="menu rounded-box w-72 border shadow-xl">
                <p class="px-4 py-2 text-lg font-bold">Категорії</p>
                <category-item
                    :selected-categories="categoriesArray"
                    v-on:category-clicked="handleCategoryClicked"
                    v-for="category in categories"
                    :key="category.id"
                    :category="category"
                ></category-item>
            </ul>
        </div>
        <div class="products-data bg-base-100 w-full">
            <ContentSkeleton v-if="!isDataLoaded"/>
            <div class="mobile lg:hidden flex flex-row justify-between items-center mb-4">
                <div class="drawer z-[1]">
                    <input id="my-drawer" type="checkbox" class="drawer-toggle"/>
                    <div class="drawer-content">
                        <label for="my-drawer" class="btn btn-neutral drawer-button w-64">Категорії
                            <svg class="h-6 w-6" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                 stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z"/>
                                <path d="M5.5 5h13a1 1 0 0 1 0.5 1.5L14 12L14 19L10 16L10 12L5 6.5a1 1 0 0 1 0.5 -1.5"/>
                            </svg>
                        </label>
                    </div>
                    <div class="drawer-side">
                        <label for="my-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
                        <div class="menu bg-base-100 text-base-content border shadow-xl min-h-full w-80 p-2">
                            <p class="px-4 py-2 text-lg font-bold">Категорії</p>
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
                <button class="btn btn-outline w-20" @click="toggleMode">
                    <svg class="h-8 w-8" v-if="mode === '1-row'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    <svg v-else class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>

                </button>
            </div>
            <label class="input input-bordered flex items-center gap-2 mb-6">
                <input @input="delaySearch" v-model="search" type="text" class="grow" placeholder="Пошук"/>
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 16 16"
                    fill="currentColor"
                    class="h-4 w-4 opacity-70">
                    <path
                        fill-rule="evenodd"
                        d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"
                        clip-rule="evenodd"/>
                </svg>
                <svg v-if="search !== ''" @click="clearSearch" class="h-5 w-5 cursor-pointer" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                     stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z"/>
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </label>
            <div class="products lg:grid-cols-4 grid gap-3" :class="classes" v-if="isDataLoaded && data.data.length">
                <Item v-for="product in data.data" :product="product" :key="product.id"/>
            </div>
            <div v-if="isDataLoaded && !data.data.length">
                <p class="text-center text-lg font-bold">Товари не знайдені</p>
            </div>
            <div class="mt-4 w-full border shadow-xl rounded-2xl p-2 flex flex-row justify-between items-center"
                 v-if="isDataLoaded && data?.meta?.last_page > 1">
                <Pagination
                    :limit="5"
                    :data="data"
                    @pagination-change-page="updatePage"
                />

                <div class="flex flex-row justify-between items-center">
                    <span>Показувати:</span>
                    <select v-model="filters.per_page" class="select select-sm">
                        <option value="20">20 записів</option>
                        <option value="50">50 записів</option>
                        <option value="100">100 записів</option>
                    </select>
                </div>
            </div>
        </div>
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
            filters: {
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
    mounted() {
        this.fetchCategories();
        this.resolveQueryParams();
        this.search = this.filters.search;
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
        fetchData() {
            RouteHelper.updateQueryParams(this.queryParams);
            this.isDataLoaded = false;
            axios.get('/api/products', {params: this.filters})
                .then((response) => {
                    this.data = response.data;
                })
                .finally(() => {
                    this.isDataLoaded = true;
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
                categoriesArray.push(category.slug);
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
        }
    }
};
</script>
