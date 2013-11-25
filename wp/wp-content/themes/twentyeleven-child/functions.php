<?php
$content_width = 782;


add_shortcode('wp_caption', 'fixed_img_caption_shortcode');
add_shortcode('caption', 'fixed_img_caption_shortcode');

/**
 * Remove wp-caption inline style width in Wordpress 3.4
 * @param $attr
 * @param null $content
 * @return mixed|null|string|void
 */
function fixed_img_caption_shortcode($attr, $content = null) {
	// New-style shortcode with the caption inside the shortcode with the link and image tags.
	if (!isset($attr['caption'])) {
		if (preg_match('#((?:<a [^>]+>\s*)?<img [^>]+>(?:\s*</a>)?)(.*)#is', $content, $matches)) {
			$content = $matches[1];
			$attr['caption'] = trim($matches[2]);
		}
	}

	// Allow plugins/themes to override the default caption template.
	$output = apply_filters('img_caption_shortcode', '', $attr, $content);
	if ($output != '') {
		return $output;
	}

	extract(shortcode_atts(array(
		'id' => '',
		'align' => 'alignnone',
		'width' => '',
		'caption' => ''
	), $attr));

	if (1 > (int) $width || empty($caption)) {
		return $content;
	}

	if ($id) {
		$id = 'id="'.esc_attr($id).'" ';
	}

	return '<div '.$id.'class="wp-caption '.esc_attr($align).'" style="width: '.$width.'px">'
		.do_shortcode($content).'<p class="wp-caption-text">'.$caption.'</p></div>';
}