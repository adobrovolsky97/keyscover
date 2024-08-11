import {createStore} from 'vuex'
import {LIGHT_THEME} from "./env.js";

const store = createStore({
    state: {
        user: localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : null,
        token: localStorage.getItem('token'),
        theme: localStorage.getItem('theme') || LIGHT_THEME,
        cart: localStorage.getItem('cart') ? JSON.parse(localStorage.getItem('cart')) : [],
        configs: localStorage.getItem('configs') ? JSON.parse(localStorage.getItem('configs')) : {},
        mode: localStorage.getItem('mode') || '1-row',
    },
    mutations: {
        clearUser(state) {
            state.user = null;
            state.token = null;
            localStorage.removeItem('user');
            localStorage.removeItem('token');
        },
        setUser(state, user) {
            store.state.user = user;
            localStorage.setItem('user', JSON.stringify(user));
        },
        setCart(state, cart) {
            store.state.cart = cart;
            localStorage.setItem('cart', JSON.stringify(cart));
        },
        setMode(state, mode) {
            store.state.mode = mode;
            localStorage.setItem('mode', mode);
        },
        setConfigs(state, configs) {
            store.state.configs = configs;
            localStorage.setItem('configs', JSON.stringify(configs));
        },
        setToken(state, token) {
            state.token = token;
            localStorage.setItem('token', token);
        },
        setTheme(state, theme) {
            state.theme = theme;
            localStorage.setItem('theme', theme);
        }
    },
    getters: {
        isLightTheme(state) {
            return state.theme === LIGHT_THEME;
        }
    }
})

export default store;
