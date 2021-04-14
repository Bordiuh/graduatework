<?php

// Check if shortcodes settings are now used
if ( !function_exists( 'junotoys_shortcodes_is_used' ) ) {
	function junotoys_shortcodes_is_used() {
		return junotoys_options_is_used() 															// All modes when Theme Options are used
			|| (is_admin() && isset($_POST['action']) 
					&& in_array($_POST['action'], array('vc_edit_form', 'wpb_show_edit_form')))		// AJAX query when save post/page
			|| (is_admin() && junotoys_strpos($_SERVER['REQUEST_URI'], 'vc-roles')!==false)			// VC Role Manager
			|| (function_exists('junotoys_vc_is_frontend') && junotoys_vc_is_frontend());			// VC Frontend editor mode
	}
}

// Width and height params
if ( !function_exists( 'junotoys_shortcodes_width' ) ) {
	function junotoys_shortcodes_width($w="") {
		return array(
			"title" => esc_html__("Width", "junotoys"),
			"divider" => true,
			"value" => $w,
			"type" => "text"
		);
	}
}
if ( !function_exists( 'junotoys_shortcodes_height' ) ) {
	function junotoys_shortcodes_height($h='') {
		return array(
			"title" => esc_html__("Height", "junotoys"),
			"desc" => wp_kses_data( __("Width and height of the element", "junotoys") ),
			"value" => $h,
			"type" => "text"
		);
	}
}

// Return sc_param value
if ( !function_exists( 'junotoys_get_sc_param' ) ) {
	function junotoys_get_sc_param($prm) {
		return junotoys_storage_get_array('sc_params', $prm);
	}
}

// Set sc_param value
if ( !function_exists( 'junotoys_set_sc_param' ) ) {
	function junotoys_set_sc_param($prm, $val) {
		junotoys_storage_set_array('sc_params', $prm, $val);
	}
}

// Add sc settings in the sc list
if ( !function_exists( 'junotoys_sc_map' ) ) {
	function junotoys_sc_map($sc_name, $sc_settings) {
		junotoys_storage_set_array('shortcodes', $sc_name, $sc_settings);
	}
}

// Add sc settings in the sc list after the key
if ( !function_exists( 'junotoys_sc_map_after' ) ) {
	function junotoys_sc_map_after($after, $sc_name, $sc_settings='') {
		junotoys_storage_set_array_after('shortcodes', $after, $sc_name, $sc_settings);
	}
}

// Add sc settings in the sc list before the key
if ( !function_exists( 'junotoys_sc_map_before' ) ) {
	function junotoys_sc_map_before($before, $sc_name, $sc_settings='') {
		junotoys_storage_set_array_before('shortcodes', $before, $sc_name, $sc_settings);
	}
}

// Compare two shortcodes by title
if ( !function_exists( 'junotoys_compare_sc_title' ) ) {
	function junotoys_compare_sc_title($a, $b) {
		return strcmp($a['title'], $b['title']);
	}
}



