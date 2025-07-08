import './bootstrap';
import '../css/app.css';

import {createApp} from 'vue';
import router from "./router.js";
import store from './store'

import axios from "axios";
import VueGtag from 'vue-gtag-next';

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

const emitter = mitt()

import App from './views/App.vue';
import mitt from "mitt";
import {createHead} from "@vueuse/head";

const head = createHead()

const app = createApp(App);
app.use(router);
app.use(store);
app.use(head)

app.use(VueGtag, {
    property: {
        id: 'G-HXZKCWZ3Y4',
    }
}, router);

app.config.globalProperties.emitter = emitter

app.mount('#app');

setTimeout(() => {
    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", "sb4da4yw92");
}, 1000); // або більше
