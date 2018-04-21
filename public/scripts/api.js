"use strict";

/**
 * common object for interact with API
 * all methods return callback with vue-resource response object
 */
const phpMPMApi = {
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
    }
};