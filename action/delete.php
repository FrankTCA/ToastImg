<?php
require "../creds.php";
require "../../sso/common.php";
validate_token("https://infotoast.org/img/action/delete.php");

if (!isset($_GET["fname"])) {
    die("noinfo");
} else if (!verify_file($_GET["fname"])) {
    die("badfilename");
}

$conn = mysqli_connect(get_database_host(), get_database_username(), get_database_password(), get_database_db());
if ($conn->connect_error) {
    die("dbconn");
}

$sql = $conn->prepare("DELETE FROM files WHERE user_id = ? AND file_name LIKE ?;");
$uid = get_user_id();
$file_name = $_GET["fname"];
$sql->bind_param('is', $uid, $file_name);
$sql->execute();
$conn->commit();

$username = get_username();
$file_path = "images/" . $username . "/" . $_GET["fname"];
if (file_exists($file_path)) {
    $size = filesize($file_path);
    for ($i = 0; $i < 5; $i++) {
        $rand = random_bytes($size);
        file_put_contents($file_path, $rand);
    }
    unlink($file_path);
}
