<?php
require_once __DIR__.'/../../scripts/php/inc_script.php';
$sideNav->arrItem[1]->setActive();
?>
<!DOCTYPE html>
<html lang="<?php echo $language->get(); ?>">
<head>
    <title><?php echo $web->pageTitle; ?>: 3D(HTML) cube in pure JavaScript</title>
    <?php echo $head->render(); ?>
    <script src="./3d.min.js" type="text/javascript"></script>
    <link href="3d.min.css" rel="stylesheet">
</head>

<body>
<?php echo $bodyStart->render($mainNav, $sideNav, $langNav); ?>
<h1>3D(HTML) cube in pure JavaScript</h1>
<p>Mozilla testcase can be found <a href="3d.htm" id="opener">here</a> (opens in new window).</p>
<div id="viewAreaCont">
    <div id="viewArea">Click me to start/stop.</div>
</div>
<?php echo $bodyEnd->render(); ?>
</body>
</html>