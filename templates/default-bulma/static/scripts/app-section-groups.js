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
            html += '<td>' + (groups[i].totalUsers ? groups[i].totalUsers : 0) + '</td>';
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
    $(this).find("input.tmp_user_id").remove();
    var userIds = getUsers($("table#add_group_userlist"));
    if (userIds != null && userIds.length > 0) {
        for (var i = 0; i < userIds.length; i++) {
            $(this).append('<input class="tmp_user_id" type="hidden" name="users[' + i + ']" value="' + userIds[i] + '" />')
        }
    }
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
                    clearUsersTable($("table#add_group_userlist"));
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
    $(this).find("input.tmp_user_id").remove();
    var userIds = getUsers($("table#update_group_userlist"));
    if (userIds != null && userIds.length > 0) {
        for (var i = 0; i < userIds.length; i++) {
            $(this).append('<input class="tmp_user_id" type="hidden" name="users[' + i + ']" value="' + userIds[i] + '" />')
        }
    }
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
                    clearUsersTable($("table#update_group_userlist"));
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
    selectFirstTab($("form#frm_add_group"));
    $("select.group_user_list").val("");
    fillUserLists();
});

/**
 * reset & assign form values before show modal
 */
$('table tbody').on("click", ".btn_update_group", function(e) {
    mpm.form.reset($("form#frm_delete_group"));
    selectFirstTab($("form#frm_update_group"));
    $("select.group_user_list").val("");
    fillUserLists();
    var id = $(this).closest("tr").data("id");
    getGroup(id, function(data) {
        if (data === null) {
            mpm.error.showModal();
        } else {
            $("input#update_group_id").val(id);
            $("input#update_group_name").val(data.name);
            $("input#update_group_description").val(data.description);
            if (data.users && data.users.length > 0) {
                for (var i = 0; i < data.users.length; i++) {
                    appendUser("table#update_group_userlist", data.users[i].id, data.users[i].name, data.users[i].email);
                }
            }
        }
    });
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

/**
 * select first tab contained on form
 */
function selectFirstTab(form) {
    $(form).find("div.tabs ul li").removeClass("is-active");
    $(form).find("div.tabs ul li:first").addClass("is-active");
    $(form).find("div.tab-content").addClass("is-hidden");
    $(form).find("div.tab-content:first").removeClass("is-hidden");
}

/**
 * clear previous users table
 */
function clearUsersTable(table) {
    $(table).find("tbody").html("");
}

/**
 * fill users combo form controls
 */
function fillUsersCombo(users) {
    var html = '<option value="">select user</option>';
    if (users && users.length > 0) {
        for (var i = 0; i < users.length; i++) {
            html += '<option value="' + users[i].id + '" data-id="' + users[i].id + '" data-name="' + users[i].name + '" data-email="' + users[i].email + '">' + users[i].name + ' (' + users[i].email + ')</option>'
        }
    }
    $("select.group_user_list").html(html);
}

/**
 * get user list & fill into controls
 */
function fillUserLists() {
    // load available users combo if empty
    if ($("select.group_user_list:first option").length == 1) {
        var formData = new FormData();
        formData.append("page", 1);
        formData.append("resultsPage", 0);
        mpm.xhr("POST", "/api/user/search.php", formData, function(httpStatusCode, response) {
            switch (httpStatusCode) {
                case 200:
                    if (!(response && response.success)) {
                        mpm.error.showModal();
                    } else {
                        fillUsersCombo(response.data.results);
                    }
                    break;
                default:
                    mpm.error.showModal();
                    break;
            }
        });
    }
}

/**
 * get group info from server
 */
function getGroup(id, callback) {
    var formData = new FormData();
    formData.append("id", id);
    mpm.xhr("POST", "/api/group/get.php", formData, function(httpStatusCode, response) {
        switch (httpStatusCode) {
            case 200:
                if (!(response && response.success)) {
                    callback(null);
                } else {
                    callback(response.data);
                }
                break;
            default:
                callback(null);
                break;
        }
    });
}

/**
 * selected user changed event
 * description: toggle add user button state (enabled if user is selected && not exists in table)
 */
$("select.group_user_list").change(function(e) {
    e.preventDefault();
    var btn = $(this).closest("p").find("a.btn_add_group_user");
    var v = $(this).val();
    if (v) {
        var selectedId = $(this).find("option:selected").data("id");
        if ($('table#add_group_userlist tbody tr[data-id="' + selectedId + '"]').length > 0) {
            $(btn).addClass("is-disabled");
        } else {
            $(btn).removeClass("is-disabled");
        }
    } else {
        $(btn).addClass("is-disabled");
    }
});

/**
 * get users contained in specified table
 */
function getUsers(table) {
    var userIds = [];
    $(table).find("tbody tr").each(function(i) {
        userIds.push(String($(this).data("id")));
    });
    return (userIds);
}

/**
 * add new user to group user list table
 */
function appendUser(table, id, name, email) {
    var html = "";
    html += '<tr data-id="' + id + '">';
    html += '<td><a class="button btn_delete_row"><span class="icon"><i class="fa fa-trash"></i></span><span>Delete</span></a></td>';
    html += "<td>" + name + "</td>";
    html += "<td>" + email + "</td>";
    html += "</tr>";
    $("table").find("tbody").append(html);
}

/**
 * add user button click event
 * description: add selected user on table, reset user select combo & disable this button again
 */
$("a.btn_add_group_user").click(function(e) {
    var o = $(this).closest("p").find("select.group_user_list option:selected");
    appendUser("table#add_group_userlist", $(o).data("id"), $(o).data("name"), $(o).data("email"));
    $("select.group_user_list").val("");
    $(this).addClass("is-disabled");
});

/**
 * launch search on start
 */
$("form#frm_admin_search").submit();