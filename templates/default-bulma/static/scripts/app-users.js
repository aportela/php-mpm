function refreshUsersTable() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/api/user/search.php", true);
    xhr.onreadystatechange = function(e) {
        if (this.readyState == 4) {
            var response = null;
            try {
                response = JSON.parse(xhr.responseText);
            } catch (e) {
                console.groupCollapsed("Error parsing JSON response");
                console.log(e);
                console.log(xhr.responseText);
                console.groupEnd();
            } finally {
                switch (this.status) {
                    case 200:
                        if (response === null) {
                            // TODO: error
                            console.log(this.status);
                        } else {
                            var html = null;
                            if (response.data && response.data.results.length > 0) {
                                for (var i = 0; i < response.data.results.length; i++) {
                                    html += '<tr data-id="' + response.data.results[i].id + '">';
                                    html += '<td class="has-text-centered"><a class="button is-small is-info modal-button" data-target="#modal_update">Update</a> <a class="button is-small is-danger modal-button"  data-target="#modal_delete">Delete</a></td>'
                                    if (response.data.results[i].type == 1) {
                                        html += '<td ><span class="icon is-small"><i class="fa fa-1x fa-user-md" aria-hidden="true"></i></span> <span>super</span></td>';
                                    } else {
                                        html += '<td><span class="icon is-small"><i class="fa fa-1x fa-user" aria-hidden="true"></i></span> <span>normal</span></td>';
                                    }
                                    html += '<td  class="is-small">' + response.data.results[i].name + '</td>';
                                    html += '<td><a href="mailto:' + response.data.results[i].email + '">' + response.data.results[i].email + '<a/></td>';
                                    html += '<td data-id="' + response.data.results[i].creatorId + '">' + (response.data.results[i].creatorId != response.data.results[i].id ? response.data.results[i].creatorName : "auto-register") + '</td>';
                                    html += '<td data-date="' + response.data.results[i].creationDate + '">' + new moment(response.data.results[i].creationDate).fromNow() + '</td>';
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