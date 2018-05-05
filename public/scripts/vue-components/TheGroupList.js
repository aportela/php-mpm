const TheGroupList = (function () {
    "use strict";

    var template = function () {
        return `
            <div>
                <table class="table is-bordered is-striped is-narrow is-fullwidth is-unselectable">
                    <thead>
                        <tr>
                            <th class="phpmpm-cursor-pointer" v-on:click.prevent="toggleSort('name');">
                                <i v-if="sortBy == 'name'" class="fas" v-bind:class="{ 'fa-sort-alpha-up': sortOrder == 'ASC', 'fa-sort-alpha-down': sortOrder == 'DESC' }"></i>
                                Name
                            </th>
                            <th class="phpmpm-cursor-pointer" v-on:click.prevent="toggleSort('description');">
                                <i v-if="sortBy == 'description'" class="fas" v-bind:class="{ 'fa-sort-alpha-up': sortOrder == 'ASC', 'fa-sort-alpha-down': sortOrder == 'DESC' }"></i>
                                Description
                            </th>
                            <th class="has-text-right phpmpm-cursor-pointer" v-on:click.prevent="toggleSort('userCount');">
                                <i v-if="sortBy == 'userCount'" class="fas" v-bind:class="{ 'fa-sort-amount-up': sortOrder == 'ASC', 'fa-sort-amount-down': sortOrder == 'DESC' }"></i>
                                User count
                            </th>
                            <th class="phpmpm-cursor-pointer" v-on:click.prevent="toggleSort('created');">
                                <i v-if="sortBy == 'created'" class="fas" v-bind:class="{ 'fa-sort-amount-up': sortOrder == 'ASC', 'fa-sort-amount-down': sortOrder == 'DESC' }"></i>
                                Created
                            </th>
                            <th>Operations</th>
                        </tr>
                        <tr>
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
                                        <input class="input" type="text" placeholder="search by description" v-bind:disabled="loading" v-model:trim="searchByDescription" v-on:keyup.enter.prevent="search(true);">
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
                                <div class="control has-icons-left is-expanded">
                                    <div class="select is-fullwidth">
                                        <select v-model="searchByUserCount" disabled>
                                            <option value="">Any</option>
                                            <option value="F">With users</option>
                                            <option value="E">Without users</option>
                                        </select>
                                    </div>
                                    <div class="icon is-small is-left">
                                        <i class="fas fa-filter"></i>
                                    </div>
                                </div>
                            </th>
                            <th>
                            </th>
                            <th>
                                <div class="field is-grouped">
                                    <p class="control is-expanded">
                                        <a class="button is-small is-fullwidth is-link" v-bind:disabled="loading" v-on:click.prevent="onShowAddModal();">
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
                        <the-group-list-item v-for="group in items" v-bind:key="group.id" v-bind:group="group" v-bind:loading="loading" v-on:show-update-group-modal="onShowUpdateModal" v-on:show-delete-group-modal="onShowDeleteModal"></the-group-list-item>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5">
                                <table-pagination v-bind:loading="loading" v-bind:data="pager"></table-pagination>
                            </th>
                        </tr>
                    </tfoot>
                </table>

                <the-group-modal-form v-if="itemModalOpts.show" v-bind:opts="itemModalOpts" v-on:close-item-modal="onCloseItemModal"></the-group-modal-form>
                <the-delete-confirmation-modal v-bind:id="deleteItemId" v-if="showDeleteConfirmationModal" v-on:confirm-delete="onConfirmDelete" v-on:cancel-delete="onCancelDelete"></the-delete-confirmation-modal>

            </div>
        `;
    };

    var module = Vue.component('the-group-list', {
        template: template(),
        mixins: [mixinManageAdminEntities],
        data: function () {
            return ({
                loading: false,
                searchByUserCount: "",
                searchByName: null,
                searchByDescription: null,
                sortBy: "name"
            });
        },
        watch: {
            searchByUserCount: function () {
                this.search(true);
            }
        },
        computed: {
            exportOptions: function () {
                return (
                    {
                        name: 'groups',
                        elements: this.items,
                        fields: ['id', 'name', 'description', 'created']
                    }
                );
            }
        },
        methods: {
            search(resetPager) {
                var self = this;
                if (resetPager) {
                    this.pager.actualPage = 1;
                }
                self.loading = true;
                phpMPMApi.group.search(this.searchByName, this.searchByDescription, self.pager.actualPage, self.pager.resultsPage, self.sortBy, self.sortOrder, function (response) {
                    self.loading = false;
                    if (response.ok) {
                        self.pager.actualPage = response.body.pagination.actualPage;
                        self.pager.totalPages = response.body.pagination.totalPages;
                        self.pager.totalResults = response.body.pagination.totalResults;
                        self.items = response.body.groups;
                    } else {
                        self.$router.push({ name: 'the500' });
                    }
                });
            },
            delete(groupId) {
                var self = this;
                self.loading = true;
                phpMPMApi.group.delete(groupId, function (response) {
                    if (response.ok) {
                        self.showDeleteConfirmationModal = false;
                        self.deleteItemId = null;
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