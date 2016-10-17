$("form.frm_search_templates").submit(function(e) {
    e.preventDefault();
    mpm.form.submit(this, function(httpStatusCode, response) {
        switch (httpStatusCode) {
            case 200:
                if (!(response && response.success)) {
                    mpm.error.showModal();
                } else {
                    mpm.pagination.setControls(response.data.pager.actualPage, response.data.pager.totalPages);
                    var html = null;
                    if (response.data && response.data.results.length > 0) {
                        for (var i = 0; i < response.data.results.length; i++) {
                            html += '<tr data-id="' + response.data.results[i].id + '">';
                            html += '<td colspan="5">TODO</td>';
                            // TODO
                            html += '</tr>';
                        }
                    }
                    $("table#templates tbody").html(html);
                }
                break;
            default:
                mpm.error.showModal();
                break;
        }
    });
});

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
                    $("form.frm_search_templates").submit();
                }
                break;
            default:
                mpm.error.showModal();
                break;
        }
    });
});
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
                    $("form.frm_search_templates").submit();
                }
                break;
            default:
                mpm.error.showModal();
                break;
        }
    });
});

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
                    $("form.frm_search_templates").submit();
                }
                break;
            default:
                mpm.error.showModal();
                break;
        }
    });
});

$('table thead').on("click", ".btn_add_template", function(e) {});

$('table tbody').on("click", ".btn_delete_group", function(e) {
    $("input#delete_template_id").val($(this).closest("tr").data("id"));
    $("strong#delete_template_name").text($(this).closest("tr").find("td:nth-child(2)").text());
});

$('table tbody').on("click", ".btn_update_template", function(e) {
    $("input#update_template_id").val($(this).closest("tr").data("id"));
    $("input#update_template_name").val($(this).closest("tr").find("td:nth-child(2)").text());
});

$(".btn_previous_page").click(function(e) {
    var v = parseInt($(".i_page").val());
    v--;
    if (v < 1) {
        v = 1;
        $(this).addClass("is-disabled");
    } else {
        $(this).removeClass("is-disabled");
    }
    $(".i_page").val(v);
    $("form.frm_search_templates").submit();
});

$(".btn_next_page").click(function(e) {
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
    $("form.frm_search_templates").submit();
});

$("select#s_results_page").change(function(e) {
    $(".i_page").val(1);
    $("form.frm_search_groups").submit();
});

$("form.frm_search_templates").submit();