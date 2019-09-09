    <div class="row1 header-before"></div>
    <header class="row1">
        <a href="<?php echo $web->getWebRoot().$lang->createPage('index.php'); ?>"><img src="/layout/images/layout-logo.gif" title="Simon Speich | speich.net" alt="speich.net logo"></a>
        <img src="/layout/images/layout-top.jpg">
    </header>
    <div class="row2 nav-before"></div>
    <nav class="row2 main"><?php echo $mainNav->render(); ?></nav>
    <nav class="row2 lang"><?php echo $langNav->render(); ?></nav>
    <div class="row2 nav-after"></div>
    <nav class="sub"><?php echo $sideNav->render(); ?>
    <?php
    if (function_exists('get_template_part')) {
    	get_template_part('template-parts/footer/footer', 'widgets');
    	if (has_nav_menu('footer')) { ?>
    		<nav class="footer-navigation" aria-label="<?php esc_attr_e('Footer Menu', 'twentynineteen'); ?>">
        <?php
        wp_nav_menu([
            'theme_location' => 'footer',
            'menu_class' => 'footer-menu',
            'depth' => 1,
        ]);
        }
        ?>
    	</nav>
    <?php }	?>
    </nav>
    <main>