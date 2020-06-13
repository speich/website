<?php
use PhotoDb\Map;
use PhotoDb\PhotoQueryString;
use WebsiteTemplate\Controller;
use WebsiteTemplate\Error;
use WebsiteTemplate\Header;

require_once __DIR__.'/../../scripts/php/inc_script.php';

$err = new Error();
$header = new Header();
$header->setContentType('json');
$ctrl = new Controller($header, $err);
$resources = $ctrl->getResource();
$controller = array_shift($resources);
$response = false;
$header = false;
$params = new PhotoQueryString($_GET);

if ($controller === 'marker') {
	$db = new Map($web->getWebRoot());
	$response = $db->loadMarkerData($params);
}
else if ($controller === 'country') {
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