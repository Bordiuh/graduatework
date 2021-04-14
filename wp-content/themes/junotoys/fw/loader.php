<?php
/**
 * Juno Toys Framework
 *
 * @package junotoys
 * @since junotoys 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Framework directory path from theme root
if ( ! defined( 'JUNOTOYS_FW_DIR' ) )		define( 'JUNOTOYS_FW_DIR',		'fw' );
if ( ! defined( 'JUNOTOYS_THEME_PATH' ) )	define( 'JUNOTOYS_THEME_PATH',	trailingslashit( get_template_directory() ) );
if ( ! defined( 'JUNOTOYS_FW_PATH' ) )		define( 'JUNOTOYS_FW_PATH',		JUNOTOYS_THEME_PATH . JUNOTOYS_FW_DIR . '/' );

// Include theme variables storage
require_once JUNOTOYS_FW_PATH . 'core/core.storage.php';

// Theme variables storage
junotoys_storage_set('options_prefix', 'junotoys');	//.'_'.str_replace(' ', '_', trim(strtolower(get_stylesheet()))));	// Prefix for the theme options in the postmeta and wp options
junotoys_storage_set('page_template', '');			// Storage for current page template name (used in the inheritance system)
junotoys_storage_set('widgets_args', array(			// Arguments to register widgets
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h5 class="widget_title">',
		'after_title'   => '</h5>',
	)
);

/* Theme setup section
-------------------------------------------------------------------- */
if ( !function_exists( 'junotoys_loader_theme_setup' ) ) {
	add_action( 'after_setup_theme', 'junotoys_loader_theme_setup', 20 );
	function junotoys_loader_theme_setup() {

		// Before init theme
		do_action('junotoys_action_before_init_theme');

		// Load current values for main theme options
		junotoys_load_main_options();

		// Theme core init - only for admin side. In frontend it called from action 'wp'
		if ( is_admin() ) {
			junotoys_core_init_theme();
		}
	}
}


/* Include core parts
------------------------------------------------------------------------ */
// Manual load important libraries before load all rest files
// core.strings must be first - we use junotoys_str...() in the junotoys_get_file_dir()
require_once JUNOTOYS_FW_PATH . '/core/core.strings.php';
// core.files must be first - we use junotoys_get_file_dir() to include all rest parts
require_once JUNOTOYS_FW_PATH . '/core/core.files.php';

// Include debug utilities
require_once JUNOTOYS_FW_PATH . '/core/core.debug.php';

// Include custom theme files
junotoys_autoload_folder( 'includes' );

// Include core files
junotoys_autoload_folder( 'core' );

// Include theme-specific plugins and post types
junotoys_autoload_folder( 'plugins' );

// Include theme templates
junotoys_autoload_folder( 'templates' );

// Include theme widgets
junotoys_autoload_folder( 'widgets' );
?>