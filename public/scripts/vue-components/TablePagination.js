const TablePagination = (function () {
    "use strict";

    var template = function () {
        return `

        <nav class="pagination is-right" role="navigation" aria-label="pagination">
            <a class="pagination-previous">Previous page</a>
            <a class="pagination-next">Next page</a>
            <ul class="pagination-list">
                <li><a class="pagination-link" aria-label="Goto page 1">1</a></li>
                <li><span class="pagination-ellipsis">&hellip;</span></li>
                <li><a class="pagination-link" aria-label="Goto page 45">45</a></li>
                <li><a class="pagination-link is-current" aria-label="Page 46" aria-current="page">46</a></li>
                <li><a class="pagination-link" aria-label="Goto page 47">47</a></li>
                <li><span class="pagination-ellipsis">&hellip;</span></li>
                <li><a class="pagination-link" aria-label="Goto page 86">86</a></li>
            </ul>
            <div class="control has-icons-left">
                <div class="select">
                <select>
                    <option selected>32 results/page</option>
                    <option>64 results/page</option>
                    <option>128 results/page</option>
                    <option>256 results/page</option>
                    <option>All results</option>
                </select>
                </div>
                <span class="icon is-medium is-left">
                <i class="fas fa-list-ol"></i>
                </span>
            </div>
      </nav>


        `;
    };

    var module = Vue.component('table-pagination', {
        template: template(),
        data: function () {
            return ({
            });
        }
    });

    return (module);
})();