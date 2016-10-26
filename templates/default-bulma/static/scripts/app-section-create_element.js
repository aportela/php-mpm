"use strict";

$("div#element").on("submit", "form#frm_new_element", function (e) {
    e.preventDefault();
    console.log("submit");
});

var templateId = mpm.url.queryString().template_id;

if (templateId) {
    mpm.element.create(templateId, function (httpStatusCode, response) {
        switch (httpStatusCode) {
            case 200:
                if (!(response && response.success)) {
                    mpm.error.showModal();
                } else {
                    $("div#element").html(response.data.htmlForm);
                }
                break;
            default:
                mpm.error.showModal();
                break;
        }
    });
} else {
    mpm.error.showModal();
}