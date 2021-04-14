<?php 
//if (is_singular()) {
//	if (junotoys_get_theme_option('use_ajax_views_counter')=='yes') {
//		junotoys_storage_set_array('js_vars', 'ajax_views_counter', array(
//			'post_id' => get_the_ID(),
//			'post_views' => junotoys_get_post_views(get_the_ID())
//		));
//	} else
//		junotoys_set_post_views(get_the_ID());
//}

if (is_singular() && junotoys_get_theme_option('use_ajax_views_counter')=='no') {
    junotoys_set_post_views(get_the_ID());
}
?>