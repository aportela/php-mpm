"use strict";

/**
 * fill users table data
 */
function fillTable(actualPage, totalPages, users) {
    mpm.pagination.setControls(actualPage, totalPages);
    var html = null;
    if (users && users.length > 0) {
        var userId = $("a#a_profile").data("id");
        for (var i = 0; i < users.length; i++) {
            html += '<tr data-id="' + users[i].id + '">';
            if (users[i].id != userId) {
                html += '<td class="has-text-centered ignore_on_export"><a class="button is-small is-info modal-button btn_update_user" data-target="#modal_update">Update</a> <a class="button is-small is-danger modal-button btn_delete_user" data-target="#modal_delete">Delete</a></td>'
            } else {
                html += '<td class="has-text-centered ignore_on_export"><a class="button is-small is-info modal-button btn_update_user" data-target="#modal_update">Update</a></td>'
            }            
            if (users[i].type == 1) {
                html += '<td ><span class="icon is-small"><i class="fa fa-1x fa-user-md" aria-hidden="true"></i></span> <span>super</span></td>';
            } else {
                html += '<td><span class="icon is-small"><i class="fa fa-1x fa-user" aria-hidden="true"></i></span> <span>normal</span></td>';
            }
            html += '<td  class="is-small">' + users[i].name + '</td>';
            html += '<td><a href="mailto:' + users[i].email + '">' + users[i].email + '<a/></td>';
            html += '<td data-id="' + users[i].creatorId + '">' + (users[i].creatorId != users[i].id ? users[i].creatorName : "auto-register") + '</td>';
            html += '<td data-date="' + users[i].creationDate + '">' + new moment(users[i].creationDate).fromNow() + '</td>';
            html += '</tr>';
        }
    }
    $("table#users tbody").html(html);
}

/**
 * add user modal form submit event
 */
$("form#frm_add_user").submit(function(e) {
    e.preventDefault();
    var json = {
        id: mpm.util.uuid(),
        email: $(this).find('input[name="email"]').val(),
        password: $(this).find('input[name="password"]').val(),
        name: $(this).find('input[name="name"]').val(),
        type: $(this).find('select[name="type"]').val()
    };
    mpm.form.submitJSON(this, json, function(httpStatusCode, response) {
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
 * update user modal form submit event
 */
$("form#frm_update_user").submit(function(e) {
    e.preventDefault();
    var json = {
        id: $(this).find('input[name="id"]').val(),
        email: $(this).find('input[name="email"]').val(),
        password: $(this).find('input[name="password"]').val(),
        name: $(this).find('input[name="name"]').val(),
        type: $(this).find('select[name="type"]').val()
    };
    mpm.form.submitJSON(this, json, function(httpStatusCode, response) {
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
 * delete user modal form submit event
 */
$("form#frm_delete_user").submit(function(e) {
    e.preventDefault();
    var json = {
        id: $(this).find('input[name="id"]').val(),
    };
    mpm.form.submitJSON(this, json, function(httpStatusCode, response) {
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
$('table thead').on("click", ".btn_add_user", function(e) {
    mpm.form.reset($("form#frm_add_user"));
});

/**
 * reset & assign form values before show modal
 */
$('table tbody').on("click", ".btn_update_user", function(e) {
    mpm.form.reset($("form#frm_update_user"));
    var tr = $(this).closest("tr");
    $("input#update_user_id").val($(tr).data("id"));
    $("input#update_user_email").val($(tr).find("td:nth-child(4)").text());
    $("input#update_user_name").val($(tr).find("td:nth-child(3)").text());
    $("select#update_user_type").val(($(tr).find("td:nth-child(2)").text().trim() == "super" ? "1" : "0"));
});

/**
 * reset & assign form values before show modal
 */
$('table tbody').on("click", ".btn_delete_user", function(e) {
    mpm.form.reset($("form#frm_delete_user"));
    var tr = $(this).closest("tr");
    $("input#delete_user_id").val($(tr).data("id"));
    $("strong#delete_user_name").text($(tr).find("td:nth-child(3)").text());
});

/**
 * launch search on start
 */
$("form#frm_admin_search").submit();