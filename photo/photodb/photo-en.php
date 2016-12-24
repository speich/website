<?php
use WebsiteTemplate\PagedNav;

require_once __DIR__.'/../../library/inc_script.php';
require_once 'photoinc.php';

$photos = $photo->loadPhotos($params);
$numRec = $photo->getNumRec($params);

$pagedNav = new PagedNav($numRec, $params->numRecPerPage);
$pagedNav->renderText = false;

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
<link href="photodb.css" rel="stylesheet" type="text/css">
</head>

<body class="tundra">
<?php require_once 'inc_body_begin.php'; ?>
<div class="toolbar">
<?php echo $pagingBar; ?>
<div class="search">
<script>
  (function() {
    var cx = '<?php echo ($lang->get() == 'de' ? '000284793056488053930:zkcmsdcpu2k' : '000284793056488053930:vzx-zdwjz0w'); ?>';
    var gcse = document.createElement('script');
    gcse.type = 'text/javascript';
    gcse.async = true;
    gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') +
        '//www.google.com/cse/cse.js?cx=' + cx;
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(gcse, s);
  })();
</script>
<gcse:search></gcse:search>
</div>
<div class="optionBar">
<div class="barTxt"><?php
	echo $i18n['sorting'];
	echo $mSort->render(); ?>
</div>
<div class="barVertSeparator"></div>
<div class="barTxt"><?php
	echo $i18n['rating'];
	echo $mRating->render();
?></div>
<div class="barVertSeparator"></div>
<div id="showMap" class="button buttShowMap" title="<?php echo $i18n['show on map']; ?>"><a href="photo-mapsearch.php<?php echo $web->getQuery(); ?>	"><?php echo $i18n['map']; ?><img src="../../layout/images/icon_map.gif" alt="icon to display photos on a map"></a></div>
</div>
</div>

<div><ul><?php echo $photo->renderData($photos, $web, $lang, $i18n); ?></ul></div>

<?php echo $pagingBar ?>

<div id="slideFullScreenCont">
<div class="slideFullScreen">
<span class="slideFullScreenAuthor"><?php echo $i18n['photo']; ?> Simon Speich, www.speich.net</span>

<?php require_once 'inc_body_end.php'; ?>
<script type="text/javascript">
var dojoConfig = {
	async: true,
	locale: '<?php echo $locale = $lang->get(); ?>'
};
</script>
<script type="text/javascript" src="../../library/dojo/1.12.1/dojo/dojo.js"></script>
require(['../../photo/photodb/photoApp.js'], function(photoApp) {
	photoApp.init();
});
</body>
</html>