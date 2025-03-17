<template>
    <div class="border rounded-2xl shadow-xl p-4">
        <div class="flex flex-row justify-between items-center">
            <h3 class="font-bold text-lg mb-4">Оновлення налаштування</h3>
        </div>
        <div class="form">
            <div class="flex flex-col justify-between items-start gap-2">

                <div v-if="config?.type === 'boolean'" class="form-control">
                    <label class="label cursor-pointer">
                        <span class="label-text mr-2">Увімкнено</span>
                        <input type="checkbox" class="toggle toggle-success" v-model="config.value" true-value="1"
                               false-value="0"/>
                    </label>
                </div>

                <div v-if="config?.type === 'string' && config?.key !== 'welcome_message'" class="form-control w-full">
                    <div class="label">
                        <span class="label-text">Значення</span>
                    </div>
                    <input type="text" class="input input-bordered" v-model="config.value"/>
                </div>

                <div v-if="config?.type === 'string' && config?.key === 'welcome_message'" class="form-control w-full">
                    <div class="form-control w-full">
                        <div class="label">
                            <span class="label-text">Значення</span>
                        </div>
                        <MdEditor :language="'en_EN'" v-model="config.value"/>
                    </div>
                </div>

                <span class="text-red-600 text-xs" v-if="errors?.value">{{ errors.value[0] }}</span>

                <div v-if="config?.description">
                    *<i class="text-sm"> {{ config.description }}</i>
                </div>

                <button :disabled="isLoading" class="btn btn-success self-end text-white" @click="updateConfig">
                    Зберегти
                </button>
            </div>
        </div>
    </div>
</template>
<script>
import 'md-editor-v3/lib/style.css';
import {toast} from "vue3-toastify";
import {useHead} from "@vueuse/head";
import {MdEditor} from "md-editor-v3";

export default {
    components: {MdEditor},
    setup() {
        useHead({
            title: 'Зміна налаштування',
        });
    },
    data() {
        return {
            isLoading: false,
            errors: {},
            config: {
                key: null,
                type: null,
                value: null,
                description: null
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
                        key: response.data.data.key,
                        value: response.data.data.value || '',
                        type: response.data.data.type,
                        description: response.data.data.description
                    }
                })
                .catch(error => {
                    console.log(error);
                });
        },
        updateConfig() {
            this.isLoading = true;
            this.errors = {};

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