/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'junotoys_shortcodes_settings_theme_setup' ) ) {
//	if ( junotoys_vc_is_frontend() )
	if ( (isset($_GET['vc_editable']) && $_GET['vc_editable']=='true') || (isset($_GET['vc_action']) && $_GET['vc_action']=='vc_inline') )
		add_action( 'junotoys_action_before_init_theme', 'junotoys_shortcodes_settings_theme_setup', 20 );
	else
		add_action( 'junotoys_action_after_init_theme', 'junotoys_shortcodes_settings_theme_setup' );
	function junotoys_shortcodes_settings_theme_setup() {
		if (junotoys_shortcodes_is_used()) {

			// Sort templates alphabetically
			$tmp = junotoys_storage_get('registered_templates');
			ksort($tmp);
			junotoys_storage_set('registered_templates', $tmp);

			// Prepare arrays 
			junotoys_storage_set('sc_params', array(
			
				// Current element id
				'id' => array(
					"title" => esc_html__("Element ID", "junotoys"),
					"desc" => wp_kses_data( __("ID for current element", "junotoys") ),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
			
				// Current element class
				'class' => array(
					"title" => esc_html__("Element CSS class", "junotoys"),
					"desc" => wp_kses_data( __("CSS class for current element (optional)", "junotoys") ),
					"value" => "",
					"type" => "text"
				),
			
				// Current element style
				'css' => array(
					"title" => esc_html__("CSS styles", "junotoys"),
					"desc" => wp_kses_data( __("Any additional CSS rules (if need)", "junotoys") ),
					"value" => "",
					"type" => "text"
				),
			
			
				// Switcher choises
				'list_styles' => array(
					'ul'	=> esc_html__('Unordered', 'junotoys'),
					'ol'	=> esc_html__('Ordered', 'junotoys'),
					'iconed'=> esc_html__('Iconed', 'junotoys')
				),

				'yes_no'	=> junotoys_get_list_yesno(),
				'on_off'	=> junotoys_get_list_onoff(),
				'dir' 		=> junotoys_get_list_directions(),
				'align'		=> junotoys_get_list_alignments(),
				'float'		=> junotoys_get_list_floats(),
				'hpos'		=> junotoys_get_list_hpos(),
				'show_hide'	=> junotoys_get_list_showhide(),
				'sorting' 	=> junotoys_get_list_sortings(),
				'ordering' 	=> junotoys_get_list_orderings(),
				'shapes'	=> junotoys_get_list_shapes(),
				'sizes'		=> junotoys_get_list_sizes(),
				'sliders'	=> junotoys_get_list_sliders(),
				'controls'	=> junotoys_get_list_controls(),
                    'categories'=> is_admin() && junotoys_get_value_gp('action')=='vc_edit_form' && substr(junotoys_get_value_gp('tag'), 0, 4)=='trx_' && isset($_POST['params']['post_type']) && $_POST['params']['post_type']!='post'
                        ? junotoys_get_list_terms(false, junotoys_get_taxonomy_categories_by_post_type($_POST['params']['post_type']))
                        : junotoys_get_list_categories(),
				'columns'	=> junotoys_get_list_columns(),
				'images'	=> array_merge(array('none'=>"none"), junotoys_get_list_files("images/icons", "png")),
				'icons'		=> array_merge(array("inherit", "none"), junotoys_get_list_icons()),
				'locations'	=> junotoys_get_list_dedicated_locations(),
				'filters'	=> junotoys_get_list_portfolio_filters(),
				'formats'	=> junotoys_get_list_post_formats_filters(),
				'hovers'	=> junotoys_get_list_hovers(true),
				'hovers_dir'=> junotoys_get_list_hovers_directions(true),
				'schemes'	=> junotoys_get_list_color_schemes(true),
				'animations'		=> junotoys_get_list_animations_in(),
				'margins' 			=> junotoys_get_list_margins(true),
				'blogger_styles'	=> junotoys_get_list_templates_blogger(),
				'forms'				=> junotoys_get_list_templates_forms(),
				'posts_types'		=> junotoys_get_list_posts_types(),
				'googlemap_styles'	=> junotoys_get_list_googlemap_styles(),
				'field_types'		=> junotoys_get_list_field_types(),
				'label_positions'	=> junotoys_get_list_label_positions()
				)
			);

			// Common params
			junotoys_set_sc_param('animation', array(
				"title" => esc_html__("Animation",  'junotoys'),
				"desc" => wp_kses_data( __('Select animation while object enter in the visible area of page',  'junotoys') ),
				"value" => "none",
				"type" => "select",
				"options" => junotoys_get_sc_param('animations')
				)
			);
			junotoys_set_sc_param('top', array(
				"title" => esc_html__("Top margin",  'junotoys'),
				"divider" => true,
				"value" => "inherit",
				"type" => "select",
				"options" => junotoys_get_sc_param('margins')
				)
			);
			junotoys_set_sc_param('bottom', array(
				"title" => esc_html__("Bottom margin",  'junotoys'),
				"value" => "inherit",
				"type" => "select",
				"options" => junotoys_get_sc_param('margins')
				)
			);
			junotoys_set_sc_param('left', array(
				"title" => esc_html__("Left margin",  'junotoys'),
				"value" => "inherit",
				"type" => "select",
				"options" => junotoys_get_sc_param('margins')
				)
			);
			junotoys_set_sc_param('right', array(
				"title" => esc_html__("Right margin",  'junotoys'),
				"desc" => wp_kses_data( __("Margins around this shortcode", "junotoys") ),
				"value" => "inherit",
				"type" => "select",
				"options" => junotoys_get_sc_param('margins')
				)
			);

			junotoys_storage_set('sc_params', apply_filters('junotoys_filter_shortcodes_params', junotoys_storage_get('sc_params')));

			// Shortcodes list
			//------------------------------------------------------------------
			junotoys_storage_set('shortcodes', array());
			
			// Register shortcodes
			do_action('junotoys_action_shortcodes_list');

			// Sort shortcodes list
			$tmp = junotoys_storage_get('shortcodes');
			uasort($tmp, 'junotoys_compare_sc_title');
			junotoys_storage_set('shortcodes', $tmp);
		}
	}
}
?>