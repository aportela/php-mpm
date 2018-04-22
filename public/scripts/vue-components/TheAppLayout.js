const TheAppLayout = (function () {
    "use strict";

    var template = function () {
        return `
            <div>
                <the-app-layout-header></the-app-layout-header>
                <section class="section">
                    <div class="container-fluid">
                        <transition name="fade">
                            <router-view></router-view>
                        </transition>
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