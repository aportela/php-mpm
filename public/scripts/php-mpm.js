"use strict";

var phpMPM = phpMPM || {};

phpMPM.util = phpMPM.util || {};

/**
 * uuid generator
 *
 * (broofa) http://stackoverflow.com/a/2117523
 */
phpMPM.util.uuid = function () {
    var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        var r = Math.random() * 16 | 0,
            v = c == 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
    return (uuid);
}

/**
 * simple json/csv exporter
 * @param {*} name name of exported file (format will be appended)
 * @param {*} elements collection of elements to export
 * @param {*} opts export options
 */
phpMPM.util.export = function (name, elements, opts) {
    if (opts.format == "json") {
        let data = [];
        data.push(opts.fields);
        for (let i = 0; i < elements.length; i++) {
            let o = {};
            for (let j = 0; j < opts.fields.length; j++) {
                o[opts.fields[j]] = elements[i][opts.fields[j]];
            }
            data.push(o);
        }
        saveAs(new Blob([JSON.stringify(data)], { type: "application/json; charset=utf-8" }), name + ".json");
        return (true);
    } else if (opts.format == "csv") {
        let escapeValue = function (value) {
            // borrowed some ideas from
            // (Xavier John) http://stackoverflow.com/a/24922761
            value = value.replace(/"/g, '""');
            if (value.search(/("|,|\n)/g) >= 0) {
                value = '"' + value + '"';
            }
            return (value);
        };
        let data = '<xml><' + name + '>';
        let fields = [];
        for (let j = 0; j < opts.fields.length; j++) {
            var row = '<element>';
            fields.push(escapeValue(opts.fields[j]));
        }
        data += fields.join(", ") + "\n";

        for (let i = 0; i < elements.length; i++) {
            fields = [];
            for (let j = 0; j < opts.fields.length; j++) {
                fields.push(elements[i][opts.fields[j]]);
            }
            data += fields.join(", ") + "\n";
        }
        saveAs(new Blob([data], { type: "text/csv; charset=utf-8" }), name + ".csv");
        return (true);
    } else {
        return (false);
    }
}
