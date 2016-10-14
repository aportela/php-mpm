"use strict";


/**
 * show top right static loading icon while xhr (ajax) events
 * (John Culviner) http://stackoverflow.com/a/27363569
 */

var $loading = $('i#ajax_icon').hide();

(function() {
    var origOpen = XMLHttpRequest.prototype.open;
    XMLHttpRequest.prototype.open = function() {
        $loading.show();
        this.addEventListener('load', function() {
            $loading.hide();
        });
        origOpen.apply(this, arguments);
    };
})();


