<?php
require_once 'scripts/php/inc_script.php';
require_once 'index_inc.php';
?>
<!DOCTYPE html>
<html lang="de-ch">
<head>
<title><?php echo $web->pageTitle; ?></title>
<meta name="description" content="Website von Simon Speich über Fotografie und Webprogrammierung">
<meta name="keywords" content="Simon Speich, Foto, Fotografie, Webprogrammierung, Bilddatenbank, dojo, dojotoolkit, JavaScript, PHP, Natur, Tiere, Vögel, Flora und Fauna">
<?php require_once 'layout/inc_head.php' ?>
<link rel="stylesheet" href="index.css">
</head>

<body>
<?php require_once 'inc_body_begin.php'; ?>
<h1>Ein- und Aussichten</h1>
<h2>Website von Simon Speich über Fotografie und Webprogrammierung</h2>
<figure><a href="<?php echo $url; ?>" title="zufällig gewähltes Foto aus der Bildatenbank"><img class="imgFrame" src="<?php echo $src; ?>" alt="Foto: <?php echo $photo->ImgTitle ?>"></a>
<figcaption><?php echo $photo->ImgTitle; ?></figcaption>
</figure>
<p>Haben Sie Freude an schönen Naturfotos insbesondere zum Thema <a href="photo/photodb/photo.php?theme=1">Vögel</a> und andere Tiere,
	dann ist meine <a href="photo/photodb/photo.php">Bilddatenbank</a> genau das Richtige für Sie.</p>
<p>Interessieren Sie sich eher für die Webprogrammierung oder andere IT Themen, dann lesen Sie doch einen meiner Artikel (in Englisch), z.B. über
	<a href="articles/en/category/php/">PHP</a>, <a href="articles/en/category/javascript/">JavaScript</a> oder <a href="articles/en/tag/sql/">SQL</a>.</p>
<?php require_once 'inc_body_end.php'; ?>
</body>
</html>