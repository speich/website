<?php

use PhotoDb\PhotoDb;
use PhotoDb\PhotoDetail;
use PhotoDb\SqlPhotoDetail;


require_once __DIR__.'/../../scripts/php/inc_script.php';
require_once __DIR__.'/photo_inc.php';

if (isset($_GET['imgId'])) {
    $imgId = $_GET['imgId'];
} else {
    header('Location: https://www.speich.net/photo/photodb/photo.php');
}

// TODO: move date scanned out of Images?

// pass data to js
$jsConfig = [
    'lat' => empty($photo[0]['imgLat']) ? null : $photo[0]['imgLat'],
    'lng' => empty($photo[0]['imgLng']) ? null : $photo[0]['imgLng']
];
$jsConfig = json_encode($jsConfig, JSON_NUMERIC_CHECK);
$jsConfig = htmlspecialchars($jsConfig, ENT_COMPAT, $web->charset);

$photoDb = new PhotoDb($web->getWebRoot());
$photoDb->connect();
$photoDetail = new PhotoDetail($photoDb);
$sql = new SqlPhotoDetail();
$sql->imgId = $imgId;
$sql->setLangPostfix($language);
$photo = $photoDetail->get($sql);