<div id="layoutTop01"><a href="<?php echo $web->getWebRoot().$lang->createPage('index.php'); ?>"><img src="<?php echo $web->getWebRoot().'layout/images/layout-logo.gif'; ?>" title="Simon Speich | speich.net" alt="speich.net logo"></a></div>
<div id="layoutTop02"><?php echo $mainNav->render(); echo $lang->renderNav(null, $web); ?></div>
<div id="layoutMiddle">
<div id="layoutNav"><?php echo $sideNav->render(); ?></div>
<div id="layoutMain">