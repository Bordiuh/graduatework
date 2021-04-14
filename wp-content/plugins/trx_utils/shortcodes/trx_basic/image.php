<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('junotoys_sc_image_theme_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_sc_image_theme_setup' );
	function junotoys_sc_image_theme_setup() {
		add_action('junotoys_action_shortcodes_list', 		'junotoys_sc_image_reg_shortcodes');
		if (function_exists('junotoys_exists_visual_composer') && junotoys_exists_visual_composer())
			add_action('junotoys_action_shortcodes_list_vc','junotoys_sc_image_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

if (!function_exists('junotoys_sc_image')) {	
	function junotoys_sc_image($atts, $content=null){	
		if (junotoys_in_shortcode_blogger()) return '';
		extract(junotoys_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			"align" => "",
			"shape" => "square",
			"src" => "",
			"url" => "",
			"icon" => "",
			"link" => "",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => "",
			"width" => "",
			"height" => ""
		), $atts)));
		$css .= ($css ? ';' : '') . junotoys_get_css_position_from_values($top, $right, $bottom, $left);
		$css .= junotoys_get_css_dimensions_from_values($width, $height);
		$src = $src!='' ? $src : $url;
		if ($src > 0) {
			$attach = wp_get_attachment_image_src( $src, 'full' );
			if (isset($attach[0]) && $attach[0]!='')
				$src = $attach[0];
		}
		if (!empty($width) || !empty($height)) {
			$w = !empty($width) && strlen(intval($width)) == strlen($width) ? $width : null;
			$h = !empty($height) && strlen(intval($height)) == strlen($height) ? $height : null;
			if ($w || $h) $src = junotoys_get_resized_image_url($src, $w, $h);
		}
		if (trim($link)) junotoys_enqueue_popup();
		$output = empty($src) ? '' : ('<figure' . ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ' class="sc_image ' . ($align && $align!='none' ? ' align' . esc_attr($align) : '') . (!empty($shape) ? ' sc_image_shape_'.esc_attr($shape) : '') . (!empty($class) ? ' '.esc_attr($class) : '') . '"'
			. (!junotoys_param_is_off($animation) ? ' data-animation="'.esc_attr(junotoys_get_animation_classes($animation)).'"' : '')
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
			. '>'
				. (trim($link) ? '<a href="'.esc_url($link).'">' : '')
				. '<img src="'.esc_url($src).'" alt="" />'
				. (trim($link) ? '</a>' : '')
				. (trim($title) || trim($icon) ? '<figcaption><span'.($icon ? ' class="'.esc_attr($icon).'"' : '').'></span> ' . ($title) . '</figcaption>' : '')
			. '</figure>');
		return apply_filters('junotoys_shortcode_output', $output, 'trx_image', $atts, $content);
	}
	junotoys_require_shortcode('trx_image', 'junotoys_sc_image');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'junotoys_sc_image_reg_shortcodes' ) ) {
	//add_action('junotoys_action_shortcodes_list', 'junotoys_sc_image_reg_shortcodes');
	function junotoys_sc_image_reg_shortcodes() {
	
		junotoys_sc_map("trx_image", array(
			"title" => esc_html__("Image", 'junotoys'),
			"desc" => wp_kses_data( __("Insert image into your post (page)", 'junotoys') ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"url" => array(
					"title" => esc_html__("URL for image file", 'junotoys'),
					"desc" => wp_kses_data( __("Select or upload image or write URL from other site", 'junotoys') ),
					"readonly" => false,
					"value" => "",
					"type" => "media",
					"before" => array(
						'sizes' => true		// If you want allow user select thumb size for image. Otherwise, thumb size is ignored - image fullsize used
					)
				),
				"title" => array(
					"title" => esc_html__("Title", 'junotoys'),
					"desc" => wp_kses_data( __("Image title (if need)", 'junotoys') ),
					"value" => "",
					"type" => "text"
				),
				"icon" => array(
					"title" => esc_html__("Icon before title",  'junotoys'),
					"desc" => wp_kses_data( __('Select icon for the title from Fontello icons set',  'junotoys') ),
					"value" => "",
					"type" => "icons",
					"options" => junotoys_get_sc_param('icons')
				),
				"align" => array(
					"title" => esc_html__("Float image", 'junotoys'),
					"desc" => wp_kses_data( __("Float image to left or right side", 'junotoys') ),
					"value" => "",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => junotoys_get_sc_param('float')
				), 
				"shape" => array(
					"title" => esc_html__("Image Shape", 'junotoys'),
					"desc" => wp_kses_data( __("Shape of the image: square (rectangle) or round", 'junotoys') ),
					"value" => "square",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => array(
						"square" => esc_html__('Square', 'junotoys'),
						"round" => esc_html__('Round', 'junotoys')
					)
				), 
				"link" => array(
					"title" => esc_html__("Link", 'junotoys'),
					"desc" => wp_kses_data( __("The link URL from the image", 'junotoys') ),
					"value" => "",
					"type" => "text"
				),
				"width" => junotoys_shortcodes_width(),
				"height" => junotoys_shortcodes_height(),
				"top" => junotoys_get_sc_param('top'),
				"bottom" => junotoys_get_sc_param('bottom'),
				"left" => junotoys_get_sc_param('left'),
				"right" => junotoys_get_sc_param('right'),
				"id" => junotoys_get_sc_param('id'),
				"class" => junotoys_get_sc_param('class'),
				"animation" => junotoys_get_sc_param('animation'),
				"css" => junotoys_get_sc_param('css')
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'junotoys_sc_image_reg_shortcodes_vc' ) ) {
	//add_action('junotoys_action_shortcodes_list_vc', 'junotoys_sc_image_reg_shortcodes_vc');
	function junotoys_sc_image_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_image",
			"name" => esc_html__("Image", 'junotoys'),
			"description" => wp_kses_data( __("Insert image", 'junotoys') ),
			"category" => esc_html__('Content', 'junotoys'),
			'icon' => 'icon_trx_image',
			"class" => "trx_sc_single trx_sc_image",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "url",
					"heading" => esc_html__("Select image", 'junotoys'),
					"description" => wp_kses_data( __("Select image from library", 'junotoys') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Image alignment", 'junotoys'),
					"description" => wp_kses_data( __("Align image to left or right side", 'junotoys') ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(junotoys_get_sc_param('float')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "shape",
					"heading" => esc_html__("Image shape", 'junotoys'),
					"description" => wp_kses_data( __("Shape of the image: square or round", 'junotoys') ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
						esc_html__('Square', 'junotoys') => 'square',
						esc_html__('Round', 'junotoys') => 'round'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", 'junotoys'),
					"description" => wp_kses_data( __("Image's title", 'junotoys') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Title's icon", 'junotoys'),
					"description" => wp_kses_data( __("Select icon for the title from Fontello icons set", 'junotoys') ),
					"class" => "",
					"value" => junotoys_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "link",
					"heading" => esc_html__("Link", 'junotoys'),
					"description" => wp_kses_data( __("The link URL from the image", 'junotoys') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				junotoys_get_vc_param('id'),
				junotoys_get_vc_param('class'),
				junotoys_get_vc_param('animation'),
				junotoys_get_vc_param('css'),
				junotoys_vc_width(),
				junotoys_vc_height(),
				junotoys_get_vc_param('margin_top'),
				junotoys_get_vc_param('margin_bottom'),
				junotoys_get_vc_param('margin_left'),
				junotoys_get_vc_param('margin_right')
			)
		) );
		
		class WPBakeryShortCode_Trx_Image extends JUNOTOYS_VC_ShortCodeSingle {}
	}
}
?>