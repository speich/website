<?php
require_once __DIR__.'/../../../../library/vendor/autoload.php';

// resize image on the fly
if (isset($_SERVER['PATH_INFO'], $_GET['w'])) {
    $imgPath = __DIR__.'/../../../..'.$_SERVER['PATH_INFO'];

    // set headers to allow for caching of photos
    $etag = md5($imgPath.$_GET['w']);   // include width to make different versions of different size, but same image cachable
    $etagHeader = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false;
    header("ETag: $etag");
    if ($etagHeader === $etag) {
        http_response_code(304);    // 304 Not Modified
    } else {
        // resize image
        $newWidth = $_GET['w'];
        $img = new Imagick($imgPath);
        $img->thumbnailImage($newWidth, $newWidth, true);
        header('Content-Type: '.$img->getImageMimeType());
        if ($etagHeader !== false) {
            // photo or censoring changed do not cache (next time)
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Expires: 0');
        }
        echo $img->getImageBlob();
    }
} else {
    http_response_code(400);
}