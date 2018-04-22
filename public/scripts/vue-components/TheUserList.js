const TheUserList = (function () {
    "use strict";

    var template = function () {
        return `
            <table class="table is-bordered is-striped is-narrow is-fullwidth">
                <thead>
                    <tr>
                        <th>Account type</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Created by</th>
                        <th>Created on </th>
                        <th>Operations</th>
                    </tr>
                    <tr>
                        <th>
                            <div class="control has-icons-left is-expanded">
                                <div class="select is-fullwidth">
                                    <select>
                                        <option>All types</option>
                                        <option>Only administrators</option>
                                        <option>Only normal users</option>
                                    </select>
                                </div>
                                <div class="icon is-small is-left">
                                    <i class="fas fa-filter"></i>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="field has-addons">
                                <div class="control has-icons-left is-expanded">
                                    <input class="input" type="text" placeholder="search by name" v-model.trim="searchByName">
                                    <span  class="icon is-small is-left">
                                        <i class="fas fa-filter"></i>
                                    </span >
                                </div>
                                <div class="control">
                                    <a class="button" v-on:click.prevent="search();">
                                        <span class="icon">
                                            <i class="fas fa-search"></i>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="field has-addons">
                                <div class="control has-icons-left is-expanded">
                                    <input class="input" type="text" placeholder="search by email" v-model:trim="searchByEmail">
                                    <span  class="icon is-small is-left">
                                        <i class="fas fa-filter"></i>
                                    </span >
                                </div>
                                <div class="control">
                                    <a class="button" v-on:click.prevent="search();">
                                        <span class="icon">
                                            <i class="fas fa-search"></i>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </th>
                        <th>
                        </th>
                        <th>
                            <div class="control has-icons-left is-expanded">
                                <div class="select is-fullwidth">
                                    <select>
                                        <option>Anytime</option>
                                        <option>Today</option>
                                        <option>Yesterday</option>
                                        <option>Last week</option>
                                        <option>Last month</option>
                                        <option>Last Year</option>
                                    </select>
                                </div>
                                <div class="icon is-small is-left">
                                    <i class="fas fa-filter"></i>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="field has-addons">
                                <p class="control">
                                    <a class="button is-link" v-on:click.prevent="$router.push({ name: 'theUserAddForm' });">
                                        <span class="icon is-small"><i class="fas fa-plus"></i></span>
                                        <span>Add</span>
                                    </a>
                                </p>
                                <p class="control">
                                    <a class="button is-info" v-bind:disabled="true">
                                        <span class="icon is-small">
                                        <i class="fas fa-table"></i>
                                        </span>
                                        <span>Export</span>
                                    </a>
                                </p>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users">
                        <td v-if="user.isAdmin">
                            <span class="icon"><i class="fas fa-user"></i></span>
                            <span>administrator</span>
                        </td>
                        <td v-else>
                            <span class="icon"><i class="far fa-user"></i></span>
                            <span>normal user</span>
                        </td>
                        <td>{{ user.name }}</td>
                        <td>{{ user.email }}</td>
                        <td>{{ user.createdBy }}</td>
                        <td>{{ user.createdOn }}</td>
                        <td>
                            <div class="field has-addons">
                                <p class="control">
                                    <a class="button is-link">
                                        <span class="icon is-small"><i class="fas fa-edit"></i></span>
                                        <span>Update</span>
                                    </a>
                                </p>
                                <p class="control">
                                    <a class="button is-danger" v-bind:disabled="true">
                                        <span class="icon is-small"><i class="fas fa-trash"></i></span>
                                        <span>Remove</span>
                                    </a>
                                </p>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6">
                            <table-pagination  v-bind:loading="loading" v-bind:data="pager"></table-pagination>
                        </th>
                    </tr>
                </tfoot>
            </table>
        `;
    };

    var module = Vue.component('the-user-list', {
        template: template(),
        data: function () {
            return ({
                loading: false,
                pager: getPager(),
                users: [],
                searchByName: null,
                searchByEmail: null,
            });
        },
        created: function () {
            this.search();
            var self = this;
            this.pager.refresh = function () {
                self.search();
            }
        },
        methods: {
            search() {
                var self = this;
                self.loading = true;
                phpMPMApi.user.search(this.searchByName, this.searchByEmail, self.pager.actualPage, self.pager.resultsPage, "", function (response) {
                    self.loading = false;
                    if (response.ok) {
                        self.pager.actualPage = response.body.pagination.actualPage;
                        self.pager.totalPages = response.body.pagination.totalPages;
                        self.pager.totalResults = response.body.pagination.totalResults;
                        self.users = response.body.users;
                    } else {
                    }
                });
            }
        }
    });

    return (module);
})();