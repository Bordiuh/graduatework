<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('junotoys_sc_anchor_theme_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_sc_anchor_theme_setup' );
	function junotoys_sc_anchor_theme_setup() {
		add_action('junotoys_action_shortcodes_list', 		'junotoys_sc_anchor_reg_shortcodes');
		if (function_exists('junotoys_exists_visual_composer') && junotoys_exists_visual_composer())
			add_action('junotoys_action_shortcodes_list_vc','junotoys_sc_anchor_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_anchor id="unique_id" description="Anchor description" title="Short Caption" icon="icon-class"]
*/

if (!function_exists('junotoys_sc_anchor')) {
	function junotoys_sc_anchor($atts, $content = null) {
		if (junotoys_in_shortcode_blogger()) return '';
		extract(junotoys_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			"description" => '',
			"icon" => '',
			"url" => "",
			"separator" => "no",
			// Common params
			"id" => ""
		), $atts)));
		$output = $id 
			? '<a id="'.esc_attr($id).'"'
				. ' class="sc_anchor"' 
				. ' title="' . ($title ? esc_attr($title) : '') . '"'
				. ' data-description="' . ($description ? esc_attr(junotoys_strmacros($description)) : ''). '"'
				. ' data-icon="' . ($icon ? $icon : '') . '"' 
				. ' data-url="' . ($url ? esc_attr($url) : '') . '"' 
				. ' data-separator="' . (junotoys_param_is_on($separator) ? 'yes' : 'no') . '"'
				. '></a>'
			: '';
		return apply_filters('junotoys_shortcode_output', $output, 'trx_anchor', $atts, $content);
	}
	junotoys_require_shortcode("trx_anchor", "junotoys_sc_anchor");
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'junotoys_sc_anchor_reg_shortcodes' ) ) {
	//add_action('junotoys_action_shortcodes_list', 'junotoys_sc_anchor_reg_shortcodes');
	function junotoys_sc_anchor_reg_shortcodes() {
	
		junotoys_sc_map("trx_anchor", array(
			"title" => esc_html__("Anchor", "junotoys"),
			"desc" => wp_kses_data( __("Insert anchor for the TOC (table of content)", "junotoys") ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"icon" => array(
					"title" => esc_html__("Anchor's icon",  'junotoys'),
					"desc" => wp_kses_data( __('Select icon for the anchor from Fontello icons set',  'junotoys') ),
					"value" => "",
					"type" => "icons",
					"options" => junotoys_get_sc_param('icons')
				),
				"title" => array(
					"title" => esc_html__("Short title", "junotoys"),
					"desc" => wp_kses_data( __("Short title of the anchor (for the table of content)", "junotoys") ),
					"value" => "",
					"type" => "text"
				),
				"description" => array(
					"title" => esc_html__("Long description", "junotoys"),
					"desc" => wp_kses_data( __("Description for the popup (then hover on the icon). You can use:<br>'{{' and '}}' - to make the text italic,<br>'((' and '))' - to make the text bold,<br>'||' - to insert line break", "junotoys") ),
					"value" => "",
					"type" => "text"
				),
				"url" => array(
					"title" => esc_html__("External URL", "junotoys"),
					"desc" => wp_kses_data( __("External URL for this TOC item", "junotoys") ),
					"value" => "",
					"type" => "text"
				),
				"separator" => array(
					"title" => esc_html__("Add separator", "junotoys"),
					"desc" => wp_kses_data( __("Add separator under item in the TOC", "junotoys") ),
					"value" => "no",
					"type" => "switch",
					"options" => junotoys_get_sc_param('yes_no')
				),
				"id" => junotoys_get_sc_param('id')
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'junotoys_sc_anchor_reg_shortcodes_vc' ) ) {
	//add_action('junotoys_action_shortcodes_list_vc', 'junotoys_sc_anchor_reg_shortcodes_vc');
	function junotoys_sc_anchor_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_anchor",
			"name" => esc_html__("Anchor", "junotoys"),
			"description" => wp_kses_data( __("Insert anchor for the TOC (table of content)", "junotoys") ),
			"category" => esc_html__('Content', 'junotoys'),
			'icon' => 'icon_trx_anchor',
			"class" => "trx_sc_single trx_sc_anchor",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Anchor's icon", 'junotoys'),
					"description" => wp_kses_data( __("Select icon for the anchor from Fontello icons set", 'junotoys') ),
					"class" => "",
					"value" => junotoys_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Short title", "junotoys"),
					"description" => wp_kses_data( __("Short title of the anchor (for the table of content)", "junotoys") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "description",
					"heading" => esc_html__("Long description", "junotoys"),
					"description" => wp_kses_data( __("Description for the popup (then hover on the icon). You can use:<br>'{{' and '}}' - to make the text italic,<br>'((' and '))' - to make the text bold,<br>'||' - to insert line break", "junotoys") ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "url",
					"heading" => esc_html__("External URL", "junotoys"),
					"description" => wp_kses_data( __("External URL for this TOC item", "junotoys") ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "separator",
					"heading" => esc_html__("Add separator", "junotoys"),
					"description" => wp_kses_data( __("Add separator under item in the TOC", "junotoys") ),
					"class" => "",
					"value" => array("Add separator" => "yes" ),
					"type" => "checkbox"
				),
				junotoys_get_vc_param('id')
			),
		) );
		
		class WPBakeryShortCode_Trx_Anchor extends JUNOTOYS_VC_ShortCodeSingle {}
	}
}
?>