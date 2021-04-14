<?php
/**
 * Attachment page
 */
get_header(); 

while ( have_posts() ) { the_post();

	// Move junotoys_set_post_views to the javascript - counter will work under cache system
	if (junotoys_get_custom_option('use_ajax_views_counter')=='no') {
		junotoys_set_post_views(get_the_ID());
	}

	junotoys_show_post_layout(
		array(
			'layout' => 'attachment',
			'sidebar' => !junotoys_param_is_off(junotoys_get_custom_option('show_sidebar_main'))
		)
	);

}

get_footer();
?>