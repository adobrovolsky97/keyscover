<template>
    <div class="flex flex-col items-center justify-center gap-3 register-controller relative h-full w-full">
        <div class="flex-cell self-center pb-2">
            <h3 class="title text-4xl font-bold">Реєстрація</h3>
        </div>
        <div class="flex flex-col w-full m-auto p-4">
            <label class="form-control w-full">
                <div class="label">
                    <span class="label-text">Ім'я</span>
                </div>
                <input type="text" v-model="form.name" placeholder="Ім'я"
                       class="input input-bordered w-full"/>
                <span class="text-red-600 text-xs" v-if="errors?.name">{{ errors.name[0] }}</span>
            </label>

            <label class="form-control w-full">
                <div class="label">
                    <span class="label-text">Email</span>
                </div>
                <input type="text" name="eml" v-model="form.email" placeholder="Email" autocomplete="off" class="input input-bordered w-full"/>
                <span class="text-red-600 text-xs" v-if="errors?.email">{{ errors.email[0] }}</span>
            </label>

            <label class="form-control w-full">
                <div class="label">
                    <span class="label-text">Пароль</span>
                </div>
                <input type="password" name="pwd" autocomplete="off" v-model="form.password" placeholder="Пароль"
                       class="input input-bordered w-full"/>
                <span class="text-red-600 text-xs" v-if="errors?.password">{{ errors.password[0] }}</span>
            </label>
            <label class="form-control w-full">
                <div class="label">
                    <span class="label-text">Повтор пароля</span>
                </div>
                <input type="password" v-model="form.password_confirmation" placeholder="Повтор пароля"
                       class="input input-bordered w-full"/>
                <span class="text-red-600 text-xs"
                      v-if="errors?.password_confirmation">{{ errors.password_confirmation[0] }}</span>
            </label>
            <button class="btn w-full btn-outline mt-8" @click="register">Зареєструватись</button>
        </div>
        <div>
            Вже зареєстровані?
            <router-link to="/login" class="link">
                Увійдіть
            </router-link>
        </div>
    </div>
</template>
<script>
import {googleTokenLogin} from "vue3-google-login";
import Auth from "../../api/Auth/Auth.js";
import {CLIENT_ID, CLIENT_SECRET} from "../../env.js";
import User from "../../api/User/User.js";
import {useLoading} from "vue3-loading-overlay";

export default {
    data() {
        return {
            errors: {},
            loader: useLoading(),
            validationRules: {
                email: 'required|email',
                password: 'required|minLength:8',
            },
            auth: Auth,
            form: {
                name: null,
                email: null,
                password: null,
                password_confirmation: null,
            }
        }
    },
    methods: {
        /**
         * Register user
         */
        register() {
            this.errors = {};
            this.loader.show({container: this.$refs.form, loader: 'bars'});

            Auth.register(this.form)
                .then(() => {
                    Auth.login({
                        username: this.form.email,
                        password: this.form.password,
                        grant_type: 'password',
                        client_id: CLIENT_ID,
                        client_secret: CLIENT_SECRET,
                    })
                        .then((response) => {
                            this.$store.commit('setToken', response.data.access_token)
                        })
                        .then(() => {
                            User.getMe()
                                .then((response) => {
                                    this.$store.commit('setUser', response.data.data);
                                    this.$router.push({name: 'home'});
                                })
                                .finally(() => {
                                    this.loader.hide();
                                })
                        })
                        .catch(() => {
                            this.loader.hide();
                        })
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors
                    }
                    this.loader.hide();
                })

        }
    }
}
</script>
