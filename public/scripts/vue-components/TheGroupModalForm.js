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
                                    <li v-bind:class="{ 'is-active': isUserPermissionsTabActive }"><a v-on:click.prevent="changeTab('userPermissions');">User permissions ({{userPermissionCount}})</a></li>
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
                            <div v-show="isUserPermissionsTabActive">
                                <table class="table is-bordered is-striped is-narrow is-fullwidth is-unselectable">
                                    <thead>
                                        <tr>
                                            <th colspan="4">
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
                                                        <button type="submit" class="button is-info" v-bind:disabled="isAddUserPermissionDisabled" v-on:click.prevent="addUserGroup">Add to list</button>
                                                    </div>
                                                </div>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Name</th>
                                            <th class="has-text-centered">Allow view</th>
                                            <th class="has-text-centered">Allow modify</th>
                                            <th class="has-text-centered">Operations</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(userPermission, index) in group.userPermissions" v-bind:key="userPermission.user.id">
                                            <th>{{ userPermission.user.name }}</th>
                                            <th class="has-text-centered">
                                                <label class="checkbox">
                                                    <input type="checkbox" v-model="userPermission.privileges.allowView" disabled>
                                                </label>
                                            </th>
                                            <th class="has-text-centered">
                                                <label class="checkbox">
                                                    <input type="checkbox" v-model="userPermission.privileges.allowModify">
                                                </label>
                                            </th>
                                            <th>
                                                <p class="control is-expanded">
                                                    <a class="button is-small is-fullwidth is-outlined is-danger" v-bind:disabled="isRemoveDisabled" v-on:click.prevent="removeUserGroup(index);">
                                                        <span class="icon is-small"><i class="fas fa-trash"></i></span>
                                                        <span>Remove</span>
                                                    </a>
                                                </p>
                                            </th>
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
        data: function () {
            return ({
                validator: getValidator(),
                loading: false,
                tab: 'metadata',
                isUserListLoading: false,
                availableUsers: [],
                selectedUser: "",
                group: {
                    id: null,
                    name: null,
                    description: null,
                    userPermissions: []
                }
            });
        },
        props: [
            'opts'
        ],
        created: function () {
            this.getAvailableUsers();
            if (this.opts.type == "add") {
                this.group.id = phpMPM.util.uuid();
                this.$nextTick(() => this.$refs.name.focus());
            } else if (this.opts.type == "update") {
                this.group.id = this.opts.groupId;
                this.get(this.group.id);
            } else {
                this.$router.push({ name: 'the500' });
            }
        },
        computed: {
            isMetadataTabActive: function () {
                return (this.tab == 'metadata');
            },
            isUserPermissionsTabActive: function () {
                return (this.tab == 'userPermissions');
            },
            isAddForm: function () {
                return (this.opts.type == "add");
            },
            isUpdateForm: function () {
                return (this.opts.type == "update");
            },
            isSaveDisabled: function () {
                return (!(this.group && this.group.id && this.group.name) || this.loading);
            },
            isCancelDisabled: function () {
                return (this.loading);
            },
            isAddUserPermissionDisabled: function() {
                if (this.selectedUser) {
                    return(this.group.userPermissions.findIndex(permission => permission.user.id == this.selectedUser.id) >= 0);
                } else {
                    return(true);
                }
            },
            isRemoveDisabled: function () {
                return (this.loading);
            },
            userPermissionCount: function () {
                return (this.group.userPermissions ? this.group.userPermissions.length : 0);
            }
        },
        methods: {
            changeTab: function (name) {
                if (this.tab != name) {
                    this.tab = name;
                }
            },
            closeModal: function (withChanges) {
                this.$emit("close-group-modal", withChanges);
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
                this.group.userPermissions.push(
                    {
                        user: this.selectedUser,
                        privileges: {
                            allowView: true,
                            allowModify: true
                        }
                    }
                );
            },
            removeUserGroup: function (index) {
                this.group.userPermissions.splice(index, 1);
            },
            validate: function () {
                this.validator.clear();
                if (!this.group.name) {
                    this.validator.setInvalid("name", "Invalid name");
                }
                return (!this.validator.hasInvalidFields());
            },
            save: function () {
                if (this.validate()) {
                    if (this.isAddForm) {
                        this.add();
                    } else {
                        this.update();
                    }
                }
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