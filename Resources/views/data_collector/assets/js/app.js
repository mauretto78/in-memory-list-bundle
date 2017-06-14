/*
 * This file is part of the InMemoryList Bundle package.
 *
 * (c) Mauro Cassani<https://github.com/mauretto78>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Helper function: get the size of an object
Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) {
            size++;
        }
    }

    return size;
};

// Delete a DOM element
function removeElementById(uuid) {
    var elem = document.getElementById(uuid);
    elem.parentNode.removeChild(elem);
}

// Show no data
function showNoData() {
    removeElementById("inmemory_list_table");
    removeElementById("inmemory_list_flush_cache");
    document.getElementById("inmemory_list_show").innerHTML = "<p>No data in cache</p>";
}

// Show no data if table is empty
function showNoDataIfTableIsEmpty() {
    var rows = document.getElementById("inmemory_list_table").getElementsByTagName("tr").length;
    if((rows -1) === 0){
        showNoData();
    }
}

// XMLHttpRequest
var xmlhttp = new XMLHttpRequest();

// Flush entire cache
document.getElementById("inmemory_list_flush_cache").onclick = function(e) {
    if (confirm("Are you sure you want purge the entire cache?")) {
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState === XMLHttpRequest.DONE ) {
                if (xmlhttp.status === 204) {
                    showNoData();
                }
            }
        };

        xmlhttp.open("GET", "/_profiler/_inmemorylist/flush", true);
        xmlhttp.send();
    }

    e.preventDefault();
};

// Delete a list
function deleteList() {
    var deleteElement = document.getElementsByClassName("inmemory_list_delete_element");
    for (var i = 0 ; i < deleteElement.length; i++) {
        var element = deleteElement[i];
        element.addEventListener("click" , function (e) {
            if (confirm("Are you sure you want delete this list?")) {
                var uuid = this.getAttribute("data-id");

                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState === XMLHttpRequest.DONE ) {
                        if (xmlhttp.status === 204) {
                            removeElementById(uuid);
                            showNoDataIfTableIsEmpty();
                        }
                    }
                };

                xmlhttp.open("GET", "/_profiler/_inmemorylist/delete/" + uuid, true);
                xmlhttp.send();
            }

            e.preventDefault();
        });
    }
}

// Decrement Ttl
function decrTtl(ttl, element) {
    var refreshIntervalId = setInterval(function () {
        ttl = ttl - 1;
        element.innerHTML = ttl;

        if(ttl === 0){
            removeElementById(element.parentElement.getAttribute("id"));
            showNoDataIfTableIsEmpty();
            clearInterval(refreshIntervalId);
        }
    }, 1000);
}

// Ttl countdown
function ttlCountDown() {
    var ttlElement = document.getElementsByClassName("ttl");
    for (var i = 0 ; i < ttlElement.length; i++) {
        var element = ttlElement[i],
            ttl = element.getAttribute("data-ttl");

        decrTtl(ttl, element);
    }
}

// Display the list
function showList() {
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState === XMLHttpRequest.DONE ) {
            if (xmlhttp.status === 200) {
                var list = JSON.parse(xmlhttp.responseText),
                    response = "";

                if(Object.size(list)>0) {
                    response += "<table id='inmemory_list_table'>";
                    response += "<thead>";
                    response += "<tr>";
                    response += "<th class='key' scope='col'>List</td>";
                    response += "<th scope='col'>Created on</td>";
                    response += "<th scope='col'>Expires on</td>";
                    response += "<th scope='col'>Ttl</td>";
                    response += "<th scope='col'>Items</td>";
                    response += "<th scope='col'>Chunks</td>";
                    response += "<th scope='col'>Chunk size</td>";
                    response += "<th scope='col'></td>";
                    response += "</tr>";
                    response += "</thead>";

                    for (var item in list) {
                        if ({}.hasOwnProperty.call(list, item)) {
                            var listItem = list[item];

                            response += "<tr id='"+listItem["uuid"]+"'>";
                            response += "<th class='font-normal'>"+listItem["uuid"]+"</th>";
                            response += "<td class='font-normal'>"+listItem["created_on"]+"</td>";
                            response += "<td class='font-normal'>"+listItem["expires_on"]+"</td>";
                            response += "<td class='font-normal ttl' data-ttl='"+listItem["ttl"]+"'>"+listItem["ttl"]+"</td>";
                            response += "<td class='font-normal'>"+listItem["size"]+"</td>";
                            response += "<td class='font-normal'>"+listItem["chunks"]+"</td>";
                            response += "<td class='font-normal'>"+listItem["chunk-size"]+"</td>";
                            response += "<td class='font-normal'><a href='#' class='inmemory_list_delete_element' data-id='"+listItem["uuid"]+"'>Delete</a></td>";
                            response += "</tr>";
                        }
                    }

                    response += "</table>";
                } else {
                    response += "<p>No data in cache</p>";
                }

                // render content
                document.getElementById("inmemory_list_show").innerHTML = response;

                // ttl countdown
                ttlCountDown();

                // delete item
                deleteList();
            }
        }
    };

    xmlhttp.open("GET", "/_profiler/_inmemorylist/index", true);
    xmlhttp.send();
}

showList();
