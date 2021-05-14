<?php
require_once __DIR__.'/../../../../scripts/php/inc_script.php';

// remember: in wordpress head footer are included in function scope, so variables can not be shared directly
add_action('wp_head', static function() use ($head) {
    echo $head->render();
});
add_action('wp_body_open', static function() use($bodyStart, $mainNav, $sideNav, $langNav) {
    $str = $bodyStart->render($mainNav, $sideNav, $langNav);
    // TODO: find better solution
    $str = str_replace('<main>',   '', $str);
    $str = preg_replace('/<nav class="sub">(.|\s)*?<\/nav>/', '', $str);
    echo $str;
    echo '<nav class="sub">';
    get_template_part('template-parts/content/sidemenu');
    echo '</nav>';
    echo '<main>';
});
add_action('wp_footer', static function() use ($bodyEnd) {
    echo $bodyEnd->render();
});

add_action('wp_enqueue_scripts', static function() {
    wp_enqueue_style('style', get_stylesheet_directory_uri().'/style2.min.css');
    wp_enqueue_style('prismjs', '/../library/prismjs-1.20.0/prism.min.css');
    wp_enqueue_script('prismjs', '/../library/prismjs-1.20.0/prism.min.js');
});
add_filter( 'wp_nav_menu_args', function($args) {
    $args['menu_class'] .= ' sideMenu';

    return $args;
});