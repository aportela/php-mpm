"use strict";

/**
 * global object for events between vuejs components
 */
const bus = new Vue();

/**
 * vue-router route definitions
 */
const routes = [
    { path: '/auth', name: 'auth', component: TheAuth },
    { path: '/app', name: 'theAppLayout', component: TheAppLayout },

];

/**
 * main vue-router component inicialization
 */
const router = new VueRouter({
    routes
});

/**
 * main app component
 */
const app = new Vue({
    router,
    data: function () {
        return ({
            loading: false,
        });
    },
    created: function () {
        if (! initialState.logged) {
            this.$router.push({ name: 'auth' });
        } else {
            this.$router.push({ name: 'theAppLayout' });
        }
    }
}).$mount('#app');

