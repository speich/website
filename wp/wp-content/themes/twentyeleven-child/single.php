<?php
/**
 * Template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
get_header(); ?>

<div id="primary">
<div id="content" role="main">
	<?php
	twentyeleven_content_nav('nav-above');
	while (have_posts()) : the_post();
		get_template_part('content-single', get_post_format());
		comments_template('', true);
	endwhile;
	twentyeleven_content_nav('nav-below');
	?>
</div><!-- #content -->
</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
?>