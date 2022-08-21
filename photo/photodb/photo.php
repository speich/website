<?php
require_once __DIR__.'/../../scripts/php/inc_script.php';
require_once __DIR__.'/photo_inc.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $language->get(); ?>">
<head>
<title><?php echo $i18n['page title'].' | '.$web->pageTitle; ?></title>
<?php echo $head->render(); ?>
<link href="photodb.min.css" rel="stylesheet" type="text/css">
<link href="photo.min.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="../../library/photoswipe/5.3.0/photoswipe.css">
<script src="photo.js" type="module"></script>
</head>

<body>
<?php echo $bodyStart->render($mainNav, $sideNav, $langNav); ?>
<div class="toolbar">
<div class="bar-cont">
<form method="GET" role="search" class="bar-item frmSearch">
<label class="visuallyHidden" for="q"><?php echo $i18n['search photos']; ?></label><input type="text" id="q" name="q"
	value="<?php echo isset($_GET['q']) ? htmlentities($_GET['q'], ENT_QUOTES, $web->charset) : ''; ?>" placeholder="<?php echo $i18n['search photos']; ?>">
<button type="submit">
	<svg class="icon">
		<use xlink:href="<?php echo $web->getWebRoot(); ?>layout/images/symbols.svg#magnifying-glass"></use>
	</svg>
</button>
</form>
<div class="bar-sep-vert"></div>
<div class="bar-options">
<div class="bar-item"><label><?php echo $i18n['sorting']; ?></label><?php echo $mSort->render(); ?></div>
<div class="bar-sep-vert"></div>
<div class="bar-item"><label><?php echo $i18n['rating']; ?></label><?php echo $mRating->render(); ?></div>
<div class="bar-sep-vert"></div>
<button id="map" class="bar-item" title="<?php echo $i18n['show on map']; ?>"><a href="photo-mapsearch.php<?php echo $query->getString(); ?>">
        <?php echo $i18n['map']; ?>
		<svg class="icon">
			<use xlink:href="<?php echo $web->getWebRoot(); ?>layout/images/symbols.svg#map-marker"></use>
		</svg>
	</a></button>
</div>
</div>
<div class="bar-cont"><?php echo $pagingBar; ?></div>
</div>
<div>
<ul id="slides"><?php echo $photo->renderData($photos, $web, $language, $i18n); ?></ul>
</div>
<div class="toolbar">
<div class="bar-cont"><?php echo $pagingBar ?></div>
</div>
<?php echo $bodyEnd->render(); ?>
</body>
</html>