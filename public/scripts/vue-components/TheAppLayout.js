const TheAppLayout = (function () {
    "use strict";

    var template = function () {
        return `
            <div>
                <the-app-layout-header></the-app-layout-header>
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