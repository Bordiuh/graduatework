<?php
/*
 * The template for displaying "Page 404"
*/

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'junotoys_template_404_theme_setup' ) ) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_template_404_theme_setup', 1 );
	function junotoys_template_404_theme_setup() {
		junotoys_add_template(array(
			'layout' => '404',
			'mode'   => 'internal',
			'title'  => 'Page 404',
			'theme_options' => array(
				'article_style' => 'stretch'
			)
		));
	}
}

// Template output
if ( !function_exists( 'junotoys_template_404_output' ) ) {
	function junotoys_template_404_output() {
		?>
		<article class="post_item post_item_404">
			<div class="post_content">
				<h1 class="page_title"><?php esc_html_e( '404', 'junotoys' ); ?></h1>
				<p class="page_description"><?php echo wp_kses_data( sprintf( __('Can\'t find what you need? Take a moment and  start from <a href="%s">our homepage</a>.', 'junotoys'), esc_url(home_url('/')) ) ); ?></p>
				<a href="<?php echo esc_url(home_url('/')); ?>" class="sc_button sc_button_round sc_button_color_5 large sc_button_size_large"><?php esc_html_e( 'Go Back', 'junotoys' ); ?></a>
			</div>
		</article>
		<?php
	}
}
?>