$("form#frm_signin").submit(function(e) {
    e.preventDefault();
    var json = {
        email: $(this).find('input[name="email"]').val(),
        password: $(this).find('input[name="password"]').val()
    };
    mpm.form.submitJSON(this, json, function(httpStatusCode, result) {
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