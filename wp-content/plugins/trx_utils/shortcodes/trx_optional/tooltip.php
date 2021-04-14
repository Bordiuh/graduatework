<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('junotoys_sc_tooltip_theme_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_sc_tooltip_theme_setup' );
	function junotoys_sc_tooltip_theme_setup() {
		add_action('junotoys_action_shortcodes_list', 		'junotoys_sc_tooltip_reg_shortcodes');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

if (!function_exists('junotoys_sc_tooltip')) {	
	function junotoys_sc_tooltip($atts, $content=null){	
		if (junotoys_in_shortcode_blogger()) return '';
		extract(junotoys_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts)));
		$output = '<span' . ($id ? ' id="'.esc_attr($id).'"' : '') 
					. ' class="sc_tooltip_parent'. (!empty($class) ? ' '.esc_attr($class) : '').'"'
					. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
					. '>'
						. do_shortcode($content)
						. '<span class="sc_tooltip">' . ($title) . '</span>'
					. '</span>';
		return apply_filters('junotoys_shortcode_output', $output, 'trx_tooltip', $atts, $content);
	}
	junotoys_require_shortcode('trx_tooltip', 'junotoys_sc_tooltip');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'junotoys_sc_tooltip_reg_shortcodes' ) ) {
	//add_action('junotoys_action_shortcodes_list', 'junotoys_sc_tooltip_reg_shortcodes');
	function junotoys_sc_tooltip_reg_shortcodes() {
	
		junotoys_sc_map("trx_tooltip", array(
			"title" => esc_html__("Tooltip", 'junotoys'),
			"desc" => wp_kses_data( __("Create tooltip for selected text", 'junotoys') ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"title" => array(
					"title" => esc_html__("Title", 'junotoys'),
					"desc" => wp_kses_data( __("Tooltip title (required)", 'junotoys') ),
					"value" => "",
					"type" => "text"
				),
				"_content_" => array(
					"title" => esc_html__("Tipped content", 'junotoys'),
					"desc" => wp_kses_data( __("Highlighted content with tooltip", 'junotoys') ),
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
?>