<?php
require_once __DIR__.'/../scripts/php/inc_script.php';

/*
 * Simple PHP page that checks if all HTTP request methods are supported by your apache installation
 * and directly writes the parameters back as json
 */
if (array_key_exists('PATH_INFO', $_SERVER)) {
    $resource = $_SERVER['PATH_INFO'];
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'POST' || $method === 'PUT' || $method === 'DELETE') {
        parse_str(file_get_contents('php://input'), $_DATA);
    } else {
        $_DATA = $_GET;
    }

    $arr = [
        'method' => $method,
        'resource' => $resource,
        'data' => $_DATA
    ];

    // send (fake) HTTP status responses if forceError is set
    switch ($method) {
        case 'POST':
            if ($_DATA['forceError'] === 'false') {
                $status = 'HTTP/1.1 201 Created';
            } else {
                $status = 'HTTP/1.1 500 Internal Server Error';
            }
            break;
        case 'GET':
            if ($_DATA['forceError'] === 'false') {
                $status = 'HTTP/1.1 200 OK';
            } else {
                $status = 'HTTP/1.1 500 Internal Server Error';
            }
            break;
        case 'PUT':
            if ($_DATA['forceError'] === 'false') {
                $status = 'HTTP/1.1 200 OK';
            } else {
                $status = 'HTTP/1.1 404 Not Found';
            }
            break;
        case 'DELETE':
            if ($_DATA['forceError'] === 'false') {
                $status = 'HTTP/1.1 200 OK';
                // With 204 there would be no status code available on the client-side
                //$status = 'HTTP/1.1 204 No Content';
            } else {
                $status = 'HTTP/1.1 500 Internal Server Error';
            }
            break;
    }
    header($status);
    if ($_DATA['forceError'] === 'false') {
        // only send response data back when successful
        echo json_encode($arr);
    }
} else {
    ?>
	<!DOCTYPE html>
	<html lang="<?php echo $language->get(); ?>">
	<head>
	<title><?php echo $web->pageTitle; ?>: REST with Dojo and PHP</title>
  <?php echo $head->render(); ?>
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
  <?php echo $bodyStart->render($mainNav, $sideNav, $langNav); ?>
	<h1>Check availability of HTTP request methods</h1>
	<p>To be able to use a REST API with apache and PHP you need to know if your ISP allows the different HTTP request
		methods POST, GET, PUT and DELETE.
		Many providers only allow for POST and GET.</p>
	<p>To test if all methods are available, just upload and unzip this file on your server. It only checks if these
		methods are available,
		but does not actually do any CRUD (create, read, update or delete) operations. It simply sends back the posted
		resource and query data. If
		any of the methods are not available, a HTTP error will be thrown. You can also force a server error by selecting
		the checkbox.</p>
	<ul>
	<li>This demo belongs to part 1 of the tutorial: <a
		href="https://www.speich.net/articles/en/2010/02/13/tutorial-part1-rest-with-dojo-and-php/">REST with Dojo and
		PHP</a>.
	</li>
	<li>Checkout also the second demo part 1: <a href="dojo-jsonreststore.php">Lazy load, update, create and delete dijit
		tree items</a>.
	</li>
	</ul>
	<h2><a name="demo"></a>Demo to check if all HTTP request methods are available</h2>
	<p><input type="checkbox" id="fldforceError">force HTTP request error</p>
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
  <?php echo $bodyEnd->render(); ?>
	</body>
	<script src="../library/dojo/dojo/dojo.js" data-dojo-config="async: true, locale: '<?php echo $locale = $language->get(); ?>'"></script>
	<script type="text/javascript">
	require(['dojo/_base/lang', 'dojo/dom', 'dojo/request/xhr', 'dojo/on', 'dojo/query', 'dojo/io-query'], function(lang, dom, xhr, on, query, ioQuery) {
		var demo = {
			// general arguments object for the different xhr methods.
			url: 'php-rest-check.php',
			request: {
				handleAs: 'json',
				data: {
					forceError: false,	// if set to true, server responds with an HTTP error
					var1: 'someVal1',		// just some variables
					var2: 'someVal2'
				}
			},
			load: function(request) {
				request.response.then(function(response) {
					// log response
					dom.byId('log').innerHTML += '<p>HTTP status: ' + response.status + ' ' + response.xhr.statusText + '<br/>' +
						'method: <strong>' + response.data.method + '</strong><br/>' +
						'response data: ' + ioQuery.objectToQuery(response.data.data) + '</p>';

				}, function(error) {
					// log HTTP error status
					dom.byId('log').innerHTML += '<p>HTTP error status: ' + error.response.status;
				});
			},

			// create
			post: function() {
				var prom, reqObj = lang.clone(this.request);	// otherwise we keeping adding '/name/1' with each new post

				// create new item with id = 1 at '/images'
				prom = xhr.post(this.url + '/images/1', reqObj);
				this.load(prom);
			},
			// read/load
			get: function() {
				var prom, reqObj = lang.clone(this.request);

				reqObj.query = reqObj.data;
				delete(reqObj.data);
				// load all items at '/images'
				prom = xhr.get(this.url + '/images', reqObj);
				this.load(prom);
			},
			// update
			put: function() {
				var prom, reqObj = lang.clone(this.request);

				// update item with id = 1 at '/images
				prom = xhr.put(this.url + '/images/1', reqObj);
				this.load(prom);
			},
			// delete
			del: function() {
				var prom, reqObj = lang.clone(this.request);

				// delete item with id = 1 at resource
				prom = xhr.del(this.url + '/images/1', reqObj);
				this.load(prom);
			}
		};

		// add each request method to one of the links
		query('#ulDemo li').forEach(function(node, i) {
			on(node, 'click', function(evt) {
				evt.preventDefault();	// prevent link action
				demo.request.data.forceError = dom.byId('fldforceError').checked ? true: false;
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
	</html>
<?php } ?>