const TheAttributeList = (function () {
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
                            <th class="phpmpm-cursor-pointer" v-on:click.prevent="toggleSort('type');">
                                <i v-if="sortBy == 'type'" class="fas" v-bind:class="{ 'fa-sort-amount-up': sortOrder == 'ASC', 'fa-sort-amount-down': sortOrder == 'DESC' }"></i>
                                Type
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
                                        <select v-model="searchByType" v-bind:disabled="loading">
                                            <option value="">All types</option>
                                            <option v-for="type in types" v-bind:key="type.id" v-bind:value="type.id">{{ type.name }}</option>
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
                                        <a class="button is-small is-fullwidth is-link" v-bind:disabled="loading" v-on:click.prevent="onShowAddAttributeModal();">
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
                        <the-attribute-list-item v-for="attribute in attributes" v-bind:key="attribute.id" v-bind:attribute="attribute" v-bind:loading="loading" v-on:show-update-attribute-modal="onShowUpdateAttributeModal" v-on:show-delete-attribute-modal="onShowDeleteAttributeModal"></the-attribute-list-item>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5">
                                <table-pagination v-bind:loading="loading" v-bind:data="pager"></table-pagination>
                            </th>
                        </tr>
                    </tfoot>
                </table>

                <the-attribute-modal-form v-if="attributeModalOpts.show" v-bind:opts="attributeModalOpts" v-on:close-attribute-modal="onCloseAttributeModal"></the-attribute-modal-form>
                <the-delete-confirmation-modal v-bind:id="deleteAttributeId" v-if="showDeleteConfirmationModal" v-on:confirm-delete="onConfirmDelete" v-on:cancel-delete="onCancelDelete"></the-delete-confirmation-modal>

            </div>
        `;
    };

    var module = Vue.component('the-attribute-list', {
        template: template(),
        data: function () {
            return ({
                loading: false,
                pager: getPager(),
                attributes: [],
                sortBy: "name",
                sortOrder: "ASC",
                searchByName: null,
                searchByDescription: null,
                searchByType: "",
                attributeModalOpts: {
                    show: false,
                    type: null,
                    attributeId: null
                },
                types: [],
                deleteAttributeId: null,
                showDeleteConfirmationModal: false
            });
        },
        created: function () {
            this.getTypes();
            this.search(true);
            var self = this;
            this.pager.refresh = function () {
                self.search(false);
            }
        },
        watch: {
            searchByType: function () {
                this.search(true);
            }
        },
        computed: {
            exportOptions: function () {
                return (
                    {
                        name: 'attributes',
                        elements: this.attributes,
                        fields: ['id', 'name', 'description', 'typeId', 'typeName']
                    }
                );
            }
        },
        methods: {
            onShowAddAttributeModal: function () {
                this.attributeModalOpts.type = "add";
                this.attributeModalOpts.show = true;
            },
            onShowUpdateAttributeModal: function (attributeId) {
                this.attributeModalOpts.type = "update";
                this.attributeModalOpts.attributeId = attributeId;
                this.attributeModalOpts.show = true;
            },
            onShowDeleteAttributeModal: function (attributeId) {
                this.deleteAttributeId = attributeId;
                this.showDeleteConfirmationModal = true;
            },
            onCloseAttributeModal: function (withChanges) {
                this.attributeModalOpts = {
                    show: false,
                    type: null,
                    id: null
                };
                if (withChanges) {
                    this.search(false);
                }
            },
            onConfirmDelete: function (attributeId) {
                this.delete(attributeId);
            },
            onCancelDelete: function () {
                this.showDeleteConfirmationModal = false;
                this.deleteAttributeId = null;
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
                phpMPMApi.attribute.search(this.searchByType, this.searchByName, this.searchByDescription, self.pager.actualPage, self.pager.resultsPage, self.sortBy, self.sortOrder, function (response) {
                    self.loading = false;
                    if (response.ok) {
                        self.pager.actualPage = response.body.pagination.actualPage;
                        self.pager.totalPages = response.body.pagination.totalPages;
                        self.pager.totalResults = response.body.pagination.totalResults;
                        self.attributes = response.body.attributes;
                    } else {
                        self.$router.push({ name: 'the500' });
                    }
                });
            },
            delete(attributeId) {
                var self = this;
                self.loading = true;
                phpMPMApi.attribute.delete(attributeId, function (response) {
                    if (response.ok) {
                        self.showDeleteConfirmationModal = false;
                        self.deleteAttributeId = null;
                        self.search(false);
                    } else {
                        self.$router.push({ name: 'the500' });
                    }
                });
            },
            getTypes: function() {
                var self = this;
                self.loading = true;
                phpMPMApi.attribute.getTypes(function (response) {
                    self.loading = false;
                    if (response.ok) {
                        self.types = response.body.types;
                    } else {
                        self.$router.push({ name: 'the500' });
                    }
                });
            }
        }
    });

    return (module);
})();