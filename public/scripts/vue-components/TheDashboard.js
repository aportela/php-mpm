const TheDashboard = (function () {
    "use strict";

    var template = function () {
        return `
            <div>
                <div class="notification">
                    Welcome administrator
                </div>
                <div class="columns">
                    <div class="column">
                        <div class="notification is-primary">
                            <button class="delete"></button>
                            Primar lorem ipsum dolor sit amet, consectetur
                            adipiscing elit lorem ipsum dolor. <strong>Pellentesque risus mi</strong>, tempus quis placerat ut, porta nec nulla. Vestibulum rhoncus ac ex sit amet fringilla. Nullam gravida purus diam, et dictum <a>felis venenatis</a> efficitur. Sit amet,
                            consectetur adipiscing elit
                        </div>
                    </div>
                    <div class="column">
                        <div class="notification is-link">
                            <button class="delete"></button>
                            Primar lorem ipsum dolor sit amet, consectetur
                            adipiscing elit lorem ipsum dolor. <strong>Pellentesque risus mi</strong>, tempus quis placerat ut, porta nec nulla. Vestibulum rhoncus ac ex sit amet fringilla. Nullam gravida purus diam, et dictum <a>felis venenatis</a> efficitur. Sit amet,
                            consectetur adipiscing elit
                        </div>
                    </div>
                    <div class="column">
                        <div class="notification is-info">
                            <button class="delete"></button>
                            Primar lorem ipsum dolor sit amet, consectetur
                            adipiscing elit lorem ipsum dolor. <strong>Pellentesque risus mi</strong>, tempus quis placerat ut, porta nec nulla. Vestibulum rhoncus ac ex sit amet fringilla. Nullam gravida purus diam, et dictum <a>felis venenatis</a> efficitur. Sit amet,
                            consectetur adipiscing elit
                        </div>
                    </div>
                    <div class="column">
                        <div class="notification is-success">
                            <button class="delete"></button>
                            Primar lorem ipsum dolor sit amet, consectetur
                            adipiscing elit lorem ipsum dolor. <strong>Pellentesque risus mi</strong>, tempus quis placerat ut, porta nec nulla. Vestibulum rhoncus ac ex sit amet fringilla. Nullam gravida purus diam, et dictum <a>felis venenatis</a> efficitur. Sit amet,
                            consectetur adipiscing elit
                        </div>
                    </div>
                    <div class="column">
                        <div class="notification is-warning">
                            <button class="delete"></button>
                            Primar lorem ipsum dolor sit amet, consectetur
                            adipiscing elit lorem ipsum dolor. <strong>Pellentesque risus mi</strong>, tempus quis placerat ut, porta nec nulla. Vestibulum rhoncus ac ex sit amet fringilla. Nullam gravida purus diam, et dictum <a>felis venenatis</a> efficitur. Sit amet,
                            consectetur adipiscing elit
                        </div>
                    </div>
                    <div class="column">
                        <div class="notification is-danger">
                            <button class="delete"></button>
                            Primar lorem ipsum dolor sit amet, consectetur
                            adipiscing elit lorem ipsum dolor. <strong>Pellentesque risus mi</strong>, tempus quis placerat ut, porta nec nulla. Vestibulum rhoncus ac ex sit amet fringilla. Nullam gravida purus diam, et dictum <a>felis venenatis</a> efficitur. Sit amet,
                            consectetur adipiscing elit
                        </div>
                    </div>
                </div>
            </div>
        `;
    };

    var module = Vue.component('the-dashboard', {
        template: template(),
        data: function () {
            return ({
            });
        }
    });

    return (module);
})();