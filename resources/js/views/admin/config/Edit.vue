<template>
    <div class="border rounded-2xl shadow-xl p-4">
        <div class="flex flex-row justify-between items-center">
            <h3 class="font-bold text-lg mb-4">Оновлення налаштування</h3>
        </div>
        <div class="form">
            <div class="flex flex-col justify-between items-start gap-2">
                <div class="form-control">
                    <label class="label cursor-pointer">
                        <span class="label-text mr-2">Увімкнено</span>
                        <input type="checkbox" class="toggle toggle-success" v-model="config.value" true-value="1"
                               false-value="0"/>
                    </label>
                </div>

                <button :disabled="isLoading" class="btn btn-success self-end text-white" @click="updateConfig">
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
import {useHead} from "@vueuse/head";

export default {
    setup() {
        useHead({
            title: 'Зміна налаштування',
        });
    },
    data() {
        return {
            isLoading: false,
            config: {
                value: null,
            },
        }
    },
    mounted() {
        this.fetchConfig();
    },
    methods: {
        fetchConfig() {
            axios.get(`/api/configs/${this.$route.params.id}`)
                .then(response => {
                    this.config = {
                        value: response.data.data.value,
                    }
                })
                .catch(error => {
                    console.log(error);
                });
        },
        updateConfig() {
            this.isLoading = true;

            axios.put(`/api/configs/${this.$route.params.id}`, {value: this.config.value})
                .then(response => {
                    toast.success('Налаштування успішно оновлено');
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
