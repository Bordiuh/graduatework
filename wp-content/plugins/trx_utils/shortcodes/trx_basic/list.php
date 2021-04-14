<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('junotoys_sc_list_theme_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_sc_list_theme_setup' );
	function junotoys_sc_list_theme_setup() {
		add_action('junotoys_action_shortcodes_list', 		'junotoys_sc_list_reg_shortcodes');
		if (function_exists('junotoys_exists_visual_composer') && junotoys_exists_visual_composer())
			add_action('junotoys_action_shortcodes_list_vc','junotoys_sc_list_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

if (!function_exists('junotoys_sc_list')) {	
	function junotoys_sc_list($atts, $content=null){	
		if (junotoys_in_shortcode_blogger()) return '';
		extract(junotoys_html_decode(shortcode_atts(array(
			// Individual params
			"style" => "ul",
			"icon" => "icon-right",
			"icon_color" => "",
			"color" => "",
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
		$css .= $color !== '' ? 'color:' . esc_attr($color) .';' : '';
		if (trim($style) == '' || (trim($icon) == '' && $style=='iconed')) $style = 'ul';
		junotoys_storage_set('sc_list_data', array(
			'counter' => 0,
            'icon' => empty($icon) || junotoys_param_is_inherit($icon) ? "icon-right" : $icon,
            'icon_color' => $icon_color,
            'style' => $style
            )
        );
		$output = '<' . ($style=='ol' ? 'ol' : 'ul')
				. ($id ? ' id="'.esc_attr($id).'"' : '')
				. ' class="sc_list sc_list_style_' . esc_attr($style) . (!empty($class) ? ' '.esc_attr($class) : '') . '"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. (!junotoys_param_is_off($animation) ? ' data-animation="'.esc_attr(junotoys_get_animation_classes($animation)).'"' : '')
				. '>'
				. do_shortcode($content)
				. '</' .($style=='ol' ? 'ol' : 'ul') . '>';
		return apply_filters('junotoys_shortcode_output', $output, 'trx_list', $atts, $content);
	}
	junotoys_require_shortcode('trx_list', 'junotoys_sc_list');
}


if (!function_exists('junotoys_sc_list_item')) {	
	function junotoys_sc_list_item($atts, $content=null) {
		if (junotoys_in_shortcode_blogger()) return '';
		extract(junotoys_html_decode(shortcode_atts( array(
			// Individual params
			"color" => "",
			"icon" => "",
			"icon_color" => "",
			"title" => "",
			"link" => "",
			"target" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts)));
		junotoys_storage_inc_array('sc_list_data', 'counter');
		$css .= $color !== '' ? 'color:' . esc_attr($color) .';' : '';
		if (trim($icon) == '' || junotoys_param_is_inherit($icon)) $icon = junotoys_storage_get_array('sc_list_data', 'icon');
		if (trim($color) == '' || junotoys_param_is_inherit($icon_color)) $icon_color = junotoys_storage_get_array('sc_list_data', 'icon_color');
		$content = do_shortcode($content);
		if (empty($content)) $content = $title;
		$output = '<li' . ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ' class="sc_list_item' 
			. (!empty($class) ? ' '.esc_attr($class) : '')
			. (junotoys_storage_get_array('sc_list_data', 'counter') % 2 == 1 ? ' odd' : ' even') 
			. (junotoys_storage_get_array('sc_list_data', 'counter') == 1 ? ' first' : '')  
			. '"' 
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
			. ($title ? ' title="'.esc_attr($title).'"' : '') 
			. '>' 
			. (!empty($link) ? '<a href="'.esc_url($link).'"' . (!empty($target) ? ' target="'.esc_attr($target).'"' : '') . '>' : '')
			. (junotoys_storage_get_array('sc_list_data', 'style')=='iconed' && $icon!='' ? '<span class="sc_list_icon '.esc_attr($icon).'"'.($icon_color !== '' ? ' style="color:'.esc_attr($icon_color).';"' : '').'></span>' : '')
			. trim($content)
			. (!empty($link) ? '</a>': '')
			. '</li>';
		return apply_filters('junotoys_shortcode_output', $output, 'trx_list_item', $atts, $content);
	}
	junotoys_require_shortcode('trx_list_item', 'junotoys_sc_list_item');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'junotoys_sc_list_reg_shortcodes' ) ) {
	//add_action('junotoys_action_shortcodes_list', 'junotoys_sc_list_reg_shortcodes');
	function junotoys_sc_list_reg_shortcodes() {
	
		junotoys_sc_map("trx_list", array(
			"title" => esc_html__("List", 'junotoys'),
			"desc" => wp_kses_data( __("List items with specific bullets", 'junotoys') ),
			"decorate" => true,
			"container" => false,
			"params" => array(
				"style" => array(
					"title" => esc_html__("Bullet's style", 'junotoys'),
					"desc" => wp_kses_data( __("Bullet's style for each list item", 'junotoys') ),
					"value" => "ul",
					"type" => "checklist",
					"options" => junotoys_get_sc_param('list_styles')
				), 
				"color" => array(
					"title" => esc_html__("Color", 'junotoys'),
					"desc" => wp_kses_data( __("List items color", 'junotoys') ),
					"value" => "",
					"type" => "color"
				),
				"icon" => array(
					"title" => esc_html__('List icon',  'junotoys'),
					"desc" => wp_kses_data( __("Select list icon from Fontello icons set (only for style=Iconed)",  'junotoys') ),
					"dependency" => array(
						'style' => array('iconed')
					),
					"value" => "",
					"type" => "icons",
					"options" => junotoys_get_sc_param('icons')
				),
				"icon_color" => array(
					"title" => esc_html__("Icon color", 'junotoys'),
					"desc" => wp_kses_data( __("List icons color", 'junotoys') ),
					"value" => "",
					"dependency" => array(
						'style' => array('iconed')
					),
					"type" => "color"
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
				"name" => "trx_list_item",
				"title" => esc_html__("Item", 'junotoys'),
				"desc" => wp_kses_data( __("List item with specific bullet", 'junotoys') ),
				"decorate" => false,
				"container" => true,
				"params" => array(
					"_content_" => array(
						"title" => esc_html__("List item content", 'junotoys'),
						"desc" => wp_kses_data( __("Current list item content", 'junotoys') ),
						"rows" => 4,
						"value" => "",
						"type" => "textarea"
					),
					"title" => array(
						"title" => esc_html__("List item title", 'junotoys'),
						"desc" => wp_kses_data( __("Current list item title (show it as tooltip)", 'junotoys') ),
						"value" => "",
						"type" => "text"
					),
					"color" => array(
						"title" => esc_html__("Color", 'junotoys'),
						"desc" => wp_kses_data( __("Text color for this item", 'junotoys') ),
						"value" => "",
						"type" => "color"
					),
					"icon" => array(
						"title" => esc_html__('List icon',  'junotoys'),
						"desc" => wp_kses_data( __("Select list item icon from Fontello icons set (only for style=Iconed)",  'junotoys') ),
						"value" => "",
						"type" => "icons",
						"options" => junotoys_get_sc_param('icons')
					),
					"icon_color" => array(
						"title" => esc_html__("Icon color", 'junotoys'),
						"desc" => wp_kses_data( __("Icon color for this item", 'junotoys') ),
						"value" => "",
						"type" => "color"
					),
					"link" => array(
						"title" => esc_html__("Link URL", 'junotoys'),
						"desc" => wp_kses_data( __("Link URL for the current list item", 'junotoys') ),
						"divider" => true,
						"value" => "",
						"type" => "text"
					),
					"target" => array(
						"title" => esc_html__("Link target", 'junotoys'),
						"desc" => wp_kses_data( __("Link target for the current list item", 'junotoys') ),
						"value" => "",
						"type" => "text"
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
if ( !function_exists( 'junotoys_sc_list_reg_shortcodes_vc' ) ) {
	//add_action('junotoys_action_shortcodes_list_vc', 'junotoys_sc_list_reg_shortcodes_vc');
	function junotoys_sc_list_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_list",
			"name" => esc_html__("List", 'junotoys'),
			"description" => wp_kses_data( __("List items with specific bullets", 'junotoys') ),
			"category" => esc_html__('Content', 'junotoys'),
			"class" => "trx_sc_collection trx_sc_list",
			'icon' => 'icon_trx_list',
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => false,
			"as_parent" => array('only' => 'trx_list_item'),
			"params" => array(
				array(
					"param_name" => "style",
					"heading" => esc_html__("Bullet's style", 'junotoys'),
					"description" => wp_kses_data( __("Bullet's style for each list item", 'junotoys') ),
					"class" => "",
					"admin_label" => true,
					"value" => array_flip(junotoys_get_sc_param('list_styles')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Color", 'junotoys'),
					"description" => wp_kses_data( __("List items color", 'junotoys') ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("List icon", 'junotoys'),
					"description" => wp_kses_data( __("Select list icon from Fontello icons set (only for style=Iconed)", 'junotoys') ),
					"admin_label" => true,
					"class" => "",
					'dependency' => array(
						'element' => 'style',
						'value' => array('iconed')
					),
					"value" => junotoys_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "icon_color",
					"heading" => esc_html__("Icon color", 'junotoys'),
					"description" => wp_kses_data( __("List icons color", 'junotoys') ),
					"class" => "",
					'dependency' => array(
						'element' => 'style',
						'value' => array('iconed')
					),
					"value" => "",
					"type" => "colorpicker"
				),
				junotoys_get_vc_param('id'),
				junotoys_get_vc_param('class'),
				junotoys_get_vc_param('animation'),
				junotoys_get_vc_param('css'),
				junotoys_get_vc_param('margin_top'),
				junotoys_get_vc_param('margin_bottom'),
				junotoys_get_vc_param('margin_left'),
				junotoys_get_vc_param('margin_right')
			),
			'default_content' => '
				[trx_list_item][/trx_list_item]
				[trx_list_item][/trx_list_item]
			'
		) );
		
		
		vc_map( array(
			"base" => "trx_list_item",
			"name" => esc_html__("List item", 'junotoys'),
			"description" => wp_kses_data( __("List item with specific bullet", 'junotoys') ),
			"class" => "trx_sc_container trx_sc_list_item",
			"show_settings_on_create" => true,
			"content_element" => true,
			"is_container" => true,
			'icon' => 'icon_trx_list_item',
			"as_child" => array('only' => 'trx_list'), // Use only|except attributes to limit parent (separate multiple values with comma)
			"as_parent" => array('except' => 'trx_list'),
			"params" => array(
				array(
					"param_name" => "title",
					"heading" => esc_html__("List item title", 'junotoys'),
					"description" => wp_kses_data( __("Title for the current list item (show it as tooltip)", 'junotoys') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "link",
					"heading" => esc_html__("Link URL", 'junotoys'),
					"description" => wp_kses_data( __("Link URL for the current list item", 'junotoys') ),
					"admin_label" => true,
					"group" => esc_html__('Link', 'junotoys'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "target",
					"heading" => esc_html__("Link target", 'junotoys'),
					"description" => wp_kses_data( __("Link target for the current list item", 'junotoys') ),
					"admin_label" => true,
					"group" => esc_html__('Link', 'junotoys'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Color", 'junotoys'),
					"description" => wp_kses_data( __("Text color for this item", 'junotoys') ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("List item icon", 'junotoys'),
					"description" => wp_kses_data( __("Select list item icon from Fontello icons set (only for style=Iconed)", 'junotoys') ),
					"admin_label" => true,
					"class" => "",
					"value" => junotoys_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "icon_color",
					"heading" => esc_html__("Icon color", 'junotoys'),
					"description" => wp_kses_data( __("Icon color for this item", 'junotoys') ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				junotoys_get_vc_param('id'),
				junotoys_get_vc_param('class'),
				junotoys_get_vc_param('css')
			)
		
		) );
		
		class WPBakeryShortCode_Trx_List extends JUNOTOYS_VC_ShortCodeCollection {}
		class WPBakeryShortCode_Trx_List_Item extends JUNOTOYS_VC_ShortCodeContainer {}
	}
}
?>