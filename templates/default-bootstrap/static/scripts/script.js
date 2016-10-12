$.fn.clearValidationErrors = function() {
    $("div.form-group").removeClass("has-danger");
    $("div.form-group .form-control-danger").removeClass("form-control-danger");
    $("div.form-group div.form-control-feedback").remove();
}

$.fn.putValidationError = function(elementId, message) {
    var element = $("div#" + elementId);
    $(element).append('<div class="form-control-feedback">' + message + '</div>');
    $(element).find("input").addClass('form-control-danger');
    $(element).addClass("has-danger");
}

$("form#frm_signin").submit(function(e) {
    e.preventDefault();
    var self = $(this);
    self.clearValidationErrors();
    var xhr = new XMLHttpRequest();
    xhr.open($(this).attr("method"), $(this).attr("action"), true);
    xhr.onreadystatechange = function(e) {
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
                switch (this.status) {
                    case 404:
                        self.putValidationError("fg_email", "email not found");
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
});

$("a#logout").click(function(e) {
    e.preventDefault();
    var xhr = new XMLHttpRequest();
    xhr.open("GET", $(this).attr("href"), true);
    xhr.onreadystatechange = function(e) {
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