<?php
session_start();

$resource = isset($_SERVER['PATH_INFO']) ? ltrim($_SERVER['PATH_INFO'], '/') : null;
$method = $_SERVER['REQUEST_METHOD'];
$status = null;
$protocol = $_SERVER["SERVER_PROTOCOL"];
$moduleType = 'session';
$json = false;

if ($method == 'POST' || $method == 'PUT') {
	$_DATA = file_get_contents('php://input');
	$_DATA = json_decode($_DATA);
}
else if ($method == 'GET') {
	$_DATA = (object) $_GET;	// note: + signs form sorted are converted to _
}
else {
	$_DATA = null;
}

switch($moduleType) {
	case 'session':
		// use session to store the user's filesystem
		// root dir is used for the session's name 
		require_once 'ModuleSession.php';
		$rootDir ='virtFileSystem';
		$rfe = new ModuleSession($rootDir);
		break;
	case 'sqlite':
		// TODO: use SQLite to store user's file system
		break;
	case 'disk':
		// TODO: use webserver's filesystem
		break;
}

//sleep(1); // for testing async
//time_nanosleep(0, 500000000);	// = 0.5 seconds

switch($method) {
	case 'GET':
		$json = $rfe->get($resource);
		break;
	case 'POST':
		$json = $rfe->create($resource, $_DATA);
		break;
	case 'PUT':
		$json = $rfe->update($resource, $_DATA);
		break;
	case 'DELETE':
		$json = $rfe->delete($resource);
		break;
}

if ($json) {
	$method == 'POST' ? header($protocol.' 201 Created') : header($protocol.' 200 OK');
	header("Content-Type", "application/json");
	echo $json;
}
	// resource not found
else {
	header($protocol.' 404 Not Found');
	echo '[{"msg": "Ressource nicht gefunden."}]';
}


?>