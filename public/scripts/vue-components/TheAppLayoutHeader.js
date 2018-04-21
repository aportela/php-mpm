const TheAppLayoutHeader = (function () {
    "use strict";

    var template = function () {
        return `
            <div>
                <nav class="navbar" role="navigation" aria-label="main navigation">
                    <div class="navbar-brand">
                        <a class="navbar-item" href="https://github.com/aportela/php-mpm" target="_blank">
                            <span class="icon"><i class="fab fa-github"></i></span>
                            <span>php-mpm</span>
                        </a>
                        <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false">
                            <span aria-hidden="true"></span>
                            <span aria-hidden="true"></span>
                            <span aria-hidden="true"></span>
                        </a>
                    </div>
                    <div class="navbar-menu">
                        <div class="navbar-end">
                            <div class="navbar-item has-dropdown is-hoverable">
                                <a class="navbar-link">Signed as administrator</a>
                                <div class="navbar-dropdown">
                                    <a class="navbar-item"><span class="icon"><i class="fas fa-user"></i></span><span>My Profile</span></a>
                                    <a class="navbar-item" v-on:click.prevent="signOut();"><span class="icon"><i class="fas fa-sign-out-alt"></i></span><span>Sign Out</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
        `;
    };

    var module = Vue.component('the-app-layout-header', {
        template: template(),
        data: function () {
            return ({
            });
        }, methods: {
            signOut: function() {
                var self = this;
                phpMPMApi.signOut(function (response) {
                    if (response.ok) {
                        self.$router.push({ name: 'auth' });
                    } else {
                        // TODO
                    }
                });
            }
        }
    });

    return (module);
})();