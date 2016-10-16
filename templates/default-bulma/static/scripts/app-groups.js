$("form.frm_search_groups").submit(function(e) {
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
                            html += '<td class="has-text-centered ignore_on_export"><a class="button is-small is-info modal-button btn_update_group" data-target="#modal_update">Update</a> <a class="button is-small is-danger modal-button btn_delete_group" data-target="#modal_delete">Delete</a></td>';
                            html += '<td>' + response.data.results[i].name + '</td>';
                            html += '<td>' + (response.data.results[i].description ? response.data.results[i].description : "") + '</td>';
                            html += '<td>0</td>';
                            html += '<td data-id="' + response.data.results[i].creatorId + '">' + (response.data.results[i].creatorId != response.data.results[i].id ? response.data.results[i].creatorName : "auto-register") + '</td>';
                            html += '<td data-date="' + response.data.results[i].creationDate + '">' + new moment(response.data.results[i].creationDate).fromNow() + '</td>';
                            html += '</tr>';
                        }
                    }
                    $("table#groups tbody").html(html);
                }
                break;
            default:
                mpm.error.showModal();
                break;
        }
    });
});

$("form#frm_add_group").submit(function(e) {
    e.preventDefault();
    mpm.form.submit(this, function(httpStatusCode, response) {
        switch (httpStatusCode) {
            case 409:
                mpm.form.putValidationError("ca_name", GROUP_ADD_NAME_EXISTS);
                break;
            case 200:
                if (!(response && response.success)) {
                    mpm.error.showModal();
                } else {
                    $('html').removeClass('is-clipped');
                    $('div.modal').removeClass('is-active');
                    $("form.frm_search_groups").submit();
                }
                break;
            default:
                mpm.error.showModal();
                break;
        }
    });
});
$("form#frm_delete_group").submit(function(e) {
    e.preventDefault();
    mpm.form.submit(this, function(httpStatusCode, response) {
        switch (httpStatusCode) {
            case 200:
                if (!(response && response.success)) {
                    mpm.error.showModal();
                } else {
                    $('html').removeClass('is-clipped');
                    $('div.modal').removeClass('is-active');
                    $("form.frm_search_groups").submit();
                }
                break;
            default:
                mpm.error.showModal();
                break;
        }
    });
});

$("form#frm_update_group").submit(function(e) {
    e.preventDefault();
    mpm.form.submit(this, function(httpStatusCode, response) {
        switch (httpStatusCode) {
            case 409:
                mpm.form.putValidationError("ca_name", GROUP_UPDATE_NAME_EXISTS);
                break;
            case 200:
                if (!(response && response.success)) {
                    mpm.error.showModal();
                } else {
                    $('html').removeClass('is-clipped');
                    $('div.modal').removeClass('is-active');
                    $("form.frm_search_groups").submit();
                }
                break;
            default:
                mpm.error.showModal();
                break;
        }
    });
});

$('table thead').on("click", ".btn_add_group", function(e) {
    $("input#add_group_name").val("");
    $("input#add_group_description").val("");
});

$('table tbody').on("click", ".btn_delete_group", function(e) {
    $("input#delete_group_id").val($(this).closest("tr").data("id"));
    $("strong#delete_group_name").text($(this).closest("tr").find("td:nth-child(2)").text());
});

$('table tbody').on("click", ".btn_update_group", function(e) {
    $("input#update_group_id").val($(this).closest("tr").data("id"));
    $("input#update_group_name").val($(this).closest("tr").find("td:nth-child(2)").text());
    $("input#update_group_description").val($(this).closest("tr").find("td:nth-child(3)").text());
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
    $("form.frm_search_groups").submit();
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
    $("form.frm_search_groups").submit();
});

$("select#s_results_page").change(function(e) {
    $(".i_page").val(1);
    $("form.frm_search_groups").submit();
});

var timer = null;
$("input#fast_search_filter").keyup(function(e) {
    e.preventDefault();
    if (timer) {
        clearTimeout(timer);
    }
    timer = setTimeout(function() {
        $("form.frm_search_groups").submit();
    }, 500);
});

var formData = new FormData();
formData.append("page", 1);
formData.append("resultsPage", 0);
mpm.xhr("POST", "/api/user/search.php", formData, function(httpStatusCode, response) {
    switch (httpStatusCode) {
        case 200:
            if (!(response && response.success)) {
                mpm.error.showModal();
            } else {
                var html = '<option value="">select user</option>';
                if (response.data && response.data.results.length > 0) {
                    for (var i = 0; i < response.data.results.length; i++) {
                        html += '<option value="' + response.data.results[i].id + '" data-id="' + response.data.results[i].id + '" data-name="' + response.data.results[i].name + '" data-email="' + response.data.results[i].email + '">' + response.data.results[i].name + ' (' + response.data.results[i].email + ')</option>'
                    }
                }
                $("select#add_group_user_list").html(html);
            }
            break;
        default:
            mpm.error.showModal();
            break;
    }
});

$("select#add_group_user_list").change(function(e) {
    e.preventDefault();
    var v = $(this).val()
    if (v) {
        if ($('table#add_group_userlist tbody tr[data-id="' + $("select#add_group_user_list option:selected").data("id") + '"]').length > 0) {
            $("#btn_add_group_user").addClass("is-disabled");
        } else {
            $("#btn_add_group_user").removeClass("is-disabled");
        }
    } else {
        console.log("no");
        $("#btn_add_group_user").addClass("is-disabled");
    }
});

$("#btn_add_group_user").click(function(e) {
    var o = $("select#add_group_user_list option:selected");
    var html = "";
    html += '<tr data-id="' + $(o).data("id") + '">';
    html += '<td><a class="button btn_delete_row"><span class="icon"><i class="fa fa-trash"></i></span><span>Delete</span></a></td>';
    html += "<td>" + $(o).data("name") + "</td>";
    html += "<td>" + $(o).data("email") + "</td>";
    html += "</tr>";
    $("table#add_group_userlist tbody").append(html);
    $("select#add_group_user_list").val("");
    $("#btn_add_group_user").addClass("is-disabled");
});

$("form.frm_search_groups").submit();