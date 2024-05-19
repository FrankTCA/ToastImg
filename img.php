<?php
require "../sso/common.php";
require "creds.php";
if (!isset($_GET["f"]) && !isset($_GET["fname"])) {
    http_response_code(400);
    die("Didn't get enough info!");
}

function download_file($filename, $chunksize) {
    if (file_exists($filename)) {
        set_time_limit(600);
        $size = intval(sprintf("%u", filesize($filename)));

        header('Content-Transfer-Encoding: binary');
        header('Content-Length: '.$size);
        if ($size > $chunksize) {
            $handler = fopen($filename, "rb");

            while (!feof($handler)) {
                print(@fread($handler, $chunksize));

                ob_flush();
                flush();
            }

            fclose($handler);
        }
    } else {
        $file_data = file_get_contents($filename);
        echo $file_data;
    }
}

if (isset($_GET["f"])) {
    $conn = mysqli_connect(get_database_host(), get_database_username(), get_database_password(), get_database_db());
    if ($conn->connect_error) {
        die("ERROR: Could not connect to database!");
    }
    $sql = $conn->prepare("SELECT * FROM files WHERE access_id = ?;");
    $access_id = $_GET["f"];
    $sql->bind_param('s', $access_id);
    $sql->execute();

    if ($result = $sql->get_result()) {
        while ($row = $result->fetch_assoc()) {
            header("Content-Type: " . $row["mime_type"]);
            $sql2 = $conn->prepare("INSERT INTO image_access_log (image_id, ip, user_agent, country) VALUES (?, ?, ?, ?);");
            $iid = $row['id'];
            $user_ip = getUserIP();
            $user_agent = $_SERVER["HTTP_USER_AGENT"];
            $user_country = $_SERVER['HTTP_CF_IPCOUNTRY'];
            $sql2->bind_param('isss', $iid, $user_ip, $user_agent, $user_country);
            $sql2->execute();
            $filename = "images/" . $row["user_name"] . "/" . $row["file_name"];
            $chunksize = 5 * (1024 * 1024);
            download_file($filename, $chunksize);
            $conn->close();
            exit;
        }
    }
    http_response_code(404);
    die("ERROR: File not found.");
} else if (isset($_GET["fname"])) {
    validate_token("https://infotoast.org/aka/img.php");
    $filename = "images/" . get_username() . "/" . $_GET["fname"];
    $chunksize = 5 * (1024 * 1024);
    download_file($filename, $chunksize);
}


