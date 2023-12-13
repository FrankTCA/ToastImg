<?php
/*
 * Compatible MIME Types:
 * - audio/ogg
 * - audio/wav
 * - audio/webm
 * - video/mp4
 * - video/mpeg
 * - video/webm
 * - video/quicktime
 * - image/gif
 * - image/jpeg
 * - image/png
 * - image/svg+xml
 * - image/tiff
 * - image/webp
 */
require "../sso/common.php";
require "creds.php";
validate_token("https://infotoast.org/img/upload.php");

if (!is_uploaded_file($_FILES["fileUpload"]["tmp_name"])) {
    http_response_code(400);
    die("noinfo");
}

if (!isset($_POST["aka"])) {
    $aka = null;
} else {
    $aka = $_POST["aka"];
}

$mime_type = mime_content_type($_FILES["fileUpload"]["tmp_name"]);
$allowed_file_types = ["audio/ogg", "audio/wav", "video/mp4", "audio/mpeg", "video/mpeg", "video/mp4", "video/webm", "video/quicktime", "image/gif", "image/jpeg", "image/png", "image/svg+xml", "image/tiff", "image/webp"];
if (!in_array($mime_type, $allowed_file_types)) {
    http_response_code(403);
    die("invalidmime");
}

$conn = mysqli_connect(get_database_host(), get_database_username(), get_database_password(), get_database_db());
if ($conn->connect_error) {
    die("ERROR: Could not connect to database!");
}
$sql = $conn->prepare("SELECT * FROM files WHERE user_id = ?;");
$userid = get_user_id();
$sql->bind_param('i', $userid);
$sql->execute();

$file_size = $_FILES["fileUpload"]["size"];

if ($result = $sql->get_result()) {
    $data_count = 0;
    while ($row = $result->fetch_assoc()) {
        $data_count += $row["file_size"];
        if ($_FILES["fileUpload"]["name"] == $row["file_name"]) {
            http_response_code(400);
            die("<html><head><title>File upload failed!</title><meta http-equiv='refresh' content='1,url=https://infotoast.org/img'></head><body><p>File already exists!</p></body></html>");
        }
    }
    $data_count += $file_size;
    if (get_user_level() == 1) {
        if ($data_count > 1000000000) {
            http_response_code(403);
            die("<html><head><title>File upload failed!</title><meta http-equiv='refresh' content='1,url=https://infotoast.org/img'></head><body><p>You have used more data than your plan allows for!</p></body></html>");
        }
    } else if (get_user_level() == 2) {
        if ($data_count > 25000000000) {
            http_response_code(403);
            die("<html><head><title>File upload failed!</title><meta http-equiv='refresh' content='1,url=https://infotoast.org/img'></head><body><p>You have used more data than your plan allows for!</p></body></html>");
        }
    } else if (get_user_level() >= 3) {
        if ($data_count > 100000000000) {
            http_response_code(403);
            die("<html><head><title>File upload failed!</title><meta http-equiv='refresh' content='1,url=https://infotoast.org/img'></head><body><p>You have used more data than your plan allows for!</p></body></html>");
        }
    }
}

if (!file_exists("images/" . get_username())) {
    mkdir("images/" . get_username(), '0770', true);
}

$file_name = $_FILES["fileUpload"]["name"];
move_uploaded_file($_FILES["fileUpload"]["tmp_name"], "images/" . get_username() . "/" . $file_name);
$sql2 = $conn->prepare("INSERT INTO files (user_id, user_name, file_name, access_id, file_size, mime_type, file_url, aka) VALUES (?, ?, SHA2(224, ?), ?, ?, ?, ?);");
$uid = get_user_id();
$uname = get_username();
$fname = $file_name;
$access_id_str = get_username() . "," . $file_name . "," . time();
$fsize = $file_size;
$ftype = $mime_type;
$file_url = get_username() . "/" . $file_name;
$theAka = $aka;
$sql2->bind_param('isssisss', $uid, $uname, $fname, $access_id_str, $fsize, $ftype, $file_url, $theAka);
$sql2->execute();
$conn->commit();

if ($aka !== null) {
    $sql3 = $conn->prepare("SELECT * FROM files WHERE user_id = ? AND file_name = ?;");
    $uid2 = get_user_id();
    $fname2 = $file_name;
    $sql3->bind_param('is', $uid2, $fname2);
    $sql3->execute();

    $access_id = null;

    if ($result2 = $sql3->get_result()) {
        while ($row = $result2->fetch_assoc()) {
            $access_id = $row["access_id"];
        }
    }

    $_POST["name"] = $aka;
    $_POST["url"] = "https://infotoast.org/img/img.php?f=" . $access_id;
    $_POST["called_from"] = true;
    require "../aka/php/action_mklink.php";
}

$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Success!</title>
    <meta http-equiv="refresh" content="3;url=https://infotoast.org/img/" />
    <link rel="stylesheet" type="text/css" href="resources/css/upload.css"/>
    <script type="text/javascript" src="resources/js/jquery-3.7.1.min.js"></script>
    <script type="text/javascript" src="resources/js/upload.js"></script>
</head>
<body>
<div class="inline-block">
    <svg height="128" width="128" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
         viewBox="0 0 512 512" enable-background="new 0 0 512 512" xml:space="preserve">
<g>
    <circle fill="#7DD88A" cx="256" cy="256" r="256"/>
</g>
        <g>

            <image overflow="visible" width="348" height="255" xlink:href="data:image/png;base64,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" transform="matrix(1 0 0 1 100 140)">
            </image>
            <g>
                <polyline fill="#7DD88A" points="113,263 190,366 432,152 		"/>
                <polyline fill="none" stroke="#FFFFFF" stroke-width="30" stroke-miterlimit="10" points="113,263 190,366 432,152 		"/>
            </g>
        </g>
</svg>
    <div class="above-block">
        <h1 class="theHeader">File uploaded! The page should refresh itself in 3 seconds.</h1>
        <p>If not, click <a href="https://infotoast.org/img/">here</a>.</p>
        <p id="clipboardNotice"></p>
        <input type="text" id="copy" name="copy" value="<?php echo "https://infotoast.org/img/img.php?f=" . $access_id; ?>">
    </div>
</div>
</body>
</html>
