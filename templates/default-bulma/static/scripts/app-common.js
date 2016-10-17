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