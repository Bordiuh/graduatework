<?php
/**
 * Juno Toys Framework: Theme options custom fields
 *
 * @package	junotoys
 * @since	junotoys 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'junotoys_options_custom_theme_setup' ) ) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_options_custom_theme_setup' );
	function junotoys_options_custom_theme_setup() {

		if ( is_admin() ) {
			add_action("admin_enqueue_scripts",	'junotoys_options_custom_load_scripts');
		}
		
	}
}

// Load required styles and scripts for custom options fields
if ( !function_exists( 'junotoys_options_custom_load_scripts' ) ) {
	//Handler of add_action("admin_enqueue_scripts", 'junotoys_options_custom_load_scripts');
	function junotoys_options_custom_load_scripts() {
		wp_enqueue_script( 'junotoys-options-custom-script',	junotoys_get_file_url('core/core.options/js/core.options-custom.js'), array(), null, true );
	}
}


// Show theme specific fields in Post (and Page) options
if ( !function_exists( 'junotoys_show_custom_field' ) ) {
	function junotoys_show_custom_field($id, $field, $value) {
		$output = '';
		switch ($field['type']) {
			case 'reviews':
				$output .= '<div class="reviews_block">' . trim(junotoys_reviews_get_markup($field, $value, true)) . '</div>';
				break;
	
			case 'mediamanager':
				wp_enqueue_media( );
				$output .= '<a id="'.esc_attr($id).'" class="button mediamanager junotoys_media_selector"
					data-param="' . esc_attr($id) . '"
					data-choose="'.esc_attr(isset($field['multiple']) && $field['multiple'] ? esc_html__( 'Choose Images', 'junotoys') : esc_html__( 'Choose Image', 'junotoys')).'"
					data-update="'.esc_attr(isset($field['multiple']) && $field['multiple'] ? esc_html__( 'Add to Gallery', 'junotoys') : esc_html__( 'Choose Image', 'junotoys')).'"
					data-multiple="'.esc_attr(isset($field['multiple']) && $field['multiple'] ? 'true' : 'false').'"
					data-linked-field="'.esc_attr($field['media_field_id']).'"
					>' . (isset($field['multiple']) && $field['multiple'] ? esc_html__( 'Choose Images', 'junotoys') : esc_html__( 'Choose Image', 'junotoys')) . '</a>';
				break;
		}
		return apply_filters('junotoys_filter_show_custom_field', $output, $id, $field, $value);
	}
}
?>