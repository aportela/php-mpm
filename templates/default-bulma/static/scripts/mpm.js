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
    mpm.form.disableSubmit(form);
    mpm.form.clearValidationMessages(form);
    var xhr = new XMLHttpRequest();
    xhr.open($(form).attr("method"), $(form).attr("action"), true);
    xhr.onreadystatechange = function(e) {
        if (this.readyState == 4) {
            mpm.form.enableSubmit(form);
            var response = null;
            try {
                response = JSON.parse(xhr.responseText);
            } catch (e) {
                console.groupCollapsed("Error parsing JSON response");
                console.log(e);
                console.log(xhr.responseText);
                console.groupEnd();
            } finally {
                callback(this.status, response);
            }
        }
    }
    xhr.send(new FormData($(form)[0]), null, 2);
};

mpm.pagination = mpm.pagination || {};

mpm.pagination.setControls = function(actualPage, totalPages) {
    $(".pager_actual_page").text(actualPage);
    $(".pager_total_pages").text(totalPages);
    if (actualPage < totalPages) {
        $(".btn_next_page").removeClass("is-disabled");
    } else {
        $(".btn_next_page").addClass("is-disabled");
    }
    if (actualPage > 1) {
        $(".btn_previous_page").removeClass("is-disabled");
    } else {
        $(".btn_previous_page").addClass("is-disabled");
    }
}

mpm.error = mpm.error || {};

mpm.error.getStackTrace = function() {
    var stackTrace = null;
    try {
        throw new Error();
    } catch (e) {
        stackTrace = e.stack;
    }
    return (stackTrace);
};

mpm.error.showModal = function() {
    $("div#stack_trace").text(mpm.error.getStackTrace());
    $('html').addClass('is-clipped');
    $("div#modal_general_error").addClass('is-active');
}

mpm.data = mpm.data || {};

// TODO: escape chars!!!
mpm.data.tableExport = function(table, format) {
    var tableName = $(table).attr("id");
    var collectionName = tableName || "rows";
    if (!tableName) {
        tableName = "mpm-table-export-" + (new Date()).toISOString().slice(0, 10).replace(/-/g, "");
    } else {
        tableName += "-" + (new Date()).toISOString().slice(0, 10).replace(/-/g, "")
    }
    // get real cell value (removing icons)
    var getCellText = function(element) {
        var cloned = $(element).clone();
        $(cloned).children("i.fa").remove();
        return ($(cloned).text().trim());
    };
    if (format === "json") {
        var fields = [];
        $(table).find("thead tr:last th").each(function(i) {
            if (!$(this).hasClass("ignore_on_export")) {
                fields.push(getCellText($(this)));
            }
        });
        var data = {};
        data[collectionName] = [];
        $(table).find("tbody tr").each(function(i) {
            var element = {};
            if ($(this).data("id")) {
                element.id = $(this).data("id");
            }
            var fieldIdx = 0;
            $(this).find("td").each(function(j) {
                if (!$(this).hasClass("ignore_on_export")) {
                    element[fields[fieldIdx]] = $(this).data("date") ? $(this).data("date") : getCellText($(this));
                    fieldIdx++;
                }
            });
            data[collectionName].push(element);
        });
        saveAs(new Blob([JSON.stringify(data)], { type: "application/json; charset=utf-8" }), tableName + ".json");
    } else if (format == "xml") {
        var fields = [];
        $(table).find("thead tr:last th").each(function(i) {
            if (!$(this).hasClass("ignore_on_export")) {
                fields.push(getCellText($(this)));
            }
        });
        var data = '<xml><' + collectionName + '>';
        $(table).find("tbody tr").each(function(i) {
            var row = '<element>';
            if ($(this).data("id)")) {
                row += '<col name="id">' + $(this).data("id)") + '</col>';
            }
            var fieldIdx = 0;
            $(this).find("td").each(function(j) {
                if (!$(this).hasClass("ignore_on_export")) {
                    row += '<col name="' + fields[fieldIdx] + '">' + ($(this).data("date") ? $(this).data("date") : getCellText($(this))) + '</col>';
                    fieldIdx++;
                }
            });
            row += '</element>';
            data += row;
        });
        data += '</' + collectionName + '></xml>';
        saveAs(new Blob([data], { type: "text/xml; charset=utf-8" }), tableName + ".xml");
    } else {
        mpm.error.showModal();
    }
}