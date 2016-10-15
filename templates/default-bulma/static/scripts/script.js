$.fn.clearValidationMessages = function() {
    $("input.input").removeClass("is-danger").removeClass("is-warning");
    $("span.help").remove();
}

$.fn.putValidationWarning = function(elementId, message) {
    var element = $("p#" + elementId);
    $(element).find("input.input").addClass("is-warning");
    $(element).append('<span class="help is-warning">' + message + '</span>');
}

$.fn.putValidationError = function(elementId, message) {
    var element = $("p#" + elementId);
    $(element).find("input.input").addClass("is-danger");
    $(element).append('<span class="help is-danger">' + message + '</span>');
}

$.fn.disableSubmit = function() {
    $("button[type=submit]").addClass("ajax_disabled").prop("disabled", true);
}

$.fn.enableSubmit = function() {
    $("button.ajax_disabled").removeClass("ajax_disabled").prop("disabled", false);
}

$("div.tabs ul li a").click(function(e) {
    e.preventDefault();
    $(this).closest("ul").find("li").removeClass("is-active");
    $(this).closest("li").addClass("is-active");
    $(".tab-content").addClass("is-hidden");
    $("#" + $(this).data("target")).removeClass("is-hidden");
});


$("a#signout").click(function(e) {
    e.preventDefault();
    var xhr = new XMLHttpRequest();
    xhr.open("GET", $(this).attr("href"), true);
    xhr.onreadystatechange = function(e) {
        if (this.readyState == 4) {
            var result = null;
            try {
                result = JSON.parse(xhr.responseText);
            } catch (e) {
                console.groupCollapsed("Error parsing JSON response");
                console.log(e);
                console.log(xhr.responseText);
                console.groupEnd();
            } finally {
                window.location.reload();
            }
        }
    };
    xhr.send();
});




if ($("table#users").length == 1) {
    refreshUsersTable();
} else if ($("table#groups").length == 1) {
    refreshGroupsTable();
} else if ($("table#attributes").length == 1) {
    refreshAttributesTable();
}

$('table tbody').on("click", ".modal-button", function() {
    var target = $(this).data('target');
    $('html').addClass('is-clipped');
    $(target).addClass('is-active');
    $(".modal_error").addClass("is-hidden");
});

$('body').on("click", '.modal-background, .modal_close', function() {
    $('html').removeClass('is-clipped');
    $('div.modal').removeClass('is-active');
});