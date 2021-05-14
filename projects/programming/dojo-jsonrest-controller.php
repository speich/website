<?php
$method = $_SERVER['REQUEST_METHOD'];
$protocol = $_SERVER["SERVER_PROTOCOL"];
$json = false;

switch($method) {
	case 'GET':
		$json = isset($_GET['parId']) ? '[{id:1,parId:2}]' : '{id:2,parId:2}';
		break;
	case 'POST':
		$json = '{id:2,parId:2}';
		break;
	case 'PUT':
		$json = '{id:2,parId:2}';
		break;
	case 'DELETE':
		$json = '{msg: "item deleted"}';
		break;
}

if ($json) {
	$method === 'POST' ? header($protocol.' 201 Created') : header($protocol.' 200 OK');
	header("Content-Type", "application/json");
	echo $json;
}
else {
	header($protocol.' 404 Not Found');
}
?>