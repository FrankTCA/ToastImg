<?php
require "creds.php";
require "../sso/common.php";
validate_token("https://infotoast.org/img/");
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Info Toast IMG</title>
        <link type="text/css" rel="stylesheet" href="resources/css/jquery-ui.min.css"/>
        <link type="text/css" rel="stylesheet" href="/sso/resources/login-box.css"/>
        <link type="text/css" rel="stylesheet" href="resources/css/old.css"/>
        <link type="text/css" rel="stylesheet" href="resources/css/global.css"/>
        <link type="text/css" rel="stylesheet" href="resources/css/index.css"/>
        <script type="text/javascript" src="resources/js/jquery-3.7.1.min.js"></script>
        <script type="text/javascript" src="resources/js/jquery-ui.min.js"></script>
        <script type="text/javascript" src="/sso/resources/node_modules/js-cookie/dist/js.cookie.min.js"></script>
        <script type="text/javascript" src="/sso/resources/login-box.js"></script>
        <script type="text/javascript" src="resources/js/index.js"></script>
    </head>
    <body>
        <div class="top">
            <div class="topleft">
                <h1>Info Toast Image Host</h1>
            </div>
            <div class="topright">
                <div class="loginbutton"></div>
            </div>
        </div>
        <div class="theBody">
            <div class="iconBodyHeader" id="firstHeader">
                <h2>Data Usage:</h2>
            </div>
            <p class="errorMsg"></p>
            <div class="iconSet">
                <?php
                if (get_user_level() == 1) {
                    echo "<p>You have 1 GB of data for free. Here is your usage:</p>";
                } else if (get_user_level() == 2) {
                    echo "<p>You get 25 GB of data with your plan. Here is your usage:</p>";
                } else if (get_user_level() >= 3) {
                    echo "<p>You get 200 GB of data with your plan. Here is your usage:</p>";
                }
                ?>
                <div id="dataUsageBar"></div>
            </div>
            <div class="iconBodyHeader">
                <h2>Upload:</h2>
            </div>
            <div class="iconSet">
                <form action="upload.php" enctype="multipart/form-data" method="POST">
                    AKA Link (Optional): <input type="text" id="akaLink" name="aka"><br>
                    <label for="images" class="drop" id="drop">
                        <span>Drop files here</span>
                        <input type="file" id="fileUpload" name="fileUpload" accept="audio/ogg,audio/wav,audio/webm,audio/mpeg,video/mpeg,video/mp4,video/webm,video/quicktime,image/gif,image/jpeg,image/png,image/svg+xml,image/tiff,image/webp" required>
                    </label>
                    <input type="submit" id="submitButton">
                </form>
            </div>
            <div class="iconBodyHeader">
                <h2>Your Files:</h2>
            </div>
            <div class="iconSet">
                <table id="files" width="100%">
                    <tr><th>Image</th><th>Name</th><th>Size</th><th>Time Uploaded</th><th>AKA Link</th><th>Open/Copy/Delete</th><th>Log</th></tr>
                </table>
            </div>
            <input type="text" name="copy" id="copy">
        </div>
    </body>
</html>
