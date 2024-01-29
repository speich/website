<?php
require_once __DIR__.'/../../../../library/vendor/autoload.php';

// resize image on the fly
if (isset($_SERVER['PATH_INFO'], $_GET['w'])) {
    $imgPath = __DIR__.'/../../../..'.$_SERVER['PATH_INFO'];

    // set headers to allow for caching of photos
    $etag = md5($imgPath.$_GET['w']);   // include width to make different versions of different size, but same image cacheable
    $etagHeader = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false;
    header('ETag: "'.$etag.'"');
    header('Cache-Control: max-age=86400');
    if ($etagHeader === $etag) {
        http_response_code(304);    // 304 Not Modified
    } else {
        // resize image
        $newWidth = $_GET['w'];
        $img = new Imagick($imgPath);
        $img->thumbnailImage($newWidth, $newWidth, true);
        header('Content-Type: '.$img->getImageMimeType());
        echo $img->getImageBlob();
    }
} else {
    http_response_code(400);
}