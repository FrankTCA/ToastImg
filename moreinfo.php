<?php
require 'creds.php';
require "../sso/common.php";
validate_token("https://infotoast.org/img/moreinfo.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>More Info</title>
    <script type="text/javascript" src="resources/js/jquery-3.7.1.min.js"></script>
    <script type="text/javascript" src="/sso/resources/node_modules/js-cookie/dist/js.cookie.min.js"></script>
    <script type="text/javascript" src="/sso/resources/login-box.js"></script>
    <link rel="stylesheet" type="text/css" href="/sso/resources/login-box.css"/>
    <link rel="stylesheet" href="resources/css/old.css"/>
    <link rel="stylesheet" href="resources/css/global.css"/>
    <meta name="robots" content="noindex,nofollow">
</head>
<body>
<div class="top">
    <div class="topleft">
        <h1><a href="/img/">◀ More Info</a></h1>
    </div>
    <div class="topright">
        <div class="loginbutton"></div>
    </div>
</div>
<?php

$conn = new mysqli(get_database_host(), get_database_username(), get_database_password(), get_database_db());

if ($conn->connect_error) {
    die("Error connecting to database!");
}

$link = $_GET['file'];
$userid = get_user_id();

$stmt = $conn->prepare("SELECT * FROM `files` WHERE user_id = ? AND file_name = ?;");
$uid = get_user_id();
$lnk = $link;
$stmt->bind_param("is", $uid, $lnk);

$stmt->execute();
$results = $stmt->get_result();

while ($row = mysqli_fetch_assoc($results)) {
    $linkid = $row['id'];
    if ($row['user_id'] != $userid) {
        ?>
        <div class="container" id="differentuser">
            <h1 class="center">Access denied.</h1>
            <p class="center">You are attempting to see someone else's link!</p>
            <p class="center">Go back to the dashboard and report this problem if it continues.</p>
        </div>
        <?php
    } else {
        ?>
        <div class="theBody">
        <div class="iconBodyHeader">
            <h1>📋Link access log</h1>
        </div>
        <div class="iconBodyHeader">

            <p class="center">

                <?php
                $canSeeIps = get_user_level() > 2;

                if ($canSeeIps) {
                    echo "Your user account has been allowed to see user IPs. Use this privilege with caution.";
                } else {
                    echo "IP visibility has been turned off for your account. You can still see the countries and user agents of users. Please purchase Super Premium to see IPs.";
                }
                ?></p>
            <div style="overflow-x:auto;">
                <table style="width: 100%">
                    <tr>
                        <th style="width: 5%">Log ID</th>
                        <?php
                        if ($canSeeIps) {
                            ?>
                            <th style="width: 15%">Client IP</th>
                            <?php
                        }
                        ?>
                        <th style="width: 10%">Timestamp Accessed</th>
                        <th>User Agent</th>
                        <th style="width: 15%">Country</th>
                    </tr>
                    <?php
                    $stmt3 = $conn->prepare("SELECT * FROM `image_access_log` WHERE image_id = ?;");
                    $lid = $linkid;
                    $stmt3->bind_param("i", $lid);

                    $stmt3->execute();
                    $results3 = $stmt3->get_result();

                    while ($row3 = mysqli_fetch_assoc($results3)) {
                        echo "<tr><td>".$row3['id']."</td><td>";
                        if ($canSeeIps) {
                            echo $row3['ip']."</td><td>";
                        }
                        echo $row3['timestamp']."</td><td>".$row3['user_agent']."</td><td>".$row3['country']."</td></tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
        </div><?php
    }
}
?>
</body>
</html>
