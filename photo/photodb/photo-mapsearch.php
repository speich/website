<?php

require_once 'photo_inc.php';
$i18n = require_once __DIR__.'/nls/'.$lang->get().'/photo-mapsearch.php';
$web->setLastPage();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang->get(); ?>">
<head>
<title><?php echo $i18n['page title'].' | '.$web->pageTitle; ?></title>
<?php require_once 'inc_head.php' ?>
<link href="photodb.min.css" rel="stylesheet" type="text/css">
<link href="photo-mapsearch.min.css" rel="stylesheet" type="text/css">
</head>

<body>
<?php require_once 'inc_body_begin.php'; ?>
<div id="mapContainer"><div id="map-canvas"></div></div>
<div class="toolbar hidden">
<?php echo $mRating->render(); ?>
<button id="showPhotos" class="bar-item"><a href="photo.php"><?php echo $i18n['photos']; ?><svg class="icon"><use xlink:href="../../layout/images/symbols.svg#grid"></svg></a></button>
</div>

<?php require_once 'inc_body_end.php'; ?>
<script type="text/javascript" src="../../library/dojo/1.13.0/dojo/dojo.js" data-dojo-config="async: true,
	gmapsApiKey: 'AIzaSyBEPhZpv_OQYeJH_mVYEOubDgGLlY5aLWg',
	locale: '<?php echo $locale = $lang->get(); ?>',
	packages: [
		{name: 'gmap', location: './../../../gmap'}
	]"></script>
<script type="text/javascript" src="photo-mapsearch.min.js"></script>
</body>
</html>