<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('junotoys_sc_emailer_theme_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_sc_emailer_theme_setup' );
	function junotoys_sc_emailer_theme_setup() {
		add_action('junotoys_action_shortcodes_list', 		'junotoys_sc_emailer_reg_shortcodes');
		if (function_exists('junotoys_exists_visual_composer') && junotoys_exists_visual_composer())
			add_action('junotoys_action_shortcodes_list_vc','junotoys_sc_emailer_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

//[trx_emailer group=""]

if (!function_exists('junotoys_sc_emailer')) {	
	function junotoys_sc_emailer($atts, $content = null) {
		if (junotoys_in_shortcode_blogger()) return '';
		extract(junotoys_html_decode(shortcode_atts(array(
			// Individual params
			"group" => "",
			"open" => "yes",
			"align" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => "",
			"width" => "",
			"height" => ""
		), $atts)));
		$css .= ($css ? ';' : '') . junotoys_get_css_position_from_values($top, $right, $bottom, $left);
		$css .= junotoys_get_css_dimensions_from_values($width, $height);
		// Load core messages
		junotoys_enqueue_messages();
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
					. ' class="sc_emailer' . ($align && $align!='none' ? ' align' . esc_attr($align) : '') . (junotoys_param_is_on($open) ? ' sc_emailer_opened' : '') . (!empty($class) ? ' '.esc_attr($class) : '') . '"' 
					. ($css ? ' style="'.esc_attr($css).'"' : '') 
					. (!junotoys_param_is_off($animation) ? ' data-animation="'.esc_attr(junotoys_get_animation_classes($animation)).'"' : '')
					. '>'
				. '<form class="sc_emailer_form">'
				. '<input type="text" class="sc_emailer_input" name="email" value="" placeholder="'.esc_attr__('Email', 'junotoys').'">'
				. '<a href="#" class="sc_emailer_button icon-mail62" title="'.esc_attr__('Submit', 'junotoys').'" data-group="'.esc_attr($group ? $group : esc_html__('E-mailer subscription', 'junotoys')).'"></a>'
				.'<div class="mcfwp-agree-input">'
                    .'<label class="mcfwp-agree-input"><input type="checkbox" name="i_agree_privacy_policy" value="1" required="" /><span><a href="/privacy-policy/" target="_blank">I have read and agree to the terms &amp; conditions</a></span></label>'
                . '</div>'
                . '</form>'
			. '</div>';
		return apply_filters('junotoys_shortcode_output', $output, 'trx_emailer', $atts, $content);
	}
	junotoys_require_shortcode("trx_emailer", "junotoys_sc_emailer");
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'junotoys_sc_emailer_reg_shortcodes' ) ) {
	//add_action('junotoys_action_shortcodes_list', 'junotoys_sc_emailer_reg_shortcodes');
	function junotoys_sc_emailer_reg_shortcodes() {
	
		junotoys_sc_map("trx_emailer", array(
			"title" => esc_html__("E-mail collector", 'junotoys'),
			"desc" => wp_kses_data( __("Collect the e-mail address into specified group", 'junotoys') ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"group" => array(
					"title" => esc_html__("Group", 'junotoys'),
					"desc" => wp_kses_data( __("The name of group to collect e-mail address", 'junotoys') ),
					"value" => "",
					"type" => "text"
				),
				"open" => array(
					"title" => esc_html__("Open", 'junotoys'),
					"desc" => wp_kses_data( __("Initially open the input field on show object", 'junotoys') ),
					"divider" => true,
					"value" => "yes",
					"type" => "switch",
					"options" => junotoys_get_sc_param('yes_no')
				),
				"align" => array(
					"title" => esc_html__("Alignment", 'junotoys'),
					"desc" => wp_kses_data( __("Align object to left, center or right", 'junotoys') ),
					"divider" => true,
					"value" => "none",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => junotoys_get_sc_param('align')
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
if ( !function_exists( 'junotoys_sc_emailer_reg_shortcodes_vc' ) ) {
	//add_action('junotoys_action_shortcodes_list_vc', 'junotoys_sc_emailer_reg_shortcodes_vc');
	function junotoys_sc_emailer_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_emailer",
			"name" => esc_html__("E-mail collector", 'junotoys'),
			"description" => wp_kses_data( __("Collect e-mails into specified group", 'junotoys') ),
			"category" => esc_html__('Content', 'junotoys'),
			'icon' => 'icon_trx_emailer',
			"class" => "trx_sc_single trx_sc_emailer",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "group",
					"heading" => esc_html__("Group", 'junotoys'),
					"description" => wp_kses_data( __("The name of group to collect e-mail address", 'junotoys') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "open",
					"heading" => esc_html__("Opened", 'junotoys'),
					"description" => wp_kses_data( __("Initially open the input field on show object", 'junotoys') ),
					"class" => "",
					"value" => array(esc_html__('Initially opened', 'junotoys') => 'yes'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", 'junotoys'),
					"description" => wp_kses_data( __("Align field to left, center or right", 'junotoys') ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(junotoys_get_sc_param('align')),
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
		
		class WPBakeryShortCode_Trx_Emailer extends JUNOTOYS_VC_ShortCodeSingle {}
	}
}
?>