$("form.frm_search_users").submit(function(e) {
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
                            html += '<td class="has-text-centered ignore_on_export"><a class="button is-small is-info modal-button btn_update_user" data-target="#modal_update">Update</a> <a class="button is-small is-danger modal-button btn_delete_user"  data-target="#modal_delete">Delete</a></td>'
                            if (response.data.results[i].type == 1) {
                                html += '<td ><span class="icon is-small"><i class="fa fa-1x fa-user-md" aria-hidden="true"></i></span> <span>super</span></td>';
                            } else {
                                html += '<td><span class="icon is-small"><i class="fa fa-1x fa-user" aria-hidden="true"></i></span> <span>normal</span></td>';
                            }
                            html += '<td  class="is-small">' + response.data.results[i].name + '</td>';
                            html += '<td><a href="mailto:' + response.data.results[i].email + '">' + response.data.results[i].email + '<a/></td>';
                            html += '<td data-id="' + response.data.results[i].creatorId + '">' + (response.data.results[i].creatorId != response.data.results[i].id ? response.data.results[i].creatorName : "auto-register") + '</td>';
                            html += '<td data-date="' + response.data.results[i].creationDate + '">' + new moment(response.data.results[i].creationDate).fromNow() + '</td>';
                            html += '</tr>';
                        }
                    }
                    $("table#users tbody").html(html);
                }
                break;
            default:
                mpm.error.showModal();
                break;
        }
    });
});

$("form#frm_add_user").submit(function(e) {
    e.preventDefault();
    mpm.form.submit(this, function(httpStatusCode, response) {
        switch (httpStatusCode) {
            case 409:
                mpm.form.putValidationError("ca_email", USER_ADD_EMAIL_EXISTS);
                break;
            case 200:
                if (!(response && response.success)) {
                    mpm.error.showModal();
                } else {
                    $('html').removeClass('is-clipped');
                    $('div.modal').removeClass('is-active');
                    $("form.frm_search_users").submit();
                }
                break;
            default:
                mpm.error.showModal();
                break;
        }
    });
});


$("form#frm_delete_user").submit(function(e) {
    e.preventDefault();
    mpm.form.submit(this, function(httpStatusCode, response) {
        switch (httpStatusCode) {
            case 200:
                if (!(response && response.success)) {
                    mpm.error.showModal();
                } else {
                    $('html').removeClass('is-clipped');
                    $('div.modal').removeClass('is-active');
                    $("form.frm_search_users").submit();
                }
                break;
            default:
                mpm.error.showModal();
                break;
        }
    });
});

$("form#frm_update_user").submit(function(e) {
    e.preventDefault();
    mpm.form.submit(this, function(httpStatusCode, response) {
        switch (httpStatusCode) {
            case 409:
                mpm.form.putValidationError("cu_email", USER_UPDATE_EMAIL_EXISTS);
                break;
            case 200:
                if (!(response && response.success)) {
                    mpm.error.showModal();
                } else {
                    $('html').removeClass('is-clipped');
                    $('div.modal').removeClass('is-active');
                    $("form.frm_search_users").submit();
                }
                break;
            default:
                mpm.error.showModal();
                break;
        }
    });
});

$('table thead').on("click", ".btn_add_user", function(e) {
    $("input#add_user_email").val("");
    $("input#add_user_name").val("");
    $("input#add_user_password").val("");
});

$('table tbody').on("click", ".btn_delete_user", function(e) {
    $("input#delete_user_id").val($(this).closest("tr").data("id"));
    $("strong#delete_user_name").text($(this).closest("tr").find("td:nth-child(3)").text());
});

$('table tbody').on("click", ".btn_update_user", function(e) {
    $("input#update_user_id").val($(this).closest("tr").data("id"));
    $("input#update_user_email").val($(this).closest("tr").find("td:nth-child(4)").text());
    $("input#update_user_name").val($(this).closest("tr").find("td:nth-child(3)").text());
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
    $("form.frm_search_users").submit();
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
    $("form.frm_search_users").submit();
});

$("select#s_results_page").change(function(e) {
    $(".i_page").val(1);
    $("form.frm_search_users").submit();
});

$("form.frm_search_users").submit();