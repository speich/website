<?php
require_once 'scripts/php/inc_script.php';
require_once 'index_inc.php';
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
<title><?php echo $web->pageTitle; ?></title>
<meta name="description" content="Simon Speich's website about photography and web programming">
<meta name="keywords" content="Simon Speich, photo, photography, web programming, photo archive, dojo, dojotoolkit, JavaScript, PHP, nature, animals, birds, wildlife">
<?php require_once 'layout/inc_head.php' ?>
<link rel="stylesheet" href="index.css">
</head>

<body>
<?php require_once 'inc_body_begin.php'; ?>
<h1>Insights and views</h1>
<h2>Simon Speich's website about photography and web programming</h2>
<figure><a href="<?php echo $url; ?>" title="random photo from the image database"><img src="<?php echo $src; ?>" alt="photo: <?php echo $photo->ImgTitle ?>"></a>
	<figcaption><?php echo $photo->ImgTitle; ?></figcaption>
</figure>
<p>If you enjoy looking at beautiful nature photos, especially <a href="photo/photodb/photo-en.php?theme=1">birds</a>,
	then my <a href="photo/photodb/photo-en.php">photo archive</a> should be exactly right for you.</p>
<p>If you are more interested in web programming or other IT topics, why not read one of my articles about
	<a href="articles/en/category/php/">PHP</a>, <a href="articles/en/category/javascript/">JavaScript</a> or <a href="articles/en/tag/sql/">SQL</a>.</p>
<?php require_once 'inc_body_end.php'; ?>
</body>
</html>