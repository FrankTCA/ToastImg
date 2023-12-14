<?php
require "../../sso/common.php";
require "../creds.php";
require "file.php";
validate_token("https://infotoast.org/img/action/get_main_info.php");

$conn = mysqli_connect(get_database_host(), get_database_username(), get_database_password(), get_database_db());
if ($conn->connect_error) {
    die("dbconn");
}

$sql = $conn->prepare("SELECT * FROM files WHERE user_id = ?;");
$uid = get_user_id();
$sql->bind_param('i', $uid);
$sql->execute();

$data_use = 0;
$files = array();

if ($result = $sql->get_result()) {
    while ($row = $result->fetch_assoc()) {
        $data_use += $row["file_size"];
        $file = new File();
        $file->set_vars($row["id"], $row["file_name"], $row["access_id"], $row["file_size"], $row["mime_type"], $row["time_added"]);
        if (not_null($row["aka"])) {
            $file->set_aka($row["aka"]);
        }
        $files[] = $file;
    }
}

$available_data = match (get_user_level()) {
    1 => 1000000000,
    2 => 25000000000,
    default => 100000000000,
};

echo "{\"username\": \"" . get_username() . "\", \"data_available\": " . $available_data . ", \"data_used\": " . $data_use .
    ", \"files\": [";
for ($i = 0; $i < sizeof($files); $i++) {
    if ($i > 0) {
        echo ", ";
    }
    $aka_info = (is_null($files[$i]->get_aka())) ? "null" : "\"" . $files[$i]->get_aka() . "\"";
    echo "{\"id\": " . $files[$i]->get_id() . ", \"name\": \"" . $files[$i]->get_file_name() . "\", \"access_id\": \"" .
        $files[$i]->get_access_id() . "\", \"size\": " . $files[$i]->get_file_size() . ", \"type\": \"" . $files[$i]->get_mime_type() .
        "\", \"timestamp\": \"" . $files[$i]->get_timestamp() . "\", \"aka\": " . $aka_info . "}";
}
echo "]}";
