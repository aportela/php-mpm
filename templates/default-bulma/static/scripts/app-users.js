$("form.frm_search_users").submit(function (e) {
    e.preventDefault();
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/api/user/search.php", true);
    xhr.onreadystatechange = function (e) {
        if (this.readyState == 4) {
            var response = null;
            try {
                response = JSON.parse(xhr.responseText);
            } catch (e) {
                console.groupCollapsed("Error parsing JSON response");
                console.log(e);
                console.log(xhr.responseText);
                console.groupEnd();
            } finally {
                switch (this.status) {
                    case 200:
                        if (response === null) {
                            // TODO: error
                            console.log(this.status);
                        } else {
                            $(".pager_actual_page").text(response.data.pager.actualPage);
                            $(".pager_total_pages").text(response.data.pager.totalPages);
                            if (response.data.pager.actualPage < response.data.pager.totalPages) {
                                $(".btn_next_page").removeClass("is-disabled");
                            } else {
                                $(".btn_next_page").addClass("is-disabled");
                            }
                            if (response.data.pager.actualPage > 1) {
                                $(".btn_previous_page").removeClass("is-disabled");
                            } else {
                                $(".btn_previous_page").addClass("is-disabled");
                            }
                            var html = null;
                            if (response.data && response.data.results.length > 0) {
                                for (var i = 0; i < response.data.results.length; i++) {
                                    html += '<tr data-id="' + response.data.results[i].id + '">';
                                    html += '<td class="has-text-centered"><a class="button is-small is-info modal-button btn_update_user" data-target="#modal_update">Update</a> <a class="button is-small is-danger modal-button btn_delete_user"  data-target="#modal_delete">Delete</a></td>'
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
                        // TODO: error
                        console.log(this.status);
                        break;
                }
            }
        }
    }
    xhr.send(new FormData($(this)[0]), null, 2);
});

$("form#frm_delete_user").submit(function (e) {
    e.preventDefault();
    var xhr = new XMLHttpRequest();
    xhr.open($(this).attr("method"), $(this).attr("action"), true);
    xhr.onreadystatechange = function (e) {
        if (this.readyState == 4) {
            var response = null;
            try {
                response = JSON.parse(xhr.responseText);
            } catch (e) {
                console.groupCollapsed("Error parsing JSON response");
                console.log(e);
                console.log(xhr.responseText);
                console.groupEnd();
            } finally {
                switch (this.status) {
                    case 200:
                        $('html').removeClass('is-clipped');
                        $('div.modal').removeClass('is-active');
                        $("form.frm_search_users").submit();
                        break;
                    default:
                        $(".modal_error").removeClass("is-hidden");
                        $(".modal_error .message-body").text("operation error");
                        break;
                }
            }
        }
    }
    xhr.send(new FormData($(this)[0]), null, 2);
});

$("form#frm_update_user").submit(function (e) {
    e.preventDefault();
    e.stopPropagation();
    var xhr = new XMLHttpRequest();
    xhr.open($(this).attr("method"), $(this).attr("action"), true);
    xhr.onreadystatechange = function (e) {
        if (this.readyState == 4) {
            var response = null;
            try {
                response = JSON.parse(xhr.responseText);
            } catch (e) {
                console.groupCollapsed("Error parsing JSON response");
                console.log(e);
                console.log(xhr.responseText);
                console.groupEnd();
            } finally {
                switch (this.status) {
                    case 200:
                        $('html').removeClass('is-clipped');
                        $('div.modal').removeClass('is-active');
                        $("form.frm_search_users").submit();
                        break;
                    default:
                        $(".modal_error").removeClass("is-hidden");
                        $(".modal_error .message-body").text("operation error");
                        break;
                }
            }
        }
    }
    xhr.send(new FormData($(this)[0]), null, 2);
});

$('table tbody').on("click", ".btn_delete_user", function (e) {
    $("input#delete_user_id").val($(this).closest("tr").data("id"));
    $("strong#delete_user_name").text($(this).closest("tr").find("td:nth-child(3)").text());
});

$('table tbody').on("click", ".btn_update_user", function (e) {
    $("input#update_user_id").val($(this).closest("tr").data("id"));
    $("input#update_user_email").val($(this).closest("tr").find("td:nth-child(4)").text());
    $("input#update_user_name").val($(this).closest("tr").find("td:nth-child(3)").text());
});

$(".btn_previous_page").click(function (e) {
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

$(".btn_next_page").click(function (e) {
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

$("select#s_results_page").change(function (e) {
    $(".i_page").val(1);
    $("form.frm_search_users").submit();
});

$("form.frm_search_users").submit();