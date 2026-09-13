<?php

use PhotoDb\PhotoDb;
use PhotoDb\PhotoDetail;
use PhotoDb\SqlPhotoDetail;


require_once __DIR__.'/../../scripts/php/inc_script.php';

if (isset($_GET['imgId'])) {
    $imgId = $_GET['imgId'];
} else {
    header('Location: https://www.speich.net/photo/photodb/photo.php');
}

// TODO: move date scanned out of Images?

$photoDb = new PhotoDb($web->getWebRoot());
$photoDb->connect();
$photo = new PhotoDetail($photoDb, $imgId, $language);
$title = $photo->renderTitle();
if ($language->get() === 'de') {
    $pageTitle = $title.' | Fotodatenbank';
    $metaDesc = ($photo->data['imgDesc'] ?: $title).'. Ein Bild fotografiert von Simon Speich zum Thema '.$photo->data['themes'].'.';
} else {
    $pageTitle = $title.' | Photo database';
    $metaDesc = $title.'. A photo taken by Simon Speich about the topic '.$photo->data['themes'].'.';
}