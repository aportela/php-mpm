const TheDeleteConfirmationModal = (function () {
    "use strict";

    var template = function () {
        return `
            <div class="modal is-active">
                <div class="modal-background"></div>
                <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">Confirmation required</p>
                    <button class="delete" aria-label="close" v-on:click.prevent="cancelDelete();"></button>
                </header>
                <section class="modal-card-body">
                    Are you sure you want to permanently remove this item ?
                </section>
                <footer class="modal-card-foot">
                    <button class="button is-danger" v-on:click.prevent="confirmDelete();">Ok</button>
                    <button class="button" v-on:click.prevent="cancelDelete();">Cancel</button>
                </footer>
                </div>
            </div>
        `;
    };

    var module = Vue.component('the-delete-confirmation-modal', {
        template: template(),
        props: [
            'id'
        ],
        methods: {
            confirmDelete: function () {
                this.$emit('confirm-delete', this.id);
            },
            cancelDelete: function () {
                this.$emit('cancel-delete');
            }
        }
    });

    return (module);
})();