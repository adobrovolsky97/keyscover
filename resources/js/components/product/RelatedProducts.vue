<template>
    <div class="py-4 px-2 border rounded-lg w-full">
        <p class="font-bold text-lg">{{ title }}</p>
        <div ref="searchContainer">
            <label class="form-control w-full">
                <div class="label">
                    <span class="label-text">Пошук товара за назвою чи артикулом</span>
                </div>
                <input type="text" @focusin="isShowSearchContainer=true" @input="searchProducts"
                       v-model="search" placeholder="Пошук"
                       class="input input-bordered w-full"/>
            </label>
            <div class="results" v-if="isShowSearchContainer">
                <ul class="bg-base-50 max-h-52 overflow-y-scroll p-4 border rounded-l">
                    <li @click="toggleProduct(product)" v-if="productsData.length" v-for="product in productsData"
                        :key="product.id" class="hover:bg-base-300 cursor-pointer p-1"
                        :class="{'bg-base-300' : relatedProductIds?.includes(product.id)}">
                        <a>{{ product.sku }} / {{ product.name }}</a>
                    </li>
                    <li v-else>
                        <a>Товарів не знайдено</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>


    <div v-if="localRelatedProducts" class="overflow-x-auto w-full border rounded-lg mt-4">
        <table class="table">
            <!-- head -->
            <thead>
            <tr>
                <th></th>
                <th>SKU</th>
                <th>Назва</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="product in localRelatedProducts" :key="product.id">
                <th>{{ product.id }}</th>
                <td>{{ product.sku }}</td>
                <td>
                    <router-link
                        :to="{ name: 'product.show', params: { id: product.id } }"
                        class="font-bold product-name hover:text-gray-400 cursor-pointer overflow-x-auto"
                    >
                        {{ product.name }}
                    </router-link>
                </td>
                <td>
                    <svg class="h-6 w-6 cursor-pointer text-red-500"
                         @click="removeRelatedProduct(product.id)" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"/>
                        <path
                            d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        <line x1="10" y1="11" x2="10" y2="17"/>
                        <line x1="14" y1="11" x2="14" y2="17"/>
                    </svg>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
export default {
    props: {
        id: {
            type: Number,
            required: true
        },
        title: {
            type: String,
            default: 'Додаткові товари'
        },
        modelValue: {
            type: Array,
            default: () => []
        },
    },
    computed: {
        relatedProductIds() {
            return this.localRelatedProducts?.map(product => product.id) ?? [];
        }
    },
    mounted() {
        document.addEventListener('click', this.handleClickOutside);
    },
    beforeUnmount() {
        document.removeEventListener('click', this.handleClickOutside);
    },
    data() {
        return {
            isShowSearchContainer: false,
            localRelatedProducts: this.modelValue,
            timer: null,
            search: null,
            productsData: [],
        }
    },
    watch: {
        localRelatedProducts: {
            handler() {
                this.$emit('update:modelValue', this.localRelatedProducts);
            },
            deep: true
        }
    },
    methods: {
        removeRelatedProduct(id) {
            this.localRelatedProducts = this.localRelatedProducts.filter(product => product.id !== id);
        },
        toggleProduct(product) {
            if (!this.localRelatedProducts) {
                this.localRelatedProducts = [];
            }

            // I want to push whole product object
            if (this.localRelatedProducts.some(productModel => productModel.id === product.id)) {
                this.localRelatedProducts = this.localRelatedProducts.filter(productModel => productModel.id !== product.id);
            } else {
                this.localRelatedProducts.push(product);
            }
        },
        handleClickOutside(event) {
            const searchContainer = this.$refs.searchContainer;
            if (searchContainer && !searchContainer.contains(event.target)) {
                this.isShowSearchContainer = false;
            }
        },
        searchProducts() {
            clearTimeout(this.timer);

            if (!this.search) {
                this.productsData = [];
                return;
            }

            this.timer = setTimeout(() => {
                axios.get('/api/products', {params: {search: this.search, exclude: [this.id]}})
                    .then((response) => {
                        this.productsData = response.data.data;
                        this.isShowSearchContainer = true;
                    })
            }, 800);
        },
    }
}
</script>
