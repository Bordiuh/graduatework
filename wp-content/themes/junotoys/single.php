<?php
/**
 * Single post
 */
get_header(); 

$single_style = junotoys_storage_get('single_style');
if (empty($single_style)) $single_style = junotoys_get_custom_option('single_style');

while ( have_posts() ) { the_post();
	junotoys_show_post_layout(
		array(
			'layout' => $single_style,
			'sidebar' => !junotoys_param_is_off(junotoys_get_custom_option('show_sidebar_main')),
			'content' => junotoys_get_template_property($single_style, 'need_content'),
			'terms_list' => junotoys_get_template_property($single_style, 'need_terms')
		)
	);
}

get_footer();
?>