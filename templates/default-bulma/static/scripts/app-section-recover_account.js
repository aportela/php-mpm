$("form#frm_recover_account").submit(function(e) {
    e.preventDefault();
    var json = {
        email: $(this).find('input[name="email"]').val()
    };
    mpm.form.submitJSON(this, json, function(httpStatusCode, result) {
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