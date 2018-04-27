<?php
// Demo for article on hacks.mozilla.org
// Render a list of images to demonstrate why setting hash on page load to scroll to specific image does not work as expected.
// Because images load asynchronously, height of

// render list of images
use PhotoDb\PhotoDb;


require_once __DIR__.'/../../../photo/photodb/scripts/php/PhotoDb.php';
$photoDb = new PhotoDb('/');
$sql = "SELECT i.ImgFolder, i.ImgName, i.Id, n.NameEn 
    FROM Images i
    INNER JOIN Images_ScientificNames sn ON i.Id = sn.ImgId
    INNER JOIN ScientificNames n ON sn.ScientificNameId = n.Id
    WHERE i.RatingId = 3 AND n.NameEn NOT NULL 
    ORDER BY n.NameEn ASC
    LIMIT 100";
$photoDb->connect();
$stmt = $photoDb->db->prepare($sql);
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
header('Content-Type: application/json');
echo json_encode($records);