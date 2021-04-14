<?php
/* Revolution Slider support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('junotoys_revslider_theme_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_revslider_theme_setup', 1 );
	function junotoys_revslider_theme_setup() {
		if (junotoys_exists_revslider()) {
			add_filter( 'junotoys_filter_list_sliders',					'junotoys_revslider_list_sliders' );
			add_filter( 'junotoys_filter_shortcodes_params',			'junotoys_revslider_shortcodes_params' );
			add_filter( 'junotoys_filter_theme_options_params',			'junotoys_revslider_theme_options_params' );
			if (is_admin()) {
				add_filter( 'junotoys_filter_importer_options',			'junotoys_revslider_importer_set_options', 10, 2 );
				add_action( 'junotoys_action_importer_params',			'junotoys_revslider_importer_show_params', 10, 1 );
				add_action( 'junotoys_action_importer_clear_tables',	'junotoys_revslider_importer_clear_tables', 10, 2 );
				add_action( 'junotoys_action_importer_import',			'junotoys_revslider_importer_import', 10, 2 );
				add_action( 'junotoys_action_importer_import_fields',	'junotoys_revslider_importer_import_fields', 10, 1 );
				add_action( 'junotoys_action_importer_export',			'junotoys_revslider_importer_export', 10, 1 );
				add_action( 'junotoys_action_importer_export_fields',	'junotoys_revslider_importer_export_fields', 10, 1 );
			}
		}
		if (is_admin()) {
			add_filter( 'junotoys_filter_importer_required_plugins',	'junotoys_revslider_importer_required_plugins', 10, 2 );
			add_filter( 'junotoys_filter_required_plugins',				'junotoys_revslider_required_plugins' );
		}
	}
}

if ( !function_exists( 'junotoys_revslider_settings_theme_setup2' ) ) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_revslider_settings_theme_setup2', 3 );
	function junotoys_revslider_settings_theme_setup2() {
		if (junotoys_exists_revslider()) {

			// Add Revslider specific options in the Theme Options
			junotoys_storage_set_array_after('options', 'slider_engine', "slider_alias", array(
				"title" => esc_html__('Revolution Slider: Select slider',  'junotoys'),
				"desc" => wp_kses_data( __("Select slider to show (if engine=revo in the field above)", 'junotoys') ),
				"override" => "category,services_group,page",
				"dependency" => array(
					'show_slider' => array('yes'),
					'slider_engine' => array('revo')
				),
				"std" => "",
				"options" => junotoys_get_options_param('list_revo_sliders'),
				"type" => "select"
				)
			);

		}
	}
}

// Check if RevSlider installed and activated
if ( !function_exists( 'junotoys_exists_revslider' ) ) {
	function junotoys_exists_revslider() {
		return function_exists('rev_slider_shortcode');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'junotoys_revslider_required_plugins' ) ) {
	//Handler of add_filter('junotoys_filter_required_plugins',	'junotoys_revslider_required_plugins');
	function junotoys_revslider_required_plugins($list=array()) {
		if (in_array('revslider', junotoys_storage_get('required_plugins'))) {
			$path = junotoys_get_file_dir('plugins/install/revslider.zip');
			if (file_exists($path)) {
				$list[] = array(
					'name' 		=> esc_html__('Revolution Slider', 'junotoys'),
					'slug' 		=> 'revslider',
					'source'	=> $path,
					'required' 	=> false
					);
			}
		}
		return $list;
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check RevSlider in the required plugins
if ( !function_exists( 'junotoys_revslider_importer_required_plugins' ) ) {
	//Handler of add_filter( 'junotoys_filter_importer_required_plugins',	'junotoys_revslider_importer_required_plugins', 10, 2 );
	function junotoys_revslider_importer_required_plugins($not_installed='', $list='') {
		//if (in_array('revslider', junotoys_storage_get('required_plugins')) && !junotoys_exists_revslider() )
		if (junotoys_strpos($list, 'revslider')!==false && !junotoys_exists_revslider() )
			$not_installed .= '<br>' . esc_html__('Revolution Slider', 'junotoys');
		return $not_installed;
	}
}

// Set plugin's specific importer options
if ( !function_exists( 'junotoys_revslider_importer_set_options' ) ) {
	//Handler of add_filter( 'junotoys_filter_importer_options',	'junotoys_revslider_importer_set_options', 10, 2 );
	function junotoys_revslider_importer_set_options($options=array()) {
		if (junotoys_exists_revslider() && in_array('revslider', junotoys_storage_get('required_plugins'))) {
			if (is_array($options['files']) && count($options['files']) > 0) {
				foreach ($options['files'] as $k => $v) {
					$options['files'][$k]['file_with_revslider'] = str_replace('name.ext', 'revslider.txt', $v['file_with_']);
				}
			}
		}
		return $options;
	}
}

// Add checkbox to the one-click importer
if ( !function_exists( 'junotoys_revslider_importer_show_params' ) ) {
	//Handler of add_action( 'junotoys_action_importer_params',	'junotoys_revslider_importer_show_params', 10, 1 );
	function junotoys_revslider_importer_show_params($importer) {
		if (!empty($importer->options['files'][$importer->options['demo_type']]['file_with_revslider'])) {
			$importer->show_importer_params(array(
				'slug' => 'revslider',
				'title' => esc_html__('Import Revolution Sliders', 'junotoys'),
				'part' => 1
				));
		}
	}
}

// Clear tables
if ( !function_exists( 'junotoys_revslider_importer_clear_tables' ) ) {
	//Handler of add_action( 'junotoys_action_importer_clear_tables',	'junotoys_revslider_importer_clear_tables', 10, 2 );
	function junotoys_revslider_importer_clear_tables($importer, $clear_tables) {
		if (junotoys_strpos($clear_tables, 'revslider')!==false && $importer->last_slider==0) {
			if ($importer->options['debug']) dfl(esc_html__('Clear Revolution Slider tables', 'junotoys'));
			global $wpdb;
			$res = $wpdb->query("TRUNCATE TABLE " . esc_sql($wpdb->prefix) . "revslider_sliders");
			if ( is_wp_error( $res ) ) dfl( esc_html__( 'Failed truncate table "revslider_sliders".', 'junotoys' ) . ' ' . ($res->get_error_message()) );
			$res = $wpdb->query("TRUNCATE TABLE " . esc_sql($wpdb->prefix) . "revslider_slides");
			if ( is_wp_error( $res ) ) dfl( esc_html__( 'Failed truncate table "revslider_slides".', 'junotoys' ) . ' ' . ($res->get_error_message()) );
			$res = $wpdb->query("TRUNCATE TABLE " . esc_sql($wpdb->prefix) . "revslider_static_slides");
			if ( is_wp_error( $res ) ) dfl( esc_html__( 'Failed truncate table "revslider_static_slides".', 'junotoys' ) . ' ' . ($res->get_error_message()) );
		}
	}
}

// Import posts
if ( !function_exists( 'junotoys_revslider_importer_import' ) ) {
	//Handler of add_action( 'junotoys_action_importer_import',	'junotoys_revslider_importer_import', 10, 2 );
	function junotoys_revslider_importer_import($importer, $action) {
		if ( $action == 'import_revslider' && !empty($importer->options['files'][$importer->options['demo_type']]['file_with_revslider']) ) {
			if (file_exists(WP_PLUGIN_DIR . '/revslider/revslider.php')) {
				require_once WP_PLUGIN_DIR . '/revslider/revslider.php';
				if ($importer->options['debug']) dfl( esc_html__('Import Revolution sliders', 'junotoys') );
				// Get last processed slider
				$last_arh = $importer->response['start_from_id'] = isset($_POST['start_from_id']) ? $_POST['start_from_id'] : '';
				// Get list of the sliders
				if ( ($txt = get_option('junotoys_import_revsliders')) == '' ) {
					if ( ($txt = $importer->get_file($importer->options['files'][$importer->options['demo_type']]['file_with_revslider'])) === false)
						return;
					else
						update_option('junotoys_import_revsliders', $txt);
				}
				$files = junotoys_unserialize($txt);
				if (!is_array($files)) $files = explode("\n", str_replace("\r\n", "\n", $files));
				// Process next slider
				$slider = new RevSlider();
				// Process files
				$counter = 0;
				$result = 0;
				if (!is_array($_FILES)) $_FILES = array();
				foreach ($files as $file) {
					$counter++;
					if ( ($file = trim($file)) == '' )
						continue;
					if (!empty($last_arh)) {
						if ($file==$last_arh) 
							$last_arh = '';
						continue;
					}
					$need_del = false;
					// Load single file into system temp folder
					if ( ($zip = $importer->download_file($file, round(max(0, $counter-1) / count($files) * 100))) != '') {
						$need_del = substr($zip, 0, 5)=='http:' || substr($zip, 0, 6)=='https:';
						$_FILES["import_file"] = array("tmp_name" => $zip, 'error' => UPLOAD_ERR_OK);
						$response = $slider->importSliderFromPost();
						if ($need_del && file_exists($_FILES["import_file"]["tmp_name"]))
							unlink($_FILES["import_file"]["tmp_name"]);
					if ($response["success"] == false) {
							$msg = sprintf(esc_html__('Revolution Slider "%s" import error.', 'junotoys'), $file);
							unset($importer->response['attempt']);
							$importer->response['error'] = $msg;
						if ($importer->options['debug'])  {
							dfl( $msg );
							dfo( $response );
						}
					} else {
							$importer->response['start_from_id'] = $file;
							$importer->response['result'] = min(100, round($counter / count($files) * 100));
						if ($importer->options['debug']) 
								dfl( sprintf(__('Slider "%s" imported', 'junotoys'), basename($file)) );
						}
					}
					break;
				}
				if ($counter == count($files)) {
					update_option('junotoys_import_revsliders', '');
				}
			} else {
				if ($importer->options['debug']) 
					dfl( sprintf(__('Can not locate plugin Revolution Slider: %s', 'junotoys'), WP_PLUGIN_DIR.'/revslider/revslider.php') );
			}
		}
	}
}

// Display import progress
if ( !function_exists( 'junotoys_revslider_importer_import_fields' ) ) {
	//Handler of add_action( 'junotoys_action_importer_import_fields',	'junotoys_revslider_importer_import_fields', 10, 1 );
	function junotoys_revslider_importer_import_fields($importer) {
		$importer->show_importer_fields(array(
			'slug' => 'revslider',
			'title' => esc_html__('Revolution Slider', 'junotoys')
			));
	}
}

// Export posts
if ( !function_exists( 'junotoys_revslider_importer_export' ) ) {
	//Handler of add_action( 'junotoys_action_importer_export',	'junotoys_revslider_importer_export', 10, 1 );
	function junotoys_revslider_importer_export($importer) {
		// Sliders list
		junotoys_fpc(junotoys_get_file_dir('core/core.importer/export/revslider.txt'), join("\n", array_keys(junotoys_get_list_revo_sliders())));
	}
}

// Display exported data in the fields
if ( !function_exists( 'junotoys_revslider_importer_export_fields' ) ) {
	//Handler of add_action( 'junotoys_action_importer_export_fields',	'junotoys_revslider_importer_export_fields', 10, 1 );
	function junotoys_revslider_importer_export_fields($importer) {
		$importer->show_exporter_fields(array(
			'slug' => 'revslider',
			'title' => esc_html__('Revolution Sliders', 'junotoys')
			));
	}
}


// Lists
//------------------------------------------------------------------------

// Add RevSlider in the sliders list, prepended inherit (if need)
if ( !function_exists( 'junotoys_revslider_list_sliders' ) ) {
	//Handler of add_filter( 'junotoys_filter_list_sliders',					'junotoys_revslider_list_sliders' );
	function junotoys_revslider_list_sliders($list=array()) {
		$list["revo"] = esc_html__("Layer slider (Revolution)", 'junotoys');
		return $list;
	}
}

// Return Revo Sliders list, prepended inherit (if need)
if ( !function_exists( 'junotoys_get_list_revo_sliders' ) ) {
	function junotoys_get_list_revo_sliders($prepend_inherit=false) {
		if (($list = junotoys_storage_get('list_revo_sliders'))=='') {
			$list = array();
			if (junotoys_exists_revslider()) {
				global $wpdb;
				$rows = $wpdb->get_results( "SELECT alias, title FROM " . esc_sql($wpdb->prefix) . "revslider_sliders" );
				if (is_array($rows) && count($rows) > 0) {
					foreach ($rows as $row) {
						$list[$row->alias] = $row->title;
					}
				}
			}
			$list = apply_filters('junotoys_filter_list_revo_sliders', $list);
			if (junotoys_get_theme_setting('use_list_cache')) junotoys_storage_set('list_revo_sliders', $list);
		}
		return $prepend_inherit ? junotoys_array_merge(array('inherit' => esc_html__("Inherit", 'junotoys')), $list) : $list;
	}
}

// Add RevSlider in the shortcodes params
if ( !function_exists( 'junotoys_revslider_shortcodes_params' ) ) {
	//Handler of add_filter( 'junotoys_filter_shortcodes_params',			'junotoys_revslider_shortcodes_params' );
	function junotoys_revslider_shortcodes_params($list=array()) {
		$list["revo_sliders"] = junotoys_get_list_revo_sliders();
		return $list;
	}
}

// Add RevSlider in the Theme Options params
if ( !function_exists( 'junotoys_revslider_theme_options_params' ) ) {
	//Handler of add_filter( 'junotoys_filter_theme_options_params',			'junotoys_revslider_theme_options_params' );
	function junotoys_revslider_theme_options_params($list=array()) {
		$list["list_revo_sliders"] = array('$junotoys_get_list_revo_sliders' => '');
		return $list;
	}
}
?>