$("form#frm_signin").submit(function(e) {
    e.preventDefault();
    var self = $(this);
    self.clearValidationMessages();
    var xhr = new XMLHttpRequest();
    xhr.open($(this).attr("method"), $(this).attr("action"), true);
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
                self.enableSubmit();
                switch (this.status) {
                    case 404:
                        self.putValidationWarning("c_email", "email not found");
                        break;
                    case 400:
                        // TODO
                        break;
                    case 200:
                        if (result === null) {
                            // TODO
                        } else {
                            if (!result.success) {
                                self.putValidationError("c_password", "invalid password");
                            } else {
                                window.location.reload();
                            }
                        }
                        break;
                    default:
                        // TODO
                        break;
                }
            }
        }
    };
    xhr.send(new FormData($(this)[0]), null, 2);
    self.disableSubmit();
});