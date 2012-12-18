<?php
session_start();

$resource = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : null;
$method = $_SERVER['REQUEST_METHOD'];
$status = null;
$protocol = $_SERVER["SERVER_PROTOCOL"];
$moduleType = 'session';

if ($method == 'POST' || $method == 'PUT') {
	$_DATA = file_get_contents('php://input');
	$_DATA = json_decode($_DATA);
}
else if ($method == 'GET') {
	// TODO: convert to stdClass to behave same as POST/PUT?
	$_DATA = $_GET;
}
else {
	$_DATA = null;	// delete does not have any data associated with it
}

// TODO: think about if it is necessary to sanitize input



switch($moduleType) {
	case 'session':
		// use session to store the user's filesystem
		// root dir is used for the session's name 
		require_once('SessionModule.php');
		$rootDir ='virtFileSystem';
		$rfe = new SessionModule($rootDir);
		break;
	case 'sqlite':
		// use SQLite to store user's file system
		break;
	case 'disk':
		// use webserver's filesystem
		require_once('DiskModule.php');
		$rootDir = $_SERVER['DOCUMENT_ROOT'].'/images';
		$rfe = new DiskModule($rootDir);
		break;
}

//echo "<br>";
//$rfe->deleteReference('/folder2/subfolder21', 'testfnc', $rfe);
//echo "<br>";

switch($method) {
	case 'GET':
		if (isset($_DATA['fnc']) && $_DATA['fnc'] == 'loadAll') {
			$rfe->printAll();
		}
		else {
			$arr = $rfe->read($resource);
			echo json_encode($arr);
		}
		break;
	case 'POST':
		$newItem = $rfe->create($resource, $_DATA);
		$newResource = '/'.$newItem['id'];
		if ($newResource) {
			header($protocol.' 201 Created');
			header('Location: http://'.$_SERVER['SERVER_NAME'].$newResource);
			echo json_encode($newItem);
		}
		else {
			header($protocol.' 500 Internal Server Error');			
		}
		break;
	case 'PUT':
		$succ = $rfe->update($resource, $_DATA);
		header($succ ? $protocol.' 200 OK' : $protocol.' 404 Not Found');
		break;
	case 'DELETE':
		/* A successful response SHOULD be 200 (OK) if the response includes an
		 * entity describing the status, 202 (Accepted) if the action has not
		 * yet been enacted, or 204 (No Content) if the action has been enacted
		 * but the response does not include an entity.
		 */
		$succ = $rfe->delete($resource);
		header($succ ? $protocol.' 204 No Content' : $protocol.' 500 Internal Server Error');	// returning this would prevent deleting
		break;
}


	

?>