<?php

use PhotoDb\PhotoList;
use PhotoDb\SqlPhotoList;
use WebsiteTemplate\PagedNav;


require_once __DIR__.'/../../scripts/php/inc_script.php';
require_once __DIR__.'/photo_inc.php';

$photo = new PhotoList($db);
$sql = new SqlPhotoList();
$sql->qual = $params->qual;
$sql->theme = $params->theme;
$sql->country = $params->country;
$sql->lat1 = $params->lat1;
$sql->lng1 = $params->lng1;
$sql->lat2 = $params->lat2;
$sql->lng2 = $params->lng2;
$numRec = $photo->getNumRec($sql);
$sql->offset = $params->pg + $params->pg * $params->numPerPg;
$sql->limit = $params->numPerPg;
$sql->setSort($params->sort);
$photos = $photo->loadPhotos($sql);
$pagedNav = new PagedNav($numRec, $params->numPerPg);
$pagedNav->cssClass = 'bar-item pgNav';
$pagedNav->renderText = false;
$pagedNav->setWhitelist($web->getWhitelistQueryString());
$word = 'photo'.($numRec > 1 ? 's' : '');
$pagingBar = '<div class="bar-paging">'.
    '<div class="bar-item">'.$numRec.' '.$i18n[$word].'</div>'.
    '<div class="bar-sep-vert"></div>'.
    '<div class="bar-item">'.$i18n['per page'].'</div>'.
		'<div class="bar-item">'.$mRecPp->render().'</div>'.
    '<div class="bar-sep-vert"></div>'.
    $pagedNav->render($params->pg + 1, $web).
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
<ul id="slides"><?php echo $photo->renderData($photos, $web, $lang, $i18n); ?></ul>
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
<?php require_once 'inc_body_end.php'; ?>
<script src="../../library/tinyamd.min.js" type="text/javascript"></script>
<script type="text/javascript">require(['photo']);</script>
</body>
</html>