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