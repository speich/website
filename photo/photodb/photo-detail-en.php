<?php require_once __DIR__.'/photo-detail_inc.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $language->get(); ?>">
<head>
    <title><?php echo $pageTitle ?></title>
    <link rel="alternate" hreflang="en" href="https://www.speich.net/photo/photodb/photo-detail-en.php<?php echo $_GET['imgId'] ? '?imgId='.$_GET['imgId'] : ''; ?>">
    <link rel="alternate" hreflang="de" href="https://www.speich.net/photo/photodb/photo-detail.php<?php echo $_GET['imgId'] ? '?imgId='.$_GET['imgId'] : ''; ?>">
    <link rel="canonical" href="https://www.speich.net/photo/photodb/photo-detail-en.php<?php echo $_GET['imgId'] ? '?imgId='.$_GET['imgId'] : ''; ?>"/>
    <meta name="description" content="<?php echo $metaDesc; ?>">
    <?php echo $head->render(); ?>
    <link href="photodb.min.css" rel="stylesheet" type="text/css">
    <link href="photo-detail.min.css" rel="stylesheet" type="text/css">
    <script src="photo-detail.min.js" type="module"></script>
</head>

<body data-config="<?php echo $jsConfig; ?>">
<?php
echo $bodyStart->render($mainNav, $sideNav, $langNav);;
$photoDetail->render($photo, $language, $i18n);
echo $bodyEnd->render();
?>
</body>
</html>