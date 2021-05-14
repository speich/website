<?php
require_once __DIR__.'/photo_inc.php';
$i18n = require __DIR__.'/nls/'.$language->get().'/photo-mapsearch.php';
$web->setLastPage();
?>
<!DOCTYPE html>
<html lang="de-CH">
<head>
    <title><?php echo $i18n['page title'].' | '.$web->pageTitle; ?></title>
    <?php echo $head->render(); ?>
    <link href="photodb.min.css" rel="stylesheet" type="text/css">
    <link href="photo-mapsearch.min.css" rel="stylesheet" type="text/css">
    <script src="photo-mapsearch.min.js" type="module" defer></script>
</head>

<body>
<?php echo $bodyStart->render($mainNav, $sideNav, $langNav); ?>
<div id="mapContainer">
    <div id="map-canvas"></div>
</div>
<div class="toolbar hidden">
    <?php echo $mRating->render(); ?>
    <button id="showPhotos" class="bar-item"><a href="photo.php"><?php echo $i18n['photos']; ?>
            <svg class="icon">
                <use xlink:href="../../layout/images/symbols.svg#grid">
            </svg>
        </a></button>
</div>

<?php echo $bodyEnd->render(); ?>
</body>
</html>