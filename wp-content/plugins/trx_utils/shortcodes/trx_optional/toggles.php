<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('junotoys_sc_toggles_theme_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_sc_toggles_theme_setup' );
	function junotoys_sc_toggles_theme_setup() {
		add_action('junotoys_action_shortcodes_list', 		'junotoys_sc_toggles_reg_shortcodes');
		if (function_exists('junotoys_exists_visual_composer') && junotoys_exists_visual_composer())
			add_action('junotoys_action_shortcodes_list_vc','junotoys_sc_toggles_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

if (!function_exists('junotoys_sc_toggles')) {	
	function junotoys_sc_toggles($atts, $content=null){	
		if (junotoys_in_shortcode_blogger()) return '';
		extract(junotoys_html_decode(shortcode_atts(array(
			// Individual params
			"counter" => "off",
			"icon_closed" => "icon-rest1",
			"icon_opened" => "icon-plus34",
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
		junotoys_storage_set('sc_toggle_data', array(
			'counter' => 0,
            'show_counter' => junotoys_param_is_on($counter),
            'icon_closed' => empty($icon_closed) || junotoys_param_is_inherit($icon_closed) ? "icon-rest1" : $icon_closed,
            'icon_opened' => empty($icon_opened) || junotoys_param_is_inherit($icon_opened) ? "icon-plus34" : $icon_opened
            )
        );
		wp_enqueue_script('jquery-effects-slide', false, array('jquery','jquery-effects-core'), null, true);
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_toggles'
					. (!empty($class) ? ' '.esc_attr($class) : '')
					. (junotoys_param_is_on($counter) ? ' sc_show_counter' : '') 
					. '"'
				. (!junotoys_param_is_off($animation) ? ' data-animation="'.esc_attr(junotoys_get_animation_classes($animation)).'"' : '')
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
				. '>'
				. do_shortcode($content)
				. '</div>';
		return apply_filters('junotoys_shortcode_output', $output, 'trx_toggles', $atts, $content);
	}
	junotoys_require_shortcode('trx_toggles', 'junotoys_sc_toggles');
}


if (!function_exists('junotoys_sc_toggles_item')) {	
	function junotoys_sc_toggles_item($atts, $content=null) {
		if (junotoys_in_shortcode_blogger()) return '';
		extract(junotoys_html_decode(shortcode_atts( array(
			// Individual params
			"title" => "",
			"open" => "",
			"icon_closed" => "",
			"icon_opened" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts)));
		junotoys_storage_inc_array('sc_toggle_data', 'counter');
		if (empty($icon_closed) || junotoys_param_is_inherit($icon_closed)) $icon_closed = junotoys_storage_get_array('sc_toggles_data', 'icon_closed', '', "icon-plus34");
		if (empty($icon_opened) || junotoys_param_is_inherit($icon_opened)) $icon_opened = junotoys_storage_get_array('sc_toggles_data', 'icon_opened', '', "icon-rest1");
		$css .= junotoys_param_is_on($open) ? 'display:block;' : '';
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
					. ' class="sc_toggles_item'.(junotoys_param_is_on($open) ? ' sc_active' : '')
					. (!empty($class) ? ' '.esc_attr($class) : '')
					. (junotoys_storage_get_array('sc_toggle_data', 'counter') % 2 == 1 ? ' odd' : ' even') 
					. (junotoys_storage_get_array('sc_toggle_data', 'counter') == 1 ? ' first' : '')
					. '">'
					. '<h5 class="sc_toggles_title'.(junotoys_param_is_on($open) ? ' ui-state-active' : '').'">'
					. (!junotoys_param_is_off($icon_closed) ? '<span class="sc_toggles_icon sc_toggles_icon_closed '.esc_attr($icon_closed).'"></span>' : '')
					. (!junotoys_param_is_off($icon_opened) ? '<span class="sc_toggles_icon sc_toggles_icon_opened '.esc_attr($icon_opened).'"></span>' : '')
					. (junotoys_storage_get_array('sc_toggle_data', 'show_counter') ? '<span class="sc_items_counter">'.(junotoys_storage_get_array('sc_toggle_data', 'counter')).'</span>' : '')
					. ($title) 
					. '</h5>'
					. '<div class="sc_toggles_content"'
						. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
						.'>' 
						. do_shortcode($content) 
					. '</div>'
				. '</div>';
		return apply_filters('junotoys_shortcode_output', $output, 'trx_toggles_item', $atts, $content);
	}
	junotoys_require_shortcode('trx_toggles_item', 'junotoys_sc_toggles_item');
}


/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'junotoys_sc_toggles_reg_shortcodes' ) ) {
	//add_action('junotoys_action_shortcodes_list', 'junotoys_sc_toggles_reg_shortcodes');
	function junotoys_sc_toggles_reg_shortcodes() {
	
		junotoys_sc_map("trx_toggles", array(
			"title" => esc_html__("Toggles", 'junotoys'),
			"desc" => wp_kses_data( __("Toggles items", 'junotoys') ),
			"decorate" => true,
			"container" => false,
			"params" => array(
				"counter" => array(
					"title" => esc_html__("Counter", 'junotoys'),
					"desc" => wp_kses_data( __("Display counter before each toggles title", 'junotoys') ),
					"value" => "off",
					"type" => "switch",
					"options" => junotoys_get_sc_param('on_off')
				),
				"icon_closed" => array(
					"title" => esc_html__("Icon while closed",  'junotoys'),
					"desc" => wp_kses_data( __('Select icon for the closed toggles item from Fontello icons set',  'junotoys') ),
					"value" => "",
					"type" => "icons",
					"options" => junotoys_get_sc_param('icons')
				),
				"icon_opened" => array(
					"title" => esc_html__("Icon while opened",  'junotoys'),
					"desc" => wp_kses_data( __('Select icon for the opened toggles item from Fontello icons set',  'junotoys') ),
					"value" => "",
					"type" => "icons",
					"options" => junotoys_get_sc_param('icons')
				),
				"top" => junotoys_get_sc_param('top'),
				"bottom" => junotoys_get_sc_param('bottom'),
				"left" => junotoys_get_sc_param('left'),
				"right" => junotoys_get_sc_param('right'),
				"id" => junotoys_get_sc_param('id'),
				"class" => junotoys_get_sc_param('class'),
				"animation" => junotoys_get_sc_param('animation'),
				"css" => junotoys_get_sc_param('css')
			),
			"children" => array(
				"name" => "trx_toggles_item",
				"title" => esc_html__("Toggles item", 'junotoys'),
				"desc" => wp_kses_data( __("Toggles item", 'junotoys') ),
				"container" => true,
				"params" => array(
					"title" => array(
						"title" => esc_html__("Toggles item title", 'junotoys'),
						"desc" => wp_kses_data( __("Title for current toggles item", 'junotoys') ),
						"value" => "",
						"type" => "text"
					),
					"open" => array(
						"title" => esc_html__("Open on show", 'junotoys'),
						"desc" => wp_kses_data( __("Open current toggles item on show", 'junotoys') ),
						"value" => "no",
						"type" => "switch",
						"options" => junotoys_get_sc_param('yes_no')
					),
					"icon_closed" => array(
						"title" => esc_html__("Icon while closed",  'junotoys'),
						"desc" => wp_kses_data( __('Select icon for the closed toggles item from Fontello icons set',  'junotoys') ),
						"value" => "",
						"type" => "icons",
						"options" => junotoys_get_sc_param('icons')
					),
					"icon_opened" => array(
						"title" => esc_html__("Icon while opened",  'junotoys'),
						"desc" => wp_kses_data( __('Select icon for the opened toggles item from Fontello icons set',  'junotoys') ),
						"value" => "",
						"type" => "icons",
						"options" => junotoys_get_sc_param('icons')
					),
					"_content_" => array(
						"title" => esc_html__("Toggles item content", 'junotoys'),
						"desc" => wp_kses_data( __("Current toggles item content", 'junotoys') ),
						"rows" => 4,
						"value" => "",
						"type" => "textarea"
					),
					"id" => junotoys_get_sc_param('id'),
					"class" => junotoys_get_sc_param('class'),
					"css" => junotoys_get_sc_param('css')
				)
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'junotoys_sc_toggles_reg_shortcodes_vc' ) ) {
	//add_action('junotoys_action_shortcodes_list_vc', 'junotoys_sc_toggles_reg_shortcodes_vc');
	function junotoys_sc_toggles_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_toggles",
			"name" => esc_html__("Toggles", 'junotoys'),
			"description" => wp_kses_data( __("Toggles items", 'junotoys') ),
			"category" => esc_html__('Content', 'junotoys'),
			'icon' => 'icon_trx_toggles',
			"class" => "trx_sc_collection trx_sc_toggles",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => false,
			"as_parent" => array('only' => 'trx_toggles_item'),
			"params" => array(
				array(
					"param_name" => "counter",
					"heading" => esc_html__("Counter", 'junotoys'),
					"description" => wp_kses_data( __("Display counter before each toggles title", 'junotoys') ),
					"class" => "",
					"value" => array("Add item numbers before each element" => "on" ),
					"type" => "checkbox"
				),
				array(
					"param_name" => "icon_closed",
					"heading" => esc_html__("Icon while closed", 'junotoys'),
					"description" => wp_kses_data( __("Select icon for the closed toggles item from Fontello icons set", 'junotoys') ),
					"class" => "",
					"value" => junotoys_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "icon_opened",
					"heading" => esc_html__("Icon while opened", 'junotoys'),
					"description" => wp_kses_data( __("Select icon for the opened toggles item from Fontello icons set", 'junotoys') ),
					"class" => "",
					"value" => junotoys_get_sc_param('icons'),
					"type" => "dropdown"
				),
				junotoys_get_vc_param('id'),
				junotoys_get_vc_param('class'),
				junotoys_get_vc_param('margin_top'),
				junotoys_get_vc_param('margin_bottom'),
				junotoys_get_vc_param('margin_left'),
				junotoys_get_vc_param('margin_right')
			),
			'default_content' => '
				[trx_toggles_item title="' . esc_html__( 'Item 1 title', 'junotoys' ) . '"][/trx_toggles_item]
				[trx_toggles_item title="' . esc_html__( 'Item 2 title', 'junotoys' ) . '"][/trx_toggles_item]
			',
			"custom_markup" => '
				<div class="wpb_accordion_holder wpb_holder clearfix vc_container_for_children">
					%content%
				</div>
				<div class="tab_controls">
					<button class="add_tab" title="'.esc_attr__("Add item", 'junotoys').'">'.esc_html__("Add item", 'junotoys').'</button>
				</div>
			',
			'js_view' => 'VcTrxTogglesView'
		) );
		
		
		vc_map( array(
			"base" => "trx_toggles_item",
			"name" => esc_html__("Toggles item", 'junotoys'),
			"description" => wp_kses_data( __("Single toggles item", 'junotoys') ),
			"show_settings_on_create" => true,
			"content_element" => true,
			"is_container" => true,
			'icon' => 'icon_trx_toggles_item',
			"as_child" => array('only' => 'trx_toggles'),
			"as_parent" => array('except' => 'trx_toggles'),
			"params" => array(
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", 'junotoys'),
					"description" => wp_kses_data( __("Title for current toggles item", 'junotoys') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "open",
					"heading" => esc_html__("Open on show", 'junotoys'),
					"description" => wp_kses_data( __("Open current toggle item on show", 'junotoys') ),
					"class" => "",
					"value" => array("Opened" => "yes" ),
					"type" => "checkbox"
				),
				array(
					"param_name" => "icon_closed",
					"heading" => esc_html__("Icon while closed", 'junotoys'),
					"description" => wp_kses_data( __("Select icon for the closed toggles item from Fontello icons set", 'junotoys') ),
					"class" => "",
					"value" => junotoys_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "icon_opened",
					"heading" => esc_html__("Icon while opened", 'junotoys'),
					"description" => wp_kses_data( __("Select icon for the opened toggles item from Fontello icons set", 'junotoys') ),
					"class" => "",
					"value" => junotoys_get_sc_param('icons'),
					"type" => "dropdown"
				),
				junotoys_get_vc_param('id'),
				junotoys_get_vc_param('class'),
				junotoys_get_vc_param('css')
			),
			'js_view' => 'VcTrxTogglesTabView'
		) );
		class WPBakeryShortCode_Trx_Toggles extends JUNOTOYS_VC_ShortCodeToggles {}
		class WPBakeryShortCode_Trx_Toggles_Item extends JUNOTOYS_VC_ShortCodeTogglesItem {}
	}
}
?>