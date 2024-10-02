<?php require_once __DIR__.'/../../scripts/php/inc_script.php';
$sideNav->arrItem[1]->setActive();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<meta charset="UTF-8">
<title><?php echo 'Remote File Explorer (rfe)'; ?></title>
<link rel="stylesheet" href="/library/dojo/dijit/themes/claro/document.css">
<link rel="stylesheet" href="/library/dojo/dijit/themes/claro/claro.css">
<link rel="stylesheet" href="/library/remoteFileExplorer/js/resources/reset.css">
<link rel="stylesheet" href="/library/remoteFileExplorer/js/resources/rfe.css">
<link rel="stylesheet" href="/projects/programming/remoteFileExplorer.min.css">
<?php echo $head->render(); ?>
</head>

<body class="rfe">
<?php echo $bodyStart->render($mainNav, $sideNav, $langNav); ?>
<h1>remoteFileExplorer - a Windows Explorer like web application</h1>
<p>This is a running demo of a <a href="https://dojotoolkit.org/reference-guide/dijit/Tree.html" target="_blank">dijit tree</a>
	combined with a <a href="https://dgrid.io/" target="_blank">dgrid</a> using REST. A <a href="https://dojotoolkit.org/reference-guide/dijit/form/ComboBox.html" target="_blank">customized dijit ComboBox</a> is used for the search.</p>
<p>You can find the source code and the documentation on <a href="https://github.com/speich/remoteFileExplorer" target="_blank">GitHub</a>.</p>
<div id="remoteFileExplorer" class="claro"><div class="loading"><img src="/layout/images/icon_loading.gif" alt="loading icon">File explorer is being loaded...</div></div>
<script src="/library/dojo/dojo/dojo.js" data-dojo-config="async: true,
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
		},
		'dojo/dnd': {
			'dojo/dnd/Selector': 'rfe/dnd/Selector'
		}
	}"></script>
<script src="/projects/programming/remoteFileExplorer.min.js"></script>
<?php echo $bodyEnd->render(); ?>
</body>
</html>