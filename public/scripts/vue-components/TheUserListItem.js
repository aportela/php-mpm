const TheUserListItem = (function () {
    "use strict";

    var template = function () {
        return `
            <tr>
                <td v-if="user.accountType == 'A'">
                    <span class="icon"><i class="fas fa-user"></i></span>
                    <span>administrator</span>
                </td>
                <td v-else>
                    <span class="icon"><i class="far fa-user"></i></span>
                    <span>normal user</span>
                </td>
                <td>{{ user.name }}</td>
                <td>{{ user.email }}</td>
                <td>{{ user.created | jsonDate2Human }}</td>
                <td>
                    <div class="field is-grouped">
                        <p class="control is-expanded">
                            <a class="button is-small is-fullwidth is-outlined is-info" v-bind:disabled="loading" v-on:click.prevent="updateUser(user.id);">
                                <span class="icon is-small"><i class="fas fa-edit"></i></span>
                                <span>Update</span>
                            </a>
                        </p>
                        <p class="control is-expanded">
                            <a class="button is-small is-fullwidth is-outlined is-danger" v-bind:disabled="disableRemove" v-on:click.prevent="deleteUser(user.id);">
                                <span class="icon is-small"><i class="fas fa-trash"></i></span>
                                <span>Remove</span>
                            </a>
                        </p>
                    </div>
                </td>
            </tr>
        `;
    };

    var module = Vue.component('the-user-list-item', {
        template: template(),
        mixins: [ mixinDateTime ],
        props: [
            'loading',
            'user'
        ],
        computed: {
            disableRemove: function () {
                return (initialState.session.user.id == this.user.id);
            }
        },
        methods: {
            updateUser: function (id) {
                this.$emit('show-update-user-modal', id);
            },
            deleteUser: function (id) {
                if (!this.disableRemove) {
                    this.$emit('show-delete-user-modal', id);
                }
            }
        }
    });

    return (module);
})();