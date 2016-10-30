"use strict";

$("form#frm_signup").submit(function(e) {
    e.preventDefault();
    var json = {
        email: $(this).find('input[name="email"]').val(),
        password: $(this).find('input[name="password"]').val(),
        name: $(this).find('input[name="name"]').val()
    };
    mpm.form.submitJSON(this, json, function(httpStatusCode, result) {
        switch (httpStatusCode) {
            case 400:
                mpm.form.putValidationError("c_signup_submit", SIGN_UP_GENERAL_ERROR_MESSAGE);
                break;
            case 403:
                mpm.form.putValidationError("c_signup_submit", SIGN_UP_PUBLIC_REGISTER_NOT_ALLOWED);
                break;
            case 409:
                mpm.form.putValidationError("c_signup_email", SIGN_UP_EMAIL_EXISTS);
                break;
            case 200:
                if (result === null) {
                    mpm.form.putValidationError("c_signup_submit", SIGN_UP_GENERAL_ERROR_MESSAGE);
                } else {
                    if (!result.success) {
                        mpm.form.putValidationError("c_signup_submit", SIGN_UP_GENERAL_ERROR_MESSAGE);
                    } else {
                        // auto-start session with created user
                        $("p#c_signin_email input").val($("p#c_signup_email input").val());
                        $("p#c_signin_password input").val($("p#c_signup_password input").val());
                        $("form#frm_signin").submit();
                    }
                }
                break;
            default:
                mpm.form.putValidationError("c_signup_submit", SIGN_UP_GENERAL_ERROR_MESSAGE);
                break;
        }
    });
});