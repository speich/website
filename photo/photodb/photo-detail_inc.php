<?php

use PhotoDb\PhotoDb;
use PhotoDb\PhotoDetail;
use PhotoDb\SqlPhotoDetail;


require_once __DIR__.'/../../scripts/php/inc_script.php';
$i18n = require __DIR__.'/nls/'.$language->get().'/photo.php';

if (isset($_GET['imgId'])) {
    $imgId = $_GET['imgId'];
} else {
    header('Location: https://www.speich.net/photo/photodb/photo.php');
}

// TODO: move date scanned out of Images?

$photoDb = new PhotoDb($web->getWebRoot());
$photoDb->connect();
$photoDetail = new PhotoDetail($photoDb);
$sql = new SqlPhotoDetail();
$sql->imgId = $imgId;
$sql->setLangPostfix($language);
$photo = $photoDetail->get($sql);
if ($language->get() === 'de') {
    $pageTitle = $photo['imgTitle'].' | Fotodatenbank';
    $metaDesc = ($photo['imgDesc'] ?: $photo['imgTitle']).'. Ein Bild fotografiert von Simon Speich zum Thema '.$photo['themes'].'.';
}
else {
    $pageTitle = $photo['imgTitle'].' | Photo database';
    $metaDesc = ($photo['imgDesc'] ?: $photo['imgTitle']).'. A photo taken by Simon Speich about the topic '.$photo['themes'].'.';
}

// pass data to js
$jsConfig = [
    'lat' => empty($photo['imgLat']) ? null : $photo['imgLat'],
    'lng' => empty($photo['imgLng']) ? null : $photo['imgLng']
];
$jsConfig = json_encode($jsConfig, JSON_NUMERIC_CHECK);
$jsConfig = htmlspecialchars($jsConfig, ENT_COMPAT, $web->charset);