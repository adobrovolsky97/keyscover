<template>
    <div class="border rounded-2xl shadow-xl p-4">
        <div class="flex flex-row justify-between items-center">
            <h3 class="font-bold text-lg mb-4">Оновлення товару</h3>
            <router-link
                :to="{ name: 'product.show', params: { id: product.id } }"
                class="font-bold product-name hover:text-gray-400 cursor-pointer overflow-x-auto"
            >
                <button class="btn btn-success btn-outline">Показати на сайті</button>
            </router-link>
        </div>
        <div class="form">

            <div>
                <label class="form-control w-full max-w-xs">
                    <div class="label">
                        <span class="label-text">Зображення</span>
                    </div>
                    <input type="file" multiple class="file-input file-input-sm file-input-bordered w-full max-w-xs"
                           ref="fileInput" @change="handleFileUpload"/>
                </label>
                <div
                    class="flex flex-row justify-start items-center gap-4 flex-wrap mb-4 mt-4" v-if="previewImages">
                    <div class="avatar flex flex-col justify-center items-center"
                         v-for="(element, index) in previewImages" :key="element.id">
                        <button @click="handleFileRemove(element.id, index)"
                                class="btn btn-sm btn-error absolute rounded-full top-0 right-0">x
                        </button>
                        <div @click="setMainImage(index)" class="w-52 rounded-xl cursor-pointer"
                             :class="{'border-4 border-red-500': element.collection_name === 'main'}">
                            <img class="w-52 object-cover border rounded-xl" :src="element.url"/>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col justify-between items-start gap-2">
                <label class="form-control w-full">
                    <div class="label">
                        <span class="label-text">Назва</span>
                    </div>
                    <input type="text" placeholder="Назва" v-model="product.name" class="input input-bordered w-full"/>
                </label>

               <div class="flex flex-row justify-between items-center gap-2 w-full">
                   <label class="form-control w-full">
                       <div class="label">
                           <span class="label-text">SKU</span>
                       </div>
                       <input type="text" disabled readonly placeholder="SKU" v-model="product.sku" class="input input-bordered w-full"/>
                   </label>
                   <label class="form-control w-full">
                       <div class="label">
                           <span class="label-text">Залишок</span>
                       </div>
                       <input type="text" disabled readonly placeholder="Залишок" v-model="product.left_in_stock" class="input input-bordered w-full"/>
                   </label>
                   <label class="form-control w-full">
                       <div class="label">
                           <span class="label-text">Остання синхронізація</span>
                       </div>
                       <input type="text" disabled readonly placeholder="Остання синхронізація" v-model="product.last_sync_at" class="input input-bordered w-full"/>
                   </label>
               </div>

                <label class="form-control w-full">
                    <div class="label">
                        <span class="label-text">Опис</span>
                    </div>
                    <textarea placeholder="Опис" v-model="product.description"
                              class="textarea textarea-bordered w-full"></textarea>
                </label>

                <div class="form-control">
                    <label class="label cursor-pointer">
                        <span class="label-text mr-2">Припинити синхронізацію товару з СРМ</span>
                        <input type="checkbox" class="toggle" v-model="product.is_stop_crm_update" true-value="1"
                               false-value="0"/>
                    </label>
                </div>

                <div class="form-control">
                    <label class="label cursor-pointer">
                        <span class="label-text mr-2">Приховати товар з сайту</span>
                        <input type="checkbox" class="toggle" v-model="product.is_hidden" true-value="1"
                               false-value="0"/>
                    </label>
                </div>

                <div class="py-4 px-2 border rounded-lg w-full">
                    <p class="font-bold text-lg">Супутні товари</p>
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
                                <li @click="toggleProduct(product)" v-if="products.length" v-for="product in products"
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


                <div v-if="product.related_products" class="overflow-x-auto w-full border rounded-lg mt-4">
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
                        <tr v-for="product in product.related_products" :key="product.id">
                            <th>{{ product.id }}</th>
                            <td>{{ product.sku }}</td>
                            <td>{{ product.name }}</td>
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

                <button :disabled="isLoading" class="btn btn-success self-end text-white" @click="updateProduct">
                    Зберегти
                </button>
            </div>
        </div>
    </div>
</template>
<script>
import Pagination from "../../../components/pagination/Pagination.vue";
import {toast} from "vue3-toastify";
import * as sea from "node:sea";

export default {
    computed: {
        relatedProductIds() {
            return this.product.related_products?.map(product => product.id) ?? [];
        }
    },
    components: {Pagination},
    data() {
        return {
            isLoading: false,
            isShowSearchContainer: false,
            timer: null,
            search: null,
            products: [],
            product: {
                name: '',
                sku: '',
                left_in_stock: 0,
                last_sync_at: null,
                description: '',
                price: '',
                images: [],
                images_to_remove: [],
                is_stop_crm_update: false,
                is_hidden: false,
                main_image_index: null,
            },
            previewImages: [],
            errors: []
        }
    },
    mounted() {
        this.fetchProduct();
        document.addEventListener('click', this.handleClickOutside);
    },
    beforeUnmount() {
        document.removeEventListener('click', this.handleClickOutside);
    },
    methods: {
        handleClickOutside(event) {
            const searchContainer = this.$refs.searchContainer;
            if (searchContainer && !searchContainer.contains(event.target)) {
                this.isShowSearchContainer = false;
            }
        },
        toggleProduct(product) {
            if (!this.product.related_products) {
                this.product.related_products = [];
            }

            // I want to push whole product object
            if (this.product.related_products.some(productModel => productModel.id === product.id)) {
                this.product.related_products = this.product.related_products.filter(productModel => productModel.id !== product.id);
            } else {
                this.product.related_products.push(product);
            }
        },
        removeRelatedProduct(id) {
            this.product.related_products = this.product.related_products.filter(product => product.id !== id);
        },
        searchProducts() {
            clearTimeout(this.timer);

            if (!this.search) {
                this.products = [];
                return;
            }

            this.timer = setTimeout(() => {
                axios.get('/api/products', {params: {search: this.search}})
                    .then((response) => {
                        this.products = response.data.data;
                        this.isShowSearchContainer = true;
                    })
            }, 800);
        },
        fetchProduct() {
            axios.get(`/api/products/${this.$route.params.id}`)
                .then(response => {
                    let productResponse = response.data.data;
                    this.product = {
                        name: productResponse.name,
                        sku: productResponse.sku,
                        left_in_stock: productResponse.left_in_stock,
                        last_sync_at: productResponse.last_sync_at,
                        description: productResponse.description,
                        images: [],
                        images_to_remove: [],
                        is_stop_crm_update: productResponse.is_stop_crm_update,
                        is_hidden: productResponse.is_hidden,
                        related_products: productResponse.related_products,
                    }
                    this.previewImages = productResponse.media;

                    for (let i = 0; i < this.previewImages.length; i++) {
                        if (this.previewImages[i].collection_name === 'main') {
                            this.product.main_image_index = i;
                            break;
                        }
                    }
                })
                .catch(error => {
                    console.log(error);
                });
        },
        handleFileUpload(e) {
            const files = e.target.files;

            for (let i = 0; i < files.length; i++) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.previewImages.push({id: null, url: e.target.result});
                    this.product.images.push(files[i]);
                }
                reader.readAsDataURL(files[i]);
            }
        },
        setMainImage(index) {
            this.product.main_image_index = index;

            for (let i = 0; i < this.previewImages.length; i++) {
                this.previewImages[i].collection_name = i === index ? 'main' : 'additional';
            }
        },
        handleFileRemove(fileId, index) {
            if (fileId === null) {
                this.product.images.splice(index, 1);
            } else {
                this.product.images_to_remove.push(fileId);
            }
            this.previewImages.splice(index, 1);
        },
        updateProduct() {
            this.isLoading = true;
            const formData = new FormData();
            this.product.images.forEach(image => {
                formData.append('images[]', image);
            });

            for (const key in this.product) {

                if (key === 'images_to_remove') {
                    this.product.images_to_remove.forEach(image => {
                        formData.append('images_to_remove[]', image);
                    });
                    continue;
                }

                if (key !== 'images') {
                    formData.append(key, this.product[key]);
                }

                if(key === 'related_products') {
                    this.product.related_products.forEach(product => {
                        formData.append('related_products[]', product.id);
                    });
                }
            }

            axios.post(`/api/products/${this.$route.params.id}`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
                .then(response => {
                    toast.success('Товар успішно оновлено');
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                })
                .finally(() => {
                    this.isLoading = false;
                });
        }
    }
}
</script>
