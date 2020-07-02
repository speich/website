<?php
use PhotoDb\Map;
use PhotoDb\PhotoQueryString;
use PhotoDb\SearchQuery;
use PhotoDb\SqlMap;
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

if ($controller === 'marker') {
	$db = new Map($web->getWebRoot());
    $params = new PhotoQueryString($_GET);
	$sql = new SqlMap();
    $sql->qual = $params->qual;
    $sql->theme = $params->theme;
    $sql->country = $params->country;
    $sql->lat1 = $params->lat1;
    $sql->lng1 = $params->lng1;
    $sql->lat2 = $params->lat2;
    $sql->lng2 = $params->lng2;
    if (isset($params->search)) {
        $search = str_replace('&#34;', '"', $params->search);
        $words = SearchQuery::extractWords($search);
        $sql->search = SearchQuery::createQuery($words, $lang->get());
    }
    $response = $db->loadMarkerData($sql);
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