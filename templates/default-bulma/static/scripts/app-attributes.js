function refreshAttributesTable() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/api/attribute/search.php", true);
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
                                    html += '<td>' + result.results[i].description + '</td>';
                                    html += '<td>';
                                    switch (parseInt(result.results[i].type)) {
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
                                    html += '<td data-id="' + result.results[i].creatorId + '">' + (result.results[i].creatorId != result.results[i].id ? result.results[i].creatorName : "auto-register") + '</td>';
                                    html += '<td data-date="' + result.results[i].creationDate + '">' + new moment(result.results[i].creationDate).fromNow() + '</td>';
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