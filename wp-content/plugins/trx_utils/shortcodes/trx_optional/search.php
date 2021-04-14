<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('junotoys_sc_search_theme_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_sc_search_theme_setup' );
	function junotoys_sc_search_theme_setup() {
		add_action('junotoys_action_shortcodes_list', 		'junotoys_sc_search_reg_shortcodes');
		if (function_exists('junotoys_exists_visual_composer') && junotoys_exists_visual_composer())
			add_action('junotoys_action_shortcodes_list_vc','junotoys_sc_search_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

if (!function_exists('junotoys_sc_search')) {	
	function junotoys_sc_search($atts, $content=null){	
		if (junotoys_in_shortcode_blogger()) return '';
		extract(junotoys_html_decode(shortcode_atts(array(
			// Individual params
			"style" => "",
			"state" => "",
			"ajax" => "",
			"title" => esc_html__('Search', 'junotoys'),
			"scheme" => "original",
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
		if ($style == 'fullscreen') {
			if (empty($ajax)) $ajax = "no";
			if (empty($state)) $state = "closed";
		} else if ($style == 'expand') {
			if (empty($ajax)) $ajax = junotoys_get_theme_option('use_ajax_search');
			if (empty($state)) $state = "closed";
		} else if ($style == 'slide') {
			if (empty($ajax)) $ajax = junotoys_get_theme_option('use_ajax_search');
			if (empty($state)) $state = "closed";
		} else {
			if (empty($ajax)) $ajax = junotoys_get_theme_option('use_ajax_search');
			if (empty($state)) $state = "fixed";
		}
		// Load core messages
		junotoys_enqueue_messages();
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') . ' class="search_wrap search_style_'.esc_attr($style).' search_state_'.esc_attr($state)
						. (junotoys_param_is_on($ajax) ? ' search_ajax' : '')
						. ($class ? ' '.esc_attr($class) : '')
						. '"'
					. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
					. (!junotoys_param_is_off($animation) ? ' data-animation="'.esc_attr(junotoys_get_animation_classes($animation)).'"' : '')
					. '>
						<div class="search_form_wrap">
							<form role="search" method="get" class="search_form" action="' . esc_url(home_url('/')) . '">
								<button type="submit" class="search_submit icon-search" title="' . ($state=='closed' ? esc_attr__('Open search', 'junotoys') : esc_attr__('Start search', 'junotoys')) . '"></button>
								<input type="text" class="search_field" placeholder="' . esc_attr($title) . '" value="' . esc_attr(get_search_query()) . '" name="s" />'
								. ($style == 'fullscreen' ? '<a class="search_close icon-cancel"></a>' : '')
							. '</form>
						</div>'
						. (junotoys_param_is_on($ajax) ? '<div class="search_results widget_area' . ($scheme && !junotoys_param_is_off($scheme) && !junotoys_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '') . '"><a class="search_results_close icon-cancel"></a><div class="search_results_content"></div></div>' : '')
					. '</div>';
		return apply_filters('junotoys_shortcode_output', $output, 'trx_search', $atts, $content);
	}
	junotoys_require_shortcode('trx_search', 'junotoys_sc_search');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'junotoys_sc_search_reg_shortcodes' ) ) {
	//add_action('junotoys_action_shortcodes_list', 'junotoys_sc_search_reg_shortcodes');
	function junotoys_sc_search_reg_shortcodes() {
	
		junotoys_sc_map("trx_search", array(
			"title" => esc_html__("Search", 'junotoys'),
			"desc" => wp_kses_data( __("Show search form", 'junotoys') ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"style" => array(
					"title" => esc_html__("Style", 'junotoys'),
					"desc" => wp_kses_data( __("Select style to display search field", 'junotoys') ),
					"value" => "regular",
					"options" => junotoys_get_list_search_styles(),
					"type" => "checklist"
				),
				"state" => array(
					"title" => esc_html__("State", 'junotoys'),
					"desc" => wp_kses_data( __("Select search field initial state", 'junotoys') ),
					"value" => "fixed",
					"options" => array(
						"fixed"  => esc_html__('Fixed',  'junotoys'),
						"opened" => esc_html__('Opened', 'junotoys'),
						"closed" => esc_html__('Closed', 'junotoys')
					),
					"type" => "checklist"
				),
				"title" => array(
					"title" => esc_html__("Title", 'junotoys'),
					"desc" => wp_kses_data( __("Title (placeholder) for the search field", 'junotoys') ),
					"value" => esc_html__("Search &hellip;", 'junotoys'),
					"type" => "text"
				),
				"ajax" => array(
					"title" => esc_html__("AJAX", 'junotoys'),
					"desc" => wp_kses_data( __("Search via AJAX or reload page", 'junotoys') ),
					"value" => "yes",
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
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'junotoys_sc_search_reg_shortcodes_vc' ) ) {
	//add_action('junotoys_action_shortcodes_list_vc', 'junotoys_sc_search_reg_shortcodes_vc');
	function junotoys_sc_search_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_search",
			"name" => esc_html__("Search form", 'junotoys'),
			"description" => wp_kses_data( __("Insert search form", 'junotoys') ),
			"category" => esc_html__('Content', 'junotoys'),
			'icon' => 'icon_trx_search',
			"class" => "trx_sc_single trx_sc_search",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "style",
					"heading" => esc_html__("Style", 'junotoys'),
					"description" => wp_kses_data( __("Select style to display search field", 'junotoys') ),
					"class" => "",
					"value" => junotoys_get_list_search_styles(),
					"type" => "dropdown"
				),
				array(
					"param_name" => "state",
					"heading" => esc_html__("State", 'junotoys'),
					"description" => wp_kses_data( __("Select search field initial state", 'junotoys') ),
					"class" => "",
					"value" => array(
						esc_html__('Fixed', 'junotoys')  => "fixed",
						esc_html__('Opened', 'junotoys') => "opened",
						esc_html__('Closed', 'junotoys') => "closed"
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", 'junotoys'),
					"description" => wp_kses_data( __("Title (placeholder) for the search field", 'junotoys') ),
					"admin_label" => true,
					"class" => "",
					"value" => esc_html__("Search &hellip;", 'junotoys'),
					"type" => "textfield"
				),
				array(
					"param_name" => "ajax",
					"heading" => esc_html__("AJAX", 'junotoys'),
					"description" => wp_kses_data( __("Search via AJAX or reload page", 'junotoys') ),
					"class" => "",
					"value" => array(esc_html__('Use AJAX search', 'junotoys') => 'yes'),
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
			)
		) );
		
		class WPBakeryShortCode_Trx_Search extends JUNOTOYS_VC_ShortCodeSingle {}
	}
}
?>