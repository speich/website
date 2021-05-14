<?php
require_once __DIR__.'/scripts/php/inc_script.php';
require_once __DIR__.'/index_inc.php';
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
<title><?php echo $web->pageTitle; ?></title>
<meta name="description" content="Simon Speich's website about photography and web programming">
<meta name="keywords" content="Simon Speich, photo, photography, web programming, photo archive, dojo, dojotoolkit, JavaScript, PHP, nature, animals, birds, wildlife">
<?php echo $head->render(); ?>
<link rel="stylesheet" href="index.css">
</head>

<body>
<?php echo $bodyStart->render($mainNav, $sideNav, $langNav); ?>
<h1>Insights and views</h1>
<h2>Simon Speich's website about photography and web programming</h2>
<figure><a href="<?php echo $url; ?>" title="random photo from the image database"><img class="imgFrame" src="<?php echo $src; ?>" alt="photo: <?php echo $photo->ImgTitle ?>" width="100%" height="100%"></a>
	<figcaption><?php echo $photo->ImgTitle; ?></figcaption>
</figure>
<p>If you enjoy looking at beautiful nature photos, especially <a href="photo/photodb/photo-en.php?theme=1" title="photos of birds">birds</a>,
	then my <a href="photo/photodb/photo-en.php" title="photos of animals and landscapes">photo database</a> should be exactly right for you.</p>
<p>If you are more interested in web programming or other IT topics, why not read one of my articles about
	<a href="articles/en/category/php/">PHP</a>, <a href="articles/en/category/javascript/">JavaScript</a> or <a href="articles/en/tag/sql/">SQL</a>.</p>
<?php echo $bodyEnd->render(); ?>
</body>
</html>