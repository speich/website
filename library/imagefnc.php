<?php

use PhotoDb\PhotoDb;


require_once '../scripts/php/inc_script.php';
require_once __DIR__.'/../photo/photodb/scripts/php/PhotoDb.php';

$db = new PhotoDb($web->getWebRoot());
$sql = 'SELECT ImgFolder, ImgName FROM Images WHERE RatingId = 3 ORDER BY RANDOM() LIMIT 1';
$db->connect();
$stmt = $db->db->query($sql);
$photo = $stmt->fetchObject();
$photo = $db->getPath('img').$photo->ImgFolder.'/'.$photo->ImgName;
$photo = __DIR__.'/../'.$photo;
$imageType = exif_imagetype($photo);
$mimeType = image_type_to_mime_type($imageType);
header('Content-Type: '.$mimeType);
readfile($photo);