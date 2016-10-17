/**
 * fill templates table data
 */
function fillTable(actualPage, totalPages, templates) {
    mpm.pagination.setControls(actualPage, totalPages);
    var html = null;
    if (templates && templates.length > 0) {
        for (var i = 0; i < templates.length; i++) {
            html += '<tr data-id="' + response.data.results[i].id + '">';
            html += '<td colspan="5">TODO</td>';
        }
    }
    $("table#templates tbody").html(html);
}

/**
 * add template modal form submit event
 */
$("form#frm_add_template").submit(function(e) {
    e.preventDefault();
    mpm.form.submit(this, function(httpStatusCode, response) {
        switch (httpStatusCode) {
            case 409:
                //mpm.form.putValidationError("ca_name", TEMPLATE_ADD_NAME_EXISTS);
                break;
            case 200:
                if (!(response && response.success)) {
                    mpm.error.showModal();
                } else {
                    $('html').removeClass('is-clipped');
                    $('div.modal').removeClass('is-active');
                    $("form#frm_admin_search").submit();
                }
                break;
            default:
                mpm.error.showModal();
                break;
        }
    });
});

/**
 * update template modal form submit event
 */
$("form#frm_update_template").submit(function(e) {
    e.preventDefault();
    mpm.form.submit(this, function(httpStatusCode, response) {
        switch (httpStatusCode) {
            case 409:
                mpm.form.putValidationError("ca_name", GROUP_UPDATE_TEMPLATE_EXISTS);
                break;
            case 200:
                if (!(response && response.success)) {
                    mpm.error.showModal();
                } else {
                    $('html').removeClass('is-clipped');
                    $('div.modal').removeClass('is-active');
                    $("form#frm_admin_search").submit();
                }
                break;
            default:
                mpm.error.showModal();
                break;
        }
    });
});

/**
 * delete template modal form submit event
 */
$("form#frm_delete_template").submit(function(e) {
    e.preventDefault();
    mpm.form.submit(this, function(httpStatusCode, response) {
        switch (httpStatusCode) {
            case 200:
                if (!(response && response.success)) {
                    mpm.error.showModal();
                } else {
                    $('html').removeClass('is-clipped');
                    $('div.modal').removeClass('is-active');
                    $("form#frm_admin_search").submit();
                }
                break;
            default:
                mpm.error.showModal();
                break;
        }
    });
});

/**
 * reset add form before show modal
 */
$('table thead').on("click", ".btn_add_template", function(e) {
    mpm.form.reset($("form#frm_add_template"));
});

/**
 * reset & assign form values before show modal
 */
$('table tbody').on("click", ".btn_delete_template", function(e) {
    mpm.form.reset($("form#frm_update_template"));
    var tr = $(this).closest("tr");
    $("input#delete_template_id").val($(tr).data("id"));
    $("strong#delete_template_name").text($(tr).find("td:nth-child(2)").text());
});

/**
 * reset & assign form values before show modal
 */
$('table tbody').on("click", ".btn_update_template", function(e) {
    mpm.form.reset($("form#frm_delete_template"));
    var tr = $(this).closest("tr");
    $("input#update_template_id").val($(tr).data("id"));
    $("input#update_template_name").val($(tr).find("td:nth-child(2)").text());
});

/**
 * launch search on start
 */
$("form#frm_admin_search").submit();