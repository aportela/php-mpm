$("form#frm_signin").submit(function(e) {
    e.preventDefault();
    mpm.form.submit(this, function(httpStatusCode, result) {
        switch (httpStatusCode) {
            case 404:
                mpm.form.putValidationWarning("c_signin_email", SIGN_IN_EMAIL_NOT_FOUND);
                break;
            case 400:
                mpm.form.putValidationError("c_signin_submit", SIGN_IN_GENERAL_ERROR_MESSAGE);
                break;
            case 200:
                if (result === null) {
                    mpm.form.putValidationError("c_signin_submit", SIGN_IN_GENERAL_ERROR_MESSAGE);
                } else {
                    if (!result.success) {
                        mpm.form.putValidationError("c_signin_password", SIGN_IN_INVALID_PASSWORD);
                    } else {
                        window.location.reload();
                    }
                }
                break;
            default:
                mpm.form.putValidationError("c_signin_submit", SIGN_IN_GENERAL_ERROR_MESSAGE);
                break;
        }
    });

});

$("form#frm_signup").submit(function(e) {
    e.preventDefault();
    mpm.form.submit(this, function(httpStatusCode, result) {
        switch (httpStatusCode) {
            case 400:
                mpm.form.putValidationError("c_signup_submit", SIGN_UP_GENERAL_ERROR_MESSAGE);
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

$("form#frm_recover_account").submit(function(e) {
    e.preventDefault();
    mpm.form.submit(this, function(httpStatusCode, result) {
        switch (httpStatusCode) {
            case 404:
                mpm.form.putValidationWarning("c_recover_account_email", RECOVER_ACCOUNT_EMAIL_NOT_FOUND_ON_SERVER);
                break;
            case 400:
                mpm.form.putValidationWarning("c_recover_account_email", RECOVER_ACCOUNT_EMAIL_FIELD_REQUIRED);
                break;
            case 200:
                if (result === null) {
                    mpm.form.putValidationError("c_recover_account_submit", RECOVER_ACCOUNT_GENERAL_ERROR_MESSAGE);
                } else {
                    if (!result.success) {
                        mpm.form.putValidationError("c_recover_account_submit", RECOVER_ACCOUNT_GENERAL_ERROR_MESSAGE);
                    } else {
                        mpm.form.putValidationSuccess("c_recover_account_submit", RECOVER_ACCOUNT_SUCCESS_MESSAGE);
                    }
                }
                break;
            default:
                mpm.form.putValidationError("c_recover_account_submit", RECOVER_ACCOUNT_GENERAL_ERROR_MESSAGE);
                break;
        }
    });
});