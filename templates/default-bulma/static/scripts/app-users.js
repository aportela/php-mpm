function refreshUsersTable() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/api/user/search.php", true);
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
                switch (this.status) {
                    case 200:
                        if (result === null) {
                            // TODO: error
                            console.log(this.status);
                        } else {
                            var html = null;
                            if (result.results && result.results.length > 0) {
                                for (var i = 0; i < result.results.length; i++) {
                                    html += '<tr data-id="' + result.results[i].id + '">';
                                    html += '<td class="has-text-centered"><a class="button is-small is-info modal-button" data-target="#modal_update">Update</a> <a class="button is-small is-danger modal-button"  data-target="#modal_delete">Delete</a></td>'
                                    if (result.results[i].type == 1) {
                                        html += '<td ><span class="icon is-small"><i class="fa fa-1x fa-user-md" aria-hidden="true"></i></span> <span>super</span></td>';
                                    } else {
                                        html += '<td><span class="icon is-small"><i class="fa fa-1x fa-user" aria-hidden="true"></i></span> <span>normal</span></td>';
                                    }
                                    html += '<td  class="is-small">' + result.results[i].name + '</td>';
                                    html += '<td><a href="mailto:' + result.results[i].email + '">' + result.results[i].email + '<a/></td>';
                                    html += '<td data-id="' + result.results[i].creatorId + '">' + (result.results[i].creatorId != result.results[i].id ? result.results[i].creatorName : "auto-register") + '</td>';
                                    html += '<td data-date="' + result.results[i].creationDate + '">' + new moment(result.results[i].creationDate).fromNow() + '</td>';
                                    html += '</tr>';
                                }
                            }
                            $("table#users tbody").html(html);
                        }
                        break;
                    default:
                        // TODO: error
                        console.log(this.status);
                        break;
                }
            }
        }
    }
    var formData = new FormData();
    formData.append("page", 1);
    formData.append("resultsPage", 16);
    xhr.send(formData, null, 2);
}