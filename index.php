<?php require_once 'library/inc_script.php'; ?>
<!DOCTYPE html>
<html lang="de-ch">
<head>
<title>speich.net - Fotografie und Webprogrammierung</title>
<meta name="description" content="Website von Simon Speich über Fotografie und Webprogrammierung">
<meta name="keywords" content="Simon Speich, Foto, Fotografie, Webprogrammierung, Bilddatenbank, dojo, dojotoolkit, JavaScript, PHP, Natur, Tiere, Vögel, Flora und Fauna, Schweiz">
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
<h1>Ein- und Aussichten</h1>
<p>Dies ist die Website von Simon Speich über Fotografie und Webprogrammierung.</p>
<p>Haben Sie Freude an schönen Naturfotos insbesondere zum Thema <a href="photo/photodb/photo.php?theme=1">Vögel</a>,
	dann ist meine <a href="photo/photodb/photo.php">Bilddatenbank</a> genau das Richtige für Sie. Wenn Sie sich eher
für die Webprogrammierung interessieren lesen Sie doch einen meiner Artikel über
	<a href="articles/category/php/">PHP</a> oder <a href="articles/category/javascript/">JavaScript</a>.</p>
<div class="imgFrame"><img src="library/imagefnc.php?fnc=randDbImg" title="Foto www.speich.net" alt="zufällig ausgewähltes Foto" width="740"></div>
<?php require_once 'inc_body_end.php'; ?>
</body>
</html>