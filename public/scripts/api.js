"use strict";

/**
 * common object for interact with API
 * all methods return callback with vue-resource response object
 */
const phpMPMApi = {
    user: {
        /**
         * sign in
         */
        signIn: function (email, password, callback) {
            var params = {
                user: {
                    id: null,
                    email: email,
                    password: password
                }
            };
            Vue.http.post("api/user/signin", params).then(
                response => {
                    callback(response);
                },
                response => {
                    callback(response);
                }
            );
        },
        /**
         * sign out
         */
        signOut: function (callback) {
            Vue.http.get("api/user/signout").then(
                response => {
                    callback(response);
                },
                response => {
                    callback(response);
                }
            );
        },
        /**
         * get user data
         */
        get: function(id, callback) {
            Vue.http.get("api/users/" + id).then(
                response => {
                    callback(response);
                },
                response => {
                    callback(response);
                }
            );
        },
        /**
         * save new user
         */
        add: function(user, callback) {
            Vue.http.post("api/users/" + user.id, user).then(
                response => {
                    callback(response);
                },
                response => {
                    callback(response);
                }
            );
        },
        /**
         * save existing user
         */
        update: function(user, callback) {
            Vue.http.put("api/users/" + user.id, user).then(
                response => {
                    callback(response);
                },
                response => {
                    callback(response);
                }
            );
        },
        /**
         * search (list) users
         */
        search: function (accountType, name, email, actualPage, resultsPage, sortBy, sortOrder, callback) {
            var params = {
                accountType: accountType,
                name: name,
                email: email,
                actualPage: actualPage,
                resultsPage: resultsPage,
                sortBy: sortBy,
                sortOrder: sortOrder
            };
            Vue.http.post("api/users/", params).then(
                response => {
                    callback(response);
                },
                response => {
                    callback(response);
                }
            );

        }
    }
};