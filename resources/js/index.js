var files = [];

function get_file_by_id(id) {
    for (var i = 0; i < files.length; i++) {
        if (files[i].id === id) {
            return files[i];
        }
    }
    return false;
}

function copy_aka(id) {
    let ip = $("#aka_" + id);
    ip.select();
    navigator.clipboard.writeText(ip.attr("value"));
    $("#akabtn_" + id).text("Copied!");
    setTimeout(function() {
        $("#akabtn_" + id).text("📋");
    }, 700);
}

function copy_link(id) {
    let file = get_file_by_id(id);
    let ip = $("#copy");
    ip.attr("value", "https://infotoast.org/img/img.php?f=" + file.access_id);
    ip.show().select();
    navigator.clipboard.writeText(ip.attr("value"));
    $("#akabtn_" + id).text("Copied!");
    ip.attr("value", "").hide();
    setTimeout(function() {
        $("#akabtn_" + id).text("📋");
    }, 700);
}

function delete_file(id) {
    let file = get_file_by_id(id);
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (this.readyState == 4) {
            let data = this.responseText;
            if (data.startsWith("noinfo")) {
                $(".errorMsg").text("ERROR: Filename not provided!");
            } else if (data.startsWith("badfilename")) {
                $(".errorMsg").text("ERROR: File name not allowed!");
            } else if (data.startsWith("dbconn")) {
                $(".errorMsg").text("Error establishing database connection! Please refresh and contact frank@infotoast.org if the problem persists.");
            } else if (data.startsWith("success")) {
                $("#file_" + id).hide();
            }
        }
    }
    xhr.open("GET", "action/delete.php?fname=" + file.name, true);
    xhr.send();
}

function display_file(id) {
    let file = get_file_by_id(id);
    let readableSizeNum = file.size / 1000000;
    let readableSize = readableSizeNum.toString() + " MB";
    let akaLink = (file.aka == null) ? "<i>None</i>" : "<input type='text' id='aka_" + file.id + "' name='aka_" + file.id + "' value='infoaka.me/" + file.aka + "' width='70%' style='display: inline-block'><button onclick='copy_aka(" + file.id + ");' style='display: inline-block'>📋</button>";
    $("#files").append("<tr id='file_" + id + "'><td><a href='img.php?f=" + file.access_id + "' target='_blank'><img src='img.php?fname=" + file.name + "' width='64' height='64'></a></td><td>" + file.name + "</td><td>" + readableSize + "</td><td>" + file.timestamp + "</td><td>" + akaLink + "</td><td><a href='img.php?f=" + file.access_id + "' target='_blank'><button class='taskbtn'>🖼️</button></a><button class='taskbtn' onclick='copy_link(" + file.id + ")'>📋</button><button class='taskbtn' onclick='delete_file(" + id + ");'>🗑️</button></td><td><a href='moreinfo.php?file=" + file.name + "'>More Info</a></td></tr>");
}
$(document).ready(function() {
    $("#submitButton").hide();
    $("#copy").hide();
    $.get("action/get_main_info.php", function(data, status) {
        console.log(data);
        if (data.startsWith("dbconn")) {
            $(".errorMsg").text("ERROR: Could not connect to database! Please contact frank@infotoast.org if the problem persists after a refresh.");
        } else if (data.startsWith("{")) {
            let jsonData = JSON.parse(data);
            let percentUsed = jsonData.data_used / jsonData.data_available * 100;
            $("#dataUsageBar").progressbar({
                value: percentUsed
            });
            for (var i = 0; i < jsonData.files.length; i++) {
                files.push({
                    id: jsonData.files[i].id,
                    name: jsonData.files[i].name,
                    access_id: jsonData.files[i].access_id,
                    size: jsonData.files[i].size,
                    type: jsonData.files[i].type,
                    timestamp: jsonData.files[i].timestamp,
                    aka: jsonData.files[i].aka
                });
                display_file(jsonData.files[i].id);
                console.log(files);
            }
        }
    });

    $("#fileUpload").change(function() {
        $("#submitButton").show().click().hide();
    });

    const drop = $("#drop")
    const fileInput = $("#fileUpload")

    drop.addEventListener("dragover", (e) => {
        // prevent default to allow drop
        e.preventDefault()
    }, false)

    dropContainer.addEventListener("dragenter", () => {
        dropContainer.classList.add("drag-active")
    })

    dropContainer.addEventListener("dragleave", () => {
        dropContainer.classList.remove("drag-active")
    })

    dropContainer.addEventListener("drop", (e) => {
        e.preventDefault()
        dropContainer.classList.remove("drag-active")
        fileInput.files = e.dataTransfer.files
    })
});
