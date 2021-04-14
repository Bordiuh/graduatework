<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('junotoys_sc_icon_theme_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_sc_icon_theme_setup' );
	function junotoys_sc_icon_theme_setup() {
		add_action('junotoys_action_shortcodes_list', 		'junotoys_sc_icon_reg_shortcodes');
		if (function_exists('junotoys_exists_visual_composer') && junotoys_exists_visual_composer())
			add_action('junotoys_action_shortcodes_list_vc','junotoys_sc_icon_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

if (!function_exists('junotoys_sc_icon')) {	
	function junotoys_sc_icon($atts, $content=null){	
		if (junotoys_in_shortcode_blogger()) return '';
		extract(junotoys_html_decode(shortcode_atts(array(
			// Individual params
			"icon" => "",
			"color" => "",
			"bg_color" => "",
			"bg_shape" => "",
			"font_size" => "",
			"font_weight" => "",
			"align" => "",
			"link" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$css .= ($css ? ';' : '') . junotoys_get_css_position_from_values($top, $right, $bottom, $left);
		$css2 = ($font_weight != '' && !junotoys_is_inherit_option($font_weight) ? 'font-weight:'. esc_attr($font_weight).';' : '')
			. ($font_size != '' ? 'font-size:' . esc_attr(junotoys_prepare_css_value($font_size)) . '; line-height: ' . (!$bg_shape || junotoys_param_is_inherit($bg_shape) ? '1' : '1.2') . 'em;' : '')
			. ($color != '' ? 'color:'.esc_attr($color).';' : '')
			. ($bg_color != '' ? 'background-color:'.esc_attr($bg_color).';border-color:'.esc_attr($bg_color).';' : '')
		;
		$output = $icon!='' 
			? ($link ? '<a href="'.esc_url($link).'"' : '<span') . ($id ? ' id="'.esc_attr($id).'"' : '')
				. ' class="sc_icon '.esc_attr($icon)
					. ($bg_shape && !junotoys_param_is_inherit($bg_shape) ? ' sc_icon_shape_'.esc_attr($bg_shape) : '')
					. ($align && $align!='none' ? ' align'.esc_attr($align) : '') 
					. (!empty($class) ? ' '.esc_attr($class) : '')
				.'"'
				.($css || $css2 ? ' style="'.($class ? 'display:block;' : '') . ($css) . ($css2) . '"' : '')
				.'>'
				.($link ? '</a>' : '</span>')
			: '';
		return apply_filters('junotoys_shortcode_output', $output, 'trx_icon', $atts, $content);
	}
	junotoys_require_shortcode('trx_icon', 'junotoys_sc_icon');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'junotoys_sc_icon_reg_shortcodes' ) ) {
	//add_action('junotoys_action_shortcodes_list', 'junotoys_sc_icon_reg_shortcodes');
	function junotoys_sc_icon_reg_shortcodes() {
	
		junotoys_sc_map("trx_icon", array(
			"title" => esc_html__("Icon", 'junotoys'),
			"desc" => wp_kses_data( __("Insert icon", 'junotoys') ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"icon" => array(
					"title" => esc_html__('Icon',  'junotoys'),
					"desc" => wp_kses_data( __('Select font icon from the Fontello icons set',  'junotoys') ),
					"value" => "",
					"type" => "icons",
					"options" => junotoys_get_sc_param('icons')
				),
				"color" => array(
					"title" => esc_html__("Icon's color", 'junotoys'),
					"desc" => wp_kses_data( __("Icon's color", 'junotoys') ),
					"dependency" => array(
						'icon' => array('not_empty')
					),
					"value" => "",
					"type" => "color"
				),
				"bg_shape" => array(
					"title" => esc_html__("Background shape", 'junotoys'),
					"desc" => wp_kses_data( __("Shape of the icon background", 'junotoys') ),
					"dependency" => array(
						'icon' => array('not_empty')
					),
					"value" => "none",
					"type" => "radio",
					"options" => array(
						'none' => esc_html__('None', 'junotoys'),
						'round' => esc_html__('Round', 'junotoys'),
						'square' => esc_html__('Square', 'junotoys')
					)
				),
				"bg_color" => array(
					"title" => esc_html__("Icon's background color", 'junotoys'),
					"desc" => wp_kses_data( __("Icon's background color", 'junotoys') ),
					"dependency" => array(
						'icon' => array('not_empty'),
						'background' => array('round','square')
					),
					"value" => "",
					"type" => "color"
				),
				"font_size" => array(
					"title" => esc_html__("Font size", 'junotoys'),
					"desc" => wp_kses_data( __("Icon's font size", 'junotoys') ),
					"dependency" => array(
						'icon' => array('not_empty')
					),
					"value" => "",
					"type" => "spinner",
					"min" => 8,
					"max" => 240
				),
				"font_weight" => array(
					"title" => esc_html__("Font weight", 'junotoys'),
					"desc" => wp_kses_data( __("Icon font weight", 'junotoys') ),
					"dependency" => array(
						'icon' => array('not_empty')
					),
					"value" => "",
					"type" => "select",
					"size" => "medium",
					"options" => array(
						'100' => esc_html__('Thin (100)', 'junotoys'),
						'300' => esc_html__('Light (300)', 'junotoys'),
						'400' => esc_html__('Normal (400)', 'junotoys'),
						'700' => esc_html__('Bold (700)', 'junotoys')
					)
				),
				"align" => array(
					"title" => esc_html__("Alignment", 'junotoys'),
					"desc" => wp_kses_data( __("Icon text alignment", 'junotoys') ),
					"dependency" => array(
						'icon' => array('not_empty')
					),
					"value" => "",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => junotoys_get_sc_param('align')
				), 
				"link" => array(
					"title" => esc_html__("Link URL", 'junotoys'),
					"desc" => wp_kses_data( __("Link URL from this icon (if not empty)", 'junotoys') ),
					"value" => "",
					"type" => "text"
				),
				"top" => junotoys_get_sc_param('top'),
				"bottom" => junotoys_get_sc_param('bottom'),
				"left" => junotoys_get_sc_param('left'),
				"right" => junotoys_get_sc_param('right'),
				"id" => junotoys_get_sc_param('id'),
				"class" => junotoys_get_sc_param('class'),
				"css" => junotoys_get_sc_param('css')
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'junotoys_sc_icon_reg_shortcodes_vc' ) ) {
	//add_action('junotoys_action_shortcodes_list_vc', 'junotoys_sc_icon_reg_shortcodes_vc');
	function junotoys_sc_icon_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_icon",
			"name" => esc_html__("Icon", 'junotoys'),
			"description" => wp_kses_data( __("Insert the icon", 'junotoys') ),
			"category" => esc_html__('Content', 'junotoys'),
			'icon' => 'icon_trx_icon',
			"class" => "trx_sc_single trx_sc_icon",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Icon", 'junotoys'),
					"description" => wp_kses_data( __("Select icon class from Fontello icons set", 'junotoys') ),
					"admin_label" => true,
					"class" => "",
					"value" => junotoys_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Text color", 'junotoys'),
					"description" => wp_kses_data( __("Icon's color", 'junotoys') ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_color",
					"heading" => esc_html__("Background color", 'junotoys'),
					"description" => wp_kses_data( __("Background color for the icon", 'junotoys') ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_shape",
					"heading" => esc_html__("Background shape", 'junotoys'),
					"description" => wp_kses_data( __("Shape of the icon background", 'junotoys') ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
						esc_html__('None', 'junotoys') => 'none',
						esc_html__('Round', 'junotoys') => 'round',
						esc_html__('Square', 'junotoys') => 'square'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "font_size",
					"heading" => esc_html__("Font size", 'junotoys'),
					"description" => wp_kses_data( __("Icon's font size", 'junotoys') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "font_weight",
					"heading" => esc_html__("Font weight", 'junotoys'),
					"description" => wp_kses_data( __("Icon's font weight", 'junotoys') ),
					"class" => "",
					"value" => array(
						esc_html__('Default', 'junotoys') => 'inherit',
						esc_html__('Thin (100)', 'junotoys') => '100',
						esc_html__('Light (300)', 'junotoys') => '300',
						esc_html__('Normal (400)', 'junotoys') => '400',
						esc_html__('Bold (700)', 'junotoys') => '700'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Icon's alignment", 'junotoys'),
					"description" => wp_kses_data( __("Align icon to left, center or right", 'junotoys') ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(junotoys_get_sc_param('align')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "link",
					"heading" => esc_html__("Link URL", 'junotoys'),
					"description" => wp_kses_data( __("Link URL from this icon (if not empty)", 'junotoys') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				junotoys_get_vc_param('id'),
				junotoys_get_vc_param('class'),
				junotoys_get_vc_param('css'),
				junotoys_get_vc_param('margin_top'),
				junotoys_get_vc_param('margin_bottom'),
				junotoys_get_vc_param('margin_left'),
				junotoys_get_vc_param('margin_right')
			),
		) );
		
		class WPBakeryShortCode_Trx_Icon extends JUNOTOYS_VC_ShortCodeSingle {}
	}
}
?>