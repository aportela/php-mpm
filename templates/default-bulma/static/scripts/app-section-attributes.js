/**
 * fill attributes table data
 */
function fillTable(actualPage, totalPages, attributes) {
    mpm.pagination.setControls(actualPage, totalPages);
    var html = null;
    if (attributes && attributes.length > 0) {
        for (var i = 0; i < attributes.length; i++) {
            html += '<tr data-id="' + attributes[i].id + '">';
            html += '<td class="has-text-centered ignore_on_export"><a class="button is-small is-info modal-button btn_update_attribute" data-target="#modal_update">Update</a> <a class="button is-small is-danger modal-button btn_delete_attribute" data-target="#modal_delete">Delete</a></td>';
            html += '<td>' + attributes[i].name + '</td>';
            html += '<td>' + (attributes[i].description ? attributes[i].description : "") + '</td>';
            html += '<td>';
            switch (parseInt(attributes[i].type)) {
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
            html += '<td data-id="' + attributes[i].creatorId + '">' + (attributes[i].creatorId != attributes[i].id ? attributes[i].creatorName : "auto-register") + '</td>';
            html += '<td data-date="' + attributes[i].creationDate + '">' + new moment(attributes[i].creationDate).fromNow() + '</td>';
            html += '</tr>';
        }
    }
    $("table#attributes tbody").html(html);
}

/**
 * add attribute modal form submit event
 */
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
 * update attribute modal form submit event
 */
$("form#frm_update_attribute").submit(function(e) {
    e.preventDefault();
    mpm.form.submit(this, function(httpStatusCode, result) {
        switch (httpStatusCode) {
            case 200:
                $('html').removeClass('is-clipped');
                $('div.modal').removeClass('is-active');
                $("form#frm_admin_search").submit();
                break;
            default:
                mpm.error.showModal();
                break;
        }
    });
});

/**
 * delete attribute modal form submit event
 */
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
                $("form#frm_admin_search").submit();
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
$('table thead').on("click", ".btn_add_attribute", function(e) {
    mpm.form.reset($("form#frm_add_attribute"));
});

/**
 * reset & assign form values before show modal
 */
$('table tbody').on("click", ".btn_update_attribute", function(e) {
    mpm.form.reset($("form#frm_delete_attribute"));
    var tr = $(this).closest("tr");
    $("input#update_attribute_id").val($(tr).data("id"));
    $("input#update_attribute_name").val($(tr).find("td:nth-child(2)").text());
    $("input#update_attribute_description").val($(tr).find("td:nth-child(3)").text());
});

/**
 * reset & assign form values before show modal
 */
$('table tbody').on("click", ".btn_delete_attribute", function(e) {
    mpm.form.reset($("form#frm_update_attribute"));
    var tr = $(this).closest("tr");
    $("input#delete_attribute_id").val($(tr).data("id"));
    $("strong#delete_attribute_name").text($(tr).find("td:nth-child(2)").text());
});

/**
 * launch search on start
 */
$("form#frm_admin_search").submit();