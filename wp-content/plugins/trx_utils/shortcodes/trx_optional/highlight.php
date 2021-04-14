<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('junotoys_sc_highlight_theme_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_sc_highlight_theme_setup' );
	function junotoys_sc_highlight_theme_setup() {
		add_action('junotoys_action_shortcodes_list', 		'junotoys_sc_highlight_reg_shortcodes');
		if (function_exists('junotoys_exists_visual_composer') && junotoys_exists_visual_composer())
			add_action('junotoys_action_shortcodes_list_vc','junotoys_sc_highlight_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

if (!function_exists('junotoys_sc_highlight')) {	
	function junotoys_sc_highlight($atts, $content=null){	
		if (junotoys_in_shortcode_blogger()) return '';
		extract(junotoys_html_decode(shortcode_atts(array(
			// Individual params
			"color" => "",
			"bg_color" => "",
			"font_size" => "",
			"type" => "1",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts)));
		$css .= ($color != '' ? 'color:' . esc_attr($color) . ';' : '')
			.($bg_color != '' ? 'background-color:' . esc_attr($bg_color) . ';' : '')
			.($font_size != '' ? 'font-size:' . esc_attr(junotoys_prepare_css_value($font_size)) . '; line-height: 1em;' : '');
		$output = '<span' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_highlight'.($type>0 ? ' sc_highlight_style_'.esc_attr($type) : ''). (!empty($class) ? ' '.esc_attr($class) : '').'"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. '>' 
				. do_shortcode($content) 
				. '</span>';
		return apply_filters('junotoys_shortcode_output', $output, 'trx_highlight', $atts, $content);
	}
	junotoys_require_shortcode('trx_highlight', 'junotoys_sc_highlight');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'junotoys_sc_highlight_reg_shortcodes' ) ) {
	//add_action('junotoys_action_shortcodes_list', 'junotoys_sc_highlight_reg_shortcodes');
	function junotoys_sc_highlight_reg_shortcodes() {
	
		junotoys_sc_map("trx_highlight", array(
			"title" => esc_html__("Highlight text", 'junotoys'),
			"desc" => wp_kses_data( __("Highlight text with selected color, background color and other styles", 'junotoys') ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"type" => array(
					"title" => esc_html__("Type", 'junotoys'),
					"desc" => wp_kses_data( __("Highlight type", 'junotoys') ),
					"value" => "1",
					"type" => "checklist",
					"options" => array(
						0 => esc_html__('Custom', 'junotoys'),
						1 => esc_html__('Type 1', 'junotoys'),
						2 => esc_html__('Type 2', 'junotoys'),
						3 => esc_html__('Type 3', 'junotoys')
					)
				),
				"color" => array(
					"title" => esc_html__("Color", 'junotoys'),
					"desc" => wp_kses_data( __("Color for the highlighted text", 'junotoys') ),
					"divider" => true,
					"value" => "",
					"type" => "color"
				),
				"bg_color" => array(
					"title" => esc_html__("Background color", 'junotoys'),
					"desc" => wp_kses_data( __("Background color for the highlighted text", 'junotoys') ),
					"value" => "",
					"type" => "color"
				),
				"font_size" => array(
					"title" => esc_html__("Font size", 'junotoys'),
					"desc" => wp_kses_data( __("Font size of the highlighted text (default - in pixels, allows any CSS units of measure)", 'junotoys') ),
					"value" => "",
					"type" => "text"
				),
				"_content_" => array(
					"title" => esc_html__("Highlighting content", 'junotoys'),
					"desc" => wp_kses_data( __("Content for highlight", 'junotoys') ),
					"divider" => true,
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
				),
				"id" => junotoys_get_sc_param('id'),
				"class" => junotoys_get_sc_param('class'),
				"css" => junotoys_get_sc_param('css')
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'junotoys_sc_highlight_reg_shortcodes_vc' ) ) {
	//add_action('junotoys_action_shortcodes_list_vc', 'junotoys_sc_highlight_reg_shortcodes_vc');
	function junotoys_sc_highlight_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_highlight",
			"name" => esc_html__("Highlight text", 'junotoys'),
			"description" => wp_kses_data( __("Highlight text with selected color, background color and other styles", 'junotoys') ),
			"category" => esc_html__('Content', 'junotoys'),
			'icon' => 'icon_trx_highlight',
			"class" => "trx_sc_single trx_sc_highlight",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "type",
					"heading" => esc_html__("Type", 'junotoys'),
					"description" => wp_kses_data( __("Highlight type", 'junotoys') ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
							esc_html__('Custom', 'junotoys') => 0,
							esc_html__('Type 1', 'junotoys') => 1,
							esc_html__('Type 2', 'junotoys') => 2,
							esc_html__('Type 3', 'junotoys') => 3
						),
					"type" => "dropdown"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Text color", 'junotoys'),
					"description" => wp_kses_data( __("Color for the highlighted text", 'junotoys') ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_color",
					"heading" => esc_html__("Background color", 'junotoys'),
					"description" => wp_kses_data( __("Background color for the highlighted text", 'junotoys') ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "font_size",
					"heading" => esc_html__("Font size", 'junotoys'),
					"description" => wp_kses_data( __("Font size for the highlighted text (default - in pixels, allows any CSS units of measure)", 'junotoys') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "content",
					"heading" => esc_html__("Highlight text", 'junotoys'),
					"description" => wp_kses_data( __("Content for highlight", 'junotoys') ),
					"class" => "",
					"value" => "",
					"type" => "textarea_html"
				),
				junotoys_get_vc_param('id'),
				junotoys_get_vc_param('class'),
				junotoys_get_vc_param('css')
			),
			'js_view' => 'VcTrxTextView'
		) );
		
		class WPBakeryShortCode_Trx_Highlight extends JUNOTOYS_VC_ShortCodeSingle {}
	}
}
?>