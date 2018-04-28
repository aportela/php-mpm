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
                confirmedPassword: null,
                group: {
                    id: null,
                    name: null,
                    description: null
                }
            });
        },
        props: [
            'opts'
        ],
        created: function () {
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
        },
        methods: {
            closeModal: function (withChanges) {
                this.$emit("close-group-modal", withChanges);
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