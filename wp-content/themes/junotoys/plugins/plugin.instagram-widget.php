<?php
/* Instagram Widget support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('junotoys_instagram_widget_theme_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_instagram_widget_theme_setup', 1 );
	function junotoys_instagram_widget_theme_setup() {
		if (junotoys_exists_instagram_widget()) {
			add_action( 'junotoys_action_add_styles', 						'junotoys_instagram_widget_frontend_scripts' );
		}
		if (is_admin()) {
			add_filter( 'junotoys_filter_importer_required_plugins',		'junotoys_instagram_widget_importer_required_plugins', 10, 2 );
			add_filter( 'junotoys_filter_required_plugins',					'junotoys_instagram_widget_required_plugins' );
		}
	}
}

// Check if Instagram Widget installed and activated
if ( !function_exists( 'junotoys_exists_instagram_widget' ) ) {
	function junotoys_exists_instagram_widget() {
		return function_exists('wpiw_init');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'junotoys_instagram_widget_required_plugins' ) ) {
	//Handler of add_filter('junotoys_filter_required_plugins',	'junotoys_instagram_widget_required_plugins');
	function junotoys_instagram_widget_required_plugins($list=array()) {
		if (in_array('instagram_widget', junotoys_storage_get('required_plugins')))
			$list[] = array(
					'name' 		=> esc_html__('Instagram Widget', 'junotoys'),
					'slug' 		=> 'wp-instagram-widget',
					'required' 	=> false
				);
		return $list;
	}
}

// Enqueue custom styles
if ( !function_exists( 'junotoys_instagram_widget_frontend_scripts' ) ) {
	//Handler of add_action( 'junotoys_action_add_styles', 'junotoys_instagram_widget_frontend_scripts' );
	function junotoys_instagram_widget_frontend_scripts() {
		if (file_exists(junotoys_get_file_dir('css/plugin.instagram-widget.css')))
			wp_enqueue_style( 'junotoys-plugin.instagram-widget-style',  junotoys_get_file_url('css/plugin.instagram-widget.css'), array(), null );
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check Instagram Widget in the required plugins
if ( !function_exists( 'junotoys_instagram_widget_importer_required_plugins' ) ) {
	//Handler of add_filter( 'junotoys_filter_importer_required_plugins',	'junotoys_instagram_widget_importer_required_plugins', 10, 2 );
	function junotoys_instagram_widget_importer_required_plugins($not_installed='', $list='') {
		if (junotoys_strpos($list, 'instagram_widget')!==false && !junotoys_exists_instagram_widget() )
			$not_installed .= '<br>' . esc_html__('WP Instagram Widget', 'junotoys');
		return $not_installed;
	}
}
?>