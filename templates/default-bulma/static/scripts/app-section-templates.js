/**
 * fill templates table data
 */
function fillTable(actualPage, totalPages, templates) {
    mpm.pagination.setControls(actualPage, totalPages);
    var html = null;
    if (templates && templates.length > 0) {
        for (var i = 0; i < templates.length; i++) {
            html += '<tr data-id="' + templates[i].id + '">';
            html += '<td class="has-text-centered ignore_on_export"><a class="button is-small is-info modal-button btn_update_template" data-target="#modal_update">Update</a> <a class="button is-small is-danger modal-button btn_delete_template" data-target="#modal_delete">Delete</a></td>';
            html += '<td>' + templates[i].name + '</td>';
            html += '<td>' + (templates[i].description ? templates[i].description : "") + '</td>';
            html += '<td data-id="' + templates[i].creatorId + '">' + (templates[i].creatorId != templates[i].id ? templates[i].creatorName : "auto-register") + '</td>';
            html += '<td data-date="' + templates[i].creationDate + '">' + new moment(templates[i].creationDate).fromNow() + '</td>';
            html += '</tr>';
        }
    }
    $("table#templates tbody").html(html);
}

/**
 * add template modal form submit event
 */
$("form#frm_add_template").submit(function(e) {
    e.preventDefault();
    $(this).find("input.tmp_permission").remove();
    var permissions = getPermissions($("table#add_template_permissions"));
    if (permissions != null && permissions.length > 0) {
        for (var i = 0; i < permissions.length; i++) {
            $(this).append('<input class="tmp_permission" type="hidden" name="group_permissions[' + i + ']" value="' + permissions[i].groupId + '" />')
            $(this).append('<input class="tmp_permission" type="hidden" name="create_flag_permissions[' + i + ']" value="' + (permissions[i].allowCreate ? "1" : "0") + '" />')
            $(this).append('<input class="tmp_permission" type="hidden" name="view_flag_permissions[' + i + ']" value="' + (permissions[i].allowView ? "1" : "0") + '" />')
            $(this).append('<input class="tmp_permission" type="hidden" name="update_flag_permissions[' + i + ']" value="' + (permissions[i].allowUpdate ? "1" : "0") + '" />')
            $(this).append('<input class="tmp_permission" type="hidden" name="delete_flag_permissions[' + i + ']" value="' + (permissions[i].allowDelete ? "1" : "0") + '" />')
        }
    }
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
    $(this).find("input.tmp_permission").remove();
    var permissions = getPermissions($("table#update_template_permissions"));
    if (permissions != null && permissions.length > 0) {
        for (var i = 0; i < permissions.length; i++) {
            $(this).append('<input class="tmp_permission" type="hidden" name="group_permissions[' + i + ']" value="' + permissions[i].groupId + '" />')
            $(this).append('<input class="tmp_permission" type="hidden" name="create_flag_permissions[' + i + ']" value="' + (permissions[i].allowCreate ? "1" : "0") + '" />')
            $(this).append('<input class="tmp_permission" type="hidden" name="view_flag_permissions[' + i + ']" value="' + (permissions[i].allowView ? "1" : "0") + '" />')
            $(this).append('<input class="tmp_permission" type="hidden" name="update_flag_permissions[' + i + ']" value="' + (permissions[i].allowUpdate ? "1" : "0") + '" />')
            $(this).append('<input class="tmp_permission" type="hidden" name="delete_flag_permissions[' + i + ']" value="' + (permissions[i].allowDelete ? "1" : "0") + '" />')
        }
    }
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
    selectFirstTab($("form#frm_add_template"));
    clearPermissionsTable($("table#add_template_permissions"));
    $("select.template_group_list").val("");
    fillGroupsLists();
});

/**
 * reset & assign form values before show modal
 */
