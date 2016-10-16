$("form.frm_search_groups").submit(function(e) {
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
                            html += '<td class="has-text-centered ignore_on_export"><a class="button is-small is-info modal-button btn_update_group" data-target="#modal_update">Update</a> <a class="button is-small is-danger modal-button btn_delete_group" data-target="#modal_delete">Delete</a></td>';
                            html += '<td>' + result.data.results[i].name + '</td>';
                            html += '<td>' + (result.data.results[i].description ? result.data.results[i].description : "") + '</td>';
                            html += '<td>0</td>';
                            html += '<td data-id="' + result.data.results[i].creatorId + '">' + (result.data.results[i].creatorId != result.data.results[i].id ? result.data.results[i].creatorName : "auto-register") + '</td>';
                            html += '<td data-date="' + result.data.results[i].creationDate + '">' + new moment(result.data.results[i].creationDate).fromNow() + '</td>';
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

$("form#frm_delete_group").submit(function(e) {
    e.preventDefault();
    mpm.form.submit(this, function(httpStatusCode, result) {
        switch (httpStatusCode) {
            case 200:
                $('html').removeClass('is-clipped');
                $('div.modal').removeClass('is-active');
                $("form.frm_search_groups").submit();
                break;
            default:
                mpm.error.showModal();
                break;
        }
    });
});

$("form#frm_update_group").submit(function(e) {
    e.preventDefault();
    mpm.form.submit(this, function(httpStatusCode, result) {
        switch (httpStatusCode) {
            case 200:
                $('html').removeClass('is-clipped');
                $('div.modal').removeClass('is-active');
                $("form.frm_search_groups").submit();
                break;
            default:
                mpm.error.showModal();
                break;
        }
    });
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

$("form.frm_search_groups").submit();