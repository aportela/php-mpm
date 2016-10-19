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

/**
 * reset form
 */
mpm.form.reset = function(form) {
    $(form)[0].reset();
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

mpm.xhr = function(method, action, formData, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open(method, action, true);
    xhr.onreadystatechange = function(e) {
        if (this.readyState == 4) {
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
    xhr.send(formData, null, 2);
}

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
            if ($(this).data("id")) {
                row += '<col name="id">' + $(this).data("id") + '</col>';
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
    } else if (format === "csv") {
        // get real cell value (removing icons)
        var escapeValue = function(value) {
            // borrowed some ideas from
            // (Xavier John) http://stackoverflow.com/a/24922761
            value = value.replace(/"/g, '""');
            if (value.search(/("|,|\n)/g) >= 0) {
                value = '"' + value + '"';
            }
            return (value);
        };
        var data = "";
        var fields = ["id"];
        $(table).find("thead tr:last th").each(function(i) {
            if (!$(this).hasClass("ignore_on_export")) {
                fields.push(escapeValue(getCellText($(this))));
            }
        });
        data += fields.join(", ") + "\n";
        $(table).find("tbody tr").each(function(i) {
            var rowValues = [];
            var row = "";
            if ($(this).data("id")) {
                rowValues.push(escapeValue($(this).data("id")));
            }
            var fieldIdx = 0;
            $(this).find("td").each(function(j) {
                if (!$(this).hasClass("ignore_on_export")) {
                    var v = ($(this).data("date") ? $(this).data("date") : getCellText($(this)));
                    rowValues.push(escapeValue(v));
                }
            });
            data += rowValues.join(",") + "\n";
        });
        saveAs(new Blob([data], { type: "text/csv; charset=utf-8" }), tableName + ".csv");
    } else {
        mpm.error.showModal();
    }
}

mpm.utils = mpm.utils || {};

/**
 * get browser info from user agent
 * 
 * (Hermann Ingjaldsson) http://stackoverflow.com/a/16938481
 */
mpm.utils.getBrowserFromUserAgent = function(ua) {
    if (ua) {
        var tem, M = ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
        if (/trident/i.test(M[1])) {
            tem = /\brv[ :]+(\d+)/g.exec(ua) || [];
            return { name: 'IE', version: (tem[1] || '') };
        }
        if (M[1] === 'Chrome') {
            tem = ua.match(/\b(OPR|Edge)\/(\d+)/);
            if (tem != null) return { name: tem[1].replace('OPR', 'Opera'), version: tem[2] };
        }
        M = M[2] ? [M[1], M[2]] : [navigator.appName, navigator.appVersion, '-?'];
        if ((tem = ua.match(/version\/(\d+)/i)) != null) M.splice(1, 1, tem[1]);
        return { name: M[0], version: M[1] };
    } else {
        return (null);
    }
}

/**
 * get operating system from user agent
 */
mpm.utils.getOSFromUserAgent = function(ua) {
    if (ua) {
        var tmp = [
            // Match user agent string with operating systems
            { os: 'Windows 3.11', regex: 'Win16' },
            { os: 'Windows 95', regex: '(Windows 95)|(Win95)|(Windows_95)' },
            { os: 'Windows 98', regex: '(Windows 98)|(Win98)' },
            { os: 'Windows 2000', regex: '(Windows NT 5.0)|(Windows 2000)' },
            { os: 'Windows XP', regex: '(Windows NT 5.1)|(Windows XP)' },
            { os: 'Windows Server 2003', regex: '(Windows NT 5.2)' },
            { os: 'Windows Vista', regex: '(Windows NT 6.0)' },
            { os: 'Windows 7', regex: '(Windows NT 6.1)|(Windows NT 7.0)' },
            { os: 'Windows NT 4.0', regex: '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)' },
            { os: 'Windows ME', regex: 'Windows ME' },
            { os: 'Open BSD', regex: 'OpenBSD' },
            { os: 'Sun OS', regex: 'SunOS' },
            { os: 'Linux', regex: '(Linux)|(X11)' },
            { os: 'Mac OS', regex: '(Mac_PowerPC)|(Macintosh)' },
            { os: 'QNX', regex: 'QNX' },
            { os: 'BeOS', regex: 'BeOS' },
            { os: 'OS/2', regex: 'OS/2' },
            { os: 'Search Bot', regex: '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(MSNBot)|(Ask Jeeves/Teoma)|(ia_archiver)' }
        ];
        for (var i = 0; i < tmp.length; i++) {
            var a = new RegExp(tmp[i].regex, 'gi');
            if (ua.match(a)) {
                return (tmp[i].os);
            }
        }
        return (null);
    } else {
        return (null);
    }
}