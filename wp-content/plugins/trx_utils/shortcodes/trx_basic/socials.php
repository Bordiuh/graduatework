<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('junotoys_sc_socials_theme_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_sc_socials_theme_setup' );
	function junotoys_sc_socials_theme_setup() {
		add_action('junotoys_action_shortcodes_list', 		'junotoys_sc_socials_reg_shortcodes');
		if (function_exists('junotoys_exists_visual_composer') && junotoys_exists_visual_composer())
			add_action('junotoys_action_shortcodes_list_vc','junotoys_sc_socials_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

if (!function_exists('junotoys_sc_socials')) {	
	function junotoys_sc_socials($atts, $content=null){	
		if (junotoys_in_shortcode_blogger()) return '';
		extract(junotoys_html_decode(shortcode_atts(array(
			// Individual params
			"size" => "small",		// tiny | small | medium | large
			"shape" => "square",	// round | square
			"type" => junotoys_get_theme_setting('socials_type'),	// icons | images
			"socials" => "",
			"custom" => "no",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$css .= ($css ? ';' : '') . junotoys_get_css_position_from_values($top, $right, $bottom, $left);
		junotoys_storage_set('sc_social_data', array(
			'icons' => false,
            'type' => $type
            )
        );
		if (!empty($socials)) {
			$allowed = explode('|', $socials);
			$list = array();
			for ($i=0; $i<count($allowed); $i++) {
				$s = explode('=', $allowed[$i]);
				if (!empty($s[1])) {
					$list[] = array(
						'icon'	=> $type=='images' ? junotoys_get_socials_url($s[0]) : 'icon-'.trim($s[0]),
						'url'	=> $s[1]
						);
				}
			}
			if (count($list) > 0) junotoys_storage_set_array('sc_social_data', 'icons', $list);
		} else if (junotoys_param_is_off($custom))
			$content = do_shortcode($content);
		if (junotoys_storage_get_array('sc_social_data', 'icons')===false) junotoys_storage_set_array('sc_social_data', 'icons', junotoys_get_custom_option('social_icons'));
		$output = junotoys_prepare_socials(junotoys_storage_get_array('sc_social_data', 'icons'));
		$output = $output
			? '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_socials sc_socials_type_' . esc_attr($type) . ' sc_socials_shape_' . esc_attr($shape) . ' sc_socials_size_' . esc_attr($size) . (!empty($class) ? ' '.esc_attr($class) : '') . '"' 
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
				. (!junotoys_param_is_off($animation) ? ' data-animation="'.esc_attr(junotoys_get_animation_classes($animation)).'"' : '')
				. '>' 
				. ($output)
				. '</div>'
			: '';
		return apply_filters('junotoys_shortcode_output', $output, 'trx_socials', $atts, $content);
	}
	junotoys_require_shortcode('trx_socials', 'junotoys_sc_socials');
}


if (!function_exists('junotoys_sc_social_item')) {	
	function junotoys_sc_social_item($atts, $content=null){	
		if (junotoys_in_shortcode_blogger()) return '';
		extract(junotoys_html_decode(shortcode_atts(array(
			// Individual params
			"name" => "",
			"url" => "",
			"icon" => ""
		), $atts)));
		if (!empty($name) && empty($icon)) {
			$type = junotoys_storage_get_array('sc_social_data', 'type');
			if ($type=='images') {
				if (file_exists(junotoys_get_socials_dir($name.'.png')))
					$icon = junotoys_get_socials_url($name.'.png');
			} else
				$icon = 'icon-'.esc_attr($name);
		}
		if (!empty($icon) && !empty($url)) {
			if (junotoys_storage_get_array('sc_social_data', 'icons')===false) junotoys_storage_set_array('sc_social_data', 'icons', array());
			junotoys_storage_set_array2('sc_social_data', 'icons', '', array(
				'icon' => $icon,
				'url' => $url
				)
			);
		}
		return '';
	}
	junotoys_require_shortcode('trx_social_item', 'junotoys_sc_social_item');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'junotoys_sc_socials_reg_shortcodes' ) ) {
	//add_action('junotoys_action_shortcodes_list', 'junotoys_sc_socials_reg_shortcodes');
	function junotoys_sc_socials_reg_shortcodes() {
	
		junotoys_sc_map("trx_socials", array(
			"title" => esc_html__("Social icons", 'junotoys'),
			"desc" => wp_kses_data( __("List of social icons (with hovers)", 'junotoys') ),
			"decorate" => true,
			"container" => false,
			"params" => array(
				"type" => array(
					"title" => esc_html__("Icon's type", 'junotoys'),
					"desc" => wp_kses_data( __("Type of the icons - images or font icons", 'junotoys') ),
					"value" => junotoys_get_theme_setting('socials_type'),
					"options" => array(
						'icons' => esc_html__('Icons', 'junotoys'),
						'images' => esc_html__('Images', 'junotoys')
					),
					"type" => "checklist"
				), 
				"size" => array(
					"title" => esc_html__("Icon's size", 'junotoys'),
					"desc" => wp_kses_data( __("Size of the icons", 'junotoys') ),
					"value" => "small",
					"options" => junotoys_get_sc_param('sizes'),
					"type" => "checklist"
				), 
				"shape" => array(
					"title" => esc_html__("Icon's shape", 'junotoys'),
					"desc" => wp_kses_data( __("Shape of the icons", 'junotoys') ),
					"value" => "square",
					"options" => junotoys_get_sc_param('shapes'),
					"type" => "checklist"
				), 
				"socials" => array(
					"title" => esc_html__("Manual socials list", 'junotoys'),
					"desc" => wp_kses_data( __("Custom list of social networks. For example: twitter=http://twitter.com/my_profile|facebook=http://facebook.com/my_profile. If empty - use socials from Theme options.", 'junotoys') ),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
				"custom" => array(
					"title" => esc_html__("Custom socials", 'junotoys'),
					"desc" => wp_kses_data( __("Make custom icons from inner shortcodes (prepare it on tabs)", 'junotoys') ),
					"divider" => true,
					"value" => "no",
					"options" => junotoys_get_sc_param('yes_no'),
					"type" => "switch"
				),
				"top" => junotoys_get_sc_param('top'),
				"bottom" => junotoys_get_sc_param('bottom'),
				"left" => junotoys_get_sc_param('left'),
				"right" => junotoys_get_sc_param('right'),
				"id" => junotoys_get_sc_param('id'),
				"class" => junotoys_get_sc_param('class'),
				"animation" => junotoys_get_sc_param('animation'),
				"css" => junotoys_get_sc_param('css')
			),
			"children" => array(
				"name" => "trx_social_item",
				"title" => esc_html__("Custom social item", 'junotoys'),
				"desc" => wp_kses_data( __("Custom social item: name, profile url and icon url", 'junotoys') ),
				"decorate" => false,
				"container" => false,
				"params" => array(
					"name" => array(
						"title" => esc_html__("Social name", 'junotoys'),
						"desc" => wp_kses_data( __("Name (slug) of the social network (twitter, facebook, linkedin, etc.)", 'junotoys') ),
						"value" => "",
						"type" => "text"
					),
					"url" => array(
						"title" => esc_html__("Your profile URL", 'junotoys'),
						"desc" => wp_kses_data( __("URL of your profile in specified social network", 'junotoys') ),
						"value" => "",
						"type" => "text"
					),
					"icon" => array(
						"title" => esc_html__("URL (source) for icon file", 'junotoys'),
						"desc" => wp_kses_data( __("Select or upload image or write URL from other site for the current social icon", 'junotoys') ),
						"readonly" => false,
						"value" => "",
						"type" => "media"
					)
				)
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'junotoys_sc_socials_reg_shortcodes_vc' ) ) {
	//add_action('junotoys_action_shortcodes_list_vc', 'junotoys_sc_socials_reg_shortcodes_vc');
	function junotoys_sc_socials_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_socials",
			"name" => esc_html__("Social icons", 'junotoys'),
			"description" => wp_kses_data( __("Custom social icons", 'junotoys') ),
			"category" => esc_html__('Content', 'junotoys'),
			'icon' => 'icon_trx_socials',
			"class" => "trx_sc_collection trx_sc_socials",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => true,
			"as_parent" => array('only' => 'trx_social_item'),
			"params" => array_merge(array(
				array(
					"param_name" => "type",
					"heading" => esc_html__("Icon's type", 'junotoys'),
					"description" => wp_kses_data( __("Type of the icons - images or font icons", 'junotoys') ),
					"class" => "",
					"std" => junotoys_get_theme_setting('socials_type'),
					"value" => array(
						esc_html__('Icons', 'junotoys') => 'icons',
						esc_html__('Images', 'junotoys') => 'images'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "size",
					"heading" => esc_html__("Icon's size", 'junotoys'),
					"description" => wp_kses_data( __("Size of the icons", 'junotoys') ),
					"class" => "",
					"std" => "small",
					"value" => array_flip(junotoys_get_sc_param('sizes')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "shape",
					"heading" => esc_html__("Icon's shape", 'junotoys'),
					"description" => wp_kses_data( __("Shape of the icons", 'junotoys') ),
					"class" => "",
					"std" => "square",
					"value" => array_flip(junotoys_get_sc_param('shapes')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "socials",
					"heading" => esc_html__("Manual socials list", 'junotoys'),
					"description" => wp_kses_data( __("Custom list of social networks. For example: twitter=http://twitter.com/my_profile|facebook=http://facebook.com/my_profile. If empty - use socials from Theme options.", 'junotoys') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "custom",
					"heading" => esc_html__("Custom socials", 'junotoys'),
					"description" => wp_kses_data( __("Make custom icons from inner shortcodes (prepare it on tabs)", 'junotoys') ),
					"class" => "",
					"value" => array(esc_html__('Custom socials', 'junotoys') => 'yes'),
					"type" => "checkbox"
				),
				junotoys_get_vc_param('id'),
				junotoys_get_vc_param('class'),
				junotoys_get_vc_param('animation'),
				junotoys_get_vc_param('css'),
				junotoys_get_vc_param('margin_top'),
				junotoys_get_vc_param('margin_bottom'),
				junotoys_get_vc_param('margin_left'),
				junotoys_get_vc_param('margin_right')
			))
		) );
		
		
		vc_map( array(
			"base" => "trx_social_item",
			"name" => esc_html__("Custom social item", 'junotoys'),
			"description" => wp_kses_data( __("Custom social item: name, profile url and icon url", 'junotoys') ),
			"show_settings_on_create" => true,
			"content_element" => true,
			"is_container" => false,
			'icon' => 'icon_trx_social_item',
			"class" => "trx_sc_single trx_sc_social_item",
			"as_child" => array('only' => 'trx_socials'),
			"as_parent" => array('except' => 'trx_socials'),
			"params" => array(
				array(
					"param_name" => "name",
					"heading" => esc_html__("Social name", 'junotoys'),
					"description" => wp_kses_data( __("Name (slug) of the social network (twitter, facebook, linkedin, etc.)", 'junotoys') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "url",
					"heading" => esc_html__("Your profile URL", 'junotoys'),
					"description" => wp_kses_data( __("URL of your profile in specified social network", 'junotoys') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("URL (source) for icon file", 'junotoys'),
					"description" => wp_kses_data( __("Select or upload image or write URL from other site for the current social icon", 'junotoys') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				)
			)
		) );
		
		class WPBakeryShortCode_Trx_Socials extends JUNOTOYS_VC_ShortCodeCollection {}
		class WPBakeryShortCode_Trx_Social_Item extends JUNOTOYS_VC_ShortCodeSingle {}
	}
}
?>