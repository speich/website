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
    <title>Simon Speich - Naturfotografie und Webprogrammierung</title>
    <link rel="alternate" hreflang="en" href="https://www.speich.net/index-en.php">
    <link rel="alternate" hreflang="de" href="https://www.speich.net/index.php">
    <meta name="description" content="Website von Simon Speich über Naturfotografie und Webprogrammierung">
    <meta name="keywords" content="Simon Speich, Naturfotografie, Foto, Fotografie, Webprogrammierung, Bilddatenbank, JavaScript, PHP, Natur, Tiere, Vögel, Flora und Fauna">
    <?php echo $head->render(); ?>
</head>

<body>
<?php echo $bodyStart->render($mainNav, $sideNav, $langNav); ?>
<h1>Simon Speich - Naturfotografie und Webprogrammierung</h1>
<figure><a href="<?php echo $photo->link; ?>"><img class="imgFrame" src="<?php echo $photo->src; ?>" srcset="<?php echo $photo->srcset; ?>"
                alt="Foto: <?php echo $photo->ImgTitle ?>" width="<?php echo $photo->w; ?>" height="<?php echo $photo->h; ?>"></a>
    <figcaption><?php echo $photo->ImgTitle; ?></figcaption>
</figure>
<h2>Fotografische Ein- und Aussichten</h2>
<p>Haben Sie Freude an schönen, hochaufgelösten Fotos von der Natur, insbesondere von <a href="photo/photodb/photo.php?theme=1">Vögeln</a> und
    <a href="photo/photodb/photo.php?q=w%C3%A4lder&country=4">Wäldern in der Schweiz</a>, dann ist die <a href="photo/photodb/photo.php">Bilddatenbank</a> genau
    die
    richtige Wahl. Sie finden dort Fotos von aktuell <?php echo $numBirdSpecies; ?> Vogelarten sowie <?php echo $numOtherSpecies ?> weiteren
    Tierarten. Die Wälder sind mit <?php echo $numForestFotos; ?> Bildern vertreten. Im Blog schreibe ich ab und zu auch einen <a
            href="articles/de/category/fotografie/">Artikel zum Thema Fotografie</a>.</p>
<h2>Weblog</h2>
<p>Interessieren Sie sich für die Webprogrammierung (<a href="articles/en/category/php/">PHP</a>, <a href="articles/en/category/javascript/">JavaScript</a>,
    <a href="articles/en/category/sql/">SQL</a> oder <a href="articles/en/category/cascading-style-sheets-css/">CSS</a>) sowie andere IT Themen (<a
            href="articles/en/tag/linux/">Linux</a>, <a href="articles/en/tag/virtualbox/">VirtualBox</a>), dann lesen Sie doch einen meiner Artikel aus meinem
    Blog, zum Beispiel über «<a href="articles/en/2021/12/19/how-to-install-the-cncnet-client-on-linux/">How to Install the CnCNet
        Client on Linux</a>» oder «<a href="articles/en/2018/12/24/virtualbox-6-how-to-enable-symlinks-in-a-linux-guest-os/">VirtualBox 6: How to enable
        symlinks for shared folders</a>».</p>
<?php echo $bodyEnd->render(); ?>
</body>
</html>