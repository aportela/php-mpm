const TheGroupModalForm = (function () {
    "use strict";

    var template = function () {
        return `

            <div class="modal is-active">
                <div class="modal-background"></div>
                <div class="modal-card">
                    <form v-on:submit.prevent="save();">
                        <header class="modal-card-head">
                            <p class="modal-card-title" v-if="isAddForm">Add new group</p>
                            <p class="modal-card-title" v-if="isUpdateForm">Update existing group</p>
                            <button class="delete" aria-label="close" v-on:click.prevent="closeModal(false);"></button>
                        </header>
                        <section class="modal-card-body">

                            <div class="tabs">
                                <ul>
                                    <li v-bind:class="{ 'is-active': isMetadataTabActive }"><a v-on:click.prevent="changeTab('metadata');">Metadata</a></li>
                                    <li v-bind:class="{ 'is-active': isUsersTabActive }"><a v-on:click.prevent="changeTab('users');">Users ({{ userCount }})</a></li>
                                </ul>
                            </div>
                            <div v-show="isMetadataTabActive">
                                <div class="field">
                                    <label for="name" class="label">Group name</label>
                                    <div class="control has-icons-left">
                                        <input class="input" name="name" ref="name" type="text" placeholder="Type group name" required v-bind:class="{ 'is-danger': validator.hasInvalidField('name') }" v-bind:disabled="loading" v-model.trim="group.name">
                                        <span class="icon is-small is-left">
                                            <i class="fas fa-users"></i>
                                        </span>
                                    </div>
                                    <p class="help is-danger" v-if="validator.hasInvalidField('name')">{{ validator.getInvalidFieldMessage('name') }}</p>
                                </div>
                                <div class="field">
                                    <label for="description" class="label">Description</label>
                                    <div class="control has-icons-left">
                                        <input type="text" name="description" class="input" placeholder="Type group description (optional)" v-bind:class="{ 'is-danger': validator.hasInvalidField('description') }" v-bind:disabled="loading" v-model.trim="group.description">
                                        <span class="icon is-small is-left">
                                            <i class="fas fa-info"></i>
                                        </span>
                                    </div>
                                    <p class="help is-danger" v-if="validator.hasInvalidField('description')">{{ validator.getInvalidFieldMessage('description') }}</p>
                                </div>
                            </div>
                            <div v-show="isUsersTabActive">
                                <table class="table is-bordered is-striped is-narrow is-fullwidth is-unselectable">
                                    <thead>
                                        <tr>
                                            <th colspan="2">
                                                <div class="field has-addons">
                                                    <p class="control">
                                                        <a class="button is-static">Choose user</a>
                                                    </p>
                                                    <div class="control is-expanded">
                                                        <div class="select is-fullwidth">
                                                            <select name="user" v-bind:class="{ 'is-loading': isUserListLoading }" v-model="selectedUser">
                                                                <option value="">select an user</option>
                                                                <option v-for="user in availableUsers" v-bind:value="user">{{ user.name }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="control">
                                                        <button type="submit" class="button is-info" v-bind:disabled="isAddUserDisabled" v-on:click.prevent="addUserGroup">Add to list</button>
                                                    </div>
                                                </div>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Name</th>
                                            <th class="has-text-centered">Operations</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(user, index) in group.users" v-bind:key="user.id">
                                            <td>{{ user.name }}</td>
                                            <td>
                                                <p class="control is-expanded">
                                                    <a class="button is-small is-fullwidth is-outlined is-danger" v-bind:disabled="isRemoveDisabled" v-on:click.prevent="removeUserGroup(index);">
                                                        <span class="icon is-small"><i class="fas fa-trash"></i></span>
                                                        <span>Remove</span>
                                                    </a>
                                                </p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                        <footer class="modal-card-foot">
                            <div class="field is-grouped">
                                <div class="control">
                                    <button class="button is-link" type="submit" v-bind:disabled="isSaveDisabled">
                                        <span class="icon"><i class="fa fa-check-circle"></i></span>
                                        <span>Save changes</span>
                                    </button>
                                </div>
                                <div class="control">
                                    <button class="button is-default" type="button" v-bind:disabled="isCancelDisabled" v-on:click.prevent="closeModal(false);">
                                        <span class="icon"><i class="fa fa-ban"></i></span>
                                        <span>Cancel</span>
                                    </button>
                                </div>
                            </div>
                            <div class="is-clearfix">
                            </div>
                        </footer>
                    </form>
                </div>
            </div>

        `;
    };

    var module = Vue.component('the-group-modal-form', {
        template: template(),
        mixins: [ mixinModalAdminEntities],
        data: function () {
            return ({
                loading: false,
                tab: 'metadata',
                isUserListLoading: false,
                availableUsers: [],
                selectedUser: "",
                group: {
                    id: null,
                    name: null,
                    description: null,
                    users: []
                }
            });
        },
        created: function () {
            this.getAvailableUsers();
            if (this.opts.type == "add") {
                this.group.id = phpMPM.util.uuid();
                this.$nextTick(() => this.$refs.name.focus());
            } else if (this.opts.type == "update") {
                this.group.id = this.opts.id;
                this.get(this.group.id);
            } else {
                this.$router.push({ name: 'the500' });
            }
        },
        computed: {
            isMetadataTabActive: function () {
                return (this.tab == 'metadata');
            },
            isUsersTabActive: function () {
                return (this.tab == 'users');
            },
            isSaveDisabled: function () {
                return (!(this.group && this.group.id && this.group.name) || this.loading);
            },
            isCancelDisabled: function () {
                return (this.loading);
            },
            isAddUserDisabled: function () {
                if (this.selectedUser) {
                    return (this.group.users.findIndex(user => user.id == this.selectedUser.id) >= 0);
                } else {
                    return (true);
                }
            },
            isRemoveDisabled: function () {
                return (this.loading);
            },
            userCount: function () {
                return (this.group.users ? this.group.users.length : 0);
            }
        },
        methods: {
            changeTab: function (name) {
                if (this.tab != name) {
                    this.tab = name;
                }
            },
            getAvailableUsers: function () {
                var self = this;
                self.loading = true;
                phpMPMApi.user.search(null, null, null, 1, 0, "name", "ASC", function (response) {
                    self.loading = false;
                    if (response.ok) {
                        self.availableUsers = response.body.users;
                    } else {
                        self.$router.push({ name: 'the500' });
                    }
                });
            },
            get: function (id) {
                var self = this;
                self.loading = true;
                phpMPMApi.group.get(id, function (response) {
                    self.loading = false;
                    if (response.ok) {
                        self.group = response.body.group;
                    } else {
                        self.$router.push({ name: 'the500' });
                    }
                });
            },
            addUserGroup: function () {
                this.group.users.push(this.selectedUser);
            },
            removeUserGroup: function (index) {
                this.group.users.splice(index, 1);
            },
            validate: function () {
                this.validator.clear();
                if (!this.group.name) {
                    this.validator.setInvalid("name", "Invalid name");
                }
                return (!this.validator.hasInvalidFields());
            },
            add: function () {
                var self = this;
                self.loading = true;
                phpMPMApi.group.add(self.group, function (response) {
                    if (response.ok) {
                        self.closeModal(true);
                    } else {
                        switch (response.status) {
                            case 409:
                                if (response.isFieldInvalid("name")) {
                                    self.validator.setInvalid("name", "Selected name is used (choose another)");
                                }
                                if (!self.validator.hasInvalidFields()) {
                                    self.$router.push({ name: 'the500' });
                                }
                                break;
                            default:
                                self.$router.push({ name: 'the500' });
                                break;
                        }
                    }
                    self.loading = false;
                });
            },
            update: function () {
                var self = this;
                self.loading = true;
                phpMPMApi.group.update(self.group, function (response) {
                    if (response.ok) {
                        self.closeModal(true);
                    } else {
                        switch (response.status) {
                            case 409:
                                if (response.isFieldInvalid("name")) {
                                    self.validator.setInvalid("name", "Selected name is used (choose another)");
                                }
                                if (!self.validator.hasInvalidFields()) {
                                    self.$router.push({ name: 'the500' });
                                }
                                break;
                            default:
                                self.$router.push({ name: 'the500' });
                                break;
                        }
                    }
                    self.loading = false;
                });
            }
        }
    });

    return (module);
})();