function refreshGroupsTable() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/api/group/search.php", true);
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
                                    html += '<td class="has-text-centered"><a class="button is-small is-info modal-button" data-target="#modal_update">Update</a> <a class="button is-small is-danger modal-button"  data-target="#modal_delete">Delete</a></td>';
                                    html += '<td>' + result.results[i].name + '</td>';
                                    html += '<td>' + (result.results[i].description ? result.results[i].description : "") + '</td>';
                                    html += '<td data-id="' + result.results[i].creatorId + '">' + (result.results[i].creatorId != result.results[i].id ? result.results[i].creatorName : "auto-register") + '</td>';
                                    html += '<td data-date="' + result.results[i].creationDate + '">' + new moment(result.results[i].creationDate).fromNow() + '</td>';
                                    html += '</tr>';
                                }
                            }
                            $("table#groups tbody").html(html);
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