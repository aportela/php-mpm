const TheUserForm = (function () {
    "use strict";

    var template = function () {
        return `
            <div class="card">
                <div class="card-content is-clearfix">
                    <h1 class="title is-1 has-text-centered" v-if="isNew">Add new user</h1>
                    <h1 class="title is-1 has-text-centered" v-else>Update existing user</h1>
                    <form v-on:submit.prevent="save();">
                        <div class="field">
                            <label for="name" class="label">Full user name/surname</label>
                            <div class="control has-icons-left">
                                <input class="input" name="name" type="text" placeholder="Type complete user name" pattern=".{3,254}" title="format: 3 to 254 characters" required required v-bind:class="{ 'is-danger': validator.hasInvalidField('name') }" v-bind:disabled="loading" v-model.trim="user.name">
                                <span class="icon is-small is-left">
                                    <i class="fas fa-user"></i>
                                </span>
                            </div>
                            <p class="help is-danger" v-if="validator.hasInvalidField('name')">{{ validator.getInvalidFieldMessage('name') }}</p>
                        </div>
                        <div class="field">
                            <label for="email" class="label">Email</label>
                            <div class="control has-icons-left">
                                <input type="email" name="email" class="input" placeholder="User email (will be used for sign in)" pattern=".{3,254}" title="format: valid email with 3 to 254 characters" required v-bind:class="{ 'is-danger': validator.hasInvalidField('email') }" v-bind:disabled="loading" v-model.trim="user.email">
                                <span class="icon is-small is-left">
                                    <i class="fas fa-envelope"></i>
                                </span>
                            </div>
                            <p class="help is-danger" v-if="validator.hasInvalidField('email')">{{ validator.getInvalidFieldMessage('email') }}</p>
                        </div>
                        <div class="field">
                            <label for="password" class="label">Password</label>
                            <div class="control has-icons-left">
                                <input type="password" name="password" class="input"  placeholder="Type user password if you want to change" v-bind:disabled="loading" v-bind:class="{ 'is-danger': validator.hasInvalidField('password') }" v-model.trim="user.password">
                                <span class="icon is-small is-left">
                                    <i class="fas fa-key"></i>
                                </span>
                            </div>
                            <p class="help is-danger" v-if="validator.hasInvalidField('password')">{{ validator.getInvalidFieldMessage('password') }}</p>
                        </div>
                        <div class="field">
                            <label for="confirmedPassword" class="label">Confirm password</label>
                            <div class="control has-icons-left">
                                <input type="password" name="confirmedPassword" class="input" placeholder="Type again the user password" v-bind:disabled="loading" v-bind:class="{ 'is-danger': validator.hasInvalidField('confirmedPassword') }" v-model.trim="confirmedPassword">
                                <span class="icon is-small is-left">
                                    <i class="fas fa-key"></i>
                                </span>
                            </div>
                            <p class="help is-danger" v-if="validator.hasInvalidField('confirmedPassword')">{{ validator.getInvalidFieldMessage('confirmedPassword') }}</p>
                        </div>
                        <div class="field">
                            <label for="accountType" class="label">Account type</label>
                            <div class="control">
                                <div class="select" name="accountType">
                                    <select v-bind:disabled="loading" v-model="user.accountType">
                                        <option value="U" selected>Normal user</option>
                                        <option value="A" >Administrator</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="field is-grouped is-pulled-right">
                            <div class="control">
                                <button class="button is-link" type="submit" v-bind:disabled="isSaveDisabled">
                                    <span class="icon"><i class="fa fa-check-circle"></i></span>
                                    <label>Save changes</label>
                                </button>
                            </div>
                            <div class="control">
                                <button class="button is-default" type="button" v-bind:disabled="isCancelDisabled" v-on:click.prevent="$router.go(-1);">
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

    var module = Vue.component('the-user-form', {
        template: template(),
        data: function () {
            return ({
                validator: validator,
                loading: false,
                isNew: true,
                confirmedPassword: null,
                user: {
                    id: null,
                    name: null,
                    email: null,
                    password: null,
                    accountType: "U"
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
            isSaveDisabled: function () {
                if (this.isNew) {
                    return (!(this.user && this.user.id && this.user.name && this.user.email && this.user.password && this.user.accountType) || this.loading);
                } else {
                    return (!(this.user && this.user.id && this.user.name && this.user.email && this.user.accountType) || this.loading);
                }
            },
            isCancelDisabled: function () {
                return (this.loading);
            },
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
                        self.$router.push({ name: 'the500' });
                    }
                });
            },
            validate: function () {
                this.validator.clear();
                if (!this.user.name) {
                    this.validator.setInvalid("name", "Invalid name");
                }
                if (!this.user.email) {
                    this.validator.setInvalid("email", "Invalid email");
                }
                if (this.isNew) {
                    if (!this.user.password) {
                        this.validator.setInvalid("password", "Invalid password");
                    }
                }
                if (this.user.password && this.user.password != this.confirmedPassword) {
                    this.validator.setInvalid("confirmedPassword", "Passwords do not match");
                }
                return (! this.validator.hasInvalidFields());
            },
            save: function () {
                if (this.validate()) {
                    if (this.isNew) {
                        this.add();
                    } else {
                        this.update();
                    }
                }
            },
            add: function () {
                var self = this;
                self.loading = true;
                phpMPMApi.user.add(self.user, function (response) {
                    self.loading = false;
                    if (response.ok) {
                    } else {
                        self.$router.push({ name: 'the500' });
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
                        self.$router.push({ name: 'the500' });
                    }
                });
            }
        }
    });

    return (module);
})();