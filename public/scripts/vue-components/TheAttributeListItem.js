const TheAttributeListItem = (function () {
    "use strict";

    var template = function () {
        return `
            <tr>
                <td>{{ attribute.name }}</td>
                <td>{{ attribute.description }}</td>
                <td>{{ attribute.typeName }}</td>
                <td>{{ attribute.created | jsonDate2Human }}</td>
                <td>
                    <div class="field is-grouped">
                        <p class="control is-expanded">
                            <a class="button is-small is-fullwidth is-outlined is-info" v-bind:disabled="loading" v-on:click.prevent="updateAttribute(attribute.id);">
                                <span class="icon is-small"><i class="fas fa-edit"></i></span>
                                <span>Update</span>
                            </a>
                        </p>
                        <p class="control is-expanded">
                            <a class="button is-small is-fullwidth is-outlined is-danger" v-bind:disabled="disableRemove" v-on:click.prevent="deleteAttribute(attribute.id);">
                                <span class="icon is-small"><i class="fas fa-trash"></i></span>
                                <span>Remove</span>
                            </a>
                        </p>
                    </div>
                </td>
            </tr>
        `;
    };

    var module = Vue.component('the-attribute-list-item', {
        template: template(),
        props: [
            'loading',
            'attribute'
        ],
        filters: {
            jsonDate2Human(jsonDate) {
                return (moment(jsonDate, "YYYY-MM-DDTHH:mm:ss.SZ").fromNow());
            }
        },
        computed: {
            disableRemove: function () {
                return(false);
            }
        },
        methods: {
            updateAttribute: function (id) {
                this.$emit('show-update-attribute-modal', id);
            },
            deleteAttribute: function (id) {
                if (!this.disableRemove) {
                    this.$emit('show-delete-attribute-modal', id);
                }
            }
        }
    });

    return (module);
})();