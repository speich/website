<?php
require_once '../../library/inc_script.php';
require_once 'Search.php';

$search = new Search();
$search->connect();

if (isset($_GET['fnc'])) {
	switch ($_GET['fnc']) {
		case 'loadGMapsData':
			$lat1 = isset($_GET['lat1']) ? $_GET['lat1'] : exit;
			$lng1 = isset($_GET['lng1']) ? $_GET['lng1'] : exit;
			$lat2 = isset($_GET['lat2']) ? $_GET['lat2'] : exit;
			$lng2 = isset($_GET['lng2']) ? $_GET['lng2'] : exit;
			$qual = isset($_GET['qual']) ? $_GET['qual'] : 3;
			$data = $search->loadGMapsData($lat1, $lng1, $lat2, $lng2, $qual);
			echo '{markers: '.json_encode($data).'}';
			break;
	}
}
else {
	echo 'query fnc is missing';
	exit;
}

?>