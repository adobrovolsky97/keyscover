import './bootstrap';
import '../css/app.css';

import {createApp} from 'vue';
import router from "./router.js";
import store from './store'

import axios from "axios";

axios.defaults.headers['Content-Type'] = 'application/json';
axios.defaults.headers['Accept'] = 'application/json';

axios.interceptors.request.use(config => {
    const token = localStorage.getItem("token");
    if (token) {
        config.headers["Authorization"] = `Bearer ${token}`;
    }
    return config;
});

axios.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        if (error.response.status == 401) {
            store.commit('clearUser')
            router.push({name: 'login'});
        }
        throw error;
    }
);

import App from './views/App.vue';

const app = createApp(App);
app.use(router);
app.use(store);

app.mount('#app');
