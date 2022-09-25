<?php
require_once __DIR__.'/scripts/php/inc_script.php';
require_once __DIR__.'/index_inc.php';
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
    <title>Simon Speich - Nature photography and web programming</title>
    <link rel="alternate" hreflang="en" href="https://www.speich.net/index-en.php">
    <link rel="alternate" hreflang="de" href="https://www.speich.net/index.php">
    <meta name="description" content="Simon Speich's website about nature photography and web programming">
    <meta name="keywords" content="Simon Speich, nature photography, photo, photography, web programming, photo archive, JavaScript, PHP, Linux, nature, animals, birds, wildlife">
    <?php echo $head->render(); ?>
</head>

<body>
<?php echo $bodyStart->render($mainNav, $sideNav, $langNav); ?>
<h1>Simon Speich - Nature photography and web programming</h1>
<figure><a href="<?php echo $photo->link; ?>"><img class="imgFrame" src="<?php echo $photo->src; ?>" srcset="<?php echo $photo->srcset; ?>"
                alt="photo: <?php echo $photo->ImgTitle ?>" width="<?php echo $photo->w; ?>" height="<?php echo $photo->h; ?>"></a>
    <figcaption><?php echo $photo->ImgTitle; ?></figcaption>
</figure>
<h2>Photographic insights and perspectives</h2>
<p>Do you enjoy looking at beautiful, high-resolution photos of wildlife and nature, especially of <a href="photo/photodb/photo-en.php?theme=1">birds</a> and of
    <a href="photo/photodb/photo-en.php?q=w%C3%A4lder&country=4">Swiss forests</a>, then the image database is exactly your right choice. There, you will
    currently find photos of <?php echo $numBirdSpecies; ?> bird species as well as <?php echo $numOtherSpecies ?> other animal species. The forests are
    represented with <?php echo $numForestFotos; ?> pictures. On the blog, I write <a href="articles/en/category/photography/">articles about photography</a>
    from time to time.</p>
<h2>Weblog</h2>
<p>Are you interested in web programming (<a href="articles/en/category/php/">PHP</a>, <a href="articles/en/category/javascript/">JavaScript</a>,
    <a href="articles/en/category/sql/">SQL</a> or <a href="articles/en/category/cascading-style-sheets-css/">CSS</a>) as well as other IT topics (<a
            href="articles/en/tag/linux/">Linux</a>, <a href="articles/en/tag/virtualbox/">VirtualBox</a>), then read one of the articles from my blog,
    e.g. about «<a href="articles/en/2021/12/19/how-to-install-the-cncnet-client-on-linux/">How to Install the CnCNet
        Client on Linux</a>» or «<a href="articles/en/2018/12/24/virtualbox-6-how-to-enable-symlinks-in-a-linux-guest-os/">VirtualBox 6: How to enable
        symlinks for shared folders</a>».</p>
<?php echo $bodyEnd->render(); ?>
</body>
</html>