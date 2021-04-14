<?php
/**
 * Theme Widget: Advanced Calendar
 */

// Theme init
if (!function_exists('junotoys_widget_categories_theme_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_widget_categories_theme_setup', 1 );
	function junotoys_widget_categories_theme_setup() {

		// Register shortcodes in the shortcodes list
		if (function_exists('junotoys_exists_visual_composer') && junotoys_exists_visual_composer())
			add_action('junotoys_action_shortcodes_list_vc','junotoys_widget_categories_reg_shortcodes_vc');
	}
}

// Load widget
if (!function_exists('junotoys_widget_categories_load')) {
	add_action( 'widgets_init', 'junotoys_widget_categories_load' );
	function junotoys_widget_categories_load() {
		register_widget( 'junotoys_widget_categories' );
	}
}

// Widget Class
class junotoys_widget_categories extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_subcategories', 'description' => esc_html__('Display subcategories list', 'junotoys') );
		parent::__construct( 'junotoys_widget_subcategories', esc_html__('ThemeREX - Subcategories list', 'junotoys'), $widget_ops );
	}

	// Show widget
	function widget( $args, $instance ) {

		extract( $args );

		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '' );

		$post_type = isset($instance['post_type']) ? $instance['post_type'] : 'post';
		$taxonomy = junotoys_get_taxonomy_categories_by_post_type($post_type);

		$c = !empty( $instance['count'] ) && (int) $instance['count'] == 1 ? '1' : '0';
		$h = !empty( $instance['hierarchical'] ) && (int) $instance['hierarchical'] == 1 ? '1' : '0';
		$d = !empty( $instance['dropdown'] ) && (int) $instance['dropdown'] == 1 ? '1' : '0';

		$root = isset($instance['root']) && (int) $instance['root'] > 0 ? (int) $instance['root'] : 0;

		$cat_args = array('orderby' => 'name', 'show_count' => $c, 'hierarchical' => $h, 'taxonomy' => $taxonomy);

		if ($root > 0) $cat_args['child_of'] = $root;

		// Before widget (defined by themes)
		junotoys_show_layout($before_widget);

		if ($title) junotoys_show_layout($before_title . $title . $after_title);
		?>			
		<div class="widget_subcategories_inner">
			<?php
			if ( $d ) {
				$cat_args['show_option_none'] = esc_html__('Select Category', 'junotoys');
				wp_dropdown_categories( apply_filters( 'widget_categories_dropdown_args', $cat_args ) );
			} else {
				?>
				<ul>
					<?php
					$cat_args['title_li'] = '';
					wp_list_categories( apply_filters( 'widget_categories_args', $cat_args ) );
					?>
				</ul>
				<?php
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
		$instance['title'] 			= strip_tags( $new_instance['title'] );
		$instance['count'] 			= !empty($new_instance['count']) ? 1 : 0;
		$instance['hierarchical'] 	= !empty($new_instance['hierarchical']) ? 1 : 0;
		$instance['dropdown'] 		= !empty($new_instance['dropdown']) ? 1 : 0;
		$instance['root'] 			= (int) $new_instance['root'];
		$instance['post_type'] 		= strip_tags( $new_instance['post_type'] );
		return $instance;
	}

	// Displays the widget settings controls on the widget panel.
	function form( $instance ) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, array(
			'title'			=> '',
			'count'			=> 0,
			'dropdown'		=> 0,
			'hierarchical'	=> 0,
			'root' 			=> 0,
			'post_type'		=> 'post'
			)
		);

		$title = $instance['title'];
		$root = (int) $instance['root'];
		$post_type = $instance['post_type'];
		$count = (bool) $instance['count'];
		$hierarchical = (bool) $instance['hierarchical'];
		$dropdown = (bool) $instance['dropdown'];
		
		$posts_types = junotoys_get_list_posts_types(false);
		$categories = junotoys_get_list_terms(false, junotoys_get_taxonomy_categories_by_post_type($post_type));
		?>
		<p>
		<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e( 'Title:', 'junotoys' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('post_type')); ?>"><?php esc_html_e('Post type:', 'junotoys'); ?></label>
			<select id="<?php echo esc_attr($this->get_field_id('post_type')); ?>" name="<?php echo esc_attr($this->get_field_name('post_type')); ?>" class="widgets_param_fullwidth widgets_param_post_type_selector">
			<?php
				if (is_array($posts_types) && count($posts_types) > 0) {
					foreach ($posts_types as $type => $type_name) {
						echo '<option value="'.esc_attr($type).'"'.($post_type==$type ? ' selected="selected"' : '').'>'.esc_html($type_name).'</option>';
					}
				}
			?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('root')); ?>"><?php esc_html_e('Root category:', 'junotoys'); ?></label>
			<select id="<?php echo esc_attr($this->get_field_id('root')); ?>" name="<?php echo esc_attr($this->get_field_name('root')); ?>" class="widgets_param_fullwidth">
				<option value="0"><?php esc_html_e('-- Any category --', 'junotoys'); ?></option> 
			<?php
				if (is_array($categories) && count($categories) > 0) {
					foreach ($categories as $cat_id => $cat_name) {
						echo '<option value="'.esc_attr($cat_id).'"'.($root==$cat_id ? ' selected="selected"' : '').'>'.($cat_name).'</option>';
					}
				}
			?>
			</select>
		</p>

		<p>
		<input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('dropdown')); ?>" name="<?php echo esc_attr($this->get_field_name('dropdown')); ?>"<?php checked( $dropdown ); ?> />
		<label for="<?php echo esc_attr($this->get_field_id('dropdown')); ?>"><?php esc_html_e( 'Display as dropdown', 'junotoys' ); ?></label><br />

		<input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('count')); ?>" name="<?php echo esc_attr($this->get_field_name('count')); ?>"<?php checked( $count ); ?> />
		<label for="<?php echo esc_attr($this->get_field_id('count')); ?>"><?php esc_html_e( 'Show post counts', 'junotoys' ); ?></label><br />

		<input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('hierarchical')); ?>" name="<?php echo esc_attr($this->get_field_name('hierarchical')); ?>"<?php checked( $hierarchical ); ?> />
		<label for="<?php echo esc_attr($this->get_field_id('hierarchical')); ?>"><?php esc_html_e( 'Show hierarchy', 'junotoys' ); ?></label>
		</p>
		<?php
	}
}



