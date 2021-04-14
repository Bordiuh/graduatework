<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('junotoys_sc_promo_theme_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_sc_promo_theme_setup' );
	function junotoys_sc_promo_theme_setup() {
		add_action('junotoys_action_shortcodes_list', 		'junotoys_sc_promo_reg_shortcodes');
		if (function_exists('junotoys_exists_visual_composer') && junotoys_exists_visual_composer())
			add_action('junotoys_action_shortcodes_list_vc','junotoys_sc_promo_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */


if (!function_exists('junotoys_sc_promo')) {	
	function junotoys_sc_promo($atts, $content=null){	
		if (junotoys_in_shortcode_blogger()) return '';
		extract(junotoys_html_decode(shortcode_atts(array(
			// Individual params
			"style" => 1,
			"align" => "none",
			"image" => "",
			"bg_color" => "",
			"icon" => "",
			"scheme" => "",
			"title" => "",
			"subtitle" => "",
			"description" => "",
			"link" => '',
			"link_caption" => esc_html__('Read more', 'junotoys'),
			"link2" => '',
			"link2_caption" => '',
			"url" => "",
			"position" => "",
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
	
		if ($image > 0) {
			$attach = wp_get_attachment_image_src($image, 'full');
			if (isset($attach[0]) && $attach[0]!='')
				$image = $attach[0];
		}
		
		$width  = junotoys_prepare_css_value($width);
		$height = junotoys_prepare_css_value($height);
		
		$css .= ($css ? ' ;' : '') . junotoys_get_css_position_from_values($top, $right, $bottom, $left);
		$css .= junotoys_get_css_dimensions_from_values($width,$height);
		$css .= ($image ? 'background: url('.$image.');' : '');
		$css .= ($bg_color ? 'background-color: '.$bg_color.';' : '');
		$button_style = 'type="round" size="large" bg_color="2"';
		if($style==1) $button_style = 'type="round" size="small" bg_color="5"';
		if($style==4) $button_style = 'type="round" size="medium" bg_color="5"';
		
		$buttons = (!empty($link) || !empty($link2) 
						? '<div class="sc_promo_buttons sc_item_buttons">'
							. (!empty($link) 
								? '<div class="sc_promo_button sc_item_button">'.do_shortcode('[trx_button link="'.esc_url($link).'" '.$button_style.']'.esc_html($link_caption).'[/trx_button]').'</div>' 
								: '')
							. (!empty($link2) && $style==2 
								? '<div class="sc_promo_button sc_item_button">'.do_shortcode('[trx_button link="'.esc_url($link2).'" '.$button_style.']'.esc_html($link2_caption).'[/trx_button]').'</div>' 
								: '')
							. '</div>'
						: '');
						
		$output = '<div '.(!empty($url) ? ' data-href="'.esc_url($url).'"' : '') 
					. ($id ? ' id="'.esc_attr($id).'"' : '') 
					. ' class="sc_promo'.($style ? ' style_' . esc_attr($style) : '')
						. ($class ? ' ' . esc_attr($class) : '') 
						. ($position && $style==1 ? ' sc_promo_position_' . esc_attr($position) : '') 
						. ($style==5 ? ' small_padding' : '') 
						. ($scheme && !junotoys_param_is_off($scheme) && !junotoys_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '') 
						. ($align && $align!='none' ? ' align'.esc_attr($align) : '') 
						. '"'
					. (!junotoys_param_is_off($animation) ? ' data-animation="'.esc_attr(junotoys_get_animation_classes($animation)).'"' : '')
					. ($css ? ' style="'.esc_attr($css).'"' : '')
					.'>' 
					. '<div class="sc_promo_inner '.($style ? ' sc_promo_style_' . esc_attr($style) : '').'">'
						. (!empty($icon) && $style==5 ? '<div class="sc_promo_icon '.esc_attr($icon).'"></div>' : '')
						. '<div class="sc_promo_content">'
							. (!empty($subtitle) && $style!=4 && $style!=5 ? '<h3 class="sc_promo_subtitle">' . trim(junotoys_strmacros($subtitle)) . '</h3>' : '')
							. (!empty($title) ? '<h2 class="sc_promo_title">' . trim(junotoys_strmacros($title)) . '</h2>' : '')
							. (!empty($description) && $style!=1 ? '<div class="sc_promo_descr">' . trim(junotoys_strmacros($description)) . '</div>' : '')
							. ($style!= 5 ? $buttons : '')
						. '</div>'
					. '</div>'
				.'</div>';
	
	
	
		return apply_filters('junotoys_shortcode_output', $output, 'trx_promo', $atts, $content);
	}
	junotoys_require_shortcode('trx_promo', 'junotoys_sc_promo');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'junotoys_sc_promo_reg_shortcodes' ) ) {
	//add_action('junotoys_action_shortcodes_list', 'junotoys_sc_promo_reg_shortcodes');
	function junotoys_sc_promo_reg_shortcodes() {
	
		junotoys_sc_map("trx_promo", array(
			"title" => esc_html__("Promo", 'junotoys'),
			"desc" => wp_kses_data( __("Insert promo diagramm in your page (post)", 'junotoys') ),
			"decorate" => true,
			"container" => false,
			"params" => array(
				"style" => array(
					"title" => esc_html__("Style", 'junotoys'),
					"desc" => wp_kses_data( __("Select style to display block", 'junotoys') ),
					"value" => "1",
					"type" => "checklist",
					"options" => junotoys_get_list_styles(1, 5)
				),
				"align" => array(
					"title" => esc_html__("Alignment of the promo block", 'junotoys'),
					"desc" => wp_kses_data( __("Align whole promo block to left or right side of the page or parent container", 'junotoys') ),
					"value" => "",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => junotoys_get_sc_param('float')
				), 
				"image" => array(
					"title" => esc_html__("Image URL", 'junotoys'),
					"desc" => wp_kses_data( __("Select the promo image from the library for this section", 'junotoys') ),
					"readonly" => false,
					"value" => "",
					"type" => "media"
				),
				"bg_color" => array(
					"title" => esc_html__("Background color", 'junotoys'),
					"desc" => wp_kses_data( __("Select background color for the promo", 'junotoys') ),
					"value" => "",
					"type" => "color"
				),
				"icon" => array(
					"title" => esc_html__('Promo icon',  'junotoys'),
					"desc" => wp_kses_data( __("Select icon from Fontello icons set",  'junotoys') ),
					"dependency" => array(
						'style' => array(5)
					),
					"value" => "",
					"type" => "icons",
					"options" => junotoys_get_sc_param('icons')
				),
				"position" => array(
					"title" => esc_html__('Content position', 'junotoys'),
					"desc" => wp_kses_data( __("Select content position", 'junotoys') ),
					"dependency" => array(
						'style' => array(1)
					),
					"value" => "top_left",
					"type" => "checklist",
					"options" => array(
						'top_left' => esc_html__('Top Left', 'junotoys'),
						'top_right' => esc_html__('Top Right', 'junotoys'),
						'bottom_right' => esc_html__('Bottom Right', 'junotoys'),
						'bottom_left' => esc_html__('Bottom Left', 'junotoys')
					)
				),
				"subtitle" => array(
					"title" => esc_html__("Subtitle", 'junotoys'),
					"desc" => wp_kses_data( __("Subtitle for the block", 'junotoys') ),
					"divider" => true,
					"dependency" => array(
						'style' => array(1,2,3)
					),
					"value" => "",
					"type" => "text"
				),
				"title" => array(
					"title" => esc_html__("Title", 'junotoys'),
					"desc" => wp_kses_data( __("Title for the block", 'junotoys') ),
					"value" => "",
					"type" => "textarea"
				),
				"description" => array(
					"title" => esc_html__("Description", 'junotoys'),
					"desc" => wp_kses_data( __("Short description for the block", 'junotoys') ),
					"dependency" => array(
						'style' => array(2,3,4,5),
					),
					"value" => "",
					"type" => "textarea"
				),
				"link" => array(
					"title" => esc_html__("Button URL", 'junotoys'),
					"desc" => wp_kses_data( __("Link URL for the button at the bottom of the block", 'junotoys') ),
					"dependency" => array(
						'style' => array(1,2,3,4),
					),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
				"link_caption" => array(
					"title" => esc_html__("Button caption", 'junotoys'),
					"desc" => wp_kses_data( __("Caption for the button at the bottom of the block", 'junotoys') ),
					"dependency" => array(
						'style' => array(1,2,3,4),
					),
					"value" => "",
					"type" => "text"
				),
				"link2" => array(
					"title" => esc_html__("Button 2 URL", 'junotoys'),
					"desc" => wp_kses_data( __("Link URL for the second button at the bottom of the block", 'junotoys') ),
					"dependency" => array(
						'style' => array(2)
					),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
				"link2_caption" => array(
					"title" => esc_html__("Button 2 caption", 'junotoys'),
					"desc" => wp_kses_data( __("Caption for the second button at the bottom of the block", 'junotoys') ),
					"dependency" => array(
						'style' => array(2)
					),
					"value" => "",
					"type" => "text"
				),
				"url" => array(
					"title" => esc_html__("Link", 'junotoys'),
					"desc" => wp_kses_data( __("Link of the promo block", 'junotoys') ),
					"value" => "",
					"type" => "text"
				),
				"scheme" => array(
					"title" => esc_html__("Color scheme", 'junotoys'),
					"desc" => wp_kses_data( __("Select color scheme for the section with text", 'junotoys') ),
					"value" => "",
					"type" => "checklist",
					"options" => junotoys_get_sc_param('schemes')
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
if ( !function_exists( 'junotoys_sc_promo_reg_shortcodes_vc' ) ) {
	//add_action('junotoys_action_shortcodes_list_vc', 'junotoys_sc_promo_reg_shortcodes_vc');
	function junotoys_sc_promo_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_promo",
			"name" => esc_html__("Promo", 'junotoys'),
			"description" => wp_kses_data( __("Insert promo block", 'junotoys') ),
			"category" => esc_html__('Content', 'junotoys'),
			'icon' => 'icon_trx_promo',
			"class" => "trx_sc_single trx_sc_promo",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "style",
					"heading" => esc_html__("Block's style", 'junotoys'),
					"description" => wp_kses_data( __("Select style to display this block", 'junotoys') ),
					"class" => "",
					"admin_label" => true,
					"value" => array_flip(junotoys_get_list_styles(1, 5)),
					"type" => "dropdown"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment of the promo block", 'junotoys'),
					"description" => wp_kses_data( __("Align whole promo block to left or right side of the page or parent container", 'junotoys') ),
					"class" => "",
					"std" => 'none',
					"value" => array_flip(junotoys_get_sc_param('float')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "image",
					"heading" => esc_html__("Image URL", 'junotoys'),
					"description" => wp_kses_data( __("Select the promo image from the library for this section", 'junotoys') ),
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "bg_color",
					"heading" => esc_html__("Background color", 'junotoys'),
					"description" => wp_kses_data( __("Select background color for the promo", 'junotoys') ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Promo icon", 'junotoys'),
					"description" => wp_kses_data( __("Select promo icon from Fontello icons set (if style=iconed)", 'junotoys') ),
					"class" => "",
					'dependency' => array(
						'element' => 'style',
						'value' => array('5')
					),
					"value" => junotoys_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "position",
					"heading" => esc_html__("Content position", 'junotoys'),
					"description" => wp_kses_data( __("Select content position", 'junotoys') ),
					"class" => "",
					"admin_label" => true,
					"value" => array(
						esc_html__('Top Left', 'junotoys') => 'top_left',
						esc_html__('Top Right', 'junotoys') => 'top_right',
						esc_html__('Bottom Right', 'junotoys') => 'bottom_right',
						esc_html__('Bottom Left', 'junotoys') => 'bottom_left'
					),
					'dependency' => array(
						'element' => 'style',
						'value' => array('1')
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "subtitle",
					"heading" => esc_html__("Subtitle", 'junotoys'),
					"description" => wp_kses_data( __("Subtitle for the block", 'junotoys') ),
					'dependency' => array(
						'element' => 'style',
						'value' => array('1','2','3')
					),
					"group" => esc_html__('Captions', 'junotoys'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", 'junotoys'),
					"description" => wp_kses_data( __("Title for the block", 'junotoys') ),
					"group" => esc_html__('Captions', 'junotoys'),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textarea"
				),
				array(
					"param_name" => "description",
					"heading" => esc_html__("Description", 'junotoys'),
					"description" => wp_kses_data( __("Description for the block", 'junotoys') ),
					"group" => esc_html__('Captions', 'junotoys'),
					'dependency' => array(
						'element' => 'style',
						'value' => array('2','3','4','5')
					),
					"class" => "",
					"value" => "",
					"type" => "textarea"
				),
				array(
					"param_name" => "link",
					"heading" => esc_html__("Button URL", 'junotoys'),
					"description" => wp_kses_data( __("Link URL for the button at the bottom of the block", 'junotoys') ),
					"group" => esc_html__('Captions', 'junotoys'),
					'dependency' => array(
						'element' => 'style',
						'value' => array('1','2','3','4')
					),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "link_caption",
					"heading" => esc_html__("Button caption", 'junotoys'),
					"description" => wp_kses_data( __("Caption for the button at the bottom of the block", 'junotoys') ),
					"group" => esc_html__('Captions', 'junotoys'),
					'dependency' => array(
						'element' => 'style',
						'value' => array('1','2','3','4')
					),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "link2",
					"heading" => esc_html__("Button 2 URL", 'junotoys'),
					"description" => wp_kses_data( __("Link URL for the second button at the bottom of the block", 'junotoys') ),
					"group" => esc_html__('Captions', 'junotoys'),
					'dependency' => array(
						'element' => 'style',
						'value' => array('2')
					),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "link2_caption",
					"heading" => esc_html__("Button 2 caption", 'junotoys'),
					"description" => wp_kses_data( __("Caption for the second button at the bottom of the block", 'junotoys') ),
					"group" => esc_html__('Captions', 'junotoys'),
					'dependency' => array(
						'element' => 'style',
						'value' => array('2')
					),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "url",
					"heading" => esc_html__("Link", 'junotoys'),
					"description" => wp_kses_data( __("Link of the promo block", 'junotoys') ),
					"value" => '',
					"type" => "textfield"
				),
				array(
					"param_name" => "scheme",
					"heading" => esc_html__("Color scheme", 'junotoys'),
					"description" => wp_kses_data( __("Select color scheme for the section with text", 'junotoys') ),
					"class" => "",
					"value" => array_flip(junotoys_get_sc_param('schemes')),
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
		
		class WPBakeryShortCode_Trx_Promo extends JUNOTOYS_VC_ShortCodeSingle {}
	}
}
?>