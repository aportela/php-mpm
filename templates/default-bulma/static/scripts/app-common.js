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
 * enable tabs
 */
$("body").on("click", "div.tabs ul li a", function(e) {
    e.preventDefault();
    $(this).closest("ul").find("li").removeClass("is-active");
    $(this).closest("li").addClass("is-active");
    $(".tab-content").addClass("is-hidden");
    $("#" + $(this).data("target")).removeClass("is-hidden");
});

/**
 * show modal click event
 */
$('body').on("click", ".modal-button", function() {
    var target = $(this).data('target');
    $('html').addClass('is-clipped');
    $(target).addClass('is-active');
    $(".modal_error").addClass("is-hidden");
    mpm.form.clearValidationMessages($(target).find("form"));
    $(target).find("input:visible:first").focus();
});

/**
 * hide modal click event
 */
$('body').on("click", '.modal_close', function() {
    $('html').removeClass('is-clipped');
    $('div.modal').removeClass('is-active');
});


/**
 * signout click event
 */
$("a#signout").click(function(e) {
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

/**
 * toggle export data button enabled state on format export selection 
 */
$("select#export_table_data_format").change(function(e) {
    e.preventDefault();
    var format = $(this).val();
    if (format) {
        $("#btn_export_table_data").removeClass("is-disabled").data("format", format);
    } else {
        $("#btn_export_table_data").addClass("is-disabled").data("format", null);
    }
});

/**
 * export actual table with selected format
 */
$("a#btn_export_table_data").click(function(e) {
    e.preventDefault();
    mpm.data.tableExport($(this).closest("table"), $(this).data("format"));
});

/**
 * remove actual row
 */
$("body").on("click", ".btn_delete_row", function(e) {
    e.preventDefault();
    $(this).closest("tr").remove();
});


/**
 * user / group / attribute  / template / error administration tables common search submit form event
 * description: launch search
 * WARNING: needs a existent fillTable function (individually declared on every section script)
 */
$("form#frm_admin_search").submit(function(e) {
    e.preventDefault();
    mpm.form.submit(this, function(httpStatusCode, response) {
        switch (httpStatusCode) {
            case 200:
                if (!(response && response.success)) {
                    mpm.error.showModal();
                } else {
                    if (typeof fillTable === "function") {
                        fillTable(response.data.pager.actualPage, response.data.pager.totalPages, response.data.results);
                    } else {
                        mpm.error.showModal();
                    }
                }
                break;
            default:
                mpm.error.showModal();
                break;
        }
    });
});

/**
 * user / group / attribute  / template / error administration tables common previous page button click event
 * description: launch previous page search 
 */
$("a.btn_previous_page").click(function(e) {
    var v = parseInt($(".i_page").val());
    v--;
    if (v < 1) {
        v = 1;
        $(this).addClass("is-disabled");
    } else {
        $(this).removeClass("is-disabled");
    }
    $(".i_page").val(v);
    $("form#frm_admin_search").submit();
});

/**
 * user / group / attribute  / template / error administration tables common next page button click event
 * description: launch next page search 
 */
$("a.btn_next_page").click(function(e) {
    var v = parseInt($(".i_page").val());
    var totalPages = parseInt($(".pager_total_pages").text());
    v++;
    if (v > totalPages) {
        b = totalPages;
        $(this).addClass("is-disabled");
    } else {
        $(this).removeClass("is-disabled");
    }
    $(".i_page").val(v);
    $("form#frm_admin_search").submit();
});

/**
 * user / group / attribute  / template / error administration tables common pagination settings change event
 * description: re-launch search with new results / page
 */
$("select#s_results_page").change(function(e) {
    $(".i_page").val(1);
    $("form#frm_admin_search").submit();
});

var textSearchTimer = null;

/**
 * user / group / attribute  / template / error administration tables common text filter change event
 * description: re-launch search with selected text filter
 */
$("input#fast_search_filter").keyup(function(e) {
    e.preventDefault();
    if ($(this).val()) {
        $("a#btn_clear_text").removeClass("is-disabled");
    } else {
        $("a#btn_clear_text").addClass("is-disabled");
    }
    if (textSearchTimer) {
        clearTimeout(textSearchTimer);
    }
    textSearchTimer = setTimeout(function() {
        $("form#frm_admin_search").submit();
    }, 500);
});

/**
 * user / group / attribute  / template / error administration tables common text filter clear button click event
 * description: clears text filter and re-launch search
 */
$("a#btn_clear_text").click(function(e) {
    e.preventDefault();
    $(this).addClass("is-disabled");
    $("input#fast_search_filter").val("");
    $("form#frm_admin_search").submit();
});