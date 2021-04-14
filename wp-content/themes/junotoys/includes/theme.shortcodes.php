<?php
if (!function_exists('junotoys_theme_shortcodes_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_theme_shortcodes_setup', 1 );
	function junotoys_theme_shortcodes_setup() {
		add_filter('junotoys_filter_googlemap_styles', 'junotoys_theme_shortcodes_googlemap_styles');
	}
}


// Add theme-specific Google map styles
if ( !function_exists( 'junotoys_theme_shortcodes_googlemap_styles' ) ) {
	function junotoys_theme_shortcodes_googlemap_styles($list) {
		$list['simple']		= esc_html__('Simple', 'junotoys');
		$list['greyscale']	= esc_html__('Greyscale', 'junotoys');
		$list['inverse']	= esc_html__('Inverse', 'junotoys');
		$list['apple']		= esc_html__('Apple', 'junotoys');
		return $list;
	}
}
?>