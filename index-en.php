<?php require_once 'scripts/php/inc_script.php'; ?>
<!DOCTYPE html>
<html lang="en-us">
<head>
<title><?php echo $web->pageTitle; ?></title>
<meta name="description" content="Simon Speich's website about photography and web programming">
<meta name="keywords" content="Simon Speich, photo, photography, web programming, photo archive, dojo, dojotoolkit, JavaScript, PHP, nature, animals, birds, wildlife">
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
<h1>Insights and views</h1>
<p>This is Simon Speich's Website about photography and web programming.</p>
<p>If you enjoy looking at beautiful nature photos, especially <a href="photo/photodb/photo-en.php?theme=1">birds</a>,
	then my <a href="photo/photodb/photo-en.php">photo archive</a> is exactly what you are looking for.
	If you are more interested in web programming, have a look at my articles about
	<a href="articles/en/category/php/">PHP</a> and <a href="articles/en/category/javascript/">JavaScript</a>.</p>
<div class="imgFrame"><img src="library/imagefnc.php?fnc=randDbImg" title="photo www.speich.net" alt="random photo" width="740"></div>
<?php require_once 'inc_body_end.php'; ?>
</body>
</html>