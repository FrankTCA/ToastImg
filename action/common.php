<?php
function verify_file($file_name): bool {
    if (strlen($file_name) > 64) {
        return false;
    }
    $regex = '/^[a-zA-Z0-9_\- ]+\.[a-zA-Z0-9]+$/';
    return (preg_match($regex, $file_name) === 1);
}

function resize_image($file, $ext, $w, $h) {
    if ($ext == "jpg" || $ext == "jpeg") {
        $image = imagecreatefromjpeg($file);
    } else if ($ext == "png") {
        $image = imagecreatefrompng($file);
    } else if ($ext == "gif") {
        $image = imagecreatefromgif($file);
    } else if ($ext == "webp") {
        $image = imagecreatefromwebp($file);
    } else if ($ext == "avif") {
        $image = imagecreatefromavif($file);
    } else {
        return null;
    }

    return imagescale($image, $w, $h);
}

function save_image($image, $filename, $ext): void {
    if ($ext == "jpg" || $ext == "jpeg") {
        imagejpeg($image, $filename);
    } else if ($ext == "png") {
        imagepng($image, $filename);
    } else if ($ext == "gif") {
        imagegif($image, $filename);
    } else if ($ext == "webp") {
        imagewebp($image, $filename);
    }
}
