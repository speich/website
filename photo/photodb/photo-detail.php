<?php require_once 'photo-detail_inc.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $lang->get(); ?>">
<head>
<title>Foto | <?php echo $web->pageTitle; ?></title>
<?php require_once 'inc_head.php' ?>
<link href="photodb.css" rel="stylesheet" type="text/css">
<link href="photo-detail.css" rel="stylesheet" type="text/css">
</head>

<body data-config="<?php echo $jsConfig; ?>">
<?php
require_once 'inc_body_begin.php';
renderPhoto($photo[0], $db, $web, $lang, $i18n);
require_once 'inc_body_end.php';
?>
<script type="text/javascript" src="../../library/dojo/1.12.1/dojo/dojo.js" data-dojo-config="async: true,
    gmapsApiKey: 'AIzaSyBEPhZpv_OQYeJH_mVYEOubDgGLlY5aLWg',
	locale: '<?php echo $locale = $lang->get(); ?>',
	packages: [
		{name: 'gmap', location: './../../../gmap'}
	]"></script>
<script src="photo-detail.js"></script>
</body>
</html>