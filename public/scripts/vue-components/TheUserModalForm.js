const TheUserModalForm = (function () {
    "use strict";

    var template = function () {
        return `

            <div class="modal is-active">
                <div class="modal-background"></div>
                <div class="modal-card">
                    <form v-on:submit.prevent="save();">
                        <header class="modal-card-head">
                            <p class="modal-card-title" v-if="isAddForm">Add new user</p>
                            <p class="modal-card-title" v-if="isUpdateForm">Update existing user</p>
                            <button class="delete" aria-label="close" v-on:click.prevent="closeModal(false);"></button>
                        </header>
                        <section class="modal-card-body">
                            <div class="field">
                                <label for="name" class="label">Full user name/surname</label>
                                <div class="control has-icons-left">
                                    <input class="input" name="name" ref="name" type="text" placeholder="Type complete user name" required v-bind:class="{ 'is-danger': validator.hasInvalidField('name') }" v-bind:disabled="loading" v-model.trim="user.name">
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-user"></i>
                                    </span>
                                </div>
                                <p class="help is-danger" v-if="validator.hasInvalidField('name')">{{ validator.getInvalidFieldMessage('name') }}</p>
                            </div>
                            <div class="field">
                                <label for="email" class="label">Email</label>
                                <div class="control has-icons-left">
                                    <input type="email" name="email" class="input" placeholder="User email (will be used for sign in)" required v-bind:class="{ 'is-danger': validator.hasInvalidField('email') }" v-bind:disabled="loading" v-model.trim="user.email">
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
                                        <select v-bind:disabled="isAccountTypeDisabled" v-model="user.accountType">
                                            <option value="U" selected>Normal user</option>
                                            <option value="A" >Administrator</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <footer class="modal-card-foot">
                            <div class="field is-grouped">
                                <div class="control">
                                    <button class="button is-link" type="submit" v-bind:disabled="isSaveDisabled">
                                        <span class="icon"><i class="fa fa-check-circle"></i></span>
                                        <span>Save changes</span>
                                    </button>
                                </div>
                                <div class="control">
                                    <button class="button is-default" type="button" v-bind:disabled="isCancelDisabled" v-on:click.prevent="closeModal(false);">
                                        <span class="icon"><i class="fa fa-ban"></i></span>
                                        <span>Cancel</span>
                                    </button>
                                </div>
                            </div>
                            <div class="is-clearfix">
                            </div>
                        </footer>
                    </form>
                </div>
            </div>

        `;
    };

    var module = Vue.component('the-user-modal-form', {
        template: template(),
        data: function () {
            return ({
                validator: getValidator(),
                loading: false,
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
        props: [
            'opts'
        ],
        created: function () {
            if (this.opts.type == "add") {
                this.user.id = phpMPM.util.uuid();
                this.$nextTick(() => this.$refs.name.focus());
            } else if (this.opts.type == "update") {
                this.user.id = this.opts.userId;
                this.get(this.user.id);
            } else {
                this.$router.push({ name: 'the500' });
            }
        },
        computed: {
            isAddForm: function () {
                return (this.opts.type == "add");
            },
            isUpdateForm: function () {
                return (this.opts.type == "update");
            },
            isAccountTypeDisabled: function () {
                return (initialState.session.user.isAdmin && initialState.session.user.id == this.user.id);
            },
            isSaveDisabled: function () {
                if (this.isAddForm) {
                    return (!(this.user && this.user.id && this.user.name && this.user.email && this.user.password && this.confirmedPassword && this.user.accountType) || this.loading);
                } else {
                    return (!(this.user && this.user.id && this.user.name && this.user.email && this.user.accountType) || this.loading);
                }
            },
            isCancelDisabled: function () {
                return (this.loading);
            },
        },
        methods: {
            closeModal: function (withChanges) {
                this.$emit("close-user-modal", withChanges);
            },
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
                if (this.isAddForm) {
                    if (!this.user.password) {
                        this.validator.setInvalid("password", "Invalid password");
                    }
                }
                if (this.user.password && this.user.password != this.confirmedPassword) {
                    this.validator.setInvalid("confirmedPassword", "Passwords do not match");
                }
                return (!this.validator.hasInvalidFields());
            },
            save: function () {
                if (this.validate()) {
                    if (this.isAddForm) {
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
                    if (response.ok) {
                        self.closeModal(true);
                    } else {
                        switch (response.status) {
                            case 409:
                                if (response.isFieldInvalid("email")) {
                                    self.validator.setInvalid("email", "Selected email is used (choose another)");
                                }
                                if (!self.validator.hasInvalidFields()) {
                                    self.$router.push({ name: 'the500' });
                                }
                                break;
                            default:
                                self.$router.push({ name: 'the500' });
                                break;
                        }
                    }
                    self.loading = false;
                });
            },
            update: function () {
                var self = this;
                self.loading = true;
                phpMPMApi.user.update(self.user, function (response) {
                    if (response.ok) {
                        self.closeModal(true);
                    } else {
                        switch (response.status) {
                            case 409:
                                if (response.isFieldInvalid("email")) {
                                    self.validator.setInvalid("email", "Selected email is used (choose another)");
                                }
                                if (!self.validator.hasInvalidFields()) {
                                    self.$router.push({ name: 'the500' });
                                }
                                break;
                            default:
                                self.$router.push({ name: 'the500' });
                                break;
                        }
                    }
                    self.loading = false;
                });
            }
        }
    });

    return (module);
})();