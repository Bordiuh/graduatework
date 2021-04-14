<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('junotoys_sc_sidebar_theme_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_sc_sidebar_theme_setup' );
	function junotoys_sc_sidebar_theme_setup() {
		add_action('junotoys_action_shortcodes_list', 		'junotoys_sc_sidebar_reg_shortcodes');
		if (function_exists('junotoys_exists_visual_composer') && junotoys_exists_visual_composer())
			add_action('junotoys_action_shortcodes_list_vc','junotoys_sc_sidebar_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

if (!function_exists('junotoys_sc_sidebar')) {	
	function junotoys_sc_sidebar($atts, $content = null) {
		if (junotoys_in_shortcode_blogger()) return '';
		extract(junotoys_html_decode(shortcode_atts(array(
			// Individual params
			"name" => ""
		), $atts)));
		
		$sidebar_name = $name;
		$sidebar = '';
		$sidebar_scheme = junotoys_get_custom_option('sidebar_main_scheme');
		if (is_active_sidebar($sidebar_name)) { 
			
			$sidebar = '<div class="sidebar sc_sidebar widget_area scheme_'.esc_attr($sidebar_scheme).'">
						<div class="sidebar_inner widget_area_inner">';								
								ob_start();
								do_action( 'before_sidebar' );
								if ( !dynamic_sidebar($sidebar_name) ) {
									// Put here html if user no set widgets in sidebar
								}
								do_action( 'after_sidebar' );
								$out = ob_get_contents();
								ob_end_clean();
								$sidebar .= trim(chop(preg_replace("/<\/aside>[\r\n\s]*<aside/", "</aside><aside", $out)))
								.'</div>
							</div>
						</div>
					</div>';
			
		}
		return apply_filters('junotoys_shortcode_output', balanceTags($sidebar, true), 'trx_sidebar', $atts, $content);
	}
	junotoys_require_shortcode("trx_sidebar", "junotoys_sc_sidebar");
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'junotoys_sc_sidebar_reg_shortcodes' ) ) {
	//add_action('junotoys_action_shortcodes_list', 'junotoys_sc_sidebar_reg_shortcodes');
	function junotoys_sc_sidebar_reg_shortcodes() {
	
		junotoys_sc_map("trx_sidebar", array(
			"title" => esc_html__("Sidebar", 'junotoys'),
			"desc" => wp_kses_data( __("Insert sidebar", 'junotoys') ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"name" => array(
					"title" => esc_html__("Name", 'junotoys'),
					"desc" => wp_kses_data( __("Sidebar name or id", 'junotoys') ),
					"divider" => true,
					"value" => "",
					"type" => "text"
				)
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'junotoys_sc_sidebar_reg_shortcodes_vc' ) ) {
	//add_action('junotoys_action_shortcodes_list_vc', 'junotoys_sc_sidebar_reg_shortcodes_vc');
	function junotoys_sc_sidebar_reg_shortcodes_vc() {
	
		$sidebars = junotoys_get_list_sidebars();
		
		vc_map( array(
			"base" => "trx_sidebar",
			"name" => esc_html__("Sidebar", 'junotoys'),
			"description" => wp_kses_data( __("Insert sidebar", 'junotoys') ),
			"category" => esc_html__('Content', 'junotoys'),
			'icon' => 'icon_trx_sidebar',
			"class" => "trx_sc_single trx_sc_sidebar",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "name",
					"heading" => esc_html__("Name", 'junotoys'),
					"description" => wp_kses_data( __("Sidebar name or id", 'junotoys') ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip($sidebars),
					"type" => "dropdown"
				)
			)
		) );
		
		class WPBakeryShortCode_Trx_Sidebar extends JUNOTOYS_VC_ShortCodeSingle {}
	}
}
?>