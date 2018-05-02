const TheAttributeModalForm = (function () {
    "use strict";

    var template = function () {
        return `

            <div class="modal is-active">
                <div class="modal-background"></div>
                <div class="modal-card">
                    <form v-on:submit.prevent="save();">
                        <header class="modal-card-head">
                            <p class="modal-card-title" v-if="isAddForm">Add new attribute</p>
                            <p class="modal-card-title" v-if="isUpdateForm">Update existing attribute</p>
                            <button class="delete" aria-label="close" v-on:click.prevent="closeModal(false);"></button>
                        </header>
                        <section class="modal-card-body">
                            <div class="field">
                                <label for="name" class="label">Name</label>
                                <div class="control has-icons-left">
                                    <input class="input" name="name" ref="name" type="text" placeholder="Type attribute name" required v-bind:class="{ 'is-danger': validator.hasInvalidField('name') }" v-bind:disabled="loading" v-model.trim="attribute.name">
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-user"></i>
                                    </span>
                                </div>
                                <p class="help is-danger" v-if="validator.hasInvalidField('name')">{{ validator.getInvalidFieldMessage('name') }}</p>
                            </div>
                            <div class="field">
                                <label for="email" class="label">Description</label>
                                <div class="control has-icons-left">
                                    <input type="text" name="description" class="input" placeholder="Type attribute (optional) description" v-bind:class="{ 'is-danger': validator.hasInvalidField('description') }" v-bind:disabled="loading" v-model.trim="attribute.description">
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                </div>
                                <p class="help is-danger" v-if="validator.hasInvalidField('description')">{{ validator.getInvalidFieldMessage('description') }}</p>
                            </div>
                            <div class="field">
                                <label for="attributeType" class="label">Type</label>
                                <div class="control">
                                    <div class="select" name="attributeType">
                                        <select v-bind:disabled="isAttributeTypeDisabled" v-model="attribute.type">
                                            <option value="0" selected>Select type</option>
                                            <option v-for="type in types" v-bind:key="type.id" v-bind:value="type.id">{{ type.name }}</option>
                                        </select>
                                    </div>
                                </div>
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

    var module = Vue.component('the-attribute-modal-form', {
        template: template(),
        data: function () {
            return ({
                validator: getValidator(),
                loading: false,
                confirmedPassword: null,
                types: [],
                attribute: {
                    id: null,
                    name: null,
                    description: null,
                    type: 0
                }
            });
        },
        props: [
            'opts'
        ],
        created: function () {
            this.getTypes();
            if (this.opts.type == "add") {
                this.attribute.id = phpMPM.util.uuid();
                this.$nextTick(() => this.$refs.name.focus());
            } else if (this.opts.type == "update") {
                this.attribute.id = this.opts.attributeId;
                this.get(this.attribute.id);
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
            isAttributeTypeDisabled: function () {
                return (this.opts.type == "update");
            },
            isSaveDisabled: function () {
                return (!(this.attribute && this.attribute.id && this.attribute.name && this.attribute.type) || this.loading);
            },
            isCancelDisabled: function () {
                return (this.loading);
            },
        },
        methods: {
            closeModal: function (withChanges) {
                this.$emit("close-attribute-modal", withChanges);
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
            },
            get: function (id) {
                var self = this;
                self.loading = true;
                phpMPMApi.attribute.get(id, function (response) {
                    self.loading = false;
                    if (response.ok) {
                        self.attribute = response.body.attribute;
                    } else {
                        self.$router.push({ name: 'the500' });
                    }
                });
            },
            validate: function () {
                this.validator.clear();
                if (!this.attribute.name) {
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
                phpMPMApi.attribute.add(self.attribute, function (response) {
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
                phpMPMApi.attribute.update(self.attribute, function (response) {
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