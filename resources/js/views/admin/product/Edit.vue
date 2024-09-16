<template>
    <div class="border rounded-2xl shadow-xl p-4">
        <div class="flex flex-row justify-between items-center">
            <h3 class="font-bold text-lg mb-4">Оновлення товару</h3>
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

export default {
    components: {Pagination},
    data() {
        return {
            isLoading: false,
            product: {
                name: '',
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
    },
    methods: {
        fetchProduct() {
            axios.get(`/api/products/${this.$route.params.id}`)
                .then(response => {
                    let productResponse = response.data.data;
                    this.product = {
                        name: productResponse.name,
                        description: productResponse.description,
                        images: [],
                        images_to_remove: [],
                        is_stop_crm_update: productResponse.is_stop_crm_update,
                        is_hidden: productResponse.is_hidden,
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
