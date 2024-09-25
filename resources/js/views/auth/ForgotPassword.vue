<template>
    <div class="flex flex-col items-center justify-center gap-3 register-controller relative h-full w-full" ref="form">
        <div class="flex-cell self-center pb-2">
            <a v-if="!isCodeSent && !isCodeValidated" class="link lg:absolute left-0 lg:top-2.5 text-md" @click="goBack">< Назад</a>
            <h3 v-if="!isCodeSent && !isCodeValidated" class="title text-4xl font-bold">Скинути пароль</h3>
            <h3 v-if="isCodeSent && !isCodeValidated" class="title text-4xl font-bold">Скидання пароля</h3>
            <h3 v-if="isCodeSent && isCodeValidated" class="title text-4xl font-bold">Новий пароль</h3>
        </div>

        <div v-if="!isCodeSent" class="flex flex-col w-full m-auto p-4">
            <label class="form-control w-full">
                <div class="label">
                    <span class="label-text">Email</span>
                </div>
                <input type="text" required v-model="email" placeholder="Email" class="input input-bordered w-full"/>
                <div class="label" v-if="isError">
                    <span class="label-text text-error">Користувача не знайдено</span>
                </div>
            </label>

            <button class="btn w-full btn-outline mt-8" :disabled="isLoading" :class="{'btn-disabled': isLoading}"
                    @click="resetPassword">
                Надіслати код
            </button>
        </div>

        <div v-if="isCodeSent && !isCodeValidated" class="flex flex-col w-full m-auto p-4">
            <label class="form-control w-full">
                <div class="label">
                    <span class="label-text">Введіть код з листа вказаного e-mail</span>
                </div>
                <input type="text" required v-model="code" placeholder="123456" class="input input-bordered w-full"/>
                <div class="label" v-if="isError">
                    <span class="label-text text-error">Не вірний код</span>
                </div>
            </label>

            <button class="btn w-full btn-outline mt-8" :disabled="isLoading" :class="{'btn-disabled': isLoading}"
                    @click="validateCode">
                Скинути
                пароль
            </button>
        </div>

        <div v-if="isCodeSent && isCodeValidated" class="flex flex-col w-full m-auto p-4">
            <label class="form-control w-full">
                <div class="label">
                    <span class="label-text">Пароль</span>
                </div>
                <input type="password" required v-model="password" placeholder="Пароль" class="input input-bordered w-full"/>
            </label>

            <label class="form-control w-full">
                <div class="label">
                    <span class="label-text">Повторіть пароль</span>
                </div>
                <input type="password" required v-model="password_confirmation" placeholder="Повторіть пароль" class="input input-bordered w-full"/>
                <div class="label" v-if="isError">
                    <span class="label-text text-error">Паролі не збігаютьс</span>
                </div>
            </label>

            <button class="btn w-full btn-outline mt-8" :disabled="isLoading" :class="{'btn-disabled': isLoading}"
                    @click="setNewPassword">
                Встановити пароль
            </button>
        </div>
    </div>
</template>
<script>

import User from "../../api/User/User.js";
import Auth from "../../api/Auth/Auth.js";
import {CLIENT_SECRET, CLIENT_ID} from "../../env.js";
import {useLoading} from 'vue3-loading-overlay';
import 'vue3-loading-overlay/dist/vue3-loading-overlay.css';
import {googleTokenLogin} from "vue3-google-login";
import {toast} from "vue3-toastify";

export default {
    data() {
        return {
            isCodeSent: false,
            isCodeValidated: false,
            isLoading: false,
            isError: false,
            email: null,
            password: null,
            password_confirmation: null,
            code: null,
            loader: useLoading()
        }
    },
    methods: {
        goBack() {
            this.$router.go(-1);
        },
        /**
         * Login
         */
        resetPassword() {
            this.isLoading = true;
            // this.loader.show({container: this.$refs.form, loader: 'bars'});
            this.isError = false;
            axios.post('/api/auth/reset-password', {email: this.email})
                .then((response) => {
                    this.isCodeSent = true;
                    toast.info('Код відправлено на ваш email');
                })
                .catch(err => {
                    this.isError = true;
                    // this.loader.hide();
                })
                .finally(() => {
                    this.isLoading = false;
                    // this.loader.hide();
                });

        },

        validateCode() {
            this.isLoading = true;
            this.isError = false;
            axios.post('/api/auth/validate-code', {email: this.email, code: this.code})
                .then((response) => {
                    this.isCodeValidated = true;
                    toast.info('Код підтверджено, встановіть новий пароль');
                })
                .catch(err => {
                    this.isError = true;
                    // this.loader.hide();
                })
                .finally(() => {
                    this.isLoading = false;
                    // this.loader.hide();
                });

        },

        setNewPassword() {
            this.isLoading = true;
            this.isError = false;
            axios.post('/api/auth/set-new-password', {
                email: this.email,
                code: this.code,
                password: this.password,
                password_confirmation: this.password_confirmation
            })
                .then((response) => {
                    this.$router.push({name: 'login'});
                })
                .catch(err => {
                    this.isError = true;
                    // this.loader.hide();
                })
                .finally(() => {
                    this.isLoading = false;
                    // this.loader.hide();
                });

        }
    }
}
</script>
