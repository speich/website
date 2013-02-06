<?php require_once '../library/inc_script.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $web->getLang(); ?>">
<head>
<title>speich.net HTML5 demo: multiple file upload with drag and drop</title>
<meta charset="utf-8">
<link href="../layout/reset.css" rel="stylesheet" type="text/css">
<link href="http://ajax.googleapis.com/ajax/libs/dojo/1.8.1/dijit/themes/claro/claro.css" rel="stylesheet" type="text/css">
<link href="../layout/layout.css" rel="stylesheet" type="text/css">
<link href="../library/speich.net/fileUploader/resources/uploader.css" rel="stylesheet" type="text/css">
<style type="text/css">
#dropTarget {
	width: 400px;
	margin: 12px 0;
	font-size: 10px;
	font-weight: bold;
	text-align: center;
	min-height: 120px;
	padding: 6px;
	-moz-border-radius: 8px;
	-webkit-border-radius: 8px;
	border-radius: 8px;
	border: 1px solid #ccc;
	background-color: #eee;
}

.targetActive {
	-moz-box-shadow: 0 0 15px #00bb00;
	-webkit-box-shadow: 0 0 15px #00bb00;
	box-shadow: 0 0 15px #00bb00;
}
#snetUploader { font-size:  11px; }
</style>
</head>

<body class="claro">
<?php require_once 'inc_body_begin.php'; ?>
<h1>HTML5 demo: Multiple file upload with drag and drop</h1>
<p>This demo uses dojo and PHP to handle multiple file upload with drag and drop. It lets you pause/resume your upload.
Works with Mozilla Firefox 3.6+ and Google Chrome 7+.</p>
<div id="dropTarget"><p>Drop files from your desktop here</p></div>
<p>Note: pause/resume only works with Firefox 4+.</p>
<p>Download the <a href="https://github.com/speich/fileUploader/">demo code from github</a> or <a href="index.php?p=308#more-308">leave a comment</a>.</p>
<script type="text/javascript">
var dojoConfig = {
	async: true,
	locale: 'en-us',
	packages: [
		{ name: 'snet', location: '/library/speich.net'}
	]
};
</script>
<script src="http://ajax.googleapis.com/ajax/libs/dojo/1.7.1/dojo/dojo.js" type="text/javascript"></script>
<script type="text/javascript">
require(['dojo/_base/kernel', 'dojo/ready', 'snet/fileUploader/Uploader'], function(kernel, ready, Uploader) {
	ready(function() {
		var upl = new Uploader({
			url: kernel.moduleUrl('snet') + 'fileUploader/upload-controller.php',
			dropTarget: 'dropTarget',
			maxKBytes: 10000,
			maxNumFiles: 10
		});
	});
});
</script>
<?php require_once 'inc_body_end.php'; ?>
</body>
</html>