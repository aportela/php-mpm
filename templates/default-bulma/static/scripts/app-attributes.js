$("form.frm_search_attributes").submit(function(e) {
    e.preventDefault();
    mpm.form.submit(this, function(httpStatusCode, result) {
        switch (httpStatusCode) {
            case 200:
                if (result === null) {
                    mpm.error.showModal();
                } else {
                    mpm.pagination.setControls(result.data.pager.actualPage, result.data.pager.totalPages);
                    var html = null;
                    if (result.data && result.data.results.length > 0) {
                        for (var i = 0; i < result.data.results.length; i++) {
                            html += '<tr data-id="' + result.data.results[i].id + '">';
                            html += '<td class="has-text-centered ignore_on_export"><a class="button is-small is-info modal-button btn_update_attribute" data-target="#modal_update">Update</a> <a class="button is-small is-danger modal-button btn_delete_attribute" data-target="#modal_delete">Delete</a></td>';
                            html += '<td>' + result.data.results[i].name + '</td>';
                            html += '<td>' + (result.data.results[i].description ? result.data.results[i].description : "") + '</td>';
                            html += '<td>';
                            switch (parseInt(result.data.results[i].type)) {
                                case 1:
                                    html += '<span class="icon is-small"><i class="fa fa-1x fa-file-text" aria-hidden="true"></i></span> short text';
                                    break;
                                case 2:
                                    html += '<span class="icon is-small"><i class="fa fa-1x fa-file-text-o" aria-hidden="true"></i></span> long text';
                                    break;
                                case 3:
                                    html += '<span class="icon is-small"><i class="fa fa-1x fa-square" aria-hidden="true"></i></span> number integer';
                                    break;
                                case 4:
                                    html += '<span class="icon is-small"><i class="fa fa-1x fa-square-o" aria-hidden="true"></i></span> number decimal';
                                    break;
                                case 5:
                                    html += '<span class="icon is-small"><i class="fa fa-1x fa-calendar" aria-hidden="true"></i></span> date';
                                    break;
                                case 6:
                                    html += '<span class="icon is-small"><i class="fa fa-1x fa-clock-o" aria-hidden="true"></i></span> time';
                                    break;
                                case 7:
                                    html += '<span class="icon is-small"><i class="fa fa-1x fa-calendar-check-o" aria-hidden="true"></i></span> datetime';
                                    break;
                                default:
                                    html += '<span class="icon is-small"><i class="fa fa-1x fa-user-md" aria-hidden="true"></i></span> none';
                                    break;
                            }
                            html += '</td>';
                            html += '<td data-id="' + result.data.results[i].creatorId + '">' + (result.data.results[i].creatorId != result.data.results[i].id ? result.data.results[i].creatorName : "auto-register") + '</td>';
                            html += '<td data-date="' + result.data.results[i].creationDate + '">' + new moment(result.data.results[i].creationDate).fromNow() + '</td>';
                            html += '</tr>';
                        }
                    }
                    $("table#attributes tbody").html(html);
                }
                break;
            default:
                mpm.error.showModal();
                break;
        }
    });
});

$("form#frm_add_attribute").submit(function(e) {
    e.preventDefault();
    mpm.form.submit(this, function(httpStatusCode, response) {
        switch (httpStatusCode) {
            case 409:
                mpm.form.putValidationError("ca_name", ATTRIBUTE_ADD_NAME_EXISTS);
                break;
            case 200:
                if (!(response && response.success)) {
                    mpm.error.showModal();
                } else {
                    $('html').removeClass('is-clipped');
                    $('div.modal').removeClass('is-active');
                    $("form.frm_search_attributes").submit();
                }
                break;
            default:
                mpm.error.showModal();
                break;
        }
    });
});

$("form#frm_delete_attribute").submit(function(e) {
    e.preventDefault();
    mpm.form.submit(this, function(httpStatusCode, result) {
        switch (httpStatusCode) {
            case 409:
                mpm.form.putValidationError("ca_name", ATTRIBUTE_UPDATE_NAME_EXISTS);
                break;
            case 200:
                $('html').removeClass('is-clipped');
                $('div.modal').removeClass('is-active');
                $("form.frm_search_attributes").submit();
                break;
            default:
                mpm.error.showModal();
                break;
        }
    });
});

$("form#frm_update_attribute").submit(function(e) {
    e.preventDefault();
    mpm.form.submit(this, function(httpStatusCode, result) {
        switch (httpStatusCode) {
            case 200:
                $('html').removeClass('is-clipped');
                $('div.modal').removeClass('is-active');
                $("form.frm_search_attributes").submit();
                break;
            default:
                mpm.error.showModal();
                break;
        }
    });
});

$('table thead').on("click", ".btn_add_attribute", function(e) {
    $("input#add_attribute_name").val("");
    $("input#add_attribute_description").val("");
    $("select#add_attribute_type").val("");
});

$('table tbody').on("click", ".btn_delete_attribute", function(e) {
    $("input#delete_attribute_id").val($(this).closest("tr").data("id"));
    $("strong#delete_attribute_name").text($(this).closest("tr").find("td:nth-child(2)").text());
});

$('table tbody').on("click", ".btn_update_attribute", function(e) {
    $("input#update_attribute_id").val($(this).closest("tr").data("id"));
    $("input#update_attribute_name").val($(this).closest("tr").find("td:nth-child(2)").text());
    $("input#update_attribute_description").val($(this).closest("tr").find("td:nth-child(3)").text());
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
    $("form.frm_search_attributes").submit();
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
    $("form.frm_search_attributes").submit();
});

$("select#s_results_page").change(function(e) {
    $(".i_page").val(1);
    $("form.frm_search_attributes").submit();
});

$("form.frm_search_attributes").submit();