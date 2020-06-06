<?php
require_once 'photo_inc.php';
$i18n = require_once __DIR__.'/nls/'.$lang->get().'/photo-mapsearch.php';
$web->setLastPage();
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
<title><?php echo $i18n['page title'].' | '.$web->pageTitle; ?></title>
<?php require_once 'inc_head.php' ?>
<link href="photodb.min.css" rel="stylesheet" type="text/css">
<link href="photo-mapsearch.min.css" rel="stylesheet" type="text/css">
</head>

<body class="tundra">
<?php require_once 'inc_body_begin.php'; ?>
<div id="loading"><img src="../../layout/images/icon_loading.gif"><span id="loadingMsg"><?php echo $i18n['loading map']; ?></span></div>
<div id="mapContainer">
<?php echo $mRating->render(); ?>
<div id="showPhotos" class="button buttShowPhotos" title="<?php echo $i18n['show photos']; ?>"><a href="photo.php" id="linkShowPhotos"><?php echo $i18n['photos']; ?><img src="../../layout/images/icon_photolist.gif" alt="icon to display list of photos"></a></div>
<div id="map-canvas"></div>
</div>

<?php require_once 'inc_body_end.php'; ?>
<script type="text/javascript" src="../../library/dojo/1.13.0/dojo/dojo.js" data-dojo-config="async: true,
	gmapsApiKey: 'AIzaSyBEPhZpv_OQYeJH_mVYEOubDgGLlY5aLWg',
	locale: '<?php echo $locale = $lang->get(); ?>',
	packages: [
		{name: 'gmap', location: './../../../gmap'}
	]"></script>
<script type="text/javascript" src="photo-mapsearch.min.js">
</script>
</body>
</html>