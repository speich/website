<div id="layoutTop01"><a href="<?php echo $web->getWebRoot().$lang->createPage('index.php'); ?>"><img src="<?php echo $web->getWebRoot().'layout/images/layout-logo.gif'; ?>" title="Simon Speich | speich.net" alt="speich.net logo"></a></div>
<div id="layoutTop02"><?php echo $mainNav->render(); echo $langNav->render(); ?></div>
<div id="layoutMiddle">
<div id="layoutNav">
<?php echo $sideNav->render(); ?>
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
</div>
<div id="layoutMain">