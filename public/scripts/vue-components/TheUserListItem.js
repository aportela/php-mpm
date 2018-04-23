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
                        <p class="control">
                            <a class="button is-info is-small" v-bind:disabled="loading" v-on:click.prevent="$router.push({ name: 'theUserForm', params: { id: user.id} });">
                                <span class="icon is-small"><i class="fas fa-edit"></i></span>
                                <span>Update</span>
                            </a>
                        </p>
                        <p class="control">
                            <a class="button is-danger is-small" v-bind:disabled="disableRemove">
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
        props: [
            'loading',
            'user'
        ],
        filters: {
            jsonDate2Human(jsonDate) {
                return (moment(jsonDate, "YYYY-MM-DDTHH:mm:ss.SZ").fromNow());
            }
        },
        computed: {
            disableRemove: function() {
                return(initialState.session.user.id == this.user.id);
            }
        }
    });

    return (module);
})();