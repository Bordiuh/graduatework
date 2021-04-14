<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('junotoys_sc_dropcaps_theme_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_sc_dropcaps_theme_setup' );
	function junotoys_sc_dropcaps_theme_setup() {
		add_action('junotoys_action_shortcodes_list', 		'junotoys_sc_dropcaps_reg_shortcodes');
		if (function_exists('junotoys_exists_visual_composer') && junotoys_exists_visual_composer())
			add_action('junotoys_action_shortcodes_list_vc','junotoys_sc_dropcaps_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

//[trx_dropcaps id="unique_id" style="1-6"]paragraph text[/trx_dropcaps]

if (!function_exists('junotoys_sc_dropcaps')) {	
	function junotoys_sc_dropcaps($atts, $content=null){
		if (junotoys_in_shortcode_blogger()) return '';
		extract(junotoys_html_decode(shortcode_atts(array(
			// Individual params
			"style" => "1",
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
		$style = min(4, max(1, $style));
		$content = do_shortcode(str_replace(array('[vc_column_text]', '[/vc_column_text]'), array('', ''), $content));
		$output = junotoys_substr($content, 0, 1) == '<' 
			? $content 
			: '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_dropcaps sc_dropcaps_style_' . esc_attr($style) . (!empty($class) ? ' '.esc_attr($class) : '') . '"'
				. ($css ? ' style="'.esc_attr($css).'"' : '')
				. (!junotoys_param_is_off($animation) ? ' data-animation="'.esc_attr(junotoys_get_animation_classes($animation)).'"' : '')
				. '>' 
					. '<span class="sc_dropcaps_item">' . trim(junotoys_substr($content, 0, 1)) . '</span>' . trim(junotoys_substr($content, 1))
			. '</div>';
		return apply_filters('junotoys_shortcode_output', $output, 'trx_dropcaps', $atts, $content);
	}
	junotoys_require_shortcode('trx_dropcaps', 'junotoys_sc_dropcaps');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'junotoys_sc_dropcaps_reg_shortcodes' ) ) {
	//add_action('junotoys_action_shortcodes_list', 'junotoys_sc_dropcaps_reg_shortcodes');
	function junotoys_sc_dropcaps_reg_shortcodes() {
	
		junotoys_sc_map("trx_dropcaps", array(
			"title" => esc_html__("Dropcaps", 'junotoys'),
			"desc" => wp_kses_data( __("Make first letter as dropcaps", 'junotoys') ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"style" => array(
					"title" => esc_html__("Style", 'junotoys'),
					"desc" => wp_kses_data( __("Dropcaps style", 'junotoys') ),
					"value" => "1",
					"type" => "checklist",
					"options" => junotoys_get_list_styles(1, 2)
				),
				"_content_" => array(
					"title" => esc_html__("Paragraph content", 'junotoys'),
					"desc" => wp_kses_data( __("Paragraph with dropcaps content", 'junotoys') ),
					"divider" => true,
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
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
if ( !function_exists( 'junotoys_sc_dropcaps_reg_shortcodes_vc' ) ) {
	//add_action('junotoys_action_shortcodes_list_vc', 'junotoys_sc_dropcaps_reg_shortcodes_vc');
	function junotoys_sc_dropcaps_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_dropcaps",
			"name" => esc_html__("Dropcaps", 'junotoys'),
			"description" => wp_kses_data( __("Make first letter of the text as dropcaps", 'junotoys') ),
			"category" => esc_html__('Content', 'junotoys'),
			'icon' => 'icon_trx_dropcaps',
			"class" => "trx_sc_container trx_sc_dropcaps",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "style",
					"heading" => esc_html__("Style", 'junotoys'),
					"description" => wp_kses_data( __("Dropcaps style", 'junotoys') ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(junotoys_get_list_styles(1, 2)),
					"type" => "dropdown"
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
		
		class WPBakeryShortCode_Trx_Dropcaps extends JUNOTOYS_VC_ShortCodeContainer {}
	}
}
?>