const TheUserList = (function () {
    "use strict";

    var template = function () {
        return `
            <div>
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
                                        <input class="input" type="text" placeholder="search by name" v-bind:disabled="loading" v-model.trim="searchByName" v-on:keyup.enter.prevent="search(true);">
                                        <span  class="icon is-small is-left">
                                            <i class="fas fa-filter"></i>
                                        </span >
                                    </div>
                                    <div class="control">
                                        <a class="button" v-bind:disabled="loading" v-on:click.prevent="search(true);">
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
                                        <input class="input" type="text" placeholder="search by email" v-bind:disabled="loading" v-model:trim="searchByEmail" v-on:keyup.enter.prevent="search(true);">
                                        <span  class="icon is-small is-left">
                                            <i class="fas fa-filter"></i>
                                        </span >
                                    </div>
                                    <div class="control">
                                        <a class="button" v-bind:disabled="loading" v-on:click.prevent="search(true);">
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
                                <div class="field is-grouped">
                                    <p class="control is-expanded">
                                        <a class="button is-small is-fullwidth is-link" v-bind:disabled="loading" v-on:click.prevent="onShowAddUserModal();">
                                            <span class="icon is-small"><i class="fas fa-plus"></i></span>
                                            <span>Add</span>
                                        </a>
                                    </p>
                                    <table-export-button v-bind:loading="loading" v-bind:configuration="exportOptions"></table-export-button>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <the-user-list-item v-for="user in users" v-bind:key="user.id" v-bind:user="user" v-bind:loading="loading" v-on:show-update-user-modal="onShowUpdateUserModal" v-on:show-delete-user-modal="onShowDeleteUserModal"></the-user-list-item>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5">
                                <table-pagination  v-bind:loading="loading" v-bind:data="pager"></table-pagination>
                            </th>
                        </tr>
                    </tfoot>
                </table>

                <the-user-modal-form v-if="userModalOpts.show" v-bind:opts="userModalOpts" v-on:close-user-modal="onCloseUserModal"></the-user-modal-form>
                <the-delete-confirmation-modal v-bind:id="deleteUserId" v-if="showDeleteConfirmationModal" v-on:confirm-delete="onConfirmDelete" v-on:cancel-delete="onCancelDelete"></the-delete-confirmation-modal>

            </div>
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
                userModalOpts: {
                    show: false,
                    type: null,
                    userId: null
                },
                deleteUserId: null,
                showDeleteConfirmationModal: false
            });
        },
        created: function () {
            this.search(true);
            var self = this;
            this.pager.refresh = function () {
                self.search(false);
            }
        },
        watch: {
            searchByAccountType: function () {
                this.search(true);
            }
        },
        computed: {
            exportOptions: function () {
                return (
                    {
                        name: 'users',
                        elements: this.users,
                        fields: ['id', 'name', 'email', 'created']
                    }
                );
            }
        },
        methods: {
            onShowAddUserModal: function () {
                this.userModalOpts.type = "add";
                this.userModalOpts.show = true;
            },
            onShowUpdateUserModal: function (userId) {
                this.userModalOpts.type = "update";
                this.userModalOpts.userId = userId;
                this.userModalOpts.show = true;
            },
            onShowDeleteUserModal: function (userId) {
                this.deleteUserId = userId;
                this.showDeleteConfirmationModal = true;
            },
            onCloseUserModal: function (withChanges) {
                this.userModalOpts = {
                    show: false,
                    type: null,
                    id: null
                };
                if (withChanges) {
                    this.search(false);
                }
            },
            onConfirmDelete: function (userId) {
                this.delete(userId);
            },
            onCancelDelete: function () {
                this.showDeleteConfirmationModal = false;
                this.deleteUserId = null;
            },
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
            search(resetPager) {
                var self = this;
                if (resetPager) {
                    this.pager.actualPage = 1;
                }
                self.loading = true;
                phpMPMApi.user.search(this.searchByAccountType, this.searchByName, this.searchByEmail, self.pager.actualPage, self.pager.resultsPage, self.sortBy, self.sortOrder, function (response) {
                    self.loading = false;
                    if (response.ok) {
                        self.pager.actualPage = response.body.pagination.actualPage;
                        self.pager.totalPages = response.body.pagination.totalPages;
                        self.pager.totalResults = response.body.pagination.totalResults;
                        self.users = response.body.users;
                    } else {
                        self.$router.push({ name: 'the500' });
                    }
                });
            },
            delete(userId) {
                var self = this;
                self.loading = true;
                phpMPMApi.user.delete(userId, function (response) {
                    if (response.ok) {
                        self.showDeleteConfirmationModal = false;
                        self.deleteUserId = null;
                        self.search(false);
                    } else {
                        self.$router.push({ name: 'the500' });
                    }
                });
            }
        }
    });

    return (module);
})();