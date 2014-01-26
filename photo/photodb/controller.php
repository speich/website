<?php
use PhotoDb\PhotoDb;
use WebsiteTemplate\Controller;
use WebsiteTemplate\Error;
use WebsiteTemplate\Header;
use WebsiteTemplate\Http;

require_once '../../library/inc_script.php';
require_once 'PhotoDb.php';
require_once 'Controller.php';
require_once 'Error.php';
require_once 'Header.php';
require_once 'Http.php';

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
 * @return mixed
 */
function loadMarkerData($db, $rating = null) {
	if (is_null($db->db)) {
		$db->connect();
	}
	// Currently we just load all markers
	$sql = "SELECT Id id, imgFolder||'/'||imgName img, ROUND(imgLat, 6) lat, ROUND(imgLng, 6) lng FROM Images
		WHERE ratingId > :rating
		AND (imgLat NOT NULL OR imgLng NOT NULL)
		AND imgLat != '' AND imgLng != ''";
	$stmt = $db->db->prepare($sql);

	$stmt->bindValue(':rating', $rating - 1);
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return json_encode($res, JSON_NUMERIC_CHECK);
}


if ($controller == 'marker') {
	$rating = $data->qual ? $data->qual : 3;
	$db = new PhotoDb($web->getWebRoot());
	$response = loadMarkerData($db, $rating);
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