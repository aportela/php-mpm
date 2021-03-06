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
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                },
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                }
            );
        },
        /**
         * sign out
         */
        signOut: function (callback) {
            Vue.http.get("api/user/signout").then(
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                },
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                }
            );
        },
        /**
         * get user data
         */
        get: function (id, callback) {
            Vue.http.get("api/users/" + id).then(
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                },
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                }
            );
        },
        /**
         * save new user
         */
        add: function (user, callback) {
            var params = {
                user: user
            }
            Vue.http.post("api/users/" + user.id, params).then(
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                },
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                }
            );
        },
        /**
         * save existing user
         */
        update: function (user, callback) {
            var params = {
                user: user
            }
            Vue.http.put("api/users/" + user.id, params).then(
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                },
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                }
            );
        },
        /**
         * delete existing user
         */
        delete: function (id, callback) {
            Vue.http.delete("api/users/" + id).then(
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                },
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
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
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                },
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                }
            );

        }
    },
    group: {
        /**
         * get group data
         */
        get: function (id, callback) {
            Vue.http.get("api/groups/" + id).then(
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                },
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                }
            );
        },
        /**
         * save new group
         */
        add: function (group, callback) {
            var params = {
                group: group
            }
            Vue.http.post("api/groups/" + group.id, params).then(
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                },
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                }
            );
        },
        /**
         * save existing group
         */
        update: function (group, callback) {
            var params = {
                group: group
            }
            Vue.http.put("api/groups/" + group.id, params).then(
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                },
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                }
            );
        },
        /**
         * delete existing group
         */
        delete: function (id, callback) {
            Vue.http.delete("api/groups/" + id).then(
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                },
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                }
            );
        },
        /**
         * search (list) groups
         */
        search: function (name, description, actualPage, resultsPage, sortBy, sortOrder, callback) {
            var params = {
                name: name,
                description: description,
                actualPage: actualPage,
                resultsPage: resultsPage,
                sortBy: sortBy,
                sortOrder: sortOrder
            };
            Vue.http.post("api/groups/", params).then(
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                },
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                }
            );

        }
    },
    attribute: {
        /**
         * get attribute data
         */
        get: function (id, callback) {
            Vue.http.get("api/attributes/" + id).then(
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                },
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                }
            );
        },
        /**
         * get attribute definition types
         */
        getTypes: function (callback) {
            Vue.http.get("api/attribute_types").then(
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                },
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                }
            );
        },
        /**
         * save new attribute
         */
        add: function (attribute, callback) {
            var params = {
                attribute: attribute
            }
            Vue.http.post("api/attributes/" + attribute.id, params).then(
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                },
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                }
            );
        },
        /**
         * save existing attribute
         */
        update: function (attribute, callback) {
            var params = {
                attribute: attribute
            }
            Vue.http.put("api/attributes/" + attribute.id, params).then(
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                },
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                }
            );
        },
        /**
         * delete existing attribute
         */
        delete: function (id, callback) {
            Vue.http.delete("api/attributes/" + id).then(
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                },
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                }
            );
        },
        /**
         * search (list) users
         */
        search: function (typeId, name, description, actualPage, resultsPage, sortBy, sortOrder, callback) {
            var params = {
                typeId: typeId,
                name: name,
                description: description,
                actualPage: actualPage,
                resultsPage: resultsPage,
                sortBy: sortBy,
                sortOrder: sortOrder
            };
            Vue.http.post("api/attributes/", params).then(
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                },
                response => {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                }
            );

        }
    }
};