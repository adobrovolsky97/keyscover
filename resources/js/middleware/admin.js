export default function auth({next, store}) {
    if (!localStorage.getItem('user') || !localStorage.getItem('token')) {
        store.commit('clearUser')
        return next({name: 'login'})
    }

    let user = JSON.parse(localStorage.getItem('user'));

    if (user.role !== 'admin') {
        return next({name: '404'})
    }
    next();
}
