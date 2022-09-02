<?php require_once __DIR__.'/photo-detail_inc.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $language->get(); ?>">
<head>
    <title>Foto | <?php echo $web->pageTitle; ?></title>
    <link rel="alternate" hreflang="en" href="https://www.speich.net/photo/photodb/photo-detail-en.php"/>
    <link rel="alternate" hreflang="de" href="https://www.speich.net/photo/photodb/photo-detail.php"/>
    <?php echo $head->render(); ?>
    <link rel="canonical" href="https://www.speich.net/photo/photodb/photo-detail.php?imgId=<?php echo $_GET['imgId'] ?? ''; ?>"/>
    <link href="photodb.min.css" rel="stylesheet" type="text/css">
    <link href="photo-detail.min.css" rel="stylesheet" type="text/css">
</head>

<body data-config="<?php echo $jsConfig; ?>">
<?php
echo $bodyStart->render($mainNav, $sideNav, $langNav);;
renderPhoto($photo[0], $photoDb, $language, $i18n);
echo $bodyEnd->render();
?>
<script src="photo-detail.min.js" type="module"></script>
</body>
</html>