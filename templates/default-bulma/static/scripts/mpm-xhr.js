"use strict";

var mpm = mpm || {};

mpm.form = mpm.form || {};

mpm.form.submit = function (form, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open($(form).attr("method"), $(form).attr("action"), true);
    xhr.onreadystatechange = function (e) {
        if (this.readyState == 4) {
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