const TableExportButton = (function () {
    "use strict";

    var template = function () {
        return `
            <div class="field has-addons">
                <p class="control">
                    <span class="select is-small">
                        <select v-model="format">
                            <option value="">select format</option>
                            <option value="json">json</option>
                            <option value="csv">csv</option>
                        </select>
                    </span>
                </p>
                <p class="control is-expanded">
                    <a class="button is-small is-fullwidth is-warning" v-bind:disabled="isExportDisabled" v-on:click.prevent="exportData();">
                        <span class="icon is-small"><i class="fas fa-database"></i></span>
                        <span>Export</span>
                    </a>
                </p>
            </div>
        `;
    };

    var module = Vue.component('table-export-button', {
        template: template(),
        data: function () {
            return ({
                format: ""
            });
        },
        props: [
            'loading',
            'configuration'
        ],
        computed: {
            isExportDisabled: function () {
                return (this.format == "" || this.loading);
            }
        },
        methods: {
            exportData: function () {
                if (!this.isExportDisabled) {
                    phpMPM.util.export(this.configuration.name, this.configuration.elements, { format: this.format, fields: this.configuration.fields });
                }
            }
        }
    });

    return (module);
})();