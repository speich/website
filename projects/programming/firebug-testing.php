<?php require_once '../../library/inc_script.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $web->getLang(); ?>">
<head>
<title><?php echo $web->pageTitle; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="../../layout/layout.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
var d = document;
function startStream() {
	var output = d.getElementById("logWindow");
	output.innerHTML = '';
	var url = 'x-mixed-replace_test.php';
	var req = new XMLHttpRequest();
	req.multipart = true;
	req.onload = function() {
		d.getElementById("logWindow").innerHTML = this.responseText;
	};
	req.open('GET', url, true);
	req.send();
	return false;
}

window.addEventListener('load', function() {
	d.getElementById('start').addEventListener('click', startStream, false);
}, false);
</script>
<style type="text/css">
#logWindow {
	width: 220px;
	height: 80px;
	padding: 20px;
	border: 1px solid black;
	overflow: auto;
	text-align: center;
}
</style>
</head>

<body>
<?php require_once 'inc_body_begin.php'; ?>
<h1>Firebug Test Case</h1>
<p>Test case for issue 2285 <a href="http://code.google.com/p/fbug/issues/detail?id=2285">support for content-type: multipart/x-mixed-replace</a></p>
<p><a href="#" id="start">start test</a></p>
<div id="logWindow"></div>
<?php require_once 'inc_body_end.php'; ?>
</body>
</html>