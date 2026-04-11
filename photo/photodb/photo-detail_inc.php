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
$photo = $photoDetail->query($sql);
if ($language->get() === 'de') {
    $title = $photoDetail->renderTitle($photo, $language);
    $pageTitle = $title.' | Fotodatenbank';
    $metaDesc = ($photo['imgDesc'] ?: $title).'. Ein Bild fotografiert von Simon Speich zum Thema '.$photo['themes'].'.';
} else {
    $title = $photoDetail->renderTitle($photo, $language);
    $pageTitle = $title.' | Photo database';
    $metaDesc = $title.'. A photo taken by Simon Speich about the topic '.$photo['themes'].'.';
}