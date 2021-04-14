<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'junotoys_template_header_9_theme_setup' ) ) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_template_header_9_theme_setup', 1 );
	function junotoys_template_header_9_theme_setup() {
		junotoys_add_template(array(
			'layout' => 'header_9',
			'mode'   => 'header',
			'title'  => esc_html__('Header 9', 'junotoys'),
			'icon'   => junotoys_get_file_url('templates/headers/images/9.jpg')
			));
	}
}

// Template output
if ( !function_exists( 'junotoys_template_header_9_output' ) ) {
	function junotoys_template_header_9_output($post_options, $post_data) {

		// WP custom header
		$header_css = '';
		if ($post_options['position'] != 'over') {
			$header_image = get_header_image();
			$header_css = $header_image!='' 
				? ' style="background-image: url('.esc_url($header_image).')"' 
				: '';
		}
		?>

		<div class="top_panel_fixed_wrap"></div>

		<header class="top_panel_wrap top_panel_style_9 scheme_<?php echo esc_attr($post_options['scheme']); ?>">
			<div class="top_panel_wrap_inner top_panel_inner_style_9 top_panel_position_<?php echo esc_attr(junotoys_get_custom_option('top_panel_position')); ?>">
			
			<?php
            $is_mega_menu = false;
            $menu_slug = junotoys_get_custom_option('menu_main');
            if (!empty($menu_slug)) {
                $menu_obj = wp_get_nav_menu_object($menu_slug);
                $locations = get_nav_menu_locations();
                $is_mega_menu = !empty($locations['mega_menu']) && !empty($menu_obj->term_id) && $locations['mega_menu']==$menu_obj->term_id;
            }
            if (!$is_mega_menu)
            {
			?>
			<?php if (junotoys_get_custom_option('show_top_panel_top')=='yes') { ?>
				<div class="top_panel_top">
					<div class="content_wrap clearfix">
						<?php
						junotoys_template_set_args('top-panel-top', array(
							'top_panel_top_components' => array('contact_email_phone', 'login', 'socials', 'search')
						));
						get_template_part(junotoys_get_file_slug('templates/headers/_parts/top-panel-top.php'));
						?>
					</div>
				</div>
			<?php } ?>

				<div class="top_panel_middle" <?php junotoys_show_layout($header_css); ?>>
					<div class="content_wrap">
						<nav class="menu_main_nav_area">
							<?php
							$menu_main = junotoys_get_nav_menu('menu_main');
							if (empty($menu_main)) $menu_main = junotoys_get_nav_menu();
							junotoys_show_layout($menu_main);
							?>
						</nav>
						<div class="contact_logo">
							<?php junotoys_show_logo(); ?>
						</div>
					</div>
				</div>
		   <?php
           }
           else{
                ?>
                <div class="content_wrap">
                    <?php
                    $mega_menu = junotoys_get_nav_menu('mega_menu');
                    junotoys_show_layout($mega_menu);
                    ?>
                </div>
            <?php
            }
			?>
			</div>
		</header>

		<?php
		junotoys_storage_set('header_mobile', array(
				 'open_hours' => true,
				 'login' => true,
				 'socials' => true,
				 'bookmarks' => true,
				 'contact_address' => true,
				 'contact_phone_email' => true,
				 'woo_cart' => true,
				 'search' => true
			)
		);
	}
}
?>