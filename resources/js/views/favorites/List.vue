<template>
    <div class="flex flex-col gap-2">
        <h3 class="text-center font-bold text-2xl">Улюблені товари</h3>

        <div class="content flex flex-row justify-between items-start gap-4 p-1">
            <div class="products-data relative bg-base-100 w-full">
                <ContentSkeleton v-if="!isDataLoaded"/>
                <div class="products lg:grid-cols-4 grid gap-3" :class="classes"
                     v-if="isDataLoaded && data.data.length">
                    <Item @favorite-removed="handleFavoriteRemoved" :id="product.id" v-for="product in data.data"
                          :product="product" :key="product.id"/>
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
                <button
                    v-show="showScrollToTop"
                    @click="scrollToTop"
                    class="fixed bottom-0 right-3 z-[999] bg-gray-400 text-black opacity-50 p-3 rounded-full shadow-lg hover:bg-neutral-focus transition-opacity duration-300"
                    aria-label="Scroll to top"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                         class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                    </svg>
                </button>
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
import {useHead} from "@vueuse/head";

export default {
    setup() {
        // This is where you set the page title and meta tags
        useHead({
            title: 'Улюблені товари',
            meta: [
                {
                    name: 'description',
                    content: 'Оптовий продаж автомобільних ключів та все що з ними повязано. Співпраця виключно з майстрами!'
                },
            ]
        });
    },
    name: "List",
    data() {
        return {
            isMoveCategoriesToTop: false,
            data: {},
            mode: '1-row',
            isDataLoaded: false,
            isInitialLoad: true,
            isScrollToTop: false,
            showScrollToTop: false,
            fetchDataTimer: null,
            filters: {
                page: 1,
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
                this.fetchData(false);
            },
            deep: true
        },
    },
    created() {
        this.resolveQueryParams();
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
        handleFavoriteRemoved() {
            this.fetchData();
        },
        updatePage(page) {
            this.isScrollToTop = true;
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

            if (currentScrollY > 200) {
                this.isMoveCategoriesToTop = true;
            } else {
                this.isMoveCategoriesToTop = false;
            }
        },
        fetchData(isImmediateRequest = true) {

            if (this.fetchDataTimer) {
                clearTimeout(this.fetchDataTimer);
            }

            const timeout = isImmediateRequest ? 1 : 500;

            this.fetchDataTimer = setTimeout(() => {

                RouteHelper.updateQueryParams(this.queryParams);
                this.isDataLoaded = false;
                return axios.get('/api/favorites', {params: this.filters})
                    .then((response) => {
                        this.data = response.data;
                    })
                    .then(() => {
                        this.isDataLoaded = true;

                        if (!this.isInitialLoad && this.isScrollToTop) {
                            this.isScrollToTop = false;
                            window.scrollTo({top: 0, behavior: 'instant'});
                        } else {
                            const savedState = localStorage.getItem('favoriteListState');
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
                            localStorage.removeItem('favoriteListState');
                        }
                    })
                    .finally(() => {
                        this.isInitialLoad = false;
                    });
            }, timeout);
        },
        resolveQueryParams() {
            this.filters = {...this.filters, ...this.route.query};
        },
    }
};
</script>
