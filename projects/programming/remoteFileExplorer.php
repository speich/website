<?php require_once '../../library/inc_script.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $web->getLang(); ?>">
<head>
<title><?php echo $web->getWindowTitle(); ?>remoteFileExplorer</title>
<link rel="stylesheet" type="text/css" href="/library/dojo/1.9.0/dijit/themes/claro/document.css">
<link rel="stylesheet" type="text/css" href="/library/dojo/1.9.0/dijit/themes/claro/claro.css">
<link rel="stylesheet" href="/library/dgrid/css/skins/claro.css">
<link rel="stylesheet" href="/library/remoteFileExplorer/js/resources/reset.css">
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

<body class="claro">
<?php require_once 'inc_body_begin.php'; ?>
<h1>remoteFileExplorer - a Windows Explorer like web application</h1>
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
	  	'library/dijit/tree': {
			'library/dijit/tree/_dndSelector': 'library/remoteFileExplorer/js/dnd/TreeSelector',
      	'library/dijit/tree/dndSource': 'library/remoteFileExplorer/js/dnd/TreeSource'
		}
	}
};
</script>
<script type="text/javascript" src="/library/dojo/1.9.0/dojo/dojo.js"></script>
<script type="text/javascript">
require(['dojo/ready', 'rfe/FileExplorer'], function(ready, FileExplorer) {
	ready(function() {
		var rfe = new FileExplorer({
			id: 'remoteFileExplorer'
		});
		rfe.startup();
	});
});
</script>
<?php require_once 'inc_body_end.php'; ?>
</body>
</html>