function refreshAttributesTable() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/api/attribute/search.php", true);
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
                                    html += '<td class="has-text-centered"><a class="button is-small is-info modal-button" data-target="#modal_update">Update</a> <a class="button is-small is-danger modal-button"  data-target="#modal_delete">Delete</a></td>';
                                    html += '<td>' + response.data.results[i].name + '</td>';
                                    html += '<td>' + response.data.results[i].description + '</td>';
                                    html += '<td>';
                                    switch (parseInt(response.data.results[i].type)) {
                                        case 1:
                                            html += '<span class="icon is-small"><i class="fa fa-1x fa-file-text" aria-hidden="true"></i></span> short text';
                                            break;
                                        case 2:
                                            html += '<span class="icon is-small"><i class="fa fa-1x fa-file-text-o" aria-hidden="true"></i></span> long text';
                                            break;
                                        case 3:
                                            html += '<span class="icon is-small"><i class="fa fa-1x fa-square" aria-hidden="true"></i></span> number integer';
                                            break;
                                        case 4:
                                            html += '<span class="icon is-small"><i class="fa fa-1x fa-square-o" aria-hidden="true"></i></span> number decimal';
                                            break;
                                        case 5:
                                            html += '<span class="icon is-small"><i class="fa fa-1x fa-calendar" aria-hidden="true"></i></span> date';
                                            break;
                                        case 6:
                                            html += '<span class="icon is-small"><i class="fa fa-1x fa-clock-o" aria-hidden="true"></i></span> time';
                                            break;
                                        case 7:
                                            html += '<span class="icon is-small"><i class="fa fa-1x fa-calendar-check-o" aria-hidden="true"></i></span> datetime';
                                            break;
                                        default:
                                            html += '<span class="icon is-small"><i class="fa fa-1x fa-user-md" aria-hidden="true"></i></span> none';
                                            break;
                                    }
                                    html += '</td>';
                                    html += '<td data-id="' + response.data.results[i].creatorId + '">' + (response.data.results[i].creatorId != response.data.results[i].id ? response.data.results[i].creatorName : "auto-register") + '</td>';
                                    html += '<td data-date="' + response.data.results[i].creationDate + '">' + new moment(response.data.results[i].creationDate).fromNow() + '</td>';
                                    html += '</tr>';
                                }
                            }
                            $("table#attributes tbody").html(html);
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