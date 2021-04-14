<?php
/* WPBakery PageBuilder support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('junotoys_vc_theme_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_vc_theme_setup', 1 );
	function junotoys_vc_theme_setup() {
		if (junotoys_exists_visual_composer()) {
			if (is_admin()) {
				add_filter( 'junotoys_filter_importer_options',				'junotoys_vc_importer_set_options' );
			}
			add_action('junotoys_action_add_styles',		 				'junotoys_vc_frontend_scripts' );
		}
		if (is_admin()) {
			add_filter( 'junotoys_filter_importer_required_plugins',		'junotoys_vc_importer_required_plugins', 10, 2 );
			add_filter( 'junotoys_filter_required_plugins',					'junotoys_vc_required_plugins' );
		}
	}
}

// Check if WPBakery PageBuilder installed and activated
if ( !function_exists( 'junotoys_exists_visual_composer' ) ) {
	function junotoys_exists_visual_composer() {
		return class_exists('Vc_Manager');
	}
}

// Check if WPBakery PageBuilder in frontend editor mode
if ( !function_exists( 'junotoys_vc_is_frontend' ) ) {
	function junotoys_vc_is_frontend() {
		return (isset($_GET['vc_editable']) && $_GET['vc_editable']=='true')
			|| (isset($_GET['vc_action']) && $_GET['vc_action']=='vc_inline');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'junotoys_vc_required_plugins' ) ) {
	//Handler of add_filter('junotoys_filter_required_plugins',	'junotoys_vc_required_plugins');
	function junotoys_vc_required_plugins($list=array()) {
		if (in_array('visual_composer', junotoys_storage_get('required_plugins'))) {
			$path = junotoys_get_file_dir('plugins/install/js_composer.zip');
			if (file_exists($path)) {
				$list[] = array(
					'name' 		=> esc_html__('WPBakery PageBuilder', 'junotoys'),
					'slug' 		=> 'js_composer',
					'source'	=> $path,
					'required' 	=> false
				);
			}
		}
		return $list;
	}
}

// Enqueue VC custom styles
if ( !function_exists( 'junotoys_vc_frontend_scripts' ) ) {
	//Handler of add_action( 'junotoys_action_add_styles', 'junotoys_vc_frontend_scripts' );
	function junotoys_vc_frontend_scripts() {
		if (file_exists(junotoys_get_file_dir('css/plugin.visual-composer.css')))
			wp_enqueue_style( 'junotoys-plugin.visual-composer-style',  junotoys_get_file_url('css/plugin.visual-composer.css'), array(), null );
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check VC in the required plugins
if ( !function_exists( 'junotoys_vc_importer_required_plugins' ) ) {
	//Handler of add_filter( 'junotoys_filter_importer_required_plugins',	'junotoys_vc_importer_required_plugins', 10, 2 );
	function junotoys_vc_importer_required_plugins($not_installed='', $list='') {
		if (!junotoys_exists_visual_composer() )		// && junotoys_strpos($list, 'visual_composer')!==false
			$not_installed .= '<br>' . esc_html__('WPBakery PageBuilder', 'junotoys');
		return $not_installed;
	}
}

// Set options for one-click importer
if ( !function_exists( 'junotoys_vc_importer_set_options' ) ) {
	//Handler of add_filter( 'junotoys_filter_importer_options',	'junotoys_vc_importer_set_options' );
	function junotoys_vc_importer_set_options($options=array()) {
		if ( in_array('visual_composer', junotoys_storage_get('required_plugins')) && junotoys_exists_visual_composer() ) {
			// Add slugs to export options for this plugin
			$options['additional_options'][] = 'wpb_js_templates';
		}
		return $options;
	}
}
?>