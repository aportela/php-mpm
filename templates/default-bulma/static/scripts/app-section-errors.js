"use strict";

/**
 * fill errors table data
 */
function fillTable(actualPage, totalPages, errors) {
    mpm.pagination.setControls(actualPage, totalPages);
    var html = null;
    if (errors && errors.length > 0) {
        for (var i = 0; i < errors.length; i++) {
            html += '<tr>';
            html += '<td data-date="' + errors[i].creationDate + '">' + new moment(errors[i].creationDate).fromNow() + '</td>';
            html += '<td>' + errors[i].userName + '</td>';
            html += '<td>' + errors[i].remoteAddress + '</td>';
            var os = mpm.utils.getOSFromUserAgent(errors[i].userAgent);
            html += '<td>' + (os ? os : "unknown") + '</td>';
            var browser = mpm.utils.getBrowserFromUserAgent(errors[i].userAgent);
            html += '<td>' + (browser ? (browser.name + ' (version ' + browser.version + ')') : "unknown") + '</td>';
            html += '<td>';
            html += '<p><strong>File:</strong> ' + errors[i].filename + ':' + errors[i].line + '</p>';
            html += '<p"><strong>Exception:</strong> ' + errors[i].class + ' (' + errors[i].code + ')</p>';
            if (errors[i].message) {
                html += '<div class="card is-fullwidth"><header class="card-header"><p class="card-header-title">Message</p><a class="card-header-icon toggle_stacktrace"><i class="fa fa-angle-down"></i></a></header><div class="card-content is-hidden"><pre>' + errors[i].message + '</pre></div></div>';
            }
            if (errors[i].trace) {
                html += '<div class="card is-fullwidth"><header class="card-header"><p class="card-header-title">Stacktrace</p><a class="card-header-icon toggle_stacktrace"><i class="fa fa-angle-down"></i></a></header><div class="card-content is-hidden"><pre>' + errors[i].trace + '</pre></div></div>';
            }
            html += '</td>';
            html += '</tr>';
        }
    }
    $("table#errors tbody").html(html);
}

/**
 * collapsible effect click event for message|stacktrace error details
 */
$("table#errors").on("click", "header.card-header", function(e) {
    e.preventDefault();
    $(this).find("i").toggleClass("fa-angle-down").toggleClass("fa-angle-up");
    $(this).next("div.card-content").toggleClass("is-hidden");
});

/**
 * launch search on start
 */
$("form#frm_admin_search").submit();