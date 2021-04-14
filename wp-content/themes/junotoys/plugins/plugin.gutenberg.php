<?php
/* Gutenberg support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('junotoys_gutenberg_theme_setup')) {
    add_action( 'junotoys_action_before_init_theme', 'junotoys_gutenberg_theme_setup', 1 );
    function junotoys_gutenberg_theme_setup() {
        if (is_admin()) {
            add_filter( 'junotoys_filter_required_plugins', 'junotoys_gutenberg_required_plugins' );
        }
    }
}

// Check if Instagram Widget installed and activated
if ( !function_exists( 'junotoys_exists_gutenberg' ) ) {
    function junotoys_exists_gutenberg() {
        return function_exists( 'the_gutenberg_project' ) && function_exists( 'register_block_type' );
    }
}

// Filter to add in the required plugins list
if ( !function_exists( 'junotoys_gutenberg_required_plugins' ) ) {
    //add_filter('junotoys_filter_required_plugins',    'junotoys_gutenberg_required_plugins');
    function junotoys_gutenberg_required_plugins($list=array()) {
        if (in_array('gutenberg', (array)junotoys_storage_get('required_plugins')))
            $list[] = array(
                'name'         => esc_html__('Gutenberg', 'junotoys'),
                'slug'         => 'gutenberg',
                'required'     => false
            );
        return $list;
    }
}