<?php
// Get template args
extract(junotoys_template_get_args('counters'));

$show_all_counters = !isset($post_options['counters']);
$counters_tag = is_single() ? 'span' : 'a';

// Views
if ($show_all_counters || junotoys_strpos($post_options['counters'], 'views')!==false) {
	?>
	<<?php junotoys_show_layout($counters_tag); ?> class="post_counters_item post_counters_views icon-eye74" title="<?php echo esc_attr( sprintf(__('Views - %s', 'junotoys'), $post_data['post_views']) ); ?>" href="<?php echo esc_url($post_data['post_link']); ?>"><span class="post_counters_number"><?php junotoys_show_layout($post_data['post_views']); ?></span><?php if (junotoys_strpos($post_options['counters'], 'captions')!==false) echo ' '.esc_html__('Views', 'junotoys'); ?></<?php junotoys_show_layout($counters_tag); ?>>
	<?php
}

// Comments
if ($show_all_counters || junotoys_strpos($post_options['counters'], 'comments')!==false) {
	?>
	<a class="post_counters_item post_counters_comments icon-chat34" title="<?php echo esc_attr( sprintf(__('Comments - %s', 'junotoys'), $post_data['post_comments']) ); ?>" href="<?php echo esc_url($post_data['post_comments_link']); ?>"><span class="post_counters_number"><?php junotoys_show_layout($post_data['post_comments']); ?></span><?php if (junotoys_strpos($post_options['counters'], 'captions')!==false) echo ' '.esc_html__('Comments', 'junotoys'); ?></a>
	<?php 
}
 
// Rating
$rating = $post_data['post_reviews_'.(junotoys_get_theme_option('reviews_first')=='author' ? 'author' : 'users')];
if ($rating > 0 && ($show_all_counters || junotoys_strpos($post_options['counters'], 'rating')!==false)) { 
	?>
	<<?php junotoys_show_layout($counters_tag); ?> class="post_counters_item post_counters_rating icon-star" title="<?php echo esc_attr( sprintf(__('Rating - %s', 'junotoys'), $rating) ); ?>" href="<?php echo esc_url($post_data['post_link']); ?>"><span class="post_counters_number"><?php junotoys_show_layout($rating); ?></span></<?php junotoys_show_layout($counters_tag); ?>>
	<?php
}

// Likes
if ($show_all_counters || junotoys_strpos($post_options['counters'], 'likes')!==false) {
	// Load core messages
	junotoys_enqueue_messages();
	$likes = isset($_COOKIE['junotoys_likes']) ? $_COOKIE['junotoys_likes'] : '';
	$allow = junotoys_strpos($likes, ','.($post_data['post_id']).',')===false;
	?>
	<a class="post_counters_item post_counters_likes icon-heart213 <?php echo !empty($allow) ? 'enabled' : 'disabled'; ?>" title="<?php echo !empty($allow) ? esc_attr__('Like', 'junotoys') : esc_attr__('Dislike', 'junotoys'); ?>" href="#"
		data-postid="<?php echo esc_attr($post_data['post_id']); ?>"
		data-likes="<?php echo esc_attr($post_data['post_likes']); ?>"
		data-title-like="<?php esc_attr_e('Like', 'junotoys'); ?>"
		data-title-dislike="<?php esc_attr_e('Dislike', 'junotoys'); ?>"><span class="post_counters_number"><?php junotoys_show_layout($post_data['post_likes']); ?></span><?php if (junotoys_strpos($post_options['counters'], 'captions')!==false) echo ' '.esc_html__('Likes', 'junotoys'); ?></a>
	<?php
}

// Edit page link
if (junotoys_strpos($post_options['counters'], 'edit')!==false) {
	edit_post_link( esc_html__( 'Edit', 'junotoys' ), '<span class="post_edit edit-link">', '</span>' );
}

// Markup for search engines
if (is_single() && junotoys_strpos($post_options['counters'], 'markup')!==false) {
	?>
	<meta itemprop="interactionCount" content="User<?php echo esc_attr(junotoys_strpos($post_options['counters'],'comments')!==false ? 'Comments' : 'PageVisits'); ?>:<?php echo esc_attr(junotoys_strpos($post_options['counters'], 'comments')!==false ? $post_data['post_comments'] : $post_data['post_views']); ?>" />
	<?php
}
?>