// trx_widget_categories
//-------------------------------------------------------------

if ( !function_exists( 'junotoys_sc_widget_categories' ) ) {
	function junotoys_sc_widget_categories($atts, $content=null){	
		$atts = junotoys_html_decode(shortcode_atts(array(
			// Individual params
			"title"			=> "",
			'count'			=> 1,
			'dropdown'		=> 0,
			'hierarchical'	=> 1,
			'root' 			=> '',
			'cat' 			=> 0,
			'post_type'		=> 'post',
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts));
		if ($atts['post_type']=='') $atts['post_type'] = 'post';
		if ($atts['cat']!='' && $atts['root']=='') $atts['root'] = $atts['cat'];
		extract($atts);
		$type = 'junotoys_widget_categories';
		$output = '';
		global $wp_widget_factory;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_categories' 
								. (junotoys_exists_visual_composer() ? ' vc_widget_categories wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
						. '">';
			ob_start();
			the_widget( $type, $atts, junotoys_prepare_widgets_args(junotoys_storage_get('widgets_args'), $id ? $id.'_widget' : 'widget_categories', 'widget_categories') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('junotoys_shortcode_output', $output, 'trx_widget_categories', $atts, $content);
	}
	junotoys_require_shortcode("trx_widget_categories", "junotoys_sc_widget_categories");
}


// Add [trx_widget_categories] in the VC shortcodes list
if (!function_exists('junotoys_widget_categories_reg_shortcodes_vc')) {
	//add_action('junotoys_action_shortcodes_list_vc','junotoys_widget_categories_reg_shortcodes_vc');
	function junotoys_widget_categories_reg_shortcodes_vc() {
		
		$posts_types = junotoys_get_list_posts_types(false);
		$categories = junotoys_get_list_terms(false, junotoys_get_taxonomy_categories_by_post_type('post'));

		vc_map( array(
				"base" => "trx_widget_categories",
				"name" => esc_html__("Widget Categories", 'junotoys'),
				"description" => wp_kses_data( __("Display the subcategories list for the specified category", 'junotoys') ),
				"category" => esc_html__('Content', 'junotoys'),
				"icon" => 'icon_trx_widget_categories',
				"class" => "trx_widget_categories",
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
						"param_name" => "count",
						"heading" => esc_html__("Show posts", 'junotoys'),
						"description" => wp_kses_data( __("Show posts number in the each category", 'junotoys') ),
						"class" => "",
						"std" => "1",
						"value" => array(esc_html__('Show posts number', 'junotoys') => "1"),
						"type" => "checkbox"
					),
					array(
						"param_name" => "dropdown",
						"heading" => esc_html__("Show dropdown", 'junotoys'),
						"description" => wp_kses_data( __("Show categories as dropdown list", 'junotoys') ),
						"class" => "",
						"std" => "0",
						"value" => array(esc_html__('Show dropdown', 'junotoys') => "1"),
						"type" => "checkbox"
					),
					array(
						"param_name" => "hierarchical",
						"heading" => esc_html__("Show hierarchical", 'junotoys'),
						"description" => wp_kses_data( __("Show categories as hierarchical list", 'junotoys') ),
						"class" => "",
						"std" => "1",
						"value" => array(esc_html__('Show hierarchical', 'junotoys') => "1"),
						"type" => "checkbox"
					),
					array(
						"param_name" => "post_type",
						"heading" => esc_html__("Post type", 'junotoys'),
						"description" => wp_kses_data( __("Select post type to show", 'junotoys') ),
						"class" => "",
						"std" => "post",
						"value" => array_flip((array)$posts_types),
						"type" => "dropdown"
					),
					array(
						"param_name" => "cat",
						"heading" => esc_html__("Parent category", 'junotoys'),
						"description" => wp_kses_data( __("Select parent category. If empty - show all categories", 'junotoys') ),
						"class" => "",
						"value" => array_flip(junotoys_array_merge(array(0 => esc_html__('- Select category -', 'junotoys')), $categories)),
						"type" => "dropdown"
					),
					junotoys_get_vc_param('id'),
					junotoys_get_vc_param('class'),
					junotoys_get_vc_param('css')
				)
			) );
			
		class WPBakeryShortCode_Trx_Widget_Categories extends WPBakeryShortCode {}

	}
}
?>