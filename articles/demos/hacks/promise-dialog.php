<?php
$version = isset($_GET['demo']) ? (int)$_GET['demo'] : 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Title</title>
<style type="text/css">
html, body {
	height: calc(100% - 0.5em);
	background-color: #aaaaaa;
	padding: 0;
	margin: 0;
	font-size: 100%;
}

body {
	padding: 0.5em;
	font: 12px Verdana, Arial, Myriad, Helvetica, Tahoma, clean, sans-serif;
	font-weight: normal;
}

h1 {
	font-size: 1.5em;
}

h2 {
	font-size: 1em;
}

h1, h2 {
	font-weight: bold;
}

p {
	font-size: 1em;
}

.dialogContainer {

 }
</style>
<script type="text/javascript">
let byId = document.getElementById.bind(document),
	app = {};

	window.addEventListener('load', () => {};
</script>
</head>

<body>
<h1>Example 1: Blocking modal dialog</h1>
<p>Read the full article <a href="https://hacks.mozilla.org/" target="_blank">Promises: Two useful examples</a> on
	hacks.mozilla.org</p>
<p>The demo on this page simulates the blocking behavior of JavaScript's native
<a href="https://developer.mozilla.org/en/DOM/window.confirm">window.confirm()</a> method by using a Promise.</p>
<div class="dialogContainer">
<div></div>
</div>
</body>
</html>