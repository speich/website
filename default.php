<?php require_once 'library/inc_script.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $web->getLang(); ?>">
<head>
<title><?php echo $web->pageTitle; ?></title>
<?php require_once 'layout/inc_head.php' ?>
<style type="text/css">
.imgFrame {
	width: 740px;
	margin-left: 16px;
	border-width: 8px;
	background-image: url(library/imagefnc.php?fnc=randDbImg);
	-webkit-background-size: contain;
	-moz-background-size: contain;
	-o-background-size: contain;
	background-size: contain;
}
</style>
</head>

<body>
<?php require_once 'inc_body_begin.php'; ?>
<?php if ($web->getLang() === 'de') { ?>
<h1>Ein- und Aussichten</h1>
<p>Dies ist die Website von Simon Speich über Fotografie und Webprogrammierung.</p>
<p>Haben Sie Freude an schönen Naturfotos insbesondere zum Thema <a href="photo/photodb/photo.php?theme=1">Vögel</a>,
	dann ist meine <a href="photo/photodb/photo.php">Bilddatenbank</a> genau das Richtige für Sie. Wenn Sie sich eher
für die Webprogrammierung interessieren lesen Sie doch einen meiner Artikel über
	<a href="articles/category/php/">PHP</a> oder <a href="articles/category/javascript/">JavaScript</a>.</p>
<?php } else { ?>
<h1>Insights and views</h1>
<p>This is Simon Speich's Website about photography and web programming.</p>
<p>If you enjoy looking at beautiful nature photos, especially <a href="photo/photodb/photo.php?theme=1">birds</a>,
	then my <a href="photo/photodb/photo.php">photo archive</a> is exactly what you are looking for.
	If you are more interested in web programming, have a look at my articles about
	<a href="articles/category/php/">PHP</a> and <a href="articles/category/javascript/">JavaScript</a>.</p>
<?php } ?>

<div class="imgFrame"><img src="library/imagefnc.php?fnc=randDbImg" title="Photo www.speich.net" alt="random photo" width="740"></div>
<?php require_once 'inc_body_end.php'; ?>
</body>
</html>