<?php
/**
 * Displays the footer widget area
 *
 * @package WordPress
 * @subpackage speich
 */

if (is_active_sidebar('sidebar-1')) : ?>
	<aside class="widget-area" role="complementary" aria-label="<?php esc_attr_e('Primary', 'speich'); ?>">
		<div class="widget-column">
        <?php dynamic_sidebar('sidebar-1'); ?>
		</div>
	</aside><!-- .widget-area -->
<?php endif; ?>