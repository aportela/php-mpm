const The500 = (function () {
    "use strict";

    var template = function () {
        return `
            <section class="hero is-fullheight is-light is-bold">
                <div class="hero-body">
                    <div class="container is-vcentered">
                        <div class="notification is-warning">
                            <h1 class="title is-1 has-text-centered">500 - SERVER ERROR</h1>
                            <h2 class="subtitle is-2 has-text-centered">Oops! something went wrong</h2>
                        </div>
                    </div>
                </div>
            </section>
        `;
    };

    var module = Vue.component('the-500', {
        template: template(),
        data: function () {
            return ({
            });
        }
    });

    return (module);
})();