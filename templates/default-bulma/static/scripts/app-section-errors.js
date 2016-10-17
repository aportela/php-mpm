$("form.frm_search_errors").submit(function(e) {
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
                            html += '<tr>';
                            html += '<td data-date="' + result.data.results[i].creationDate + '">' + new moment(result.data.results[i].creationDate).fromNow() + '</td>';
                            html += '<td>' + result.data.results[i].userName + '</td>';
                            html += '<td>' + result.data.results[i].remoteAddress + '</td>';
                            var os = mpm.utils.getOSFromUserAgent(result.data.results[i].userAgent);
                            html += '<td>' + (os ? os : "unknown") + '</td>';
                            var browser = mpm.utils.getBrowserFromUserAgent(result.data.results[i].userAgent);
                            html += '<td>' + (browser ? (browser.name + ' (version ' + browser.version + ')') : "unknown") + '</td>';
                            html += '<td>';
                            html += '<p><strong>File:</strong> ' + result.data.results[i].filename + ':' + result.data.results[i].line + '</p>';
                            html += '<p"><strong>Exception:</strong> ' + result.data.results[i].class + ' (' + result.data.results[i].code + ')</p>';
                            if (result.data.results[i].message) {
                                html += '<div class="card is-fullwidth"><header class="card-header"><p class="card-header-title">Message</p><a class="card-header-icon toggle_stacktrace"><i class="fa fa-angle-down"></i></a></header><div class="card-content is-hidden"><pre>' + result.data.results[i].message + '</pre></div></div>';
                            }
                            if (result.data.results[i].trace) {
                                html += '<div class="card is-fullwidth"><header class="card-header"><p class="card-header-title">Stacktrace</p><a class="card-header-icon toggle_stacktrace"><i class="fa fa-angle-down"></i></a></header><div class="card-content is-hidden"><pre>' + result.data.results[i].trace + '</pre></div></div>';
                            }
                            html += '</td>';
                            html += '</tr>';
                        }
                    }
                    $("table#errors tbody").html(html);
                }
                break;
            default:
                mpm.error.showModal();
                break;
        }
    });
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
    $("form.frm_search_errors").submit();
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
    $("form.frm_search_errors").submit();
});

$("select#s_results_page").change(function(e) {
    $(".i_page").val(1);
    $("form.frm_search_errors").submit();
});

$("form.frm_search_errors").submit();

$("table#errors").on("click", "header.card-header", function(e) {
    e.preventDefault();
    $(this).find("i").toggleClass("fa-angle-down").toggleClass("fa-angle-up");
    $(this).next("div.card-content").toggleClass("is-hidden");
});