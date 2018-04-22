const TheUserUpdateForm = (function () {
    "use strict";

    var template = function () {
        return `
            <div class="card">
                <div class="card-content is-clearfix">
                    <form>
                        <div class="field">
                            <label class="label">Full user name/surname</label>
                            <div class="control has-icons-left">
                                <input class="input" type="text" placeholder="Type complete user name" required>
                                <span class="icon is-small is-left">
                                    <i class="fas fa-user"></i>
                                </span>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">Email</label>
                            <div class="control has-icons-left">
                                <input class="input" type="email" placeholder="User email (will be used for sign in)" required>
                                <span class="icon is-small is-left">
                                    <i class="fas fa-envelope"></i>
                                </span>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">Password</label>
                            <div class="control has-icons-left">
                                <input class="input" type="password" placeholder="User password" required>
                                <span class="icon is-small is-left">
                                    <i class="fas fa-key"></i>
                                </span>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">Confirm password</label>
                            <div class="control has-icons-left">
                                <input class="input" type="password" placeholder="Type again the user password" required>
                                <span class="icon is-small is-left">
                                    <i class="fas fa-key"></i>
                                </span>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">Account type</label>
                            <div class="control">
                                <div class="select">
                                    <select>
                                        <option selected>Normal user</option>
                                        <option>Administrator</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="field is-grouped is-pulled-right">
                            <div class="control">
                                <button class="button is-link" type="submit">
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
            });
        }
    });

    return (module);
})();