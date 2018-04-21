const TheAppLayout = (function () {
    "use strict";

    var template = function () {
        return `
            <div>
                <the-app-layout-header></the-app-layout-header>
                    <section class="section">
                        <div class="container-fluid">
                            <div class="columns">
                                <div class="column is-2">
                                    <the-app-layout-sidebar-menu></the-app-layout-sidebar-menu>
                                </div>
                                <div class="column is-10">
                                </div>
                            </div>
                        </div>
                    </section>

            </div>
        `;
    };

    var module = Vue.component('the-app-layout', {
        template: template(),
        data: function () {
            return ({
            });
        }
    });

    return (module);
})();