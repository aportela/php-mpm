/**
 * show top right static loading icon while xhr (ajax) events
 * (John Culviner) http://stackoverflow.com/a/27363569
 */
var $loading = $('i#ajax_icon').hide();

(function () {
    var origOpen = XMLHttpRequest.prototype.open;
    XMLHttpRequest.prototype.open = function () {
        $loading.show();
        this.addEventListener('load', function () {
            $loading.hide();
        });
        origOpen.apply(this, arguments);
    };
})();

$.fn.clearValidationMessages = function () {
    $("div.form-group").removeClass("has-danger").removeClass("has-warning");
    $("div.form-group .form-control-danger").removeClass("form-control-danger").removeClass("form-control-warning");
    $("div.form-group div.form-control-feedback").remove();
}

$.fn.putValidationWarning = function (elementId, message) {
    var element = $("div#" + elementId);
    $(element).append('<div class="form-control-feedback">' + message + '</div>');
    $(element).find("input").addClass('form-control-warning');
    $(element).addClass("has-warning");
}

$.fn.putValidationError = function (elementId, message) {
    var element = $("div#" + elementId);
    $(element).append('<div class="form-control-feedback">' + message + '</div>');
    $(element).find("input").addClass('form-control-danger');
    $(element).addClass("has-danger");
}

$.fn.disableSubmit = function () {
    $("button[type=submit]").addClass("ajax_disabled").prop("disabled", true);
}

$.fn.enableSubmit = function () {
    $("button.ajax_disabled").removeClass("ajax_disabled").prop("disabled", false);
}

$("form#frm_signin").submit(function (e) {
    e.preventDefault();
    var self = $(this);
    self.clearValidationMessages();
    var xhr = new XMLHttpRequest();
    xhr.open($(this).attr("method"), $(this).attr("action"), true);
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
                self.enableSubmit();
                switch (this.status) {
                    case 404:
                        self.putValidationWarning("fg_email", "email not found");
                        break;
                    case 400:
                        // TODO
                        break;
                    case 200:
                        if (result === null) {
                            // TODO
                        } else {
                            if (!result.success) {
                                self.putValidationError("fg_password", "invalid password");
                            } else {
                                window.location.reload();
                            }
                        }
                        break;
                    default:
                        // TODO
                        break;
                }
            }
        }
    };
    xhr.send(new FormData($(this)[0]), null, 2);
    self.disableSubmit();
});

$("a#logout").click(function (e) {
    e.preventDefault();
    var xhr = new XMLHttpRequest();
    xhr.open("GET", $(this).attr("href"), true);
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
                window.location.reload();
            }
        }
    };
    xhr.send();
});