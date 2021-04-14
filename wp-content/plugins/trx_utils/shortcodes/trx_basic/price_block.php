<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('junotoys_sc_price_block_theme_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_sc_price_block_theme_setup' );
	function junotoys_sc_price_block_theme_setup() {
		add_action('junotoys_action_shortcodes_list', 		'junotoys_sc_price_block_reg_shortcodes');
		if (function_exists('junotoys_exists_visual_composer') && junotoys_exists_visual_composer())
			add_action('junotoys_action_shortcodes_list_vc','junotoys_sc_price_block_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

if (!function_exists('junotoys_sc_price_block')) {	
	function junotoys_sc_price_block($atts, $content=null){	
		if (junotoys_in_shortcode_blogger()) return '';
		extract(junotoys_html_decode(shortcode_atts(array(
			// Individual params
			"style" => 1,
			"title" => "",
			"link" => "",
			"link_text" => "",
			"icon" => "",
			"money" => "",
			"currency" => "$",
			"period" => "",
			"align" => "",
			"scheme" => "",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"width" => "",
			"height" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$output = '';
		$css .= ($css ? ';' : '') . junotoys_get_css_position_from_values($top, $right, $bottom, $left);
		$css .= junotoys_get_css_dimensions_from_values($width, $height);
		if ($money) $money = do_shortcode('[trx_price money="'.esc_attr($money).'" period="'.esc_attr($period).'"'.($currency ? ' currency="'.esc_attr($currency).'"' : '').']');
		$content = do_shortcode(junotoys_sc_clear_around($content));
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
					. ' class="sc_price_block sc_price_block_style_'.max(1, min(3, $style))
						. (!empty($class) ? ' '.esc_attr($class) : '')
						. ($scheme && !junotoys_param_is_off($scheme) && !junotoys_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '') 
						. ($align && $align!='none' ? ' align'.esc_attr($align) : '') 
						. '"'
					. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
					. (!junotoys_param_is_off($animation) ? ' data-animation="'.esc_attr(junotoys_get_animation_classes($animation)).'"' : '')
					. '>'
				. (!empty($title) ? '<div class="sc_price_block_title"><span>'.($title).'</span></div>' : '')
				. '<div class="sc_price_block_money">'
					. (!empty($icon) ? '<div class="sc_price_block_icon '.esc_attr($icon).'"></div>' : '')
					. ($money)
				. '</div>'
				. (!empty($content) ? '<div class="sc_price_block_description">'.($content).'</div>' : '')
				. (!empty($link_text) ? '<div class="sc_price_block_link">'.do_shortcode('[trx_button link="'.($link ? esc_url($link) : '#').'" size="medium" type="round" bg_color="5"]'.($link_text).'[/trx_button]').'</div>' : '')
			. '</div>';
		return apply_filters('junotoys_shortcode_output', $output, 'trx_price_block', $atts, $content);
	}
	junotoys_require_shortcode('trx_price_block', 'junotoys_sc_price_block');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'junotoys_sc_price_block_reg_shortcodes' ) ) {
	//add_action('junotoys_action_shortcodes_list', 'junotoys_sc_price_block_reg_shortcodes');
	function junotoys_sc_price_block_reg_shortcodes() {
	
		junotoys_sc_map("trx_price_block", array(
			"title" => esc_html__("Price block", 'junotoys'),
			"desc" => wp_kses_data( __("Insert price block with title, price and description", 'junotoys') ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"style" => array(
					"title" => esc_html__("Block style", 'junotoys'),
					"desc" => wp_kses_data( __("Select style for this price block", 'junotoys') ),
					"value" => 1,
					"options" => junotoys_get_list_styles(1, 3),
					"type" => "checklist"
				),
				"title" => array(
					"title" => esc_html__("Title", 'junotoys'),
					"desc" => wp_kses_data( __("Block title", 'junotoys') ),
					"value" => "",
					"type" => "text"
				),
				"link" => array(
					"title" => esc_html__("Link URL", 'junotoys'),
					"desc" => wp_kses_data( __("URL for link from button (at bottom of the block)", 'junotoys') ),
					"value" => "",
					"type" => "text"
				),
				"link_text" => array(
					"title" => esc_html__("Link text", 'junotoys'),
					"desc" => wp_kses_data( __("Text (caption) for the link button (at bottom of the block). If empty - button not showed", 'junotoys') ),
					"value" => "",
					"type" => "text"
				),
				"icon" => array(
					"title" => esc_html__("Icon",  'junotoys'),
					"desc" => wp_kses_data( __('Select icon from Fontello icons set (placed before/instead price)',  'junotoys') ),
					"value" => "",
					"type" => "icons",
					"options" => junotoys_get_sc_param('icons')
				),
				"money" => array(
					"title" => esc_html__("Money", 'junotoys'),
					"desc" => wp_kses_data( __("Money value (dot or comma separated)", 'junotoys') ),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
				"currency" => array(
					"title" => esc_html__("Currency", 'junotoys'),
					"desc" => wp_kses_data( __("Currency character", 'junotoys') ),
					"value" => "$",
					"type" => "text"
				),
				"period" => array(
					"title" => esc_html__("Period", 'junotoys'),
					"desc" => wp_kses_data( __("Period text (if need). For example: monthly, daily, etc.", 'junotoys') ),
					"value" => "",
					"type" => "text"
				),
				"scheme" => array(
					"title" => esc_html__("Color scheme", 'junotoys'),
					"desc" => wp_kses_data( __("Select color scheme for this block", 'junotoys') ),
					"value" => "",
					"type" => "checklist",
					"options" => junotoys_get_sc_param('schemes')
				),
				"align" => array(
					"title" => esc_html__("Alignment", 'junotoys'),
					"desc" => wp_kses_data( __("Align price to left or right side", 'junotoys') ),
					"divider" => true,
					"value" => "",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => junotoys_get_sc_param('float')
				), 
				"_content_" => array(
					"title" => esc_html__("Description", 'junotoys'),
					"desc" => wp_kses_data( __("Description for this price block", 'junotoys') ),
					"divider" => true,
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
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
if ( !function_exists( 'junotoys_sc_price_block_reg_shortcodes_vc' ) ) {
	//add_action('junotoys_action_shortcodes_list_vc', 'junotoys_sc_price_block_reg_shortcodes_vc');
	function junotoys_sc_price_block_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_price_block",
			"name" => esc_html__("Price block", 'junotoys'),
			"description" => wp_kses_data( __("Insert price block with title, price and description", 'junotoys') ),
			"category" => esc_html__('Content', 'junotoys'),
			'icon' => 'icon_trx_price_block',
			"class" => "trx_sc_single trx_sc_price_block",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "style",
					"heading" => esc_html__("Block style", 'junotoys'),
					"desc" => wp_kses_data( __("Select style of this price block", 'junotoys') ),
					"admin_label" => true,
					"class" => "",
					"std" => 1,
					"value" => array_flip(junotoys_get_list_styles(1, 3)),
					"type" => "dropdown"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", 'junotoys'),
					"description" => wp_kses_data( __("Block title", 'junotoys') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "link",
					"heading" => esc_html__("Link URL", 'junotoys'),
					"description" => wp_kses_data( __("URL for link from button (at bottom of the block)", 'junotoys') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "link_text",
					"heading" => esc_html__("Link text", 'junotoys'),
					"description" => wp_kses_data( __("Text (caption) for the link button (at bottom of the block). If empty - button not showed", 'junotoys') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Icon", 'junotoys'),
					"description" => wp_kses_data( __("Select icon from Fontello icons set (placed before/instead price)", 'junotoys') ),
					"class" => "",
					"value" => junotoys_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "money",
					"heading" => esc_html__("Money", 'junotoys'),
					"description" => wp_kses_data( __("Money value (dot or comma separated)", 'junotoys') ),
					"admin_label" => true,
					"group" => esc_html__('Money', 'junotoys'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "currency",
					"heading" => esc_html__("Currency symbol", 'junotoys'),
					"description" => wp_kses_data( __("Currency character", 'junotoys') ),
					"admin_label" => true,
					"group" => esc_html__('Money', 'junotoys'),
					"class" => "",
					"value" => "$",
					"type" => "textfield"
				),
				array(
					"param_name" => "period",
					"heading" => esc_html__("Period", 'junotoys'),
					"description" => wp_kses_data( __("Period text (if need). For example: monthly, daily, etc.", 'junotoys') ),
					"admin_label" => true,
					"group" => esc_html__('Money', 'junotoys'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "scheme",
					"heading" => esc_html__("Color scheme", 'junotoys'),
					"description" => wp_kses_data( __("Select color scheme for this block", 'junotoys') ),
					"group" => esc_html__('Colors and Images', 'junotoys'),
					"class" => "",
					"value" => array_flip(junotoys_get_sc_param('schemes')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", 'junotoys'),
					"description" => wp_kses_data( __("Align price to left or right side", 'junotoys') ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(junotoys_get_sc_param('float')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "content",
					"heading" => esc_html__("Description", 'junotoys'),
					"description" => wp_kses_data( __("Description for this price block", 'junotoys') ),
					"class" => "",
					"value" => "",
					"type" => "textarea_html"
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
			'js_view' => 'VcTrxTextView'
		) );
		
		class WPBakeryShortCode_Trx_PriceBlock extends JUNOTOYS_VC_ShortCodeSingle {}
	}
}
?>