<?php require_once __DIR__.'/photo-detail_inc.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $language->get(); ?>">
<head>
<title>Photo |<?php echo $web->pageTitle; ?></title>
<?php echo $head->render(); ?>
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