<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'junotoys_template_no_articles_theme_setup' ) ) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_template_no_articles_theme_setup', 1 );
	function junotoys_template_no_articles_theme_setup() {
		junotoys_add_template(array(
			'layout' => 'no-articles',
			'mode'   => 'internal',
			'title'  => esc_html__('No articles found', 'junotoys')
		));
	}
}

// Template output
if ( !function_exists( 'junotoys_template_no_articles_output' ) ) {
	function junotoys_template_no_articles_output($post_options, $post_data) {
		?>
		<article class="post_item">
			<div class="post_content">
				<h2 class="post_title"><?php esc_html_e('No posts found', 'junotoys'); ?></h2>
				<p><?php esc_html_e( 'Sorry, but nothing matched your search criteria.', 'junotoys' ); ?></p>
				<p><?php echo wp_kses_data( sprintf(__('Go back, or return to <a href="%s">%s</a> home page to choose a new page.', 'junotoys'), esc_url(home_url('/')), get_bloginfo()) ); ?>
				<br><?php esc_html_e('Please report any broken links to our team.', 'junotoys'); ?></p>
				<?php junotoys_show_layout(junotoys_sc_search(array('state'=>"fixed"))); ?>
			</div>	<!-- /.post_content -->
		</article>	<!-- /.post_item -->
		<?php
	}
}
?>