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

$.fn.clearValidationMessages = function() {
    $("input.input").removeClass("is-danger").removeClass("is-warning");
    $("span.help").remove();
}

$.fn.putValidationWarning = function(elementId, message) {
    var element = $("p#" + elementId);
    $(element).find("input.input").addClass("is-warning");
    $(element).append('<span class="help is-warning">' + message + '</span>');
}

$.fn.putValidationError = function(elementId, message) {
    var element = $("p#" + elementId);
    $(element).find("input.input").addClass("is-danger");
    $(element).append('<span class="help is-danger">' + message + '</span>');
}

$.fn.disableSubmit = function() {
    $("button[type=submit]").addClass("ajax_disabled").prop("disabled", true);
}

$.fn.enableSubmit = function() {
    $("button.ajax_disabled").removeClass("ajax_disabled").prop("disabled", false);
}

$("form#frm_signin").submit(function(e) {
    e.preventDefault();
    var self = $(this);
    self.clearValidationMessages();
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
                self.enableSubmit();
                switch (this.status) {
                    case 404:
                        self.putValidationWarning("c_email", "email not found");
                        break;
                    case 400:
                        // TODO
                        break;
                    case 200:
                        if (result === null) {
                            // TODO
                        } else {
                            if (!result.success) {
                                self.putValidationError("c_password", "invalid password");
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

function refreshUsersTable() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/api/user/search.php", true);
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
                    case 200:
                        if (result === null) {
                            // TODO: error
                            console.log(this.status);
                        } else {
                            var html = null;
                            if (result.results && result.results.length > 0) {
                                for (var i = 0; i < result.results.length; i++) {
                                    html += '<tr>';
                                    html += '<td>' + result.results[i].id + '</td>';
                                    html += '<td>' + result.results[i].email + '</td>';
                                    html += '</tr>';
                                }
                            }
                            $("table#users tbody").html(html);
                        }
                        break;
                    default:
                        // TODO: error
                        console.log(this.status);
                        break;
                }
            }
        }
    }
    var formData = new FormData();
    formData.append("page", 1);
    formData.append("resultsPage", 16);
    xhr.send(formData, null, 2);
}

function refreshGroupsTable() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/api/group/search.php", true);
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
                    case 200:
                        if (result === null) {
                            // TODO: error
                            console.log(this.status);
                        } else {
                            var html = null;
                            if (result.results && result.results.length > 0) {
                                for (var i = 0; i < result.results.length; i++) {
                                    html += '<tr>';
                                    html += '<td>' + result.results[i].id + '</td>';
                                    html += '<td>' + result.results[i].name + '</td>';
                                    html += '<td>' + result.results[i].description + '</td>';
                                    html += '</tr>';
                                }
                            }
                            $("table#groups tbody").html(html);
                        }
                        break;
                    default:
                        // TODO: error
                        console.log(this.status);
                        break;
                }
            }
        }
    }
    var formData = new FormData();
    formData.append("page", 1);
    formData.append("resultsPage", 16);
    xhr.send(formData, null, 2);
}

function refreshAttributesTable() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/api/attribute/search.php", true);
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
                    case 200:
                        if (result === null) {
                            // TODO: error
                            console.log(this.status);
                        } else {
                            var html = null;
                            if (result.results && result.results.length > 0) {
                                for (var i = 0; i < result.results.length; i++) {
                                    html += '<tr>';
                                    html += '<td>' + result.results[i].id + '</td>';
                                    html += '<td>' + result.results[i].name + '</td>';
                                    html += '<td>' + result.results[i].description + '</td>';
                                    html += '<td>';
                                    switch (parseInt(result.results[i].type)) {
                                        case 1:
                                            html += 'short text';
                                            break;
                                        case 2:
                                            html += 'long text';
                                            break;
                                        case 3:
                                            html += 'number integer';
                                            break;
                                        case 4:
                                            html += 'number decimal';
                                            break;
                                        case 5:
                                            html += 'date';
                                            break;
                                        case 6:
                                            html += 'time';
                                            break;
                                        case 7:
                                            html += 'datetime';
                                            break;
                                        default:
                                            html += 'none';
                                            break;
                                    }
                                    html += '</td>';
                                    html += '</tr>';
                                }
                            }
                            $("table#attributes tbody").html(html);
                        }
                        break;
                    default:
                        // TODO: error
                        console.log(this.status);
                        break;
                }
            }
        }
    }
    var formData = new FormData();
    formData.append("page", 1);
    formData.append("resultsPage", 16);
    xhr.send(formData, null, 2);
}

if ($("table#users").length == 1) {
    refreshUsersTable();
} else if ($("table#groups").length == 1) {
    refreshGroupsTable();
} else if ($("table#attributes").length == 1) {
    refreshAttributesTable();
}