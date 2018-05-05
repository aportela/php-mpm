const TheGroupListItem = (function () {
    "use strict";

    var template = function () {
        return `
            <tr>
                <td>{{ group.name }}</td>
                <td>{{ group.description }}</td>
                <td class="has-text-right">{{ group.userCount }}</td>
                <td>{{ group.created | jsonDate2Human }}</td>
                <td>
                    <div class="field is-grouped">
                        <p class="control is-expanded">
                            <a class="button is-small is-fullwidth is-outlined is-info" v-bind:disabled="loading" v-on:click.prevent="updateGroup(group.id);">
                                <span class="icon is-small"><i class="fas fa-edit"></i></span>
                                <span>Update</span>
                            </a>
                        </p>
                        <p class="control is-expanded">
                            <a class="button is-small is-fullwidth is-outlined is-danger" v-bind:disabled="disableRemove" v-on:click.prevent="deleteGroup(group.id);">
                                <span class="icon is-small"><i class="fas fa-trash"></i></span>
                                <span>Remove</span>
                            </a>
                        </p>
                    </div>
                </td>
            </tr>
        `;
    };

    var module = Vue.component('the-group-list-item', {
        template: template(),
        mixins: [ mixinDateTime ],
        props: [
            'loading',
            'group'
        ],
        computed: {
            disableRemove: function () {
                return(false);
            }
        },
        methods: {
            updateGroup: function (id) {
                this.$emit('show-update-group-modal', id);
            },
            deleteGroup: function (id) {
                if (!this.disableRemove) {
                    this.$emit('show-delete-group-modal', id);
                }
            }
        }
    });

    return (module);
})();