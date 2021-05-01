<?php require_once 'photo-detail_inc.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $language->get(); ?>">
<head>
<title>Foto | <?php echo $web->pageTitle; ?></title>
<?php require_once 'inc_head.php' ?>
<link rel="canonical" href="https://www.speich.net/photo/photodb/photo-detail.php?imgId=<?php echo $_GET['imgId'] ?? ''; ?>"/>
<link href="photodb.min.css" rel="stylesheet" type="text/css">
<link href="photo-detail.min.css" rel="stylesheet" type="text/css">
</head>

<body data-config="<?php echo $jsConfig; ?>">
<?php
require_once 'inc_body_begin.php';
renderPhoto($photo[0], $photoDb, $language, $i18n);
require_once 'inc_body_end.php';
?>
<script src="photo-detail.min.js" type="module"></script>
</body>
</html>