<?php require_once 'photo-detail_inc.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $lang->get(); ?>">
<head>
<title>Photo |<?php echo $web->pageTitle; ?></title>
<?php require_once 'inc_head.php' ?>
<link href="photodb.min.css" rel="stylesheet" type="text/css">
<link href="photo-detail.min.css" rel="stylesheet" type="text/css">
</head>

<body data-config="<?php echo $jsConfig; ?>">
<?php
require_once 'inc_body_begin.php';
renderPhoto($photo[0], $photoDb, $lang, $i18n);
require_once 'inc_body_end.php';
?>
<script src="photo-detail.min.js" type="module"></script>
</body>
</html>