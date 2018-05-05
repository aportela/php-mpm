const TheAuth = (function () {
    "use strict";

    var template = function () {
        return `
            <section class="hero is-fullheight is-light is-bold">
                <div class="hero-body">
                    <div class="container">
                        <div class="columns is-vcentered">
                            <div class="column is-4 is-offset-4 is-unselectable">

                                <h1 class="title has-text-centered"><span class="icon is-medium"><i class="fas fa-database" aria-hidden="true"></i></span> php mpm <span class="icon is-medium"><i class="fas fa-database" aria-hidden="true"></i></span></h1>
                                <h2 class="subtitle is-6 has-text-centered"><cite>...manage & organize your data with artisan forms</cite></h2>

                                <div class="tabs is-toggle is-radiusless phpmpm-tabs-without-margin-bottom">
                                    <ul>
                                        <li class="is-active">
                                            <a>
                                                <span class="icon is-small"><i class="fas fa-user"></i></span>
                                                <span>Sign in</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <form v-on:submit.prevent="submitSignIn">
                                    <div class="box is-radiusless">
                                        <label for="email" class="label">Email</label>
                                        <p class="control has-icons-left" v-bind:class="{ 'has-icons-right' : validator.hasInvalidField('email') }">
                                            <input class="input" type="email" ref="signInEmail" name="email" maxlength="255" required v-bind:class="{ 'is-danger': validator.hasInvalidField('email') }" v-bind:disabled="loading ? true: false" v-model.trim="signInEmail">
                                            <span class="icon is-small is-left"><i class="fas fa-envelope"></i></span>
                                            <span class="icon is-small is-right" v-show="validator.hasInvalidField('email')"><i class="fas fa-warning"></i></span>
                                            <p class="help is-danger" v-show="validator.hasInvalidField('email')">{{ validator.getInvalidFieldMessage('email') }}</p>
                                        </p>
                                        <label for="password" class="label">Password</label>
                                        <p class="control has-icons-left" v-bind:class="{ 'has-icons-right' : validator.hasInvalidField('password') }">
                                            <input class="input" type="password" name="password" required v-bind:class="{ 'is-danger': validator.hasInvalidField('password') }" v-bind:disabled="loading ? true: false" v-model.trim="signInPassword">
                                            <span class="icon is-small is-left"><i class="fas fa-key"></i></span>
                                            <span class="icon is-small is-right" v-show="validator.hasInvalidField('password')"><i class="fas fa-warning"></i></span>
                                            <p class="help is-danger" v-show="validator.hasInvalidField('password')">{{ validator.getInvalidFieldMessage('password') }}</p>
                                        </p>
                                        <hr>
                                        <p class="control has-text-right">
                                            <button type="submit" class="button is-link" v-bind:class="{ 'is-loading': loading }" v-bind:disabled="isSigInSubmitDisabled">
                                                <span class="icon"><i class="fas fa-lock"></i></span>
                                                <span>Sign in</span>
                                            </button>
                                        </p>
                                    </div>
                                </form>

                                <p class="has-text-centered phpmpm-margin-top-1rem">
                                    <a href="https://github.com/aportela/php-mpm" target="_blank"><span class="icon is-small"><i class="fab fa-github"></i></span>Project page</a> | <a href="https://github.com/aportela" target="_blank">by alex</a>
                                </p>

                            </div>
                        </div>
                    </div>
                </div>
            </section>
        `;
    };

    var module = Vue.component('the-auth', {
        template: template(),
        data: function () {
            return ({
                validator: getValidator(),
                loading: false,
                signInEmail: "admin@localhost.localnet",
                signInPassword: "secret",
            });
        },
        created: function () {
            if (!initialState.session.logged) {
                this.$nextTick(() => this.$refs.signInEmail.focus());
            } else {
                this.$router.push({ name: 'theDashboard' });
            }
        },
        computed: {
            isSigInSubmitDisabled: function () {
                return (!(this.signInEmail && this.signInPassword && !this.loading));
            }
        },
        methods: {
            submitSignIn: function () {
                var self = this;
                self.loading = true;
                self.validator.clear();
                phpMPMApi.user.signIn(this.signInEmail, this.signInPassword, function (response) {
                    if (response.ok) {
                        initialState.session = response.body.session;
                        self.$router.push({ name: 'theDashboard' });
                    } else {
                        switch (response.status) {
                            case 400:
                                if (response.isFieldInvalid("email")) {
                                    self.validator.setInvalid("email", "API ERROR: invalid param");
                                }
                                if (response.isFieldInvalid("password")) {
                                    self.validator.setInvalid("password", "API ERROR: invalid param");
                                }
                                if (! self.validator.hasInvalidFields()) {
                                    self.$router.push({ name: 'the500' });
                                }
                                break;
                            case 404:
                                self.validator.setInvalid("email", "No account found with this email");
                                break;
                            case 401:
                                self.validator.setInvalid("password", "Incorrect password");
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