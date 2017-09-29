<?php
if (isset($_GET['fnc']) && $_GET['fnc'] === 'loadImg') {
    /** display a image which takes several seconds to load, for loading animation demo */
    header("Content-type: image/jpeg");
    sleep(2);
    readfile('../images/ocelot.jpg');
    exit();
}
?>
<?php require_once '../../library/inc_script.php'; ?>
<!DOCTYPE html>
<html lang="en-us">
<head>
<title>CSS loading animation demo | <?php echo $web->pageTitle; ?></title>
<?php require_once '../../layout/inc_head.php' ?>
<style type="text/css">
.loading {
	position: relative;
	height: 516px; /* height of image */
}

.loading > img {
	position: relative;
	top: 0;
	left: 0;
}

.loading:before {
	content: "";
	position: absolute;
	z-index: 2;
	top: calc(50% - 52px); /* centered (100px width + 2 * 2px border) / 2 */
	left: calc(50% - 52px);
	width: 100px;
	height: 100px;
	border: 2px solid;
	border-color: transparent #743399 transparent #743399;
	border-radius: 50%;
	animation: loader 1s linear infinite;
}

/*
.loading, .loading > * {
	position: relative;
}

.loading:before {
	content: "";
	display: inline-block;
	position: absolute;
	margin: 0.6em 0;
	width: 8em;
	height: 8em;
	border: 2px solid;
	border-color: transparent #ffcc99 transparent #ffcc99;
	border-radius: 50%;
	animation: loader 1s linear infinite;
}*/

.loadingSmall:before, .loadingSmall:after {
	margin: 0.3em 0;
	height: 1em;
	vertical-align: top;
}

.loadingSmall:before {
	width: 1em;
}

.loadingSmall:after {
	content: "lade Daten...";
	display: inline-block;
	padding-left: 0.4em;
}

@keyframes loader {
	0% {
		transform: rotate(0deg);
	}
	100% {
		transform: rotate(360deg);
	}
}

</style>
</head>

<body>
<?php require_once 'inc_body_begin.php'; ?>
<h1>Demo of a CSS loading animation</h1>
<h2>Pure CSS solution with no JavaScript</h2>
<p>The image below takes several seconds to load. While it is downloading a CSS loading animation is displayed, which
	gets covered by the image as soon as it is available, so no JavaScript is required.</p>
<p>Just reload to see animation again</p>
<div class="loading"><img src="slowimage.php?fnc=loadImg&noCache=<?php echo rand(0, 999999) ?>" title="ocelot" alt="photo of a ocelot"></div>
<h2>Flexible solution with very little JavaScript</h2>
<div><button id="source">load content</button></div>
<div id="target"></div>
<script type="text/javascript">
let button = document.getElementById('source');

button.addEventListener('click', () => {
    let div = document.getElementById('target');

	 div.classList.add('loading', 'loadingSmall');

});
</script>
<?php require_once 'inc_body_end.php'; ?>
</body>
</html>