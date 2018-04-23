"use strict";

/**
 * create & return a pagination object
 */
const getPager = function () {
    return ({
        actualPage: 1,
        previousPage: 1,
        nextPage: 1,
        totalPages: 0,
        resultsPage: initialState.defaultResultsPage
    });
}

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
                path: 'add_user',
                name: 'theUserAddForm',
                component: TheUserUpdateForm
            },
            {
                path: 'user/:id',
                name: 'theUserUpdateForm',
                component: TheUserUpdateForm
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
    },
    {
        path: '/404',
        name: 'the404',
        component: The404
    },
    {
        path: "*",
        redirect: {
            name: 'the404'
        }
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
        if (!initialState.session.logged) {
            this.$router.push({ name: 'auth' });
        } else {
            if (!this.$route.name) {
                this.$router.push({ name: 'theDashboard' });
            }
        }
    }
}).$mount('#app');