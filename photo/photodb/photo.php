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
<link rel="stylesheet" href="../../library/photoswipe/4.1.3/photoswipe.css">
<link rel="stylesheet" href="../../library/photoswipe/4.1.3/default-skin/default-skin.css">
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
<!-- Root element of PhotoSwipe. Must have class pswp. -->
<div id="gallery" class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
<div class="pswp__bg"></div>
<div class="pswp__scroll-wrap">
<div class="pswp__container">
<div class="pswp__item"></div>
<div class="pswp__item"></div>
<div class="pswp__item"></div>
</div>
<div class="pswp__ui pswp__ui--hidden">
<div class="pswp__top-bar">
<div class="pswp__counter"></div>
<button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
<button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
<button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
<div class="pswp__preloader">
<div class="pswp__preloader__icn">
<div class="pswp__preloader__cut">
<div class="pswp__preloader__donut"></div>
</div>
</div>
</div>
</div>
<div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
<div class="pswp__share-tooltip"></div>
</div>
<button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button>
<button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button>
<div class="pswp__caption">
<div class="pswp__caption__center"></div>
</div>
</div>
</div>
</div>
<?php echo $bodyEnd->render(); ?>
<script src="../../library/tinyamd.min.js" type="text/javascript"></script>
<script type="text/javascript">require(['photo']);</script>
</body>
</html>