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
    <script type="text/javascript" src="resources/jquery.min.js"></script>
    <link rel="stylesheet" href="resources/css/old.css"/>
    <link rel="stylesheet" href="resources/css/global.css"/>
    <meta name="robots" content="noindex,nofollow">
</head>
<body>
<div class="top">
    <div class="topleft">
        <h1>More Info</h1>
    </div>
    <div class="topright">
        <a href="https://infotoast.org/sso/" class="divLink" id="loginButton">
            <div class="loginbutton">
                <svg class="user" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 128 128" enable-background="new 0 0 128 128" xml:space="preserve" width="40px" height="40px"><g><path fill="#FFFFFF" fill-opacity="0" d="M30.6,111.5c5.8-29.9,18.8-49.2,33.2-49.2c14.2,0,27.1,18.9,33,48.2L30.6,111.5z"/><path d="M63.8,62.8c13.9,0,26.5,18.5,32.4,47.2l-65,0.9C37,81.7,49.7,62.8,63.8,62.8 M63.8,61.8C48.6,61.8,35.6,82.5,30,112l67.4-0.9C91.7,82.1,78.8,61.8,63.8,61.8L63.8,61.8z"/></g><line fill="none" stroke="#FFFFFF" stroke-width="0" stroke-linecap="round" stroke-linejoin="round" x1="103.5" y1="68.5" x2="82.4" y2="68.8"/><line fill="none" stroke="#FFFFFF" stroke-width="0" stroke-linecap="round" stroke-linejoin="round" x1="48.5" y1="69.3" x2="31.5" y2="69.5"/><g><path fill="#FFFFFF" fill-opacity="0" d="M64.5,59.5c-7.2,0-13-5.8-13-13v-7c0-7.2,5.8-13,13-13c7.2,0,13,5.8,13,13v7C77.5,53.7,71.7,59.5,64.5,59.5z"/><path d="M64.5,27C71.4,27,77,32.6,77,39.5v7C77,53.4,71.4,59,64.5,59C57.6,59,52,53.4,52,46.5v-7C52,32.6,57.6,27,64.5,27 M64.5,26L64.5,26C57,26,51,32,51,39.5v7C51,54,57,60,64.5,60h0C72,60,78,54,78,46.5v-7C78,32,72,26,64.5,26L64.5,26z"/></g></svg>
                <span id="loginText" class="littleMsg">Hi, <?php echo get_username() ?>!</span>
            </div>
        </a>
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
