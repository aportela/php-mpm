const TheUserUpdateForm = (function () {
    "use strict";

    var template = function () {
        return `
            <div class="card">
                <div class="card-content is-clearfix">
                    <h1 class="title is-1 has-text-centered" v-if="isNew">Add new user</h1>
                    <h1 class="title is-1 has-text-centered" v-else>Update existing user</h1>
                    <form v-on:submit.prevent="save();">
                        <div class="field">
                            <label class="label">Full user name/surname</label>
                            <div class="control has-icons-left">
                                <input class="input" type="text" placeholder="Type complete user name" pattern=".{3,254}" title="format: 3 to 254 characters" required v-bind:disabled="loading" v-model.trim="user.name">
                                <span class="icon is-small is-left">
                                    <i class="fas fa-user"></i>
                                </span>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">Email</label>
                            <div class="control has-icons-left">
                            <input class="input" type="email" placeholder="User email (will be used for sign in)" pattern=".{3,254}" title="format: valid email with 3 to 254 characters" required v-bind:disabled="loading" v-model.trim="user.email">
                                <span class="icon is-small is-left">
                                    <i class="fas fa-envelope"></i>
                                </span>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">Password</label>
                            <div class="control has-icons-left">
                                <input class="input" type="password" placeholder="Type user password if you want to change" v-bind:disabled="loading" v-model.trim="user.password">
                                <span class="icon is-small is-left">
                                    <i class="fas fa-key"></i>
                                </span>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">Confirm password</label>
                            <div class="control has-icons-left">
                                <input class="input" type="password" placeholder="Type again the user password" v-bind:disabled="loading">
                                <span class="icon is-small is-left">
                                    <i class="fas fa-key"></i>
                                </span>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">Account type</label>
                            <div class="control">
                                <div class="select">
                                    <select v-model="user.accountType" v-bind:disabled="loading">
                                        <option value="U" selected>Normal user</option>
                                        <option value="A" >Administrator</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="field is-grouped is-pulled-right">
                            <div class="control">
                                <button class="button is-link" type="submit" v-bind:disabled="! allowSave">
                                    <span class="icon"><i class="fa fa-check-circle"></i></span>
                                    <label>Save changes</label>
                                </button>
                            </div>
                            <div class="control">
                                <button class="button is-default" type="button" v-on:click.prevent="$router.go(-1);">
                                    <span class="icon"><i class="fa fa-ban"></i></span>
                                    <label>Cancel</label>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        `;
    };

    var module = Vue.component('the-user-update-form', {
        template: template(),
        data: function () {
            return ({
                loading: false,
                isNew: true,
                user: {
                    id: null,
                    name: null,
                    email: null,
                    accountType: "U",
                }
            });
        },
        created: function () {
            if (this.$route.params.id) {
                this.isNew = false;
                this.get(this.$route.params.id);
            } else {
                this.isNew = true;
                this.user.id = phpMPM.util.uuid();
            }
        },
        computed: {
            allowSave: function () {
                return (this.user && this.user.id && this.user.name && this.user.email && this.user.accountType);
            }
        },
        methods: {
            get: function (id) {
                var self = this;
                self.loading = true;
                phpMPMApi.user.get(id, function (response) {
                    self.loading = false;
                    if (response.ok) {
                        self.user = response.body.user;
                    } else {
                        // TODO
                    }
                });
            },
            save: function() {
                if (this.isNew) {
                    this.add();
                } else {
                    this.update();
                }
            },
            add: function () {
                var self = this;
                self.loading = true;
                phpMPMApi.user.add(self.user, function (response) {
                    self.loading = false;
                    if (response.ok) {
                    } else {
                        // TODO
                    }
                });
            },
            update: function () {
                var self = this;
                self.loading = true;
                phpMPMApi.user.update(self.user, function (response) {
                    self.loading = false;
                    if (response.ok) {
                    } else {
                        // TODO
                    }
                });
            }
        }
    });

    return (module);
})();