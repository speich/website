<?php
use PhotoDb\Map;
use WebsiteTemplate\Controller;
use WebsiteTemplate\Error;
use WebsiteTemplate\Header;

require_once __DIR__.'/../../scripts/php/inc_script.php';

$err = new Error();
$ctrl = new Controller(new Header(), $err);
$data = $ctrl->getDataAsObject();
$resources = $ctrl->getResource();
$controller = array_shift($resources);
$ctrl->contentType = 'json';
$response = false;
$header = false;

if ($controller == 'marker') {
	$db = new Map($web->getWebRoot());
	$params = $db->createObjectFromPost($data);
	$response = $db->loadMarkerData($params);
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