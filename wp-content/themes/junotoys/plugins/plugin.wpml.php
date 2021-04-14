<?php
/* WPML support functions
------------------------------------------------------------------------------- */

// Check if WPML installed and activated
if ( !function_exists( 'junotoys_exists_wpml' ) ) {
	function junotoys_exists_wpml() {
		return defined('ICL_SITEPRESS_VERSION') && class_exists('sitepress');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'junotoys_wpml_required_plugins' ) ) {
	//Handler of add_filter('junotoys_filter_required_plugins',	'junotoys_wpml_required_plugins');
	function junotoys_wpml_required_plugins($list=array()) {
		if (in_array('wpml', (array)junotoys_storage_get('required_plugins'))) {
			$path = junotoys_get_file_dir('plugins/install/wpml.zip');
			if (file_exists($path)) {
				$list[] = array(
					'name' 		=> esc_html__('WPML', 'junotoys'),
					'slug' 		=> 'wpml',
					'source'	=> $path,
					'required' 	=> false
					);
			}
		}
		return $list;
	}
}
?>