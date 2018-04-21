"use strict";

/**
 * global object for events between vuejs components
 */
const bus = new Vue();

/**
 * vue-router route definitions
 */
const routes = [
    {
        path: '/auth',
        name: 'auth',
        component: TheAuth
    },
    {
        path: '/app',
        name: 'theAppLayout',
        component: TheAppLayout,
        children: [
            {
                path: 'dashboard',
                name: 'theDashboard',
                component: TheDashboard
            },
            {
                path: 'users',
                name: 'theUserList',
                component: TheUserList
            },
            {
                path: 'groups',
                name: 'theGroupList',
                component: TheGroupList
            },
            {
                path: 'attributes',
                name: 'theAttributeList',
                component: TheAttributeList
            },
            {
                path: 'templates',
                name: 'theTemplateList',
                component: TheTemplateList
            },
            {
                path: 'search_templates',
                name: 'theSearchTemplateList',
                component: TheSearchTemplateList
            },
            {
                path: 'log',
                name: 'theLogList',
                component: TheLogList
            },
            {
                path: 'errors',
                name: 'theErrorList',
                component: TheErrorList
            }
        ]
    }

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
        if (!initialState.logged) {
            this.$router.push({ name: 'auth' });
        } else {
            this.$router.push({ name: 'theDashboard' });
        }
    }
}).$mount('#app');

