"use strict";


/**
 * show top right static loading icon while xhr (ajax) events
 * (John Culviner) http://stackoverflow.com/a/27363569
 */

var $loading = $('i#ajax_icon').hide();

(function() {
    var origOpen = XMLHttpRequest.prototype.open;
    XMLHttpRequest.prototype.sopen = function() {
        $loading.show();
        this.addEventListener('load', function() {
            $loading.hide();
        });
        origOpen.apply(this, arguments);
    };
})();

/**
 * clear all form (input fields) validation messages
 */
$.fn.clearFormValidationMessages = function() {
    $("input.input").removeClass("is-danger").removeClass("is-warning");
    $("span.help").remove();
}

/**
 * put form (input field) validation warning message
 */
$.fn.putFormValidationWarning = function(elementId, message) {
    var element = $("p#" + elementId);
    $(element).find("input.input").addClass("is-warning");
    $(element).append('<span class="help is-warning">' + message + '</span>');
}

/**
 * put form (input field) validation error message
 */
$.fn.putFormValidationError = function(elementId, message) {
    var element = $("p#" + elementId);
    $(element).find("input.input").addClass("is-danger");
    $(element).append('<span class="help is-danger">' + message + '</span>');
}

/**
 * put form (input field) validation success message
 */
$.fn.putFormValidationSuccess = function(elementId, message) {
    var element = $("p#" + elementId);
    $(element).append('<span class="help is-success">' + message + '</span>');
}

/**
 * disable form (button type="submit") submit
 */
$.fn.disableSubmit = function() {
    $(this).find('button[type="submit"]').prop("disabled", true);
}

/**
 * enable form (button type="submit") submit
 */
$.fn.enableSubmit = function() {
    $(this).find('button[type="submit"]').prop("disabled", false);
}