<?php require_once '../../library/inc_script.php'; ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<meta charset="UTF-8">
<title><?php echo $web->pageTitle; ?>Remote File Explorer (rfe)</title>
<link rel="stylesheet" type="text/css" href="../../library/dojo/1.10.4/dijit/themes/claro/document.css">
<link rel="stylesheet" type="text/css" href="/library/dojo/1.10.4/dijit/themes/claro/claro.css">
<link rel="stylesheet" href="/library/dgrid/css/skins/claro.css">
<link rel="stylesheet" href="/library/remoteFileExplorer/js/resources/rfe.css">
<style type="text/css">
#remoteFileExplorer {
	width: 782px;
	height: 600px;
	border: 1px solid #48B100;
}
</style>
<?php require_once 'inc_head.php' ?>
</head>

<body class="claro rfe">
<?php require_once 'inc_body_begin.php'; ?>
<h1>remoteFileExplorer - a Windows Explorer like web application</h1>
<p>This is a running demo. You can find the source code and the documentation on <a href="https://github.com/speich/remoteFileExplorer" target="_blank">GitHub</a>.</p>
<div id="remoteFileExplorer"></div>
<script type="text/javascript">
var dojoConfig = {
	async: true,
	packages: [
		{name: 'dgrid', location: '/library/dgrid'},
		{name: 'xstyle', location: '/library/xstyle'},
		{name: 'put-selector', location: '/library/put-selector'},
		{name: 'rfe', location: '/library/remoteFileExplorer/js'},
		{name: 'rfe-php', location: '/library/remoteFileExplorer/php'}
	],
	map: {
		// redirect the following modules to my own modules
		'dijit/tree': {
			'dijit/tree/_dndSelector': 'rfe/dnd/TreeSelector',
			'dijit/tree/dndSource': 'rfe/dnd/TreeSource'
		}
	}
};
</script>
<script type="text/javascript" src="../../library/dojo/1.10.4/dojo/dojo.js"></script>
<script type="text/javascript">
require(['dojo/ready', 'rfe/FileExplorer'], function(ready, FileExplorer) {
	ready(function() {
		var rfe = new FileExplorer({
			id: 'remoteFileExplorer',
			origPageUrl: '/projects/programming/remoteFileExplorer.php'
		});
		rfe.startup();
	});
});
</script>
<?php require_once 'inc_body_end.php'; ?>
</body>
</html>