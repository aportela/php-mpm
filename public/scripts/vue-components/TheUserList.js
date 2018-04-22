const TheUserList = (function () {
    "use strict";

    var template = function () {
        return `
            <table class="table is-bordered is-striped is-narrow is-fullwidth is-unselectable">
                <thead>
                    <tr>
                        <th class="phpmpm-cursor-pointer" v-on:click.prevent="toggleSort('accountType');">
                            <i v-if="sortBy == 'accountType'" class="fas" v-bind:class="{ 'fa-sort-alpha-up': sortOrder == 'ASC', 'fa-sort-alpha-down': sortOrder == 'DESC' }"></i>
                            Account type
                        </th>
                        <th class="phpmpm-cursor-pointer" v-on:click.prevent="toggleSort('name');">
                            <i v-if="sortBy == 'name'" class="fas" v-bind:class="{ 'fa-sort-alpha-up': sortOrder == 'ASC', 'fa-sort-alpha-down': sortOrder == 'DESC' }"></i>
                            Name
                        </th>
                        <th class="phpmpm-cursor-pointer" v-on:click.prevent="toggleSort('email');">
                            <i v-if="sortBy == 'email'" class="fas" v-bind:class="{ 'fa-sort-alpha-up': sortOrder == 'ASC', 'fa-sort-alpha-down': sortOrder == 'DESC' }"></i>
                            Email
                        </th>
                        <th class="phpmpm-cursor-pointer" v-on:click.prevent="toggleSort('created');">
                            <i v-if="sortBy == 'created'" class="fas" v-bind:class="{ 'fa-sort-amount-up': sortOrder == 'ASC', 'fa-sort-amount-down': sortOrder == 'DESC' }"></i>
                            Created
                        </th>
                        <th>Operations</th>
                    </tr>
                    <tr>
                        <th>
                            <div class="control has-icons-left is-expanded">
                                <div class="select is-fullwidth">
                                    <select v-model="searchByAccountType" v-bind:disabled="loading">
                                        <option value="">All types</option>
                                        <option value="A">Only administrators</option>
                                        <option value="U">Only normal users</option>
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
                                    <input class="input" type="text" placeholder="search by name" v-bind:disabled="loading" v-model.trim="searchByName" v-on:keyup.enter.prevent="search();">
                                    <span  class="icon is-small is-left">
                                        <i class="fas fa-filter"></i>
                                    </span >
                                </div>
                                <div class="control">
                                    <a class="button" v-bind:disabled="loading" v-on:click.prevent="search();">
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
                                    <input class="input" type="text" placeholder="search by email" v-bind:disabled="loading" v-model:trim="searchByEmail" v-on:keyup.enter.prevent="search();">
                                    <span  class="icon is-small is-left">
                                        <i class="fas fa-filter"></i>
                                    </span >
                                </div>
                                <div class="control">
                                    <a class="button" v-bind:disabled="loading" v-on:click.prevent="search();">
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
                            <p class="control">
                                <a class="button is-link is-fullwidth" v-bind:disabled="loading" v-on:click.prevent="$router.push({ name: 'theUserAddForm' });">
                                    <span class="icon is-small"><i class="fas fa-plus"></i></span>
                                    <span>Add new user</span>
                                </a>
                            </p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users">
                        <td v-if="user.accountType == 'A'">
                            <span class="icon"><i class="fas fa-user"></i></span>
                            <span>administrator</span>
                        </td>
                        <td v-else>
                            <span class="icon"><i class="far fa-user"></i></span>
                            <span>normal user</span>
                        </td>
                        <td>{{ user.name }}</td>
                        <td>{{ user.email }}</td>
                        <td>{{ user.created | jsonDate2Human }}</td>
                        <td>
                            <div class="field is-grouped">
                                <p class="control">
                                    <a class="button is-info is-small" v-bind:disabled="loading">
                                        <span class="icon is-small"><i class="fas fa-edit"></i></span>
                                        <span>Update</span>
                                    </a>
                                </p>
                                <p class="control">
                                    <a class="button is-danger is-small" v-bind:disabled="true">
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
                        <th colspan="5">
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
                searchByAccountType: "",
                sortBy: "name",
                sortOrder: "ASC",
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
        watch: {
            searchByAccountType: function () {
                this.search();
            }
        },
        filters: {
            jsonDate2Human(jsonDate) {
                return (moment(jsonDate, "YYYY-MM-DDTHH:mm:ss.SZ").fromNow());
            }
        },
        methods: {
            toggleSort: function (field) {
                if (!this.loading) {
                    if (field == this.sortBy) {
                        if (this.sortOrder == "ASC") {
                            this.sortOrder = "DESC";
                        } else {
                            this.sortOrder = "ASC";
                        }
                    } else {
                        this.sortBy = field;
                        this.sortOrder = "ASC";
                    }
                    this.search();
                }
            },
            search() {
                var self = this;
                self.loading = true;
                phpMPMApi.user.search(this.searchByAccountType, this.searchByName, this.searchByEmail, self.pager.actualPage, self.pager.resultsPage, self.sortBy, self.sortOrder, function (response) {
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