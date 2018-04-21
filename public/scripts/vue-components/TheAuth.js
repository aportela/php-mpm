const TheAuth = (function () {
    "use strict";

    var template = function () {
        return `
            <section class="hero is-fullheight is-light is-bold">
                <div class="hero-body">
                    <div class="container">
                        <div class="columns is-vcentered">
                            <div class="column is-4 is-offset-4">

                                <h1 class="title has-text-centered"><span class="icon is-medium"><i class="fas fa-music" aria-hidden="true"></i></span> php mpm <span class="icon is-medium"><i class="fas fa-music" aria-hidden="true"></i></span></h1>
                                <h2 class="subtitle is-6 has-text-centered"><cite>...manage & organize your data with artisant forms</cite></h2>

                                <div class="tabs is-toggle is-radiusless phpmpm-tabs-without-margin-bottom">
                                    <ul>
                                        <li v-bind:class="tab == 'signin' ? 'is-active': ''">
                                            <a v-on:click.prevent="tab = 'signin';">
                                                <span class="icon is-small"><i class="fas fa-user"></i></span>
                                                <span>Sign in</span>
                                            </a>
                                        </li>
                                        <li v-if="allowSignUp" v-bind:class="tab == 'signup' ? 'is-active': ''">
                                            <a v-on:click.prevent="tab = 'signup';">
                                                <span class="icon is-small"><i class="fas fa-user-plus"></i></span>
                                                <span>Sign up</span>
                                            </a>
                                        </li>
                                        <li v-if="allowSignUp" v-bind:class="tab == 'recoverAccount' ? 'is-active': ''">
                                            <a v-on:click.prevent="tab = 'recoverAccount';">
                                                <span class="icon is-small"><i class="fas fa-lock"></i></span>
                                                <span>Recover account</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <form v-on:submit.prevent="submitSignIn" v-if="tab == 'signin'">
                                    <div class="box is-radiusless">
                                        <label class="label">Email</label>
                                        <p class="control has-icons-left" id="login-container" v-bind:class="{ 'has-icons-right' : invalidSignInUsername }">
                                            <input class="input" type="email" ref="signInEmail" name="email" maxlength="255" required v-bind:class="{ 'is-danger': invalidSignInUsername }" v-bind:disabled="loading ? true: false" v-model="signInEmail">
                                            <span class="icon is-small is-left"><i class="fas fa-envelope"></i></span>
                                            <span class="icon is-small is-right" v-show="invalidSignInUsername"><i class="fas fa-warning"></i></span>
                                            <p class="help is-danger" v-show="invalidSignInUsername">Invalid email</p>
                                        </p>
                                        <label class="label">Password</label>
                                        <p class="control has-icons-left" id="password-container" v-bind:class="{ 'has-icons-right' : invalidSignInPassword }">
                                            <input class="input" type="password" name="password" required v-bind:class="{ 'is-danger': invalidSignInPassword }" v-bind:disabled="loading ? true: false" v-model="signInPassword">
                                            <span class="icon is-small is-left"><i class="fas fa-key"></i></span>
                                            <span class="icon is-small is-right" v-show="invalidSignInPassword"><i class="fas fa-warning"></i></span>
                                            <p class="help is-danger" v-show="invalidSignInPassword">Invalid password</p>
                                        </p>
                                        <hr>
                                        <p class="control">
                                            <button type="submit" class="button is-primary" v-bind:class="{ 'is-loading': loading }" v-bind:disabled="loading ? true: false">
                                                <span class="icon"><i class="fas fa-lock"></i></span>
                                                <span>Sign in</span>
                                            </button>
                                        </p>
                                    </div>
                                </form>


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
                allowSignUp: initialState.allowSignUp,
                tab: 'signin'

            });
        }
    });

    return (module);
})();