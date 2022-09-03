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

// query number of bird species
$sql = 'SELECT COUNT(ScientificNameId) numRec FROM (
    SELECT s.ScientificNameId, n.NameDe FROM Images_ScientificNames s
    INNER JOIN ScientificNames n on s.ScientificNameId = n.Id
    INNER JOIN Images i ON s.ImgId = i.Id
    WHERE n.ThemeId = 1
    GROUP BY ScientificNameId) t';
$stmt = $db->db->query($sql);
$rec = $stmt->fetchObject();
$numBirdSpecies = $rec->numRec;

// query number of other animal species
$sql = 'SELECT COUNT(ScientificNameId) numRec FROM (
    SELECT s.ScientificNameId FROM Images_ScientificNames s
    INNER JOIN ScientificNames n on s.ScientificNameId = n.Id
    INNER JOIN Images i ON s.ImgId = i.Id
    WHERE n.ThemeId IN (2,7,8,13,15,33)
    GROUP BY s.ScientificNameId) t';
$stmt = $db->db->query($sql);
$rec = $stmt->fetchObject();
$numOtherSpecies = $rec->numRec;

// query number of photos of forests
$sql = 'SELECT COUNT(i.id) numRec FROM Images i
    INNER JOIN Images_Themes it ON i.id = it.ImgId
    WHERE it.ThemeId = 20';
$stmt = $db->db->query($sql);
$rec = $stmt->fetchObject();
$numForestFotos = $rec->numRec;