"use strict";

var mpm = mpm || {};

mpm.form = mpm.form || {};

mpm.form.disableSubmit = function(form) {
    $(form).find('button[type="submit"]').prop("disabled", true);
};

mpm.form.enableSubmit = function(form) {
    $(form).find('button[type="submit"]').prop("disabled", false);
};

/**
 * clear all form (input fields) validation messages
 */
mpm.form.clearValidationMessages = function(form) {
    $(form).find("input.input").removeClass("is-danger").removeClass("is-warning");
    $(form).find("span.help").remove();
}

/**
 * put form (input field) validation warning message
 */
mpm.form.putValidationWarning = function(elementId, message) {
    var element = $("p#" + elementId);
    $(element).find("input.input").addClass("is-warning");
    $(element).append('<span class="help is-warning">' + message + '</span>');
}

/**
 * put form (input field) validation error message
 */
mpm.form.putValidationError = function(elementId, message) {
    var element = $("p#" + elementId);
    $(element).find("input.input").addClass("is-danger");
    $(element).append('<span class="help is-danger">' + message + '</span>');
}

/**
 * put form (input field) validation success message
 */
mpm.form.putValidationSuccess = function(elementId, message) {
    var element = $("p#" + elementId);
    $(element).append('<span class="help is-success">' + message + '</span>');
}


mpm.form.submit = function(form, callback) {
    mpm.form.disableSubmit();
    mpm.form.clearValidationMessages();
    var xhr = new XMLHttpRequest();
    xhr.open($(form).attr("method"), $(form).attr("action"), true);
    xhr.onreadystatechange = function(e) {
        if (this.readyState == 4) {
            mpm.form.enableSubmit();
            var result = null;
            try {
                result = JSON.parse(xhr.responseText);
            } catch (e) {
                console.groupCollapsed("Error parsing JSON response");
                console.log(e);
                console.log(xhr.responseText);
                console.groupEnd();
            } finally {
                callback(this.status, result);
            }
        }
    }
    xhr.send(new FormData($(form)[0]), null, 2);
};