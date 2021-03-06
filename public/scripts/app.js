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
        path: '/500',
        name: 'the500',
        component: The500
    },
    {
        path: "*",
        redirect: {
            name: 'the404'
        }
    }
];

/**
 * vue-resource interceptor for adding (on errors) custom get data function (used in api-error component) into response
 */
Vue.http.interceptors.push((request, next) => {
    next((response) => {
        if (!response.ok) {
            if (response.status == 400 || response.status == 409) {
                // helper for find invalid fields on api response
                response.isFieldInvalid = function (fieldName) {
                    return (response.body.invalidOrMissingParams.indexOf(fieldName) > -1);
                }
            }
        }
        return (response);
    });
});

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