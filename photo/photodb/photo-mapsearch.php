<?php
require_once 'photo_inc.php';
$i18n = require_once __DIR__.'/nls/'.$lang->get().'/photo-mapsearch.php';
$web->setLastPage();
?>
<!DOCTYPE html>
<html lang="de-CH">
<head>
    <title><?php echo $i18n['page title'].' | '.$web->pageTitle; ?></title>
    <?php require_once 'inc_head.php' ?>
    <link href="photodb.min.css" rel="stylesheet" type="text/css">
    <link href="photo-mapsearch.min.css" rel="stylesheet" type="text/css">
    <script src="photo-mapsearch.js" type="module" defer></script>
</head>

<body>
<?php require_once 'inc_body_begin.php'; ?>
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

<?php require_once 'inc_body_end.php'; ?>
</body>
</html>