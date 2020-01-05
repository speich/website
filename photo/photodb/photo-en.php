<?php
use WebsiteTemplate\PagedNav;


require_once __DIR__.'/../../scripts/php/inc_script.php';
require_once 'photoinc.php';

$photos = $photo->loadPhotos($params);
$numRec = $photo->getNumRec($params);

$pagedNav = new PagedNav($numRec, $params->numRecPerPage);
$pagedNav->renderText = false;
$pagedNav->setWhitelist($web->getWhitelistQueryString());

$word = 'photo'.($numRec > 1 ? 's' : '');
$pagingBar = '<div class="pagingBar">'.
	'<div class="barTxt">'.$numRec.' '.$i18n[$word].'</div>'.
	'<div class="barVertSeparator"></div>'.
	$mRecPp->render().
	'<div class="barTxt">'.$i18n['per page'].'</div>'.
	'<div class="barVertSeparator"></div>'.
	$pagedNav->render($params->page + 1, $web, $lang).
	'</div>';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang->get(); ?>">
<head>
<title><?php echo $i18n['page title'].' | '.$web->pageTitle; ?></title>
<?php require_once 'inc_head.php' ?>
<link href="photodb.min.css" rel="stylesheet" type="text/css">
<link href="photo.min.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="../../library/PhotoSwipe/dist/photoswipe.css">
<link rel="stylesheet" href="../../library/PhotoSwipe/dist/default-skin/default-skin.css">
</head>

<body>
<?php require_once 'inc_body_begin.php'; ?>
<div class="toolbar">
<div class="barContainer">
<form class="searchBar" method="GET" role="search"><label class="visuallyHidden" for="q"><?php echo $i18n['search photos']; ?></label><input type="text" id="q" name="q" value="" placeholder="<?php echo $i18n['search photos']; ?>">
<button type="submit"><svg class="icon"><use xlink:href="<?php echo $web->getWebRoot(); ?>layout/images/symbols.svg#magnifying-glass"></use></svg></button></form>
<div class="barVertSeparator"></div>
<div class="optionBar">
<div class="barTxt"><label><?php echo $i18n['sorting']; ?></label><?php echo $mSort->render(); ?></div>
<div class="barVertSeparator"></div>
<div class="barTxt"><label><?php echo $i18n['rating']; ?></label><?php echo $mRating->render(); ?></div>
<div class="barVertSeparator"></div>
<button id="map" class="barTxt" title="<?php echo $i18n['show on map']; ?>"><a href="photo-mapsearch.php<?php echo $query->getString(); ?>">
	<?php echo $i18n['map']; ?><svg class="icon"><use xlink:href="<?php echo $web->getWebRoot(); ?>layout/images/symbols.svg#map-marker"></use></svg></a></button>
</div>
</div>
<?php echo $pagingBar; ?>
</div>
<div><ul id="slides"><?php echo $photo->renderData($photos, $web, $lang, $i18n); ?></ul></div>
<?php echo $pagingBar ?>

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
<?php require_once 'inc_body_end.php'; ?>
<script src="../../library/tinyamd.min.js" type="text/javascript"></script>
<script type="text/javascript">require(['photo']);</script>
</body>
</html>