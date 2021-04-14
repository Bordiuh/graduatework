<?php
/**
 * Theme Widget: Banner
 */

// Theme init
if (!function_exists('junotoys_widget_banner_theme_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_widget_banner_theme_setup', 1 );
	function junotoys_widget_banner_theme_setup() {

		// Register shortcodes in the shortcodes list
		if (function_exists('junotoys_exists_visual_composer') && junotoys_exists_visual_composer())
			add_action('junotoys_action_shortcodes_list_vc','junotoys_widget_banner_reg_shortcodes_vc');
	}
}

// Load widget
if (!function_exists('junotoys_widget_banner_load')) {
	add_action( 'widgets_init', 'junotoys_widget_banner_load' );
	function junotoys_widget_banner_load() {
		register_widget( 'junotoys_widget_banner' );
	}
}

// Widget Class
class junotoys_widget_banner extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_banner', 'description' => esc_html__('Banner', 'junotoys') );
		parent::__construct( 'junotoys_widget_banner', esc_html__('ThemeREX - Banner', 'junotoys'), $widget_ops );
	}

	// Show widget
	function widget( $args, $instance ) {
		
		extract( $args );

		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '' );
		$banner_image = isset($instance['banner_image']) ? $instance['banner_image'] : '';
		$banner_link = isset($instance['banner_link']) ? $instance['banner_link'] : '';
		$banner_code = isset($instance['banner_code']) ? $instance['banner_code'] : '';

		// Before widget (defined by themes)
		junotoys_show_layout($before_widget);

		// Display the widget title if one was input (before and after defined by themes)
		if ($title) junotoys_show_layout($before_title . $title . $after_title);
		?>			
		<div class="widget_banner_inner">
			<?php
			if ($banner_image!='') {
				if ((int) $banner_image > 0) {
					$attach = wp_get_attachment_image_src( $banner_image, 'full' );
					if (isset($attach[0]) && $attach[0]!='')
						$banner_image = $attach[0];
				}
				$attr = junotoys_getimagesize($banner_image);
				echo (!empty($banner_link) ? '<a href="' . esc_url($banner_link) . '"' : '<span') . ' class="image_wrap"><img src="' . esc_url($banner_image) . '" alt="' . esc_attr($title) . '"'.(!empty($attr[3]) ? ' '.trim($attr[3]) : '').'>' . (!empty($banner_link) ? '</a>': '</span>');
			}
			if ($banner_code!='') {
				echo force_balance_tags(junotoys_substitute_all($banner_code));
			}
			?>
		</div>
		<?php
		
		// After widget (defined by themes)
		junotoys_show_layout($after_widget);
	}

	// Update the widget settings.
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['banner_image'] = strip_tags( $new_instance['banner_image'] );
		$instance['banner_link'] = strip_tags( $new_instance['banner_link'] );
		$instance['banner_code'] = $new_instance['banner_code'];
		return $instance;
	}

	// Displays the widget settings controls on the widget panel.
	function form( $instance ) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'banner_image' => '',
			'banner_link' => '',
			'banner_code' => ''
			)
		);
		$title = $instance['title'];
		$banner_image = $instance['banner_image'];
		$banner_link = $instance['banner_link'];
		$banner_code = $instance['banner_code'];
		?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e('Title:', 'junotoys'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($title); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'banner_image' )); ?>"><?php echo wp_kses_data( __('Image source URL:<br />(leave empty if you paste banner code)', 'junotoys') ); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'banner_image' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'banner_image' )); ?>" value="<?php echo esc_attr($banner_image); ?>"  class="widgets_param_fullwidth widgets_param_img_selector" />
            <?php
			junotoys_show_layout(junotoys_show_custom_field($this->get_field_id( 'banner_media' ), array('type'=>'mediamanager', 'media_field_id'=>$this->get_field_id( 'banner_image' )), null));
			if ($banner_image) {
			?>
	            <br /><br /><img src="<?php echo esc_url($banner_image); ?>"  class="widgets_param_maxwidth" alt="" />
			<?php
			}
			?>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'banner_link' )); ?>"><?php echo wp_kses_data( __('Image link URL:<br />(leave empty if you paste banner code)', 'junotoys') ); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'banner_link' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'banner_link' )); ?>" value="<?php echo esc_attr($banner_link); ?>"  class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'banner_code' )); ?>"><?php esc_html_e('or paste Banner Widget HTML Code:', 'junotoys'); ?></label>
			<textarea id="<?php echo esc_attr($this->get_field_id( 'banner_code' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'banner_code' )); ?>" rows="5"  class="widgets_param_fullwidth"><?php echo htmlspecialchars($banner_code); ?></textarea>
		</p>
	<?php
	}
}



// trx_widget_banner
//-------------------------------------------------------------

if ( !function_exists( 'junotoys_sc_widget_banner' ) ) {
	function junotoys_sc_widget_banner($atts, $content=null){	
		$atts = junotoys_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			"image" => "",
			"link" => "",
			"code" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts));
		extract($atts);
		$type = 'junotoys_widget_banner';
		$output = '';
		global $wp_widget_factory;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$atts['banner_image'] = $image;
			$atts['banner_link'] = $link;
			$atts['banner_code'] = $code;
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_banner' 
								. (junotoys_exists_visual_composer() ? ' vc_widget_banner wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
						. '">';
			ob_start();
			the_widget( $type, $atts, junotoys_prepare_widgets_args(junotoys_storage_get('widgets_args'), $id ? $id.'_widget' : 'widget_banner', 'widget_banner') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('junotoys_shortcode_output', $output, 'trx_widget_banner', $atts, $content);
	}
	junotoys_require_shortcode("trx_widget_banner", "junotoys_sc_widget_banner");
}


// Add [trx_widget_banner] in the VC shortcodes list
if (!function_exists('junotoys_widget_banner_reg_shortcodes_vc')) {
	//add_action('junotoys_action_shortcodes_list_vc','junotoys_widget_banner_reg_shortcodes_vc');
	function junotoys_widget_banner_reg_shortcodes_vc() {
		
		vc_map( array(
				"base" => "trx_widget_banner",
				"name" => esc_html__("Widget Banner", 'junotoys'),
				"description" => wp_kses_data( __("Insert widget with banner or any HTML and/or Javascript code", 'junotoys') ),
				"category" => esc_html__('Content', 'junotoys'),
				"icon" => 'icon_trx_widget_banner',
				"class" => "trx_widget_banner",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "title",
						"heading" => esc_html__("Widget title", 'junotoys'),
						"description" => wp_kses_data( __("Title of the widget", 'junotoys') ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "image",
						"heading" => esc_html__("Image", 'junotoys'),
						"description" => wp_kses_data( __("Select or upload image or write URL from other site for the banner (leave empty if you paste banner code)", 'junotoys') ),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					array(
						"param_name" => "link",
						"heading" => esc_html__("Banner's link", 'junotoys'),
						"description" => wp_kses_data( __("Link URL for the banner (leave empty if you paste banner code)", 'junotoys') ),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "code",
						"heading" => esc_html__("or paste Banner Widget HTML Code", 'junotoys'),
						"description" => wp_kses_data( __("Widget's HTML and/or JS code", 'junotoys') ),
						"class" => "",
						"value" => "",
						"type" => "textarea"
					),
					junotoys_get_vc_param('id'),
					junotoys_get_vc_param('class'),
					junotoys_get_vc_param('css')
				)
			) );
			
		class WPBakeryShortCode_Trx_Widget_Banner extends WPBakeryShortCode {}

	}
}
?>