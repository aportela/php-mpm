const TheAppLayout = (function () {
    "use strict";

    var template = function () {
        return `
            <div>
                <the-app-layout-header></the-app-layout-header>
                <section class="section">
                    <div class="container-fluid">
                        <keep-alive>
                            <router-view></router-view>
                        </keep-alive>
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