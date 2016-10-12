$("form#frm_signin").submit(function(e) {
    e.preventDefault();
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
                if (result === null) {
                    redirectToErrorPage();
                } else {
                    if (!result.success) {
                        switch (this.status) {
                            case 404:
                                break;
                            case 200:
                                break;
                            default:
                                break;
                        }
                    } else {
                        window.location.reload();
                    }
                }
            }
        }
    };
    xhr.send(new FormData($(this)[0]), null, 2);
});

$("a#logout").click(function(e) {
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
                if (result === null) {
                    redirectToErrorPage();
                } else {
                    if (!result.success) {
                        switch (this.status) {
                            case 404:
                                break;
                            case 200:
                                break;
                            default:
                                break;
                        }
                    } else {
                        window.location.reload();
                    }
                }
            }
        }
    };
    xhr.send();
});