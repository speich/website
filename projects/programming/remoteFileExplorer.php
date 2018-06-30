<?php require_once '../../scripts/php/inc_script.php'; ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<meta charset="UTF-8">
<title><?php echo $web->pageTitle; ?>Remote File Explorer (rfe)</title>
<link rel="stylesheet" type="text/css" href="//ajax.googleapis.com/ajax/libs/dojo/1.11.2/dijit/themes/claro/document.css">
<link rel="stylesheet" type="text/css" href="//ajax.googleapis.com/ajax/libs/dojo/1.11.2/dijit/themes/claro/claro.css">
<link rel="stylesheet" href="/library/dgrid/v0.3.16/css/skins/claro.css">
<link rel="stylesheet" href="/library/remoteFileExplorer/js/resources/reset.css">
<link rel="stylesheet" href="/library/remoteFileExplorer/js/resources/rfe.css">
<style type="text/css">
#remoteFileExplorer {
	width: 782px;
	height: 600px;
	border: 1px solid #48B100;
}
.loading {
	margin: 2em 3em;
}

.loading img {
	vertical-align: middle;
	margin-right: 0.5em;
}
</style>
<?php require_once 'inc_head.php' ?>
</head>

<body class="claro rfe">
<?php require_once 'inc_body_begin.php'; ?>
<h1>remoteFileExplorer - a Windows Explorer like web application</h1>
<p>This is a running demo of a <a href="http://dojotoolkit.org/reference-guide/dijit/Tree.html" target="_blank">dijit tree</a>
	combined with a <a href="http://dgrid.io/" target="_blank">dgrid</a> using REST. A <a href="http://dojotoolkit.org/reference-guide/dijit/form/ComboBox.html" target="_blank">customized dijit ComboBox</a> is used for the search.</p>
<p>You can find the source code and the documentation on <a href="https://github.com/speich/remoteFileExplorer" target="_blank">GitHub</a>.</p>
<div id="remoteFileExplorer"><div class="loading"><img src="/layout/images/icon_loading.gif" alt="loading icon">File explorer is being loaded...</div></div>
<script type="text/javascript">
var dojoConfig = {
	async: true,
	packages: [
		{name: 'dgrid', location: '/library/dgrid/v0.3.16'},
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
		},
		'dojo/dnd': {
			'dojo/dnd/Selector': 'rfe/dnd/Selector'
		}
	}
};
</script>
<script src="//ajax.googleapis.com/ajax/libs/dojo/1.11.2/dojo/dojo.js"></script>
<!--<script type="text/javascript" src="/dojo/1.11.1/dojo/dojo.js"></script>-->
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