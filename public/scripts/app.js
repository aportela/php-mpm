"use strict";

/**
 * global object for events between vuejs components
 */
const bus = new Vue();

/**
 * vue-router route definitions
 */
const routes = [
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
    }
}).$mount('#app');