$('table tbody').on("click", ".btn_update_template", function(e) {
    mpm.form.reset($("form#frm_delete_template"));
    selectFirstTab($("form#frm_update_template"));
    $("select.template_group_list").val("");
    clearPermissionsTable($("table#update_template_permissions"));
    fillGroupsLists();
    var id = $(this).closest("tr").data("id");
    getTemplate(id, function(data) {
        if (data === null) {
            mpm.error.showModal();
        } else {
            $("input#update_template_id").val(id);
            $("input#update_template_name").val(data.name);
            $("input#update_template_description").val(data.description);
            if (data.permissions && data.permissions.length > 0) {
                for (var i = 0; i < data.permissions.length; i++) {
                    appendPermission("table#update_template_permissions",
                        data.permissions[i].group.id,
                        data.permissions[i].group.name,
                        data.permissions[i].allowCreate,
                        data.permissions[i].allowView,
                        data.permissions[i].allowUpdate,
                        data.permissions[i].allowDelete
                    );
                }
            }
        }
    });
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
 * select first tab contained on form
 */
function selectFirstTab(form) {
    $(form).find("div.tabs ul li").removeClass("is-active");
    $(form).find("div.tabs ul li:first").addClass("is-active");
    $(form).find("div.tab-content").addClass("is-hidden");
    $(form).find("div.tab-content:first").removeClass("is-hidden");
}

/**
 * clear previous permissions table
 */
function clearPermissionsTable(table) {
    $(table).find("tbody").html("");
}

/**
 * fill groups combo form controls
 */
function fillGroupsCombo(groups) {
    var html = '<option value="">select group</option>';
    if (groups && groups.length > 0) {
        for (var i = 0; i < groups.length; i++) {
            html += '<option value="' + groups[i].id + '" data-id="' + groups[i].id + '" data-name="' + groups[i].name + '">' + groups[i].name + '</option>';
        }
    }
    $("select.template_group_list").html(html);
}

/**
 * get groups list & fill into controls
 */
function fillGroupsLists() {
    // load available groups combo if empty
    if ($("select.template_group_list:first option").length == 1) {
        var formData = new FormData();
        formData.append("page", 1);
        formData.append("resultsPage", 0);
        mpm.xhr("POST", "/api/group/search.php", formData, function(httpStatusCode, response) {
            switch (httpStatusCode) {
                case 200:
                    if (!(response && response.success)) {
                        mpm.error.showModal();
                    } else {
                        fillGroupsCombo(response.data.results);
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
function getTemplate(id, callback) {
    var formData = new FormData();
    formData.append("id", id);
    mpm.xhr("POST", "/api/template/get.php", formData, function(httpStatusCode, response) {
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
 * selected group changed event
 * description: toggle add permission button state (enabled if group is selected && not exists in table)
 */
$("select.template_group_list").change(function(e) {
    e.preventDefault();
    var btn = $(this).closest("p").find("a.btn_add_template_permission");
    var v = $(this).val();
    if (v) {
        var selectedId = $(this).find("option:selected").data("id");
        if ($(this).closest("div.tab-content").find('table tbody tr[data-id="' + selectedId + '"]').length > 0) {
            $(btn).addClass("is-disabled");
        } else {
            $(btn).removeClass("is-disabled");
        }
    } else {
        $(btn).addClass("is-disabled");
    }
});

/**
 * get groups contained in specified table
 */
function getPermissions(table) {
    var permissions = [];
    $(table).find("tbody tr").each(function(i) {
        permissions.push({
            groupId: String($(this).data("id")),
            allowCreate: $(this).find("input.allow_add").prop("checked"),
            allowView: $(this).find("input.allow_view").prop("checked"),
            allowUpdate: $(this).find("input.allow_update").prop("checked"),
            allowDelete: $(this).find("input.allow_delete").prop("checked")
        });
    });
    return (permissions);
}

/**
 * add new permission to template permission list table
 */
function appendPermission(table, id, name, allowCreate, allowView, allowUpdate, allowDelete) {
    var html = "";
    html += '<tr data-id="' + id + '">';
    html += '<td><a class="button btn_delete_row"><span class="icon"><i class="fa fa-trash"></i></span><span>Delete</span></a></td>';
    html += "<td>" + name + "</td>";
    html += '<td class="has-text-centered"><input class="allow_add" type="checkbox" ' + (allowCreate ? "checked" : "") + '/></td>';
    html += '<td class="has-text-centered"><input class="allow_view" type="checkbox" ' + (allowView ? "checked" : "") + '/></td>';
    html += '<td class="has-text-centered"><input class="allow_update" type="checkbox" ' + (allowUpdate ? "checked" : "") + '/></td>';
    html += '<td class="has-text-centered"><input class="allow_delete" type="checkbox" ' + (allowDelete ? "checked" : "") + '/></td>';
    html += "</tr>";
    $(table).find("tbody").append(html);
}

/**
 * add permission button click event
 * description: add selected group on table, reset group select combo & disable this button again
 */
$("a.btn_add_template_permission").click(function(e) {
    var o = $(this).closest("p").find("select.template_group_list option:selected");
    appendPermission($(this).closest("div.tab-content").find("table"), $(o).data("id"), $(o).data("name"), true, true, true, true);
    $("select.template_group_list").val("");
    $(this).addClass("is-disabled");
});


/**
 * launch search on start
 */
$("form#frm_admin_search").submit();