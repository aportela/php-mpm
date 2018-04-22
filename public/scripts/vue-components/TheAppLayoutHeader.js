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
                        <div class="navbar-start">
                            <a class="navbar-item" v-on:click.prevent="$router.push({ name: 'theDashboard' })" v-bind:class="{ 'is-active': $route.name == 'theDashboard' }"><span class="icon"><i class="fa fa-home"></i></span> <label>Home</label></a>
                            <div class="navbar-item has-dropdown is-hoverable">
                                <a class="navbar-link">
                                    <span class="icon"><i class="fa fa-archive"></i></span>
                                    <label>Actions</label>
                                </a>
                                <div class="navbar-dropdown">
                                    <a class="navbar-item"><span class="icon"><i class="fa fa-plus"></i></span><label>Add new element</label></a>
                                    <a class="navbar-item"><span class="icon"><i class="fa fa-calendar"></i></span><label>Recent elements</label></a>
                                </div>
                            </div>
                            <div class="navbar-item has-dropdown is-hoverable">
                                <a class="navbar-link">
                                    <span class="icon"><i class="fa fa-user-circle"></i></span>
                                    <span>Administration menu</span>
                                </a>
                                <div class="navbar-dropdown">
                                    <a class="navbar-item" v-on:click.prevent="$router.push({ name: 'theUserAddForm' })" v-bind:class="{ 'is-active': $route.name == 'theUserAddForm' }"><span class="icon"><i class="fa fa-plus"></i></span><label>Add user</label></a>
                                    <a class="navbar-item" v-on:click.prevent="$router.push({ name: 'theUserList' })" v-bind:class="{ 'is-active': $route.name == 'theUserList' }"><span class="icon"><i class="fa fa-user"></i></span><label>User list</label></a>
                                    <a class="navbar-item" v-on:click.prevent="$router.push({ name: 'theGroupList' })" v-bind:class="{ 'is-active': $route.name == 'theGroupList' }"><span class="icon"><i class="fa fa-users"></i></span><label>Groups</label></a>
                                    <hr class="navbar-divider">
                                    <a class="navbar-item" v-on:click.prevent="$router.push({ name: 'theAttributeList' })" v-bind:class="{ 'is-active': $route.name == 'theAttributeList' }"><span class="icon"><i class="fa fa-object-group"></i></span><label>Attributes</label></a>
                                    <hr class="navbar-divider">
                                    <a class="navbar-item" v-on:click.prevent="$router.push({ name: 'theTemplateList' })" v-bind:class="{ 'is-active': $route.name == 'theTemplateList' }"><span class="icon"><i class="fa fa-file"></i></span><label>Templates</label></a>
                                    <a class="navbar-item" v-on:click.prevent="$router.push({ name: 'theSearchTemplateList' })" v-bind:class="{ 'is-active': $route.name == 'theSearchTemplateList' }"><span class="icon"><i class="fa fa-search"></i></span><label>Search templates</label></a>
                                    <hr class="navbar-divider">
                                    <a class="navbar-item" v-on:click.prevent="$router.push({ name: 'theLogList' })" v-bind:class="{ 'is-active': $route.name == 'theLogList' }"><span class="icon"><i class="fa fa-list-alt"></i></span><label>Log</label></a>
                                    <a class="navbar-item" v-on:click.prevent="$router.push({ name: 'theErrorList' })" v-bind:class="{ 'is-active': $route.name == 'theErrorList' }"><span class="icon"><i class="fa fa-bomb"></i></span><label>Errors</label></a>
                                </div>
                            </div>
                        </div>
                        <div class="navbar-end">
                            <div class="navbar-item">
                                <div class="field">
                                    <div class="control has-icons-left" v-bind:class="{ 'has-icons-right, is-loading': isSearching }">
                                        <span class="icon is-small is-left">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input v-on:keyup.enter="search();" v-on:keyup.esc="searchText = null;" v-model.trim="searchText" ref="search" v-bind:disabled="isSearching" class="input is-rounded" type="text" placeholder="search...">
                                    </div>
                                </div>
                            </div>
                            <div class="navbar-item has-dropdown is-hoverable">
                                <a class="navbar-link">
                                    <span class="icon"><i class="far fa-user"></i></span>
                                    <span>Signed as administrator</span>
                                </a>
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
                searchText: null,
                isSearching: false
            });
        }, methods: {
            search: function () {
            },
            signOut: function () {
                var self = this;
                phpMPMApi.user.signOut(function (response) {
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