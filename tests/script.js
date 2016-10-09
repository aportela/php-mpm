$("form#frm_u_exists").submit(function(e) {
    e.preventDefault();
    e.stopPropagation();
    $("div").removeClass("has-success has-warning has-danger");
    $("div.form-control-feedback").text("");
    var xhr = new XMLHttpRequest();
    xhr.open($(this).attr("method"), $(this).attr("action"), true);
    xhr.onreadystatechange = function(e) {
        if (this.readyState == 4) {
            switch (xhr.status) {
                case 200:
                    $("div#fg_u_exists_email").addClass("has-warning");
                    $("div#feedback_u_exists_email").text("email already used in system");
                    break;
                case 404:
                    $("div#fg_u_exists_email").addClass("has-success");
                    $("div#feedback_u_exists_email").text("email not found in system");
                    break;
                default:
                    $("div#fg_u_exists_submit").addClass("has-danger");
                    $("div#feedback_u_exists_submit").text("general error");
                    break;
            }
        }
    };
    xhr.send(new FormData($(this)[0]), null, 2);
});

$("form#frm_u_add").submit(function(e) {
    e.preventDefault();
    e.stopPropagation();
    $("div").removeClass("has-success has-warning has-danger");
    $("div.form-control-feedback").text("");
    var xhr = new XMLHttpRequest();
    xhr.open($(this).attr("method"), $(this).attr("action"), true);
    xhr.onreadystatechange = function(e) {
        if (this.readyState == 4) {
            switch (xhr.status) {
                case 200:
                    $("div#fg_u_add_submit").addClass("has-success");
                    $("div#feedback_u_add_submit").text("user created ok");
                    break;
                case 409:
                    $("div#fg_u_add_submit").addClass("has-danger");
                    $("div#feedback_u_add_submit").text("already exists");
                    break;
                default:
                    $("div#fg_u_add_submit").addClass("has-danger");
                    $("div#feedback_u_add_submit").text("general error");
                    break;
            }
        }
    };
    xhr.send(new FormData($(this)[0]), null, 2);
});

$("form#frm_u_login").submit(function(e) {
    e.preventDefault();
    e.stopPropagation();
    $("div").removeClass("has-success has-warning has-danger");
    $("div.form-control-feedback").text("");
    var xhr = new XMLHttpRequest();
    xhr.open($(this).attr("method"), $(this).attr("action"), true);
    xhr.onreadystatechange = function(e) {
        if (this.readyState == 4) {
            switch (xhr.status) {
                case 200:
                    var result = null;
                    try {
                        result = JSON.parse(xhr.responseText);
                    } catch (e) {
                        console.groupCollapsed("Error parsing JSON response");
                        console.log(e);
                        console.log(xhr.responseText);
                        console.groupEnd();
                    } finally {
                        if (result.success) {
                            $("div#fg_u_login_submit").addClass("has-success");
                            $("div#feedback_u_login_submit").text("login ok");
                        } else {
                            $("div#fg_u_login_password").addClass("has-warning");
                            $("div#feedback_u_login_password").text("invalid password");
                        }
                    }
                    break;
                case 404:
                    $("div#fg_u_login_email").addClass("has-warning");
                    $("div#feedback_u_login_email").text("email not found in system");
                    break;
                default:
                    $("div#fg_u_login_submit").addClass("has-danger");
                    $("div#feedback_u_login_submit").text("general error");
                    break;
            }
        }
    };
    xhr.send(new FormData($(this)[0]), null, 2);
});