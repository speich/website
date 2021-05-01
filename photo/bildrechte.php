<?php require_once '../scripts/php/inc_script.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $language->get(); ?>">
<head>
<title>Bildrechte | <?php echo $web->pageTitle; ?></title>
<?php echo $head->render(); ?>
</head>

<body>
<?php echo $bodyStart->render($mainNav, $sideNav, $langNav); ?>
<?php echo $_SERVER['DOCUMENT_ROOT']; ?>
<h1>Bildrechte</h1>
<p>Die abgebildeten Fotos können Ausschnitte darstellen.</p>
</body>
</html>