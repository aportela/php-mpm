/**
 * fill groups table data
 */
function fillTable(actualPage, totalPages, groups) {
    mpm.pagination.setControls(actualPage, totalPages);
    var html = null;
    if (groups && groups.length > 0) {
        for (var i = 0; i < groups.length; i++) {
            html += '<tr data-id="' + groups[i].id + '">';
            html += '<td class="has-text-centered ignore_on_export"><a class="button is-small is-info modal-button btn_update_group" data-target="#modal_update">Update</a> <a class="button is-small is-danger modal-button btn_delete_group" data-target="#modal_delete">Delete</a></td>';
            html += '<td>' + groups[i].name + '</td>';
            html += '<td>' + (groups[i].description ? groups[i].description : "") + '</td>';
            html += '<td>0</td>';
            html += '<td data-id="' + groups[i].creatorId + '">' + (groups[i].creatorId != groups[i].id ? groups[i].creatorName : "auto-register") + '</td>';
            html += '<td data-date="' + groups[i].creationDate + '">' + new moment(groups[i].creationDate).fromNow() + '</td>';
            html += '</tr>';
        }
    }
    $("table#groups tbody").html(html);
}

/**
 * add group modal form submit event
 */
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
 * update group modal form submit event
 */
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
 * delete group modal form submit event
 */
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
$('table thead').on("click", ".btn_add_group", function(e) {
    mpm.form.reset($("form#frm_add_group"));
});

/**
 * reset & assign form values before show modal
 */
$('table tbody').on("click", ".btn_update_group", function(e) {
    mpm.form.reset($("form#frm_delete_group"));
    var tr = $(this).closest("tr");
    $("input#update_group_id").val($(tr).data("id"));
    $("input#update_group_name").val($(tr).find("td:nth-child(2)").text());
    $("input#update_group_description").val($(tr).find("td:nth-child(3)").text());
});

/**
 * reset & assign form values before show modal
 */
$('table tbody').on("click", ".btn_delete_group", function(e) {
    mpm.form.reset($("form#frm_update_group"));
    var tr = $(this).closest("tr");
    $("input#delete_group_id").val($(tr).data("id"));
    $("strong#delete_group_name").text($(tr).find("td:nth-child(2)").text());
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

/**
 * launch search on start
 */
$("form#frm_admin_search").submit();