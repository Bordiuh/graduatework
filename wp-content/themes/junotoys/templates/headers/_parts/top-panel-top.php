<?php 
// Get template args
extract(junotoys_template_get_args('top-panel-top'));

if (in_array('contact_email_phone', $top_panel_top_components) && ($contact_email=trim(junotoys_get_custom_option('contact_email')))!='' && ($contact_phone=trim(junotoys_get_custom_option('contact_phone')))!='') {
	?>
	<div class="top_panel_top_contact_area">
		<?php esc_html_e('Contact us on ', 'junotoys'); ?>
		<?php junotoys_show_layout('<a href="tel:'.($contact_phone).'">'.($contact_phone).'</a>'); ?>
		<?php esc_html_e(' or ', 'junotoys'); ?>
		<?php echo '<a href="'.esc_url(home_url('/')).'contact-us/">'.esc_html($contact_email).'</a>'; ?>
	</div>
	<?php
}

if (in_array('contact_info', $top_panel_top_components) && ($contact_info=trim(junotoys_get_custom_option('contact_info')))!='') {
	?>
	<div class="top_panel_top_contact_area">
		<?php junotoys_show_layout($contact_info); ?>
	</div>
	<?php
}
?>

<?php
if (in_array('open_hours', $top_panel_top_components) && ($open_hours=trim(junotoys_get_custom_option('contact_open_hours')))!='') {
	?>
	<div class="top_panel_top_open_hours icon-clock"><?php junotoys_show_layout($open_hours); ?></div>
	<?php
}
?>

<div class="top_panel_top_user_area">
	<?php
	if (in_array('socials', $top_panel_top_components) && junotoys_get_custom_option('show_socials')=='yes') {
		?>
		<div class="top_panel_top_socials">
			<?php junotoys_show_layout(junotoys_sc_socials(array('size'=>'small'))); ?>
		</div>
		<?php
	}

	if (in_array('search', $top_panel_top_components) && junotoys_get_custom_option('show_search')=='yes') {
		?>
		<div class="top_panel_top_search"><?php junotoys_show_layout(junotoys_sc_search(array('class'=>"top_panel_icon", 'state'=>"closed", 'style'=>"fullscreen"))); ?></div>
		<?php
	}

	$menu_user = junotoys_get_nav_menu('menu_user');
	if (empty($menu_user)) {
		?>
		<ul id="<?php echo (!empty($menu_user_id) ? esc_attr($menu_user_id) : 'menu_user'); ?>" class="menu_user_nav">
		<?php
	} else {
		$menu = junotoys_substr($menu_user, 0, junotoys_strlen($menu_user)-5);
		$pos = junotoys_strpos($menu, '<ul');
		if ($pos!==false && junotoys_strpos($menu, 'menu_user_nav')===false)
			$menu = junotoys_substr($menu, 0, $pos+3) . ' class="menu_user_nav"' . junotoys_substr($menu, $pos+3);
		if (!empty($menu_user_id))
			$menu = junotoys_set_tag_attrib($menu, '<ul>', 'id', $menu_user_id);
		echo str_replace('class=""', '', $menu);
	}
	

	if (in_array('currency', $top_panel_top_components) && function_exists('junotoys_is_woocommerce_page') && junotoys_is_woocommerce_page() && junotoys_get_custom_option('show_currency')=='yes') {
		?>
		<li class="menu_user_currency">
			<a href="#">$</a>
			<ul>
				<li><a href="#"><b>&#36;</b> <?php esc_html_e('Dollar', 'junotoys'); ?></a></li>
				<li><a href="#"><b>&euro;</b> <?php esc_html_e('Euro', 'junotoys'); ?></a></li>
				<li><a href="#"><b>&pound;</b> <?php esc_html_e('Pounds', 'junotoys'); ?></a></li>
			</ul>
		</li>
		<?php
	}

	if (in_array('language', $top_panel_top_components) && junotoys_get_custom_option('show_languages')=='yes' && function_exists('icl_get_languages')) {
		$languages = icl_get_languages('skip_missing=1');
		if (!empty($languages) && is_array($languages)) {
			$lang_list = '';
			$lang_active = '';
			foreach ($languages as $lang) {
				$lang_title = esc_attr($lang['translated_name']);	//esc_attr($lang['native_name']);
				if ($lang['active']) {
					$lang_active = $lang_title;
				}
				$lang_list .= "\n"
					.'<li><a rel="alternate" hreflang="' . esc_attr($lang['language_code']) . '" href="' . esc_url(apply_filters('WPML_filter_link', $lang['url'], $lang)) . '">'
						.'<img src="' . esc_url($lang['country_flag_url']) . '" alt="' . esc_attr($lang_title) . '" title="' . esc_attr($lang_title) . '" />'
						. ($lang_title)
					.'</a></li>';
			}
			?>
			<li class="menu_user_language">
				<a href="#"><span><?php junotoys_show_layout($lang_active); ?></span></a>
				<ul><?php junotoys_show_layout($lang_list); ?></ul>
			</li>
			<?php
		}
	}

	if (in_array('bookmarks', $top_panel_top_components) && junotoys_get_custom_option('show_bookmarks')=='yes') {
		// Load core messages
		junotoys_enqueue_messages();
		?>
		<li class="menu_user_bookmarks"><a href="#" class="bookmarks_show icon-star" title="<?php esc_attr_e('Show bookmarks', 'junotoys'); ?>"><?php esc_html_e('Bookmarks', 'junotoys'); ?></a>
		<?php 
			$list = junotoys_get_value_gpc('junotoys_bookmarks', '');
			if (!empty($list)) $list = json_decode($list, true);
			?>
			<ul class="bookmarks_list">
				<li><a href="#" class="bookmarks_add icon-star-empty" title="<?php esc_attr_e('Add the current page into bookmarks', 'junotoys'); ?>"><?php esc_html_e('Add bookmark', 'junotoys'); ?></a></li>
				<?php 
				if (!empty($list) && is_array($list)) {
					foreach ($list as $bm) {
						echo '<li><a href="'.esc_url($bm['url']).'" class="bookmarks_item">'.($bm['title']).'<span class="bookmarks_delete icon-cancel" title="'.esc_attr__('Delete this bookmark', 'junotoys').'"></span></a></li>';
					}
				}
				?>
			</ul>
		</li>
		<?php 
	}

	if (in_array('login', $top_panel_top_components) && junotoys_get_custom_option('show_login')=='yes') {
        if ( !is_user_logged_in() ) {
            // Load core messages
            junotoys_enqueue_messages();
            // Load Popup engine
            junotoys_enqueue_popup();
            // Anyone can register ?
            if ( (int) get_option('users_can_register') > 0) {
                ?><li class="menu_user_register"><?php do_action('trx_utils_action_register'); ?></li><?php
            }
            ?><li class="menu_user_login"><?php do_action('trx_utils_action_login'); ?></li><?php
        } else {
			$current_user = wp_get_current_user();
			?>
			<li class="menu_user_controls">
				<a href="#"><?php
					$user_avatar = '';
					$mult = junotoys_get_retina_multiplier();
					if ($current_user->user_email) $user_avatar = get_avatar($current_user->user_email, 16*$mult);
					if ($user_avatar) {
						?><span class="user_avatar"><?php junotoys_show_layout($user_avatar); ?></span><?php
					}?><span class="user_name"><?php junotoys_show_layout($current_user->display_name); ?></span></a>
				<ul>
					<?php if (current_user_can('publish_posts')) { ?>
					<li><a href="<?php echo esc_url(home_url('/')); ?>/wp-admin/post-new.php?post_type=post"><?php esc_html_e('New post', 'junotoys'); ?></a></li>
					<?php } ?>
					<li><a href="<?php echo get_edit_user_link(); ?>"><?php esc_html_e('Settings', 'junotoys'); ?></a></li>
				</ul>
			</li>
			<li class="menu_user_logout"><a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>"><?php esc_html_e('Logout', 'junotoys'); ?></a></li>
			<?php 
		}
	}

	if (in_array('cart', $top_panel_top_components) && function_exists('junotoys_exists_woocommerce') && junotoys_exists_woocommerce() && (junotoys_is_woocommerce_page() && junotoys_get_custom_option('show_cart')=='shop' || junotoys_get_custom_option('show_cart')=='always') && !(is_checkout() || is_cart() || defined('WOOCOMMERCE_CHECKOUT') || defined('WOOCOMMERCE_CART'))) { 
		?>
		<li class="menu_user_cart">
			<?php get_template_part(junotoys_get_file_slug('templates/headers/_parts/contact-info-cart.php')); ?>
		</li>
		<?php
	}
	?>

	</ul>

</div>