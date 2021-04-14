<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('junotoys_sc_columns_theme_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_sc_columns_theme_setup' );
	function junotoys_sc_columns_theme_setup() {
		add_action('junotoys_action_shortcodes_list', 		'junotoys_sc_columns_reg_shortcodes');
		if (function_exists('junotoys_exists_visual_composer') && junotoys_exists_visual_composer())
			add_action('junotoys_action_shortcodes_list_vc','junotoys_sc_columns_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */


if (!function_exists('junotoys_sc_columns')) {	
	function junotoys_sc_columns($atts, $content=null){	
		if (junotoys_in_shortcode_blogger()) return '';
		extract(junotoys_html_decode(shortcode_atts(array(
			// Individual params
			"count" => "2",
			"fluid" => "no",
			"margins" => "yes",
			"equalheight" => "no",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => "",
			"width" => "",
			"height" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$css .= ($css ? ';' : '') . junotoys_get_css_position_from_values($top, $right, $bottom, $left);
		$css .= junotoys_get_css_dimensions_from_values($width, $height);
		$count = max(1, min(12, (int) $count));
		junotoys_storage_set('sc_columns_data', array(
			'counter' => 1,
			'equal_selector' => '',
            'after_span2' => false,
            'after_span3' => false,
            'after_span4' => false,
            'count' => $count
            )
        );
		$content = do_shortcode($content);
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="columns_wrap sc_columns'
					. ' columns_' . (junotoys_param_is_on($fluid) ? 'fluid' : 'nofluid') 
					. (!empty($margins) && junotoys_param_is_off($margins) ? ' no_margins' : '') 
					. ' sc_columns_count_' . esc_attr($count)
					. (!empty($class) ? ' '.esc_attr($class) : '') 
				. '"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. (!junotoys_param_is_off($equalheight) ? ' data-equal-height="'.esc_attr(junotoys_storage_get_array('sc_columns_data', 'equal_selector')).'"' : '')
				. (!junotoys_param_is_off($animation) ? ' data-animation="'.esc_attr(junotoys_get_animation_classes($animation)).'"' : '')
				. '>'
					. trim($content)
				. '</div>';
		return apply_filters('junotoys_shortcode_output', $output, 'trx_columns', $atts, $content);
	}
	junotoys_require_shortcode('trx_columns', 'junotoys_sc_columns');
}


if (!function_exists('junotoys_sc_column_item')) {	
	function junotoys_sc_column_item($atts, $content=null) {
		if (junotoys_in_shortcode_blogger()) return '';
		extract(junotoys_html_decode(shortcode_atts( array(
			// Individual params
			"span" => "1",
			"align" => "",
			"color" => "",
			"bg_color" => "",
			"bg_image" => "",
			"bg_tile" => "no",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => ""
		), $atts)));
		$css .= ($align !== '' ? 'text-align:' . esc_attr($align) . ';' : '') 
			. ($color !== '' ? 'color:' . esc_attr($color) . ';' : '');
		$span = max(1, min(11, (int) $span));
		if (!empty($bg_image)) {
			if ($bg_image > 0) {
				$attach = wp_get_attachment_image_src( $bg_image, 'full' );
				if (isset($attach[0]) && $attach[0]!='')
					$bg_image = $attach[0];
			}
		}
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') . ' class="column-'.($span > 1 ? esc_attr($span) : 1).'_'.esc_attr(junotoys_storage_get_array('sc_columns_data', 'count')).' sc_column_item sc_column_item_'.esc_attr(junotoys_storage_get_array('sc_columns_data', 'counter')) 
					. (!empty($class) ? ' '.esc_attr($class) : '')
					. (junotoys_storage_get_array('sc_columns_data', 'counter') % 2 == 1 ? ' odd' : ' even') 
					. (junotoys_storage_get_array('sc_columns_data', 'counter') == 1 ? ' first' : '') 
					. ($span > 1 ? ' span_'.esc_attr($span) : '') 
					. (junotoys_storage_get_array('sc_columns_data', 'after_span2') ? ' after_span_2' : '') 
					. (junotoys_storage_get_array('sc_columns_data', 'after_span3') ? ' after_span_3' : '') 
					. (junotoys_storage_get_array('sc_columns_data', 'after_span4') ? ' after_span_4' : '') 
					. '"'
					. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
					. (!junotoys_param_is_off($animation) ? ' data-animation="'.esc_attr(junotoys_get_animation_classes($animation)).'"' : '')
					. '>'
					. ($bg_color!=='' || $bg_image !== '' ? '<div class="sc_column_item_inner" style="'
							. ($bg_color !== '' ? 'background-color:' . esc_attr($bg_color) . ';' : '')
							. ($bg_image !== '' ? 'background-image:url(' . esc_url($bg_image) . ');'.(junotoys_param_is_on($bg_tile) ? 'background-repeat:repeat;' : 'background-repeat:no-repeat;background-size:cover;') : '')
							. '">' : '')
						. do_shortcode($content)
					. ($bg_color!=='' || $bg_image !== '' ? '</div>' : '')
					. '</div>';
		junotoys_storage_inc_array('sc_columns_data', 'counter', $span);
		junotoys_storage_set_array('sc_columns_data', 'after_span2', $span==2);
		junotoys_storage_set_array('sc_columns_data', 'after_span3', $span==3);
		junotoys_storage_set_array('sc_columns_data', 'after_span4', $span==4);
		junotoys_storage_set_array('sc_columns_data', 'equal_selector', $bg_color!=='' || $bg_image !== '' ? '.sc_column_item_inner' : '.sc_column_item');
		return apply_filters('junotoys_shortcode_output', $output, 'trx_column_item', $atts, $content);
	}
	junotoys_require_shortcode('trx_column_item', 'junotoys_sc_column_item');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'junotoys_sc_columns_reg_shortcodes' ) ) {
	//add_action('junotoys_action_shortcodes_list', 'junotoys_sc_columns_reg_shortcodes');
	function junotoys_sc_columns_reg_shortcodes() {
	
		junotoys_sc_map("trx_columns", array(
			"title" => esc_html__("Columns", 'junotoys'),
			"desc" => wp_kses_data( __("Insert up to 5 columns in your page (post)", 'junotoys') ),
			"decorate" => true,
			"container" => false,
			"params" => array(
				"fluid" => array(
					"title" => esc_html__("Fluid columns", 'junotoys'),
					"desc" => wp_kses_data( __("To squeeze the columns when reducing the size of the window (fluid=yes) or to rebuild them (fluid=no)", 'junotoys') ),
					"value" => "no",
					"type" => "switch",
					"options" => junotoys_get_sc_param('yes_no')
				), 
				"margins" => array(
					"title" => esc_html__("Margins between columns", 'junotoys'),
					"desc" => wp_kses_data( __("Add margins between columns", 'junotoys') ),
					"value" => "yes",
					"type" => "switch",
					"options" => junotoys_get_sc_param('yes_no')
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
			),
			"children" => array(
				"name" => "trx_column_item",
				"title" => esc_html__("Column", 'junotoys'),
				"desc" => wp_kses_data( __("Column item", 'junotoys') ),
				"container" => true,
				"params" => array(
					"span" => array(
						"title" => esc_html__("Merge columns", 'junotoys'),
						"desc" => wp_kses_data( __("Count merged columns from current", 'junotoys') ),
						"value" => "",
						"type" => "text"
					),
					"align" => array(
						"title" => esc_html__("Alignment", 'junotoys'),
						"desc" => wp_kses_data( __("Alignment text in the column", 'junotoys') ),
						"value" => "",
						"type" => "checklist",
						"dir" => "horizontal",
						"options" => junotoys_get_sc_param('align')
					),
					"color" => array(
						"title" => esc_html__("Fore color", 'junotoys'),
						"desc" => wp_kses_data( __("Any color for objects in this column", 'junotoys') ),
						"value" => "",
						"type" => "color"
					),
					"bg_color" => array(
						"title" => esc_html__("Background color", 'junotoys'),
						"desc" => wp_kses_data( __("Any background color for this column", 'junotoys') ),
						"value" => "",
						"type" => "color"
					),
					"bg_image" => array(
						"title" => esc_html__("URL for background image file", 'junotoys'),
						"desc" => wp_kses_data( __("Select or upload image or write URL from other site for the background", 'junotoys') ),
						"readonly" => false,
						"value" => "",
						"type" => "media"
					),
					"bg_tile" => array(
						"title" => esc_html__("Tile background image", 'junotoys'),
						"desc" => wp_kses_data( __("Do you want tile background image or image cover whole column?", 'junotoys') ),
						"value" => "no",
						"dependency" => array(
							'bg_image' => array('not_empty')
						),
						"type" => "switch",
						"options" => junotoys_get_sc_param('yes_no')
					),
					"_content_" => array(
						"title" => esc_html__("Column item content", 'junotoys'),
						"desc" => wp_kses_data( __("Current column item content", 'junotoys') ),
						"divider" => true,
						"rows" => 4,
						"value" => "",
						"type" => "textarea"
					),
					"id" => junotoys_get_sc_param('id'),
					"class" => junotoys_get_sc_param('class'),
					"animation" => junotoys_get_sc_param('animation'),
					"css" => junotoys_get_sc_param('css')
				)
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'junotoys_sc_columns_reg_shortcodes_vc' ) ) {
	//add_action('junotoys_action_shortcodes_list_vc', 'junotoys_sc_columns_reg_shortcodes_vc');
	function junotoys_sc_columns_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_columns",
			"name" => esc_html__("Columns", 'junotoys'),
			"description" => wp_kses_data( __("Insert columns with margins", 'junotoys') ),
			"category" => esc_html__('Content', 'junotoys'),
			'icon' => 'icon_trx_columns',
			"class" => "trx_sc_columns",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => false,
			"as_parent" => array('only' => 'trx_column_item'),
			"params" => array(
				array(
					"param_name" => "count",
					"heading" => esc_html__("Columns count", 'junotoys'),
					"description" => wp_kses_data( __("Number of the columns in the container.", 'junotoys') ),
					"admin_label" => true,
					"value" => "2",
					"type" => "textfield"
				),
				array(
					"param_name" => "fluid",
					"heading" => esc_html__("Fluid columns", 'junotoys'),
					"description" => wp_kses_data( __("To squeeze the columns when reducing the size of the window (fluid=yes) or to rebuild them (fluid=no)", 'junotoys') ),
					"value" => array(esc_html__('Fluid columns', 'junotoys') => 'yes'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "equalheight",
					"heading" => esc_html__("Equal height", 'junotoys'),
					"description" => wp_kses_data( __("Make equal height for all columns in the row", 'junotoys') ),
					"value" => array("Equal height" => "yes" ),
					"type" => "checkbox"
				),
				array(
					"param_name" => "margins",
					"heading" => esc_html__("Margins between columns", 'junotoys'),
					"description" => wp_kses_data( __("Add margins between columns", 'junotoys') ),
					"std" => "yes",
					"value" => array(esc_html__('Disable margins between columns', 'junotoys') => 'no'),
					"type" => "checkbox"
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
			),
			'default_content' => '
				[trx_column_item][/trx_column_item]
				[trx_column_item][/trx_column_item]
			',
			'js_view' => 'VcTrxColumnsView'
		) );
		
		
		vc_map( array(
			"base" => "trx_column_item",
			"name" => esc_html__("Column", 'junotoys'),
			"description" => wp_kses_data( __("Column item", 'junotoys') ),
			"show_settings_on_create" => true,
			"class" => "trx_sc_collection trx_sc_column_item",
			"content_element" => true,
			"is_container" => true,
			'icon' => 'icon_trx_column_item',
			"as_child" => array('only' => 'trx_columns'),
			"as_parent" => array('except' => 'trx_columns'),
			"params" => array(
				array(
					"param_name" => "span",
					"heading" => esc_html__("Merge columns", 'junotoys'),
					"description" => wp_kses_data( __("Count merged columns from current", 'junotoys') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", 'junotoys'),
					"description" => wp_kses_data( __("Alignment text in the column", 'junotoys') ),
					"class" => "",
					"value" => array_flip(junotoys_get_sc_param('align')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Fore color", 'junotoys'),
					"description" => wp_kses_data( __("Any color for objects in this column", 'junotoys') ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_color",
					"heading" => esc_html__("Background color", 'junotoys'),
					"description" => wp_kses_data( __("Any background color for this column", 'junotoys') ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_image",
					"heading" => esc_html__("URL for background image file", 'junotoys'),
					"description" => wp_kses_data( __("Select or upload image or write URL from other site for the background", 'junotoys') ),
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "bg_tile",
					"heading" => esc_html__("Tile background image", 'junotoys'),
					"description" => wp_kses_data( __("Do you want tile background image or image cover whole column?", 'junotoys') ),
					"class" => "",
					'dependency' => array(
						'element' => 'bg_image',
						'not_empty' => true
					),
					"std" => "no",
					"value" => array(esc_html__('Tile background image', 'junotoys') => 'yes'),
					"type" => "checkbox"
				),
				junotoys_get_vc_param('id'),
				junotoys_get_vc_param('class'),
				junotoys_get_vc_param('animation'),
				junotoys_get_vc_param('css')
			),
			'js_view' => 'VcTrxColumnItemView'
		) );
		
		class WPBakeryShortCode_Trx_Columns extends JUNOTOYS_VC_ShortCodeColumns {}
		class WPBakeryShortCode_Trx_Column_Item extends JUNOTOYS_VC_ShortCodeCollection {}
	}
}
?>