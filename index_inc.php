<?php

use PhotoDb\PhotoDb;


$db = new PhotoDb($web->getWebRoot());

// query random image info
$sql = 'SELECT i.Id, i.ImgFolder, i.ImgName, i.ImgTitle FROM Images i
	WHERE i.RatingId = 3 ORDER BY RANDOM() LIMIT 1';
$db->connect();
$stmt = $db->db->query($sql);
$photo = $stmt->fetchObject();
$imgPath = $db->getPath('img').$photo->ImgFolder.'/'.$photo->ImgName;

// create different srcsets
// $imgPath = 'photo/photodb/images/ch/2008-09-Fenalet/2008-09-Fenalet-018.jpg';
$origSize = getimagesize($imgPath);
$resizerPath = '/scripts/php/controller/images/img1.php/'.$imgPath;
$photo->src = '/'.$imgPath;
$photo->w = $origSize ? $origSize[0] : '';
$photo->h = $origSize ? $origSize[1] : '';
$photo->srcset = $photo->src.' '.$photo->w.'w, '.$resizerPath.'?w=1024 1024w, '.$resizerPath.'?w=800 800w, '.$resizerPath.'?w=600 600w';
$photo->link = '/photo/photodb/'.$language->createPage('photo-detail.php').'?imgId='.$photo->Id;

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