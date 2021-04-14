<?php
/* Essential Grid support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('junotoys_essgrids_theme_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_essgrids_theme_setup', 1 );
	function junotoys_essgrids_theme_setup() {
		// Register shortcode in the shortcodes list
		if (junotoys_exists_essgrids()) {
			if (is_admin()) {
				add_filter( 'junotoys_filter_importer_options',			'junotoys_essgrids_importer_set_options', 10, 1 );
				add_action( 'junotoys_action_importer_params',			'junotoys_essgrids_importer_show_params', 10, 1 );
				add_action( 'junotoys_action_importer_clear_tables',	'junotoys_essgrids_importer_clear_tables', 10, 2 );
				add_action( 'junotoys_action_importer_import',			'junotoys_essgrids_importer_import', 10, 2 );
				add_filter( 'junotoys_filter_importer_import_row',		'junotoys_essgrids_importer_check_row', 9, 4);
				add_action( 'junotoys_action_importer_import_fields',	'junotoys_essgrids_importer_import_fields', 10, 1 );
			}
		}
		if (is_admin()) {
			add_filter( 'junotoys_filter_importer_required_plugins',	'junotoys_essgrids_importer_required_plugins', 10, 2 );
			add_filter( 'junotoys_filter_required_plugins',				'junotoys_essgrids_required_plugins' );
		}
	}
}


// Check if Ess. Grid installed and activated
if ( !function_exists( 'junotoys_exists_essgrids' ) ) {
	function junotoys_exists_essgrids() {
		return defined('EG_PLUGIN_PATH');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'junotoys_essgrids_required_plugins' ) ) {
	//Handler of add_filter('junotoys_filter_required_plugins',	'junotoys_essgrids_required_plugins');
	function junotoys_essgrids_required_plugins($list=array()) {
		if (in_array('essgrids', junotoys_storage_get('required_plugins'))) {
			$path = junotoys_get_file_dir('plugins/install/essential-grid.zip');
			if (file_exists($path)) {
				$list[] = array(
					'name' 		=> esc_html__('Essential Grid', 'junotoys'),
					'slug' 		=> 'essential-grid',
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

// Check in the required plugins
if ( !function_exists( 'junotoys_essgrids_importer_required_plugins' ) ) {
	//Handler of add_filter( 'junotoys_filter_importer_required_plugins',	'junotoys_essgrids_importer_required_plugins', 10, 2 );
	function junotoys_essgrids_importer_required_plugins($not_installed='', $list='') {
		if (junotoys_strpos($list, 'essgrids')!==false && !junotoys_exists_essgrids() )
			$not_installed .= '<br>'.esc_html__('Essential Grids', 'junotoys');
		return $not_installed;
	}
}

// Set plugin's specific importer options
if ( !function_exists( 'junotoys_essgrids_importer_set_options' ) ) {
	//Handler of add_filter( 'junotoys_filter_importer_options',	'junotoys_essgrids_importer_set_options', 10, 1 );
	function junotoys_essgrids_importer_set_options($options=array()) {
		if ( in_array('essgrids', junotoys_storage_get('required_plugins')) && junotoys_exists_essgrids() ) {
			if (is_array($options['files']) && count($options['files']) > 0) {
				foreach ($options['files'] as $k => $v) {
					$options['files'][$k]['file_with_essgrids'] = str_replace('name.ext', 'ess_grid.json', $v['file_with_']);
				}
			}
		}
		return $options;
	}
}

// Add checkbox to the one-click importer
if ( !function_exists( 'junotoys_essgrids_importer_show_params' ) ) {
	//Handler of add_action( 'junotoys_action_importer_params',	'junotoys_essgrids_importer_show_params', 10, 1 );
	function junotoys_essgrids_importer_show_params($importer) {
		$importer->show_importer_params(array(
			'slug' => 'essgrids',
			'title' => esc_html__('Import Essential Grid', 'junotoys'),
			'part' => 1
			));
	}
}

// Clear tables
if ( !function_exists( 'junotoys_essgrids_importer_clear_tables' ) ) {
	//Handler of add_action( 'junotoys_action_importer_clear_tables',	'junotoys_essgrids_importer_clear_tables', 10, 2 );
	function junotoys_essgrids_importer_clear_tables($importer, $clear_tables) {
		if (junotoys_exists_essgrids() && in_array('essgrids', junotoys_storage_get('required_plugins'))) {
			if (strpos($clear_tables, 'essgrids')!==false) {
				if ($importer->options['debug']) dfl(__('Clear Essential Grid tables', 'junotoys'));
				global $wpdb;
				$res = $wpdb->query("TRUNCATE TABLE " . esc_sql($wpdb->prefix) . Essential_Grid::TABLE_GRID);
				if ( is_wp_error( $res ) ) dfl( sprintf(__( 'Failed truncate table "%s". Error message: %s', 'junotoys' ), Essential_Grid::TABLE_GRID, $res->get_error_message()) );
			}
		}
	}
}

// Import posts
if ( !function_exists( 'junotoys_essgrids_importer_import' ) ) {
    //add_action( 'junotoys_action_importer_import',    'junotoys_essgrids_importer_import', 10, 2 );
    function junotoys_essgrids_importer_import($importer, $action) {

        if ( $action == 'import_essgrids' ) {

            if ( ($txt = $importer->get_file($importer->options['files'][$importer->options['demo_type']]['file_with_essgrids'])) != '') {

                $data = json_decode($txt, true);

                try {
                    $im = new Essential_Grid_Import();

                    // Prepare arrays with overwrite flags
                    $tmp = array();
                    if (is_array($data) && count($data) > 0) {
                        foreach ($data as $k=>$v) {
                            if ($k=='grids') {            $name = 'grids'; $name_1= 'grid'; $name_id='id'; }
                            else if ($k=='skins') {        $name = 'skins'; $name_1= 'skin'; $name_id='id'; }
                            else if ($k=='elements') {    $name = 'elements'; $name_1= 'element'; $name_id='id'; }
                            else if ($k=='navigation-skins') {    $name = 'navigation-skins'; $name1= 'nav-skin'; $name_id='id'; }
                            else if ($k=='punch-fonts') {    $name = 'punch-fonts'; $name1= 'punch-fonts'; $name_id='handle'; }
                            else if ($k=='custom-meta') {    $name = 'custom-meta'; $name1= 'custom-meta'; $name_id='handle'; }
                            if ($k=='global-css') {
                                $tmp['import-global-styles'] = "on";
                                $tmp['global-styles-overwrite'] = "append";
                            } else {
                                $tmp['import-'.$name] = "true";
                                $tmp['import-'.$name.'-'.$name_id] = array();
                                if (is_array($v) && count($v) > 0) {
                                    foreach ($v as $v1) {
                                        $tmp['import-'.$name.'-'.$name_id][] = $v1[$name_id];
                                        $tmp[$name_1.'-overwrite-'.$name_id] = 'append';
                                    }
                                }
                            }
                        }
                    }
                    $im->set_overwrite_data($tmp); //set overwrite data global to class

                    $skins = isset($data['skins']) ? $data['skins'] : '';
                    if (!empty($skins) && is_array($skins)){
                        $skins_ids = isset($tmp['import-skins-id']) ? $tmp['import-skins-id'] : '';
                        $skins_imported = $im->import_skins($skins, $skins_ids);
                    }

                    $navigation_skins = isset($data['navigation-skins']) ? $data['navigation-skins'] : '';
                    if (!empty($navigation_skins) && is_array($navigation_skins)){
                        $navigation_skins_ids = isset($tmp['import-navigation-skins-id']) ? $tmp['import-navigation-skins-id'] : '';
                        $navigation_skins_imported = $im->import_navigation_skins($navigation_skins, $navigation_skins_ids);
                    }

                    $grids = isset($data['grids']) ? $data['grids'] : '';
                    if (!empty($grids) && is_array($grids)){
                        $grids_ids = isset($tmp['import-grids-id']) ? $tmp['import-grids-id'] : '';
                        $grids_imported = $im->import_grids($grids, $grids_ids);
                    }

                    $elements = isset($data['elements']) ? $data['elements'] : '';
                    if (!empty($elements) && is_array($elements)){
                        $elements_ids = isset($tmp['import-elements-id']) ? $tmp['import-elements-id'] : '';
                        $elements_imported = $im->import_elements($elements, $elements_ids);
                    }

                    $custom_metas = isset($data['custom-meta']) ? $data['custom-meta'] : '';
                    if (!empty($custom_metas) && is_array($custom_metas)){
                        $custom_metas_handle = isset($tmp['import-custom-meta-handle']) ? $tmp['import-custom-meta-handle'] : '';
                        $custom_metas_imported = $im->import_custom_meta($custom_metas, $custom_metas_handle);
                    }

                    $custom_fonts = isset($data['punch-fonts']) ? $data['punch-fonts'] : '';
                    if (!empty($custom_fonts) && is_array($custom_fonts)){
                        $custom_fonts_handle = isset($tmp['import-punch-fonts-handle']) ? $tmp['import-punch-fonts-handle'] : '';
                        $custom_fonts_imported = $im->import_punch_fonts($custom_fonts, $custom_fonts_handle);
                    }

                    if (isset($tmp['import-global-styles']) && $tmp['import-global-styles'] == 'on'){
                        $global_css = isset($data['global-css']) ? $data['global-css'] : '';
                        $global_styles_imported = $im->import_global_styles($tglobal_css);
                    }

                    if ($importer->options['debug'])
                        dfl( __('Essential Grid import complete', 'junotoys') );

                } catch (Exception $d) {
                    $msg = sprintf(esc_html__('Essential Grid import error: %s', 'junotoys'), $d->getMessage());
                    $importer->response['error'] = $msg;
                    if ($importer->options['debug'])
                        dfl( $msg );

                }
            }
        }
    }
}// Import posts
if ( !function_exists( 'junotoys_essgrids_importer_import' ) ) {
    //add_action( 'junotoys_action_importer_import',    'junotoys_essgrids_importer_import', 10, 2 );
    function junotoys_essgrids_importer_import($importer, $action) {

        if ( $action == 'import_essgrids' ) {

            if ( ($txt = $importer->get_file($importer->options['files'][$importer->options['demo_type']]['file_with_essgrids'])) != '') {

                $data = json_decode($txt, true);

                try {
                    $im = new Essential_Grid_Import();

                    // Prepare arrays with overwrite flags
                    $tmp = array();
                    if (is_array($data) && count($data) > 0) {
                        foreach ($data as $k=>$v) {
                            if ($k=='grids') {            $name = 'grids'; $name_1= 'grid'; $name_id='id'; }
                            else if ($k=='skins') {        $name = 'skins'; $name_1= 'skin'; $name_id='id'; }
                            else if ($k=='elements') {    $name = 'elements'; $name_1= 'element'; $name_id='id'; }
                            else if ($k=='navigation-skins') {    $name = 'navigation-skins'; $name1= 'nav-skin'; $name_id='id'; }
                            else if ($k=='punch-fonts') {    $name = 'punch-fonts'; $name1= 'punch-fonts'; $name_id='handle'; }
                            else if ($k=='custom-meta') {    $name = 'custom-meta'; $name1= 'custom-meta'; $name_id='handle'; }
                            if ($k=='global-css') {
                                $tmp['import-global-styles'] = "on";
                                $tmp['global-styles-overwrite'] = "append";
                            } else {
                                $tmp['import-'.$name] = "true";
                                $tmp['import-'.$name.'-'.$name_id] = array();
                                if (is_array($v) && count($v) > 0) {
                                    foreach ($v as $v1) {
                                        $tmp['import-'.$name.'-'.$name_id][] = $v1[$name_id];
                                        $tmp[$name_1.'-overwrite-'.$name_id] = 'append';
                                    }
                                }
                            }
                        }
                    }
                    $im->set_overwrite_data($tmp); //set overwrite data global to class

                    $skins = isset($data['skins']) ? $data['skins'] : '';
                    if (!empty($skins) && is_array($skins)){
                        $skins_ids = isset($tmp['import-skins-id']) ? $tmp['import-skins-id'] : '';
                        $skins_imported = $im->import_skins($skins, $skins_ids);
                    }

                    $navigation_skins = isset($data['navigation-skins']) ? $data['navigation-skins'] : '';
                    if (!empty($navigation_skins) && is_array($navigation_skins)){
                        $navigation_skins_ids = isset($tmp['import-navigation-skins-id']) ? $tmp['import-navigation-skins-id'] : '';
                        $navigation_skins_imported = $im->import_navigation_skins($navigation_skins, $navigation_skins_ids);
                    }

                    $grids = isset($data['grids']) ? $data['grids'] : '';
                    if (!empty($grids) && is_array($grids)){
                        $grids_ids = isset($tmp['import-grids-id']) ? $tmp['import-grids-id'] : '';
                        $grids_imported = $im->import_grids($grids, $grids_ids);
                    }

                    $elements = isset($data['elements']) ? $data['elements'] : '';
                    if (!empty($elements) && is_array($elements)){
                        $elements_ids = isset($tmp['import-elements-id']) ? $tmp['import-elements-id'] : '';
                        $elements_imported = $im->import_elements($elements, $elements_ids);
                    }

                    $custom_metas = isset($data['custom-meta']) ? $data['custom-meta'] : '';
                    if (!empty($custom_metas) && is_array($custom_metas)){
                        $custom_metas_handle = isset($tmp['import-custom-meta-handle']) ? $tmp['import-custom-meta-handle'] : '';
                        $custom_metas_imported = $im->import_custom_meta($custom_metas, $custom_metas_handle);
                    }

                    $custom_fonts = isset($data['punch-fonts']) ? $data['punch-fonts'] : '';
                    if (!empty($custom_fonts) && is_array($custom_fonts)){
                        $custom_fonts_handle = isset($tmp['import-punch-fonts-handle']) ? $tmp['import-punch-fonts-handle'] : '';
                        $custom_fonts_imported = $im->import_punch_fonts($custom_fonts, $custom_fonts_handle);
                    }

                    if (isset($tmp['import-global-styles']) && $tmp['import-global-styles'] == 'on'){
                        $global_css = isset($data['global-css']) ? $data['global-css'] : '';
                        $global_styles_imported = $im->import_global_styles($tglobal_css);
                    }

                    if ($importer->options['debug'])
                        dfl( __('Essential Grid import complete', 'junotoys') );

                } catch (Exception $d) {
                    $msg = sprintf(esc_html__('Essential Grid import error: %s', 'junotoys'), $d->getMessage());
                    $importer->response['error'] = $msg;
                    if ($importer->options['debug'])
                        dfl( $msg );

                }
            }
        }
    }
}

// Check if the row will be imported
if ( !function_exists( 'junotoys_essgrids_importer_check_row' ) ) {
	//Handler of add_filter('junotoys_filter_importer_import_row', 'junotoys_essgrids_importer_check_row', 9, 4);
	function junotoys_essgrids_importer_check_row($flag, $table, $row, $list) {
		if ($flag || strpos($list, 'essgrids')===false) return $flag;
		if ( junotoys_exists_essgrids() ) {
			if ($table == 'posts')
				$flag = $row['post_type']==apply_filters('essgrid_PunchPost_custom_post_type', 'essential_grid');
		}
		return $flag;
	}
}


// Display import progress
if ( !function_exists( 'junotoys_essgrids_importer_import_fields' ) ) {
	//Handler of add_action( 'junotoys_action_importer_import_fields',	'junotoys_essgrids_importer_import_fields', 10, 1 );
	function junotoys_essgrids_importer_import_fields($importer) {
		$importer->show_importer_fields(array(
			'slug' => 'essgrids',
			'title' => esc_html__('Essential Grid', 'junotoys')
			));
	}
}
?>