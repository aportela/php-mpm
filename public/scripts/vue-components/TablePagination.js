const TablePagination = (function () {
    "use strict";

    var template = function () {
        return `
            <nav class="pagination is-right" role="navigation" aria-label="pagination">
                <a class="pagination-previous" v-bind:disabled="loading || this.data.actualPage <= 1" v-on:click.prevent="previous">Previous page</a>
                <a class="pagination-next" v-bind:disabled="loading || this.data.actualPage >= this.data.totalPages" v-on:click.prevent="next">Next page</a>
                <ul class="pagination-list">
                    <!--
                    <li><a class="pagination-link" aria-label="Goto page 1">1</a></li>
                    <li><span class="pagination-ellipsis">&hellip;</span></li>
                    <li><a class="pagination-link" aria-label="Goto page 45">45</a></li>
                    <li><a class="pagination-link is-current" aria-label="Page 46" aria-current="page">46</a></li>
                    <li><a class="pagination-link" aria-label="Goto page 47">47</a></li>
                    <li><span class="pagination-ellipsis">&hellip;</span></li>
                    <li><a class="pagination-link" aria-label="Goto page 86">86</a></li>
                    -->

                    <!-- vuejs pagination inspired by Jeff (https://stackoverflow.com/a/35706926) -->
                    <li v-for="pageNumber in data.totalPages" v-if="pageNumber < 3 || Math.abs(pageNumber - data.actualPage) < 3 || data.totalPages - 2 < pageNumber">
                        <a href="#" :disabled=loading v-on:click.prevent="navigateTo(pageNumber)" class="pagination-link" :class="{'is-current': data.actualPage === pageNumber}">{{ pageNumber }}</a>
                    </li>

                </ul>
                <div class="control has-icons-left">
                    <div class="select">
                    <select v-model.number="resultsPage">
                        <option value="16">16 results/page</option>
                        <option value="32">32 results/page</option>
                        <option value="64">64 results/page</option>
                        <option value="128">128 results/page</option>
                        <option value="256">256 results/page</option>
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
                resultsPage: initialState.defaultResultsPage
            });
        },
        props: [
            'data',
            'loading'
        ],
        created: function () {
            this.resultsPage = this.data.resultsPage;
        },
        computed: {
            visible: function () {
                return (this.data && this.data.totalPages > 1);
            },
            invalidPage: function () {
                return (this.data.totalPages > 0 &&
                    (this.data.actualPage < 1 || this.data.actualPage > this.data.totalPages)
                );
            }
        },
        watch: {
            resultsPage: function (v) {
                this.data.resultsPage = parseInt(v);
                this.data.refresh();
            }
        },
        methods: {
            previous: function () {
                if (this.data.actualPage > 1) {
                    this.data.actualPage--
                    this.data.refresh();
                }
            },
            next: function () {
                if (this.data.actualPage < this.data.totalPages) {
                    this.data.actualPage++
                    this.data.refresh();
                }
            },
            navigateTo: function (pageIdx) {
                if (pageIdx > 0 && pageIdx <= this.data.totalPages) {
                    this.data.actualPage = pageIdx;
                    this.data.refresh();
                }
            }
        }
    });

    return (module);
})();