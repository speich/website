<?php require_once '../scripts/php/inc_script.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo $language->get(); ?>">
<head>
    <title>Portfolio | <?php echo $web->pageTitle; ?></title>
    <?php echo $head->render(); ?>
</head>

<body>
<?php echo $bodyStart->render($mainNav, $sideNav, $langNav); ?>
<h1>Portfolio</h1>

<?php echo $bodyEnd->render(); ?>
</body>
</html>