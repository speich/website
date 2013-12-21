<?php require_once '../library/inc_script.php'; ?>
<?php
/*
 * Simple PHP page that checks if all HTTP request methods are supported by your apache installation
 * and directly writes the parameters back as json
 */
if (array_key_exists('PATH_INFO', $_SERVER)) {
	$resource = $_SERVER['PATH_INFO'];
	$method = $_SERVER['REQUEST_METHOD'];

	if ($method == 'POST' || $method == 'PUT') {
		parse_str(file_get_contents('php://input'), $_DATA);
	}
	else {
		$_DATA = $_GET;
	}
	
	$arr = array(
		'method' => $method,
		'resource' => $resource,
		'data' => $_DATA
	);	
	
	// send (fake) HTTP status responses if forceError is set
	switch($method) {
		case 'POST':
			if ($_DATA['forceError'] == 'false') {
				$status = 'HTTP/1.1 201 Created';		
			}
			else {
				$status = 'HTTP/1.1 500 Internal Server Error';
			}
			break;
		case 'GET':
			if ($_DATA['forceError'] == 'false') {
				$status = 'HTTP/1.1 200 OK';
			}
			else {
				$status = 'HTTP/1.1 500 Internal Server Error';
			}
			break;
		case 'PUT':
			if ($_DATA['forceError'] == 'false') {
				$status = 'HTTP/1.1 200 OK';
			}
			else {
				$status = 'HTTP/1.1 404 Not Found';
			}
			break;
		case 'DELETE':
			if ($_DATA['forceError'] == 'false') {
				$status = 'HTTP/1.1 200 OK';
				// With 204 there would be no status code available on the client-side
				//$status = 'HTTP/1.1 204 No Content';
			}
			else {
				$status = 'HTTP/1.1 500 Internal Server Error';
			}
			break;
	}
	header($status);
	if ($_DATA['forceError'] == 'false') {
		// only send response data back when successful
		echo json_encode($arr);
	}
}
else {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<html lang="<?php echo $web->getLang(); ?>">
<head>
<title><?php echo $web->pageTitle; ?>: REST with Dojo and PHP</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="../layout/layout.css" rel="stylesheet" type="text/css">
<link href="../library/prettify/prettify.css" type="text/css" rel="stylesheet">
<script type="text/javascript" src="../library/prettify/prettify.js"></script>
<script type="text/javascript">
var dojoConfig = {
	locale: '<?php echo $locale = $web->getLang(); ?>'
};
</script>
<script src="http://ajax.googleapis.com/ajax/libs/dojo/1.8.1/dojo/dojo.js"></script>
<script type="text/javascript">
var demo = {
	// general arguments object for the different xhr methods.
	args: {
		url: 'php-rest-check.php',
		handleAs: 'json',
		content: {
			forceError: false,	// if set to true, server responds with an HTTP error 
			var1: 'someVal1',		// just some variables
			var2: 'someVal2'
		},
		load: function(response, ioArgs) {
			// log response
			dojo.byId('log').innerHTML+= '<p>HTTP status: ' + ioArgs.xhr.statusText + ' ' + ioArgs.xhr.status + '<br/>' +
				'method: <strong>' + response.method + '</strong><br/>' +
				'resource: ' + response.resource + '<br/>' +
				'query: ' + dojo.objectToQuery(response.data) + '</p>';
		},
		error: function(error) {
			// log HTTP error status
			dojo.byId('log').innerHTML+= '<p>HTTP error status: ' + error.status;
		}
	},
	// create
	post: function() {
		var args = dojo.clone(this.args);	// otherwise we keeping adding '/name/1' with each new post
		args.url+= '/images/1'; // create new item with id = 1 at '/images' 
		dojo.xhrPost(args); 
	},
	// read/load
	get: function() {
		var args = dojo.clone(this.args);
		args.url+= '/images';	// load all items at '/images'
		dojo.xhrGet(args); 
	},
	// update
	put: function() {
		var args = dojo.clone(this.args);
		args.url+= '/images/1'; // update item with id = 1 at '/images
		dojo.xhrPut(args); 
	},
	// delete
	del: function() {
		var args = dojo.clone(this.args);
		args.url+= '/images/1';	// delete item with id = 1 at resouce
		dojo.xhrDelete(args); 
	}
};

dojo.addOnLoad(function() {
	prettyPrint();
	// add each request method to one of the links
	dojo.query('#ulDemo li').forEach(function(node, i) {
		dojo.connect(node, 'onclick', function(e) {
			dojo.stopEvent(e);	// prevent link action
			demo.args.content.forceError = dojo.byId('fldforceError').checked ? true : false;
			switch(i) {
				case 0:
					demo.post();
					break;
				case 1:
					demo.get();
					break;
				case 2:
					demo.put();
					break;
				case 3:
					demo.del();	// note: delete is reserved word
					break;
			}
		});		
	});
});
</script>
<style type="text/css">
#log {
	padding: 8px;
	border: 1px solid #ccc;
	height: 200px;
	overflow: auto;
}
</style>
</head>

<body>
<?php require_once 'inc_body_begin.php'; ?>
<h1>Check availability of HTTP request methods</h1>
<p>To be able to use a REST API with apache and PHP you need to know if your ISP allows the different HTTP request methods POST, GET, PUT and DELETE.
Many providers only allow for POST and GET.</p>
<p>To test if all methods are available, just upload and unzip this file on your server. It only checks if these methods are available,
but does not actually do any CRUD (create, read, update or delete) operations. It simply sends back the posted resource and query data. If 
any of the methods are not available, a HTTP error will be thrown. You can also force a server error by selecting the checkbox.</p>
<ul>
<li>This demo belongs to part 1 of the tutorial: <a href="index.php?p=91">REST with Dojo and PHP</a>.</li>
<li>Checkout also the second demo part 1: <a href="dojo-jsonreststore.php">Lazy load, update, create and delete dijit tree items</a>.</li>
</ul>
<h2><a name="demo"></a>Demo to check if all HTTP request methods are available</h2>
<p><input type="checkbox" id="fldforceError"/>force HTTP request error</p>
<ul id="ulDemo" class="main">
<li><a href="#">send POST</a>: php-rest-check.php/images/1</li>
<li><a href="#">send GET</a>: php-rest-check.php/images</li>
<li><a href="#">send PUT</a>: php-rest-check.php/images/1</li>
<li><a href="#">send DELETE</a>: php-rest-check.php/images/1</li>
</ul>
<div>log window</div>
<div id="log"></div>
<p>Note: DELETE and GET use the request querystring to send additional data, POST and PUT use the request body.</p>
<p>Download the zipped demo file <a href="downloads/php-rest-check.zip">php-rest-check.zip</a></p>
<?php require_once 'inc_body_end.php'; ?>
</body>
</html>
<?php } ?>