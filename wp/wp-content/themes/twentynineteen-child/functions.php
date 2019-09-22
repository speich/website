<?php
require_once __DIR__.'/../../../../scripts/php/inc_script.php';


function enqueueStyles()
{
    $parent_style = 'parent-style';
    wp_enqueue_style($parent_style, get_template_directory_uri().'/style.css');
    wp_enqueue_style('child-style', get_stylesheet_directory_uri().'/style.css', [$parent_style], wp_get_theme()->get('Version'));
    /*wp_enqueue_script('normalize', '/../layout/normalize.min.css');*/
    wp_enqueue_script('format', '/../layout/format.css');
    wp_enqueue_style('layout', '/../layout/layout.css');
    wp_enqueue_style('prismjs', '/../library/prismjs-1.17.1/prism.css');
    wp_enqueue_script('prismjs', '/../library/prismjs-1.17.1/prism.min.js');
/*
    <link rel="preload" href="<?php echo $web->getWebRoot(); ?>layout/fonts/open-sans-v17-latin-regular.woff2" as="font">
    <link rel="preload" href="<?php echo $web->getWebRoot(); ?>layout/fonts/open-sans-v17-latin-italic.woff2" as="font">
    <link rel="preload" href="<?php echo $web->getWebRoot(); ?>layout/fonts/open-sans-v17-latin-600.woff2" as="font">
    <link rel="preload" href="<?php echo $web->getWebRoot(); ?>layout/fonts/open-sans-v17-latin-600italic.woff2" as="font">
    <link rel="preload" href="<?php echo $web->getWebRoot(); ?>layout/fonts/open-sans-v17-latin-700.woff2" as="font">
    <link rel="preload" href="<?php echo $web->getWebRoot(); ?>layout/fonts/open-sans-v17-latin-700italic.woff2" as="font">
    <link rel="preload" href="<?php echo $web->getWebRoot(); ?>layout/fonts/open-sans-v17-latin-800.woff2" as="font">*/

}

// remember: in wordpress head footer are included in function scope, so variables can not be shared directly
add_action('wp_head', static function() use ($web) {
    echo '<link href="'.$web->getWebRoot().'layout/images/favicon.png'.'" type="image/png" rel="shortcut icon">';
});

add_action('wp_body_open', static function() use($lang, $web, $mainNav, $sideNav, $langNav) {
    require_once 'inc_body_begin.php';
});
add_action('wp_footer', static function() use ($lang, $web, $mainNav, $sideNav, $langNav) {
    require_once 'inc_body_end.php';
});
add_action('wp_enqueue_scripts', 'enqueueStyles');
