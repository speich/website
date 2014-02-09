<?php
use PhotoDb\PhotoDb;
use WebsiteTemplate\Controller;
use WebsiteTemplate\Error;
use WebsiteTemplate\Header;

require_once '../../library/inc_script.php';
require_once 'PhotoDb.php';
require_once 'Controller.php';
require_once 'Error.php';
require_once 'Header.php';

$err = new Error();
$ctrl = new Controller(new Header(), $err);
$data = $ctrl->getDataAsObject();
$resource = $ctrl->getResource();
$controller = $ctrl->getController();
$resources = $ctrl->getResources();
$ctrl->contentType = 'json';
$ctrl->setAutoCompress(false);	// compressing would increase from 80ms to 5500ms! at least on my local apache
$response = false;
$header = false;


/**
 * @param null $rating
 * @param PhotoDb $db
 * @param Object $data
 * @return mixed
 */
function loadMarkerData($db, $data) {
	if (is_null($db->db)) {
		$db->connect();
	}
	// Currently we just load all markers
	$sql = "SELECT i.Id id, i.ImgFolder||'/'||i.ImgName img, ROUND(i.ImgLat, 6) lat, ROUND(i.ImgLng, 6) lng FROM Images i";

	if (property_exists($data, 'theme')) {
		$sql.= "	INNER JOIN Images_Themes it ON i.Id = it.ImgId";
	}
	else if (property_exists($data, 'country')) {
		$sql.= "INNER JOIN Images_Locations il ON i.Id = il.ImgId
			INNER JOIN Locations_Countries lc ON il.LocationId = lc.LocationId";
	}
	$sql.= " WHERE i.RatingId > :rating
		AND (i.ImgLat NOT NULL OR i.ImgLng NOT NULL)
		AND i.ImgLat != '' AND i.ImgLng != ''";
	$stmt = $db->db->prepare($sql);

	// Currently we just load all markers
	$stmt->bindValue(':rating', $data->qual - 1);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return json_encode($res, JSON_NUMERIC_CHECK);
}


if ($controller == 'marker') {
	$db = new PhotoDb($web->getWebRoot());
	$response = loadMarkerData($db, $data);
}


// resource found and processed
if ($response) {
	if ($header) {
		header($header);
	}
}
else {
	$ctrl->notFound = true;
}

$ctrl->printHeader();
$ctrl->printBody($response);