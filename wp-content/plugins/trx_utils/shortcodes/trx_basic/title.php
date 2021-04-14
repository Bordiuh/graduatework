<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('junotoys_sc_title_theme_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_sc_title_theme_setup' );
	function junotoys_sc_title_theme_setup() {
		add_action('junotoys_action_shortcodes_list', 		'junotoys_sc_title_reg_shortcodes');
		if (function_exists('junotoys_exists_visual_composer') && junotoys_exists_visual_composer())
			add_action('junotoys_action_shortcodes_list_vc','junotoys_sc_title_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */


if (!function_exists('junotoys_sc_title')) {	
	function junotoys_sc_title($atts, $content=null){	
		if (junotoys_in_shortcode_blogger()) return '';
		extract(junotoys_html_decode(shortcode_atts(array(
			// Individual params
			"type" => "1",
			"style" => "regular",
			"align" => "",
			"font_weight" => "",
			"font_size" => "",
			"color" => "",
			"icon" => "",
			"image" => "",
			"picture" => "",
			"image_size" => "small",
			"position" => "left",
			"uppercase" => "no",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"width" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$css .= ($css ? ';' : '') . junotoys_get_css_position_from_values($top, $right, $bottom, $left);
		$css .= junotoys_get_css_dimensions_from_values($width)
			.($align && $align!='none' && !junotoys_param_is_inherit($align) ? 'text-align:' . esc_attr($align) .';' : '')
			.($color ? 'color:' . esc_attr($color) .';' : '')
			.($font_weight && !junotoys_param_is_inherit($font_weight) ? 'font-weight:' . esc_attr($font_weight) .';' : '')
			.($font_size   ? 'font-size:' . esc_attr($font_size) .';' : '')
			;
		$type = min(6, max(1, $type));
		if ($picture > 0) {
			$attach = wp_get_attachment_image_src( $picture, 'full' );
			if (isset($attach[0]) && $attach[0]!='')
				$picture = $attach[0];
		}
		$pic = $style!='iconed' 
			? '' 
			: '<span class="sc_title_icon sc_title_icon_'.esc_attr($position).'  sc_title_icon_'.esc_attr($image_size).($icon!='' && $icon!='none' ? ' '.esc_attr($icon) : '').'"'.'>'
				.($picture ? '<img src="'.esc_url($picture).'" alt="" />' : '')
				.(empty($picture) && $image && $image!='none' ? '<img src="'.esc_url(junotoys_strpos($image, 'http')===0 ? $image : junotoys_get_file_url('images/icons/'.($image).'.png')).'" alt="" />' : '')
				.'</span>';
		$output = '<h' . esc_attr($type) . ($id ? ' id="'.esc_attr($id).'"' : '')
				. ' class="sc_title sc_title_'.esc_attr($style)
					.($align && $align!='none' && !junotoys_param_is_inherit($align) ? ' sc_align_' . esc_attr($align) : '')
					.($uppercase == 'yes' ? ' text_uppercase' : '')
					.(!empty($class) ? ' '.esc_attr($class) : '')
					.'"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. (!junotoys_param_is_off($animation) ? ' data-animation="'.esc_attr(junotoys_get_animation_classes($animation)).'"' : '')
				. '>'
					. ($pic)
					. ($style=='divider' ? '<span class="sc_title_divider_before"'.($color ? ' style="background-color: '.esc_attr($color).'"' : '').'></span>' : '')
					. do_shortcode($content) 
					. ($style=='divider' ? '<span class="sc_title_divider_after"'.($color ? ' style="background-color: '.esc_attr($color).'"' : '').'></span>' : '')
				. '</h' . esc_attr($type) . '>';
		return apply_filters('junotoys_shortcode_output', $output, 'trx_title', $atts, $content);
	}
	junotoys_require_shortcode('trx_title', 'junotoys_sc_title');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'junotoys_sc_title_reg_shortcodes' ) ) {
	//add_action('junotoys_action_shortcodes_list', 'junotoys_sc_title_reg_shortcodes');
	function junotoys_sc_title_reg_shortcodes() {
	
		junotoys_sc_map("trx_title", array(
			"title" => esc_html__("Title", 'junotoys'),
			"desc" => wp_kses_data( __("Create header tag (1-6 level) with many styles", 'junotoys') ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"_content_" => array(
					"title" => esc_html__("Title content", 'junotoys'),
					"desc" => wp_kses_data( __("Title content", 'junotoys') ),
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
				),
				"type" => array(
					"title" => esc_html__("Title type", 'junotoys'),
					"desc" => wp_kses_data( __("Title type (header level)", 'junotoys') ),
					"divider" => true,
					"value" => "1",
					"type" => "select",
					"options" => array(
						'1' => esc_html__('Header 1', 'junotoys'),
						'2' => esc_html__('Header 2', 'junotoys'),
						'3' => esc_html__('Header 3', 'junotoys'),
						'4' => esc_html__('Header 4', 'junotoys'),
						'5' => esc_html__('Header 5', 'junotoys'),
						'6' => esc_html__('Header 6', 'junotoys'),
					)
				),
				"style" => array(
					"title" => esc_html__("Title style", 'junotoys'),
					"desc" => wp_kses_data( __("Title style", 'junotoys') ),
					"value" => "regular",
					"type" => "select",
					"options" => array(
						'regular' => esc_html__('Regular', 'junotoys'),
						'underline' => esc_html__('Underline', 'junotoys'),
						'divider' => esc_html__('Divider', 'junotoys'),
						'iconed' => esc_html__('With icon (image)', 'junotoys')
					)
				),
				"align" => array(
					"title" => esc_html__("Alignment", 'junotoys'),
					"desc" => wp_kses_data( __("Title text alignment", 'junotoys') ),
					"value" => "",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => junotoys_get_sc_param('align')
				), 
				"font_size" => array(
					"title" => esc_html__("Font_size", 'junotoys'),
					"desc" => wp_kses_data( __("Custom font size. If empty - use theme default", 'junotoys') ),
					"value" => "",
					"type" => "text"
				),
				"font_weight" => array(
					"title" => esc_html__("Font weight", 'junotoys'),
					"desc" => wp_kses_data( __("Custom font weight. If empty or inherit - use theme default", 'junotoys') ),
					"value" => "",
					"type" => "select",
					"size" => "medium",
					"options" => array(
						'inherit' => esc_html__('Default', 'junotoys'),
						'100' => esc_html__('Thin (100)', 'junotoys'),
						'300' => esc_html__('Light (300)', 'junotoys'),
						'400' => esc_html__('Normal (400)', 'junotoys'),
						'600' => esc_html__('Semibold (600)', 'junotoys'),
						'700' => esc_html__('Bold (700)', 'junotoys'),
						'900' => esc_html__('Black (900)', 'junotoys')
					)
				),
				"uppercase" => array(
					"title" => esc_html__("Uppercase", 'junotoys'),
					"desc" => wp_kses_data( __("Transform text in uppercase", 'junotoys') ),
					"divider" => true,
					"value" => "no",
					"type" => "switch",
					"options" => junotoys_get_sc_param('yes_no')
				),
				"color" => array(
					"title" => esc_html__("Title color", 'junotoys'),
					"desc" => wp_kses_data( __("Select color for the title", 'junotoys') ),
					"value" => "",
					"type" => "color"
				),
				"icon" => array(
					"title" => esc_html__('Title font icon',  'junotoys'),
					"desc" => wp_kses_data( __("Select font icon for the title from Fontello icons set (if style=iconed)",  'junotoys') ),
					"dependency" => array(
						'style' => array('iconed')
					),
					"value" => "",
					"type" => "icons",
					"options" => junotoys_get_sc_param('icons')
				),
				"image" => array(
					"title" => esc_html__('or image icon',  'junotoys'),
					"desc" => wp_kses_data( __("Select image icon for the title instead icon above (if style=iconed)",  'junotoys') ),
					"dependency" => array(
						'style' => array('iconed')
					),
					"value" => "",
					"type" => "images",
					"size" => "small",
					"options" => junotoys_get_sc_param('images')
				),
				"picture" => array(
					"title" => esc_html__('or URL for image file', 'junotoys'),
					"desc" => wp_kses_data( __("Select or upload image or write URL from other site (if style=iconed)", 'junotoys') ),
					"dependency" => array(
						'style' => array('iconed')
					),
					"readonly" => false,
					"value" => "",
					"type" => "media"
				),
				"image_size" => array(
					"title" => esc_html__('Image (picture) size', 'junotoys'),
					"desc" => wp_kses_data( __("Select image (picture) size (if style='iconed')", 'junotoys') ),
					"dependency" => array(
						'style' => array('iconed')
					),
					"value" => "small",
					"type" => "checklist",
					"options" => array(
						'small' => esc_html__('Small', 'junotoys'),
						'medium' => esc_html__('Medium', 'junotoys'),
						'large' => esc_html__('Large', 'junotoys')
					)
				),
				"position" => array(
					"title" => esc_html__('Icon (image) position', 'junotoys'),
					"desc" => wp_kses_data( __("Select icon (image) position (if style=iconed)", 'junotoys') ),
					"dependency" => array(
						'style' => array('iconed')
					),
					"value" => "left",
					"type" => "checklist",
					"options" => array(
						'top' => esc_html__('Top', 'junotoys'),
						'left' => esc_html__('Left', 'junotoys')
					)
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
if ( !function_exists( 'junotoys_sc_title_reg_shortcodes_vc' ) ) {
	//add_action('junotoys_action_shortcodes_list_vc', 'junotoys_sc_title_reg_shortcodes_vc');
	function junotoys_sc_title_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_title",
			"name" => esc_html__("Title", 'junotoys'),
			"description" => wp_kses_data( __("Create header tag (1-6 level) with many styles", 'junotoys') ),
			"category" => esc_html__('Content', 'junotoys'),
			'icon' => 'icon_trx_title',
			"class" => "trx_sc_single trx_sc_title",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "content",
					"heading" => esc_html__("Title content", 'junotoys'),
					"description" => wp_kses_data( __("Title content", 'junotoys') ),
					"class" => "",
					"value" => "",
					"type" => "textarea_html"
				),
				array(
					"param_name" => "type",
					"heading" => esc_html__("Title type", 'junotoys'),
					"description" => wp_kses_data( __("Title type (header level)", 'junotoys') ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
						esc_html__('Header 1', 'junotoys') => '1',
						esc_html__('Header 2', 'junotoys') => '2',
						esc_html__('Header 3', 'junotoys') => '3',
						esc_html__('Header 4', 'junotoys') => '4',
						esc_html__('Header 5', 'junotoys') => '5',
						esc_html__('Header 6', 'junotoys') => '6'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "style",
					"heading" => esc_html__("Title style", 'junotoys'),
					"description" => wp_kses_data( __("Title style: only text (regular) or with icon/image (iconed)", 'junotoys') ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
						esc_html__('Regular', 'junotoys') => 'regular',
						esc_html__('Underline', 'junotoys') => 'underline',
						esc_html__('Divider', 'junotoys') => 'divider',
						esc_html__('With icon (image)', 'junotoys') => 'iconed'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", 'junotoys'),
					"description" => wp_kses_data( __("Title text alignment", 'junotoys') ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(junotoys_get_sc_param('align')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "font_size",
					"heading" => esc_html__("Font size", 'junotoys'),
					"description" => wp_kses_data( __("Custom font size. If empty - use theme default", 'junotoys') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "font_weight",
					"heading" => esc_html__("Font weight", 'junotoys'),
					"description" => wp_kses_data( __("Custom font weight. If empty or inherit - use theme default", 'junotoys') ),
					"class" => "",
					"value" => array(
						esc_html__('Default', 'junotoys') => 'inherit',
						esc_html__('Thin (100)', 'junotoys') => '100',
						esc_html__('Light (300)', 'junotoys') => '300',
						esc_html__('Normal (400)', 'junotoys') => '400',
						esc_html__('Semibold (600)', 'junotoys') => '600',
						esc_html__('Bold (700)', 'junotoys') => '700',
						esc_html__('Black (900)', 'junotoys') => '900'
					),
					"type" => "dropdown"
				),
				
				array(
					"param_name" => "uppercase",
					"heading" => esc_html__("Uppercase", 'junotoys'),
					"description" => wp_kses_data( __("Transform text in uppercase", 'junotoys') ),
					"class" => "",
					"std" => 'no',
					"value" => array(esc_html__('Yes', 'junotoys') => 'yes'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Title color", 'junotoys'),
					"description" => wp_kses_data( __("Select color for the title", 'junotoys') ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Title font icon", 'junotoys'),
					"description" => wp_kses_data( __("Select font icon for the title from Fontello icons set (if style=iconed)", 'junotoys') ),
					"class" => "",
					"group" => esc_html__('Icon &amp; Image', 'junotoys'),
					'dependency' => array(
						'element' => 'style',
						'value' => array('iconed')
					),
					"value" => junotoys_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "image",
					"heading" => esc_html__("or image icon", 'junotoys'),
					"description" => wp_kses_data( __("Select image icon for the title instead icon above (if style=iconed)", 'junotoys') ),
					"class" => "",
					"group" => esc_html__('Icon &amp; Image', 'junotoys'),
					'dependency' => array(
						'element' => 'style',
						'value' => array('iconed')
					),
					"value" => junotoys_get_sc_param('images'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "picture",
					"heading" => esc_html__("or select uploaded image", 'junotoys'),
					"description" => wp_kses_data( __("Select or upload image or write URL from other site (if style=iconed)", 'junotoys') ),
					"group" => esc_html__('Icon &amp; Image', 'junotoys'),
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "image_size",
					"heading" => esc_html__("Image (picture) size", 'junotoys'),
					"description" => wp_kses_data( __("Select image (picture) size (if style=iconed)", 'junotoys') ),
					"group" => esc_html__('Icon &amp; Image', 'junotoys'),
					"class" => "",
					"value" => array(
						esc_html__('Small', 'junotoys') => 'small',
						esc_html__('Medium', 'junotoys') => 'medium',
						esc_html__('Large', 'junotoys') => 'large'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "position",
					"heading" => esc_html__("Icon (image) position", 'junotoys'),
					"description" => wp_kses_data( __("Select icon (image) position (if style=iconed)", 'junotoys') ),
					"group" => esc_html__('Icon &amp; Image', 'junotoys'),
					"class" => "",
					"std" => "left",
					"value" => array(
						esc_html__('Top', 'junotoys') => 'top',
						esc_html__('Left', 'junotoys') => 'left'
					),
					"type" => "dropdown"
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
			'js_view' => 'VcTrxTextView'
		) );
		
		class WPBakeryShortCode_Trx_Title extends JUNOTOYS_VC_ShortCodeSingle {}
	}
}
?>