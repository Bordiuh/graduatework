<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('junotoys_sc_br_theme_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_sc_br_theme_setup' );
	function junotoys_sc_br_theme_setup() {
		add_action('junotoys_action_shortcodes_list', 		'junotoys_sc_br_reg_shortcodes');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */


if (!function_exists('junotoys_sc_br')) {	
	function junotoys_sc_br($atts, $content = null) {
		if (junotoys_in_shortcode_blogger()) return '';
		extract(junotoys_html_decode(shortcode_atts(array(
			"clear" => ""
		), $atts)));
		$output = in_array($clear, array('left', 'right', 'both', 'all')) 
			? '<div class="clearfix" style="clear:' . str_replace('all', 'both', $clear) . '"></div>'
			: '<br />';
		return apply_filters('junotoys_shortcode_output', $output, 'trx_br', $atts, $content);
	}
	junotoys_require_shortcode("trx_br", "junotoys_sc_br");
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'junotoys_sc_br_reg_shortcodes' ) ) {
	//add_action('junotoys_action_shortcodes_list', 'junotoys_sc_br_reg_shortcodes');
	function junotoys_sc_br_reg_shortcodes() {
	
		junotoys_sc_map("trx_br", array(
			"title" => esc_html__("Break", 'junotoys'),
			"desc" => wp_kses_data( __("Line break with clear floating (if need)", 'junotoys') ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"clear" => 	array(
					"title" => esc_html__("Clear floating", 'junotoys'),
					"desc" => wp_kses_data( __("Clear floating (if need)", 'junotoys') ),
					"value" => "",
					"type" => "checklist",
					"options" => array(
						'none' => esc_html__('None', 'junotoys'),
						'left' => esc_html__('Left', 'junotoys'),
						'right' => esc_html__('Right', 'junotoys'),
						'both' => esc_html__('Both', 'junotoys')
					)
				)
			)
		));
	}
}
?>