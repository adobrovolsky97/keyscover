<template>
    <div v-if="isProductLoaded" class="border rounded-2xl shadow-xl p-4">
        <div class="flex flex-row justify-between items-center">
            <h3 class="font-bold text-lg mb-4">Оновлення товару</h3>
           <div>
               <button :disabled="isLoading" class="btn btn-success self-end text-white" @click="updateProduct">
                   Зберегти
               </button>
               <router-link
                   :to="{ name: 'product.show', params: { id: product.id } }"
                   class="font-bold ml-2 product-name hover:text-gray-400 cursor-pointer overflow-x-auto"
               >
                   <button class="btn btn-success btn-outline">Показати на сайті</button>
               </router-link>
           </div>
        </div>
        <div class="form">
            <div class="images">
                <label class="form-control w-full max-w-xs">
                    <div class="label">
                        <span class="label-text">Зображення</span>
                    </div>
                    <input type="file" multiple class="file-input file-input-bordered w-full max-w-xs"
                           @change="handleFileUpload"/>
                </label>
                <draggable
                    class="flex mt-4 flex-row justify-start items-center gap-4 flex-wrap mb-4"
                    v-if="product.images?.length"
                    v-model="product.images"
                    group="images"
                    item-key="id">
                    <template #item="{element, index}">
                        <div class="avatar flex flex-col justify-center items-center">
                            <button @click="handleFileRemove(element.id, index)"
                                    class="btn btn-sm btn-error absolute rounded-full top-0 right-0">x
                            </button>
                            <div class="w-64 h-64 rounded-xl">
                                <img class="w-64 h-64 object-cover object-top border rounded-xl" :src="element.url"/>
                            </div>
                        </div>
                    </template>
                </draggable>
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
                        <input type="text" disabled readonly placeholder="SKU" v-model="product.sku"
                               class="input input-bordered w-full"/>
                    </label>
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text">Залишок</span>
                        </div>
                        <input type="text" disabled readonly placeholder="Залишок" v-model="product.left_in_stock"
                               class="input input-bordered w-full"/>
                    </label>
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text">Остання синхронізація</span>
                        </div>
                        <input type="text" disabled readonly placeholder="Остання синхронізація"
                               v-model="product.last_sync_at" class="input input-bordered w-full"/>
                    </label>
                </div>

                <label class="form-control w-full">
                    <div class="label">
                        <span class="label-text">Крастність товарів у кошику</span>
                    </div>
                    <input type="number" placeholder="Крастність товарів у кошику" v-model="product.cart_increment_step" class="input input-bordered w-full"/>
                </label>

                <div class="form-control w-full">
                    <div class="label">
                        <span class="label-text">Опис</span>
                    </div>
                    <MdEditor :language="'en_EN'" v-model="product.description"/>
                </div>

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

                <hr class="mt-4 mb-4 w-full">
                <div class="w-full">
                    <RelatedProducts v-if="isProductLoaded" :id="product.id" title="Додаткові товари"
                                     v-model="product.related_products"/>
                    <span v-for="(prd, index) in product.related_products" class="text-error text-sm"
                    ><span
                        v-if="errors['related_products.' + index]">Строка {{ index + 1 }}: {{
                            errors['related_products.' + index][0]
                        }}</span></span>
                </div>

                <hr class="mt-4 mb-4 w-full">
                <div class="w-full">
                    <RelatedProducts v-if="isProductLoaded" :id="product.id" title="Схожі товари"
                                     v-model="product.similar_products"/>
                    <span v-for="(prd, index) in product.similar_products" class="text-error text-sm"
                    ><span
                        v-if="errors['similar_products.' + index]">Строка {{ index + 1 }}: {{
                            errors['similar_products.' + index][0]
                        }}</span></span>
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
import 'md-editor-v3/lib/style.css';
import {MdEditor} from "md-editor-v3";
import RelatedProducts from "../../../components/product/RelatedProducts.vue";
import {useHead} from "@vueuse/head";
import draggable from 'vuedraggable';

export default {
    setup() {
        useHead({
            title: 'Оновлення товару',
        });
    },
    components: {RelatedProducts, MdEditor, Pagination, 'draggable': draggable},
    data() {
        return {
            isLoading: false,
            isProductLoaded: false,
            products: [],
            product: {
                id: null,
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
                related_products: [],
                similar_products: [],
                cart_increment_step: 1,
            },
            errors: []
        }
    },
    mounted() {
        this.fetchProduct();
    },
    methods: {
        fetchProduct() {
            this.isProductLoaded = false;
            axios.get(`/api/products/${this.$route.params.id}`)
                .then(response => {
                    let productResponse = response.data.data;
                    this.product = {
                        id: productResponse.id,
                        name: productResponse.name,
                        sku: productResponse.sku,
                        left_in_stock: productResponse.left_in_stock,
                        last_sync_at: productResponse.last_sync_at,
                        description: productResponse.description ?? '',
                        images: productResponse.media,
                        images_to_remove: [],
                        cart_increment_step: productResponse.cart_increment_step,
                        is_stop_crm_update: productResponse.is_stop_crm_update,
                        is_hidden: productResponse.is_hidden,
                        related_products: productResponse.related_products,
                        similar_products: productResponse.similar_products,
                    }

                    this.isProductLoaded = true;
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
                    this.product.images.push({
                        id: null,
                        url: e.target.result,
                        file: files[i]
                    });
                }
                reader.readAsDataURL(files[i]);
            }
        },

        handleFileRemove(fileId, index) {
            this.product.images.splice(index, 1);

            if (fileId !== null) {
                this.product.images_to_remove.push(fileId);
            }
        },
        updateProduct() {
            this.isLoading = true;
            const formData = new FormData();

            this.product.images.forEach((image, index) => {
                formData.append(`images[${index}][id]`, image.id ?? '');
                formData.append(`images[${index}][file]`, image.file ?? '');
            });

            for (const key in this.product) {

                if (key === 'images_to_remove') {
                    this.product.images_to_remove.forEach(image => {
                        formData.append('images_to_remove[]', image);
                    });
                    continue;
                }

                if (key !== 'images' && key !== 'related_products' && key !== 'similar_products' && key !== 'id') {
                    formData.append(key, this.product[key]);
                }

                if (key === 'related_products') {
                    this.product.related_products.forEach(product => {
                        formData.append('related_products[]', product.id);
                    });
                }

                if (key === 'similar_products') {
                    this.product.similar_products.forEach(product => {
                        formData.append('similar_products[]', product.id);
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
