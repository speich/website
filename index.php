<?php
require_once __DIR__.'/scripts/php/inc_script.php';
require_once __DIR__.'/index_inc.php';

// if user requested root, e.g. www.speich.ch/ we have to redirect to the correct index.php according to language
if ($language->get() !== $language->getDefault()) {
	$url = $language->createPage('index.php');
	header('Location: '.$url);
	exit;
}
?>
<!DOCTYPE html>
<html lang="de-ch">
<head>
<title><?php echo $web->pageTitle; ?></title>
<meta name="description" content="Website von Simon Speich über Naturfotografie und Webprogrammierung">
<meta name="keywords" content="Simon Speich, Naturfotografie, Foto, Fotografie, Webprogrammierung, Bilddatenbank, JavaScript, PHP, Natur, Tiere, Vögel, Flora und Fauna">
<?php echo $head->render(); ?>
<link rel="stylesheet" href="index.css">
</head>

<body>
<?php echo $bodyStart->render($mainNav, $sideNav, $langNav); ?>
<h1>Ein- und Aussichten</h1>
<h2>Website von Simon Speich über Fotografie und Webprogrammierung</h2>
<figure><a href="<?php echo $url; ?>" title="zufällig gewähltes Foto aus der Bildatenbank"><img class="imgFrame" src="<?php echo $src; ?>" alt="Foto: <?php echo $photo->ImgTitle ?>" width="100%" height="100%"></a>
<figcaption><?php echo $photo->ImgTitle; ?></figcaption>
</figure>
<p>Haben Sie Freude an schönen Naturfotos insbesondere zum Thema <a href="photo/photodb/photo.php?theme=1" title="Fotos von Vögeln">Vögel</a> und andere Tiere,
	dann ist meine <a href="photo/photodb/photo.php" title="Fotos von Tieren und Landschaften">Bilddatenbank</a> genau das Richtige für Sie.</p>
<p>Interessieren Sie sich eher für die Webprogrammierung oder andere IT Themen, dann lesen Sie doch einen meiner Artikel (in Englisch), z.B. über
	<a href="articles/en/category/php/">PHP</a>, <a href="articles/en/category/javascript/">JavaScript</a> oder <a href="articles/en/tag/sql/">SQL</a>.</p>
<?php echo $bodyEnd->render(); ?>
</body>
</html>