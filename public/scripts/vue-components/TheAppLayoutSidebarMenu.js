const TheAppLayoutSidebarMenu = (function () {
    "use strict";

    var template = function () {
        return `
            <aside class="menu">
                <p class="menu-label">
                    General
                </p>
                <ul class="menu-list">
                    <li><a v-on:click.prevent="$router.push({ name: 'theDashboard' })" v-bind:class="{ 'is-active': $route.name == 'theDashboard' }"><span class="icon"><i class="fa fa-home"></i></span> Home</a></li>
                </ul>
                <p class="menu-label">
                    Administration
                </p>
                <ul class="menu-list">
                    <li><a v-on:click.prevent="$router.push({ name: 'theUserList' })" v-bind:class="{ 'is-active': $route.name == 'theUserList' }"><span class="icon"><i class="fa fa-user"></i></span> Users</a></li>
                    <li><a v-on:click.prevent="$router.push({ name: 'theGroupList' })" v-bind:class="{ 'is-active': $route.name == 'theGroupList' }"><span class="icon"><i class="fa fa-users"></i></span> Groups</a></li>
                    <li><a v-on:click.prevent="$router.push({ name: 'theAttributeList' })" v-bind:class="{ 'is-active': $route.name == 'theAttributeList' }"><span class="icon"><i class="fa fa-object-group"></i></span> Attributes</a></li>
                    <li><a v-on:click.prevent="$router.push({ name: 'theTemplateList' })" v-bind:class="{ 'is-active': $route.name == 'theTemplateList' }"><span class="icon"><i class="fa fa-file"></i></span> Templates</a></li>
                    <li><a v-on:click.prevent="$router.push({ name: 'theSearchTemplateList' })" v-bind:class="{ 'is-active': $route.name == 'theSearchTemplateList' }"><span class="icon"><i class="fa fa-search"></i></span> Search templates</a></li>
                </ul>
                <p class="menu-label">
                    Tools
                </p>
                <ul class="menu-list">
                    <li><a v-on:click.prevent="$router.push({ name: 'theLogList' })" v-bind:class="{ 'is-active': $route.name == 'theLogList' }"><span class="icon"><i class="fa fa-list-alt"></i></span> Log</a></li>
                    <li><a v-on:click.prevent="$router.push({ name: 'theErrorList' })" v-bind:class="{ 'is-active': $route.name == 'theErrorList' }"><span class="icon"><i class="fa fa-bomb"></i></span> Errors</a></li>
                </ul>

            </aside>
        `;
    };

    var module = Vue.component('the-app-layout-sidebar-menu', {
        template: template(),
        data: function () {
            return ({
            });
        }
    });

    return (module);
})();