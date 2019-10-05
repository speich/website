    <header class="row1">
        <a href="<?php echo $web->getWebRoot().$lang->createPage('index.php'); ?>">
	        <div class="speich-logo">
	        <div class="text">
	          <div>speich</div>
	          <div>.net</div>
	        </div>
	        <svg><use xlink:href="<?php echo $web->getWebRoot(); ?>layout/images/symbols.svg#speich-logo"></use></svg>
	        </div>
        </a>
    </header>
		<div class="row1 header-after layout-medium"></div>
    <div class="row2 nav-before layout-wide"></div>
    <div class="nav row2">
	    <nav class="main"><?php echo $mainNav->render(); ?></nav>
	    <nav class="lang"><?php echo $langNav->render(); ?></nav>
    </div>
    <div class="row2 nav-after layout-wide"></div>
    <nav class="sub"><?php echo $sideNav->render();
    if (function_exists('get_template_part')) {
    	get_template_part('template-parts/content/sidemenu');
    }	?></nav>
    <main>