<?php

use PhotoDb\PhotoDb;


$db = new PhotoDb($web->getWebRoot());
$sql = 'SELECT i.Id, i.ImgFolder, i.ImgName, i.ImgTitle FROM Images i
	WHERE i.RatingId = 3 ORDER BY RANDOM() LIMIT 1';
$db->connect();
$stmt = $db->db->query($sql);
$photo = $stmt->fetchObject();
$src = $db->getPath('img').$photo->ImgFolder.'/'.$photo->ImgName;
$url = '/photo/photodb/photo-detail.php?imgId='.$photo->Id;