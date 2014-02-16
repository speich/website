<?php
use PhotoDb\Map;
use WebsiteTemplate\Controller;
use WebsiteTemplate\Error;
use WebsiteTemplate\Header;

require_once '../../library/inc_script.php';
require_once 'Map.php';
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


if ($controller == 'marker') {
	$db = new Map($web->getWebRoot());
	$response = $db->loadMarkerData($data);
}
else if ($controller == 'country') {
	$db = new Map($web->getWebRoot());
	$response = $db->loadCountry($resources[0]);
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