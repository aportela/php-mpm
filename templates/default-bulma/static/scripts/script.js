$("div.tabs ul li a").click(function(e) {
    e.preventDefault();
    $(this).closest("ul").find("li").removeClass("is-active");
    $(this).closest("li").addClass("is-active");
    $(".tab-content").addClass("is-hidden");
    $("#" + $(this).data("target")).removeClass("is-hidden");
});


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

$('body').on("click", ".modal-button", function() {
    var target = $(this).data('target');
    $('html').addClass('is-clipped');
    $(target).addClass('is-active');
    $(".modal_error").addClass("is-hidden");
    mpm.form.clearValidationMessages($(target).find("form"));
    $(target).find("input:visible:first").focus();
});

$('body').on("click", '.modal-background, .modal_close', function() {
    $('html').removeClass('is-clipped');
    $('div.modal').removeClass('is-active');
});


$("select#export_table_data_format").change(function(e) {
    e.preventDefault();
    var format = $(this).val();
    if (format) {
        $("#btn_export_table_data").removeClass("is-disabled").data("format", format);
    } else {
        $("#btn_export_table_data").addClass("is-disabled").data("format", null);
    }
});

$("#btn_export_table_data").click(function(e) {
    e.preventDefault();
    mpm.data.tableExport($(this).closest("table"), $(this).data("format"));
});

$("body").on("click", ".btn_delete_row", function(e) {
    e.preventDefault();
    $(this).closest("tr").remove();
});