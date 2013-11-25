<?php
session_start();

require_once 'Error.php';

$err = new Error();

$resource = isset($_SERVER['PATH_INFO']) ? ltrim($_SERVER['PATH_INFO'], '/') : null;
$method = $_SERVER['REQUEST_METHOD'];
$protocol = $_SERVER["SERVER_PROTOCOL"];
$moduleType = 'session';
$json = false;
$header = false;
$status = null;

/******************************************
 *  Convert input parameters to an object	*
 ******************************************/
switch ($method) {
	case 'POST':
		$arr = file_get_contents('php://input');
		$arr = json_decode($arr);
		break;
	case 'PUT':
		$data = file_get_contents('php://input');
		$arr = json_decode($data);
		break;
	case 'GET':
		$arr = $_GET;
		break;
	case 'DELETE':
		if ($_SERVER['QUERY_STRING'] !== '') {
			// Delete has no body, but a query string is possible
			parse_str($_SERVER['QUERY_STRING'], $arr);
		}
		else {
			$arr = array();
		}
		break;
	default:
		$arr = array();
}
$data = count($arr) > 0 ? (object) $arr : null;

switch($moduleType) {
	case 'session':
		// use session to store the user's filesystem
		// root dir is used for the session's name 
		require_once('ModuleSession.php');
		$rootDir ='virtFileSystem';
		$fs = new ModuleSession($rootDir);
		break;
	case 'sqlite':
		// use ModuleSQLite to store user's file system
		break;
	case 'disk':
		// use web server's filesystem
		//require_once('ModuleDisk.php');
		//$rootDir = $_SERVER['DOCUMENT_ROOT'].'/images';
		//$rfe = new DiskModule($rootDir);
		break;
}

//sleep(1); // for testing async
//time_nanosleep(0, 500000000);	// = 0.5 seconds

switch($method) {
	case 'GET':
		if ($resource == 'search/' && isset($_SERVER['HTTP_RANGE'])) {
			$ranges = explode('-', substr($_SERVER['HTTP_RANGE'], 6));	// e.g. items=0-24
			$keyword = $data->name;
			$start = $ranges[0];
			$end = $ranges[1];
			$numRec = $fs->getNumSearchRecords($keyword);
			$json = $fs->search($keyword, $start, $end);
			$header = 'Content-Range: items '.$start.'-'.$end.'/'.$numRec;
		}
		else {
			$json = $fs->get($resource);
		}
		break;
	case 'POST':
		$json = $fs->create($resource, $data);
		break;
	case 'PUT':
		$json = $fs->update($resource, $data);
		break;
	case 'DELETE':
		$json = $fs->del($resource);
		break;
}


header("Content-Type", "application/json");
// PHP error
if (!is_null($err->get())) {
	header($_SERVER["SERVER_PROTOCOL"].' 505 Internal Server Error');
	echo $err->getAsJson();
}
// resource found and processed
else if ($json) {
	$method == 'POST' ? header($protocol.' 201 Created') : header($protocol.' 200 OK');
	if ($header) {
		header($header);
	}
	echo $json;
}
// resource not found
else {
	header($protocol.' 404 Not Found');
	echo '[{"msg": "Resource not found."}]';
}