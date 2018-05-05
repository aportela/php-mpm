/**
 * vuejs mixin for parsing dates with momentjs
 */
const mixinDateTime = {
    filters: {
        jsonDate2Human(jsonDate) {
            return (moment(jsonDate, "YYYY-MM-DDTHH:mm:ss.SZ").fromNow());
        }
    }
};

/**
 * vuejs mixin for common admin manage entities
 */
const mixinManageAdminEntities = {
    data: function () {
        return ({
            pager: getPager(),
            items: [],
            sortOrder: "ASC",
            showDeleteConfirmationModal: false,
            deleteItemId: null,
            itemModalOpts: {
                show: false,
                type: null,
                id: null
            }
        });
    },
    created: function () {
        this.search(true);
        var self = this;
        this.pager.refresh = function () {
            self.search(false);
        }
    },
    methods: {
        onShowAddModal: function () {
            this.itemModalOpts.type = "add";
            this.itemModalOpts.show = true;
        },
        onShowUpdateModal: function (itemId) {
            this.itemModalOpts.type = "update";
            this.itemModalOpts.id = itemId;
            this.itemModalOpts.show = true;
        },
        onShowDeleteModal: function (itemId) {
            this.deleteItemId = itemId;
            this.showDeleteConfirmationModal = true;
        },
        onCloseItemModal: function (withChanges) {
            this.itemModalOpts = {
                show: false,
                type: null,
                id: null
            };
            if (withChanges) {
                this.search(false);
            }
        },
        onConfirmDelete: function (itemId) {
            this.delete(itemId);
        },
        onCancelDelete: function () {
            this.showDeleteConfirmationModal = false;
            this.deleteItemId = null;
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
        }
    }
}

/**
 * vuejs mixin for common modal admin entities
 */
const mixinModalAdminEntities = {
    data: function () {
        return ({
            validator: getValidator(),
        });
    },
    props: [
        'opts'
    ],
    computed: {
        isAddForm: function () {
            return (this.opts.type == "add");
        },
        isUpdateForm: function () {
            return (this.opts.type == "update");
        }
    }, methods: {
        closeModal: function (withChanges) {
            this.$emit("close-item-modal", withChanges);
        },
        save: function () {
            if (this.validate()) {
                if (this.isAddForm) {
                    this.add();
                } else {
                    this.update();
                }
            }
        }
    }
};