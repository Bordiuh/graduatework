<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('junotoys_sc_button_theme_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_sc_button_theme_setup' );
	function junotoys_sc_button_theme_setup() {
		add_action('junotoys_action_shortcodes_list', 		'junotoys_sc_button_reg_shortcodes');
		if (function_exists('junotoys_exists_visual_composer') && junotoys_exists_visual_composer())
			add_action('junotoys_action_shortcodes_list_vc','junotoys_sc_button_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

if (!function_exists('junotoys_sc_button')) {	
	function junotoys_sc_button($atts, $content=null){	
		if (junotoys_in_shortcode_blogger()) return '';
		extract(junotoys_html_decode(shortcode_atts(array(
			// Individual params
			"type" => "square",
			"style" => "filled",
			"size" => "small",
			"icon" => "",
			"color" => "",
			"bg_color" => 1,
			"link" => "",
			"target" => "",
			"align" => "",
			"rel" => "",
			"popup" => "no",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => "",
			"width" => "",
			"height" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$css .= ($css ? ';' : '') . junotoys_get_css_position_from_values($top, $right, $bottom, $left);
		$css .= junotoys_get_css_dimensions_from_values($width, $height);
		if (junotoys_param_is_on($popup)) junotoys_enqueue_popup('magnific');
		$output = '<a href="' . (empty($link) ? '#' : $link) . '"'
			. (!empty($target) ? ' target="'.esc_attr($target).'"' : '')
			. (!empty($rel) ? ' rel="'.esc_attr($rel).'"' : '')
			. (!junotoys_param_is_off($animation) ? ' data-animation="'.esc_attr(junotoys_get_animation_classes($animation)).'"' : '')
			. ' class="sc_button sc_button_' . esc_attr($type) 
					. ' sc_button_style_' . esc_attr($style) 
					. ' sc_button_size_' . esc_attr($size)
					. ' sc_button_color_' . esc_attr($bg_color) . ' '. esc_attr($size)
					. ($align && $align!='none' ? ' align'.esc_attr($align) : '') 
					. (!empty($class) ? ' '.esc_attr($class) : '')
					. ($icon!='' ? '  sc_button_iconed '. esc_attr($icon) : '') 
					. (junotoys_param_is_on($popup) ? ' sc_popup_link' : '') 
					. '"'
			. ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
			. '>'
			. do_shortcode($content)
			. '</a>';
		return apply_filters('junotoys_shortcode_output', $output, 'trx_button', $atts, $content);
	}
	junotoys_require_shortcode('trx_button', 'junotoys_sc_button');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'junotoys_sc_button_reg_shortcodes' ) ) {
	//add_action('junotoys_action_shortcodes_list', 'junotoys_sc_button_reg_shortcodes');
	function junotoys_sc_button_reg_shortcodes() {
	
		junotoys_sc_map("trx_button", array(
			"title" => esc_html__("Button", 'junotoys'),
			"desc" => wp_kses_data( __("Button with link", 'junotoys') ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"_content_" => array(
					"title" => esc_html__("Caption", 'junotoys'),
					"desc" => wp_kses_data( __("Button caption", 'junotoys') ),
					"value" => "",
					"type" => "text"
				),
				"type" => array(
					"title" => esc_html__("Button's shape", 'junotoys'),
					"desc" => wp_kses_data( __("Select button's shape", 'junotoys') ),
					"value" => "square",
					"size" => "medium",
					"options" => array(
						'square' => esc_html__('Square', 'junotoys'),
						'round' => esc_html__('Round', 'junotoys')
					),
					"type" => "switch"
				), 
				"size" => array(
					"title" => esc_html__("Button's size", 'junotoys'),
					"desc" => wp_kses_data( __("Select button's size", 'junotoys') ),
					"value" => "small",
					"dir" => "horizontal",
					"options" => array(
						'small' => esc_html__('Small', 'junotoys'),
						'medium' => esc_html__('Medium', 'junotoys'),
						'large' => esc_html__('Large', 'junotoys')
					),
					"type" => "checklist"
				), 
				"icon" => array(
					"title" => esc_html__("Button's icon",  'junotoys'),
					"desc" => wp_kses_data( __('Select icon for the title from Fontello icons set',  'junotoys') ),
					"value" => "",
					"type" => "icons",
					"options" => junotoys_get_sc_param('icons')
				),
				"bg_color" => array(
					"title" => esc_html__("Button's backcolor", 'junotoys'),
					"desc" => wp_kses_data( __("Any color for button's background", 'junotoys') ),
					"std" => 1,
					"options" => array(
						1 => junotoys_get_file_url('images/buttons/large-1.png'),
						2 => junotoys_get_file_url('images/buttons/large-2.png'),
						3 => junotoys_get_file_url('images/buttons/large-3.png'),
						4 => junotoys_get_file_url('images/buttons/large-4.png'),
						5 => junotoys_get_file_url('images/buttons/large-5.png'),
						6 => junotoys_get_file_url('images/buttons/large-6.png')
					),
					"style" => "list",
					"type" => "images"
				),
				"align" => array(
					"title" => esc_html__("Button's alignment", 'junotoys'),
					"desc" => wp_kses_data( __("Align button to left, center or right", 'junotoys') ),
					"value" => "none",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => junotoys_get_sc_param('align')
				), 
				"link" => array(
					"title" => esc_html__("Link URL", 'junotoys'),
					"desc" => wp_kses_data( __("URL for link on button click", 'junotoys') ),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
				"target" => array(
					"title" => esc_html__("Link target", 'junotoys'),
					"desc" => wp_kses_data( __("Target for link on button click", 'junotoys') ),
					"dependency" => array(
						'link' => array('not_empty')
					),
					"value" => "",
					"type" => "text"
				),
				"popup" => array(
					"title" => esc_html__("Open link in popup", 'junotoys'),
					"desc" => wp_kses_data( __("Open link target in popup window", 'junotoys') ),
					"dependency" => array(
						'link' => array('not_empty')
					),
					"value" => "no",
					"type" => "switch",
					"options" => junotoys_get_sc_param('yes_no')
				), 
				"rel" => array(
					"title" => esc_html__("Rel attribute", 'junotoys'),
					"desc" => wp_kses_data( __("Rel attribute for button's link (if need)", 'junotoys') ),
					"dependency" => array(
						'link' => array('not_empty')
					),
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
if ( !function_exists( 'junotoys_sc_button_reg_shortcodes_vc' ) ) {
	//add_action('junotoys_action_shortcodes_list_vc', 'junotoys_sc_button_reg_shortcodes_vc');
	function junotoys_sc_button_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_button",
			"name" => esc_html__("Button", 'junotoys'),
			"description" => wp_kses_data( __("Button with link", 'junotoys') ),
			"category" => esc_html__('Content', 'junotoys'),
			'icon' => 'icon_trx_button',
			"class" => "trx_sc_single trx_sc_button",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "content",
					"heading" => esc_html__("Caption", 'junotoys'),
					"description" => wp_kses_data( __("Button caption", 'junotoys') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "type",
					"heading" => esc_html__("Button's shape", 'junotoys'),
					"description" => wp_kses_data( __("Select button's shape", 'junotoys') ),
					"class" => "",
					"value" => array(
						esc_html__('Square', 'junotoys') => 'square',
						esc_html__('Round', 'junotoys') => 'round'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "size",
					"heading" => esc_html__("Button's size", 'junotoys'),
					"description" => wp_kses_data( __("Select button's size", 'junotoys') ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
						esc_html__('Small', 'junotoys') => 'small',
						esc_html__('Medium', 'junotoys') => 'medium',
						esc_html__('Large', 'junotoys') => 'large'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Button's icon", 'junotoys'),
					"description" => wp_kses_data( __("Select icon for the title from Fontello icons set", 'junotoys') ),
					"class" => "",
					"value" => junotoys_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "bg_color",
					"heading" => esc_html__("Button's backcolor", 'junotoys'),
					"description" => wp_kses_data( __("Any color for button's background", 'junotoys') ),
					"class" => "",
					"value" => array(
						'<span style="background-image:url('.esc_url(junotoys_get_file_url('images/buttons/large-1.png')).')" class="junotoys_options_button_image" alt="Yellow"></span>' => 1,
						'<span style="background-image:url('.esc_url(junotoys_get_file_url('images/buttons/large-2.png')).')" class="junotoys_options_button_image" alt="Red"></span>' => 2,
						'<span style="background-image:url('.esc_url(junotoys_get_file_url('images/buttons/large-3.png')).')" class="junotoys_options_button_image" alt="Orange"></span>' => 3,
						'<span style="background-image:url('.esc_url(junotoys_get_file_url('images/buttons/large-4.png')).')" class="junotoys_options_button_image" alt="Blue"></span>' => 4,
						'<span style="background-image:url('.esc_url(junotoys_get_file_url('images/buttons/large-5.png')).')" class="junotoys_options_button_image" alt="White"></span>' => 5,
						'<span style="background-image:url('.esc_url(junotoys_get_file_url('images/buttons/large-6.png')).')" class="junotoys_options_button_image" alt="Green"></span>' => 6,
					),
					"type" => "checkbox"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Button's alignment", 'junotoys'),
					"description" => wp_kses_data( __("Align button to left, center or right", 'junotoys') ),
					"class" => "",
					"value" => array_flip(junotoys_get_sc_param('align')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "link",
					"heading" => esc_html__("Link URL", 'junotoys'),
					"description" => wp_kses_data( __("URL for the link on button click", 'junotoys') ),
					"class" => "",
					"group" => esc_html__('Link', 'junotoys'),
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "target",
					"heading" => esc_html__("Link target", 'junotoys'),
					"description" => wp_kses_data( __("Target for the link on button click", 'junotoys') ),
					"class" => "",
					"group" => esc_html__('Link', 'junotoys'),
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "popup",
					"heading" => esc_html__("Open link in popup", 'junotoys'),
					"description" => wp_kses_data( __("Open link target in popup window", 'junotoys') ),
					"class" => "",
					"group" => esc_html__('Link', 'junotoys'),
					"value" => array(esc_html__('Open in popup', 'junotoys') => 'yes'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "rel",
					"heading" => esc_html__("Rel attribute", 'junotoys'),
					"description" => wp_kses_data( __("Rel attribute for the button's link (if need", 'junotoys') ),
					"class" => "",
					"group" => esc_html__('Link', 'junotoys'),
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
			),
			'js_view' => 'VcTrxTextView'
		) );
		
		class WPBakeryShortCode_Trx_Button extends JUNOTOYS_VC_ShortCodeSingle {}
	}
}
?>