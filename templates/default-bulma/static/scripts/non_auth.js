$("form#frm_signin").submit(function (e) {
    e.preventDefault();
    var self = this;
    $(self).clearFormValidationMessages();
    var xhr = new XMLHttpRequest();
    xhr.open($(this).attr("method"), $(this).attr("action"), true);
    xhr.onreadystatechange = function (e) {
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
                $(self).enableSubmit();
                switch (this.status) {
                    case 404:
                        $(self).putFormValidationWarning("c_signin_email", SIGN_IN_EMAIL_NOT_FOUND);
                        break;
                    case 400:
                        $(self).putFormValidationError("c_signin_submit", SIGN_IN_GENERAL_ERROR_MESSAGE);
                        break;
                    case 200:
                        if (result === null) {
                            $(self).putFormValidationError("c_signin_submit", SIGN_IN_GENERAL_ERROR_MESSAGE);
                        } else {
                            if (!result.success) {
                                $(self).putFormValidationError("c_signin_password", SIGN_IN_INVALID_PASSWORD);
                            } else {
                                window.location.reload();
                            }
                        }
                        break;
                    default:
                        $(self).putFormValidationError("c_signin_submit", SIGN_IN_GENERAL_ERROR_MESSAGE);
                        break;
                }
            }
        }
    };
    xhr.send(new FormData($(this)[0]), null, 2);
    $(self).disableSubmit();
});

$("form#frm_signup").submit(function (e) {
    e.preventDefault();
    var self = this;
    $(self).clearFormValidationMessages();
    var xhr = new XMLHttpRequest();
    xhr.open($(this).attr("method"), $(this).attr("action"), true);
    xhr.onreadystatechange = function (e) {
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
                $(self).enableSubmit();
                switch (this.status) {
                    case 400:
                        $(self).putFormValidationError("c_signup_submit", SIGN_UP_GENERAL_ERROR_MESSAGE);
                        break;
                    case 409:
                        $(self).putFormValidationError("c_signup_email", SIGN_UP_EMAIL_EXISTS);
                        break;
                    case 200:
                        if (result === null) {
                            $(self).putFormValidationError("c_signup_submit", SIGN_UP_GENERAL_ERROR_MESSAGE);
                        } else {
                            if (!result.success) {
                                $(self).putFormValidationError("c_signup_submit", SIGN_UP_GENERAL_ERROR_MESSAGE);
                            } else {
                                // auto-start session with created user
                                $("p#c_signin_email input").val($("p#c_signup_email input").val());
                                $("p#c_signin_password input").val($("p#c_signup_password input").val());
                                $("form#frm_signin").submit();
                            }
                        }
                        break;
                    default:
                        $(self).putFormValidationError("c_signup_submit", SIGN_UP_GENERAL_ERROR_MESSAGE);
                        break;
                }
            }
        }
    };
    xhr.send(new FormData($(this)[0]), null, 2);
    $(self).disableSubmit();
});

$("form#frm_recover_account").submit(function (e) {
    e.preventDefault();
    var self = this;
    $(self).clearFormValidationMessages();
    var xhr = new XMLHttpRequest();
    xhr.open($(this).attr("method"), $(this).attr("action"), true);
    xhr.onreadystatechange = function (e) {
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
                $(self).enableSubmit();
                switch (this.status) {
                    case 404:
                        $(self).putFormValidationWarning("c_recover_account_email", RECOVER_ACCOUNT_EMAIL_NOT_FOUND_ON_SERVER);
                        break;
                    case 400:
                        $(self).putFormValidationWarning("c_recover_account_email", RECOVER_ACCOUNT_EMAIL_FIELD_REQUIRED);
                        break;
                    case 200:
                        if (result === null) {
                            $(self).putFormValidationError("c_recover_account_submit", RECOVER_ACCOUNT_GENERAL_ERROR_MESSAGE);
                        } else {
                            if (!result.success) {
                                $(self).putFormValidationError("c_recover_account_submit", RECOVER_ACCOUNT_GENERAL_ERROR_MESSAGE);
                            } else {
                                //window.location.reload();
                                $(self).putFormValidationSuccess("c_recover_account_submit", RECOVER_ACCOUNT_SUCCESS_MESSAGE);
                            }
                        }
                        break;
                    default:
                        $(self).putFormValidationError("c_recover_account_submit", RECOVER_ACCOUNT_GENERAL_ERROR_MESSAGE);
                }
            }
        }
    };
    xhr.send(new FormData($(this)[0]), null, 2);
    $(self).disableSubmit();
});