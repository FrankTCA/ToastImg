<?php
function verify_file($file_name) {
    if (strlen($file_name) > 64) {
        return false;
    }
    $regex = '/^[a-zA-Z0-9_\- ]+\.[a-zA-Z0-9]+$/';
    return (preg_match($regex, $file_name) === 1);
}
