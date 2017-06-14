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
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};

// Delete a DOM element
function removeElement(uuid) {
    var elem = document.getElementById(uuid);
    elem.parentNode.removeChild(elem);
}

// Show no data
function showNoData() {
    removeElement("inmemory_list_table");
    removeElement("inmemory_list_flush_cache");
    document.getElementById("inmemory_list_show").innerHTML = "<p>No data in cache</p>";
}

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
        deleteElement[i].addEventListener("click" , function (e) {
            if (confirm("Are you sure you want delete this list?")) {
                var uuid = this.getAttribute("data-id");

                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState === XMLHttpRequest.DONE ) {
                        if (xmlhttp.status === 204) {
                            removeElement(uuid);

                            var rows = document.getElementById("inmemory_list_table").getElementsByTagName("tr").length;
                            if((rows -1) === 0){
                                showNoData();
                            }
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
    if(ttl > 0){
        setInterval(function () {
            element.innerHTML = ttl--;

            if(ttl <= 0){
                showList()
            }
        }, 1000);
    }
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

                    for (item in list) {
                        if ({}.hasOwnProperty.call(list, item)) {
                            response += "<tr id='"+list[item]["uuid"]+"'>";
                            response += "<th class='font-normal'>"+list[item]["uuid"]+"</th>";
                            response += "<td class='font-normal'>"+list[item]["created_on"]+"</td>";
                            response += "<td class='font-normal'>"+list[item]["expires_on"]+"</td>";
                            response += "<td class='font-normal ttl' data-ttl='"+list[item]["ttl"]+"'>"+list[item]["ttl"]+"</td>";
                            response += "<td class='font-normal'>"+list[item]["size"]+"</td>";
                            response += "<td class='font-normal'>"+list[item]["chunks"]+"</td>";
                            response += "<td class='font-normal'>"+list[item]["chunk-size"]+"</td>";
                            response += "<td class='font-normal'><a href='#' class='inmemory_list_delete_element' data-id='"+list[item]["uuid"]+"'>Delete</a></td>";
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
