<?php
/**
 * Juno Toys Framework: messages subsystem
 *
 * @package	junotoys
 * @since	junotoys 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Theme init
if (!function_exists('junotoys_messages_theme_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_messages_theme_setup' );
	function junotoys_messages_theme_setup() {
		// Core messages strings
		add_filter('junotoys_filter_localize_script', 'junotoys_messages_localize_script');
	}
}


/* Session messages
------------------------------------------------------------------------------------- */

if (!function_exists('junotoys_get_error_msg')) {
	function junotoys_get_error_msg() {
		return junotoys_storage_get('error_msg');
	}
}

if (!function_exists('junotoys_set_error_msg')) {
	function junotoys_set_error_msg($msg) {
		$msg2 = junotoys_get_error_msg();
		junotoys_storage_set('error_msg', trim($msg2) . ($msg2=='' ? '' : '<br />') . trim($msg));
	}
}

if (!function_exists('junotoys_get_success_msg')) {
	function junotoys_get_success_msg() {
		return junotoys_storage_get('success_msg');
	}
}

if (!function_exists('junotoys_set_success_msg')) {
	function junotoys_set_success_msg($msg) {
		$msg2 = junotoys_get_success_msg();
		junotoys_storage_set('success_msg', trim($msg2) . ($msg2=='' ? '' : '<br />') . trim($msg));
	}
}

if (!function_exists('junotoys_get_notice_msg')) {
	function junotoys_get_notice_msg() {
		return junotoys_storage_get('notice_msg');
	}
}

if (!function_exists('junotoys_set_notice_msg')) {
	function junotoys_set_notice_msg($msg) {
		$msg2 = junotoys_get_notice_msg();
		junotoys_storage_set('notice_msg', trim($msg2) . ($msg2=='' ? '' : '<br />') . trim($msg));
	}
}


/* System messages (save when page reload)
------------------------------------------------------------------------------------- */
if (!function_exists('junotoys_set_system_message')) {
	function junotoys_set_system_message($msg, $status='info', $hdr='') {
		update_option(junotoys_storage_get('options_prefix') . '_message', array('message' => $msg, 'status' => $status, 'header' => $hdr));
	}
}

if (!function_exists('junotoys_get_system_message')) {
	function junotoys_get_system_message($del=false) {
		$msg = get_option(junotoys_storage_get('options_prefix') . '_message', false);
		if (!$msg)
			$msg = array('message' => '', 'status' => '', 'header' => '');
		else if ($del)
			junotoys_del_system_message();
		return $msg;
	}
}

if (!function_exists('junotoys_del_system_message')) {
	function junotoys_del_system_message() {
		delete_option(junotoys_storage_get('options_prefix') . '_message');
	}
}


/* Messages strings
------------------------------------------------------------------------------------- */

if (!function_exists('junotoys_messages_localize_script')) {
	//Handler of add_filter('junotoys_filter_localize_script', 'junotoys_messages_localize_script');
	function junotoys_messages_localize_script($vars) {
		$vars['strings'] = array(
			'ajax_error'		=> esc_html__('Invalid server answer', 'junotoys'),
			'bookmark_add'		=> esc_html__('Add the bookmark', 'junotoys'),
            'bookmark_added'	=> esc_html__('Current page has been successfully added to the bookmarks. You can see it in the right panel on the tab \'Bookmarks\'', 'junotoys'),
            'bookmark_del'		=> esc_html__('Delete this bookmark', 'junotoys'),
            'bookmark_title'	=> esc_html__('Enter bookmark title', 'junotoys'),
            'bookmark_exists'	=> esc_html__('Current page already exists in the bookmarks list', 'junotoys'),
			'search_error'		=> esc_html__('Error occurs in AJAX search! Please, type your query and press search icon for the traditional search way.', 'junotoys'),
			'email_confirm'		=> esc_html__('On the e-mail address "%s" we sent a confirmation email. Please, open it and click on the link.', 'junotoys'),
			'reviews_vote'		=> esc_html__('Thanks for your vote! New average rating is:', 'junotoys'),
			'reviews_error'		=> esc_html__('Error saving your vote! Please, try again later.', 'junotoys'),
			'error_like'		=> esc_html__('Error saving your like! Please, try again later.', 'junotoys'),
			'error_global'		=> esc_html__('Global error text', 'junotoys'),
			'name_empty'		=> esc_html__('The name can\'t be empty', 'junotoys'),
			'name_long'			=> esc_html__('Too long name', 'junotoys'),
			'email_empty'		=> esc_html__('Too short (or empty) email address', 'junotoys'),
			'email_long'		=> esc_html__('Too long email address', 'junotoys'),
			'email_not_valid'	=> esc_html__('Invalid email address', 'junotoys'),
			'subject_empty'		=> esc_html__('The subject can\'t be empty', 'junotoys'),
			'subject_long'		=> esc_html__('Too long subject', 'junotoys'),
			'text_empty'		=> esc_html__('The message text can\'t be empty', 'junotoys'),
			'text_long'			=> esc_html__('Too long message text', 'junotoys'),
			'send_complete'		=> esc_html__("Send message complete!", 'junotoys'),
			'send_error'		=> esc_html__('Transmit failed!', 'junotoys'),
			'not_agree'			=> esc_html__('Please, check \'I agree with Terms and Conditions\'', 'junotoys'),
			'login_empty'		=> esc_html__('The Login field can\'t be empty', 'junotoys'),
			'login_long'		=> esc_html__('Too long login field', 'junotoys'),
			'login_success'		=> esc_html__('Login success! The page will be reloaded in 3 sec.', 'junotoys'),
			'login_failed'		=> esc_html__('Login failed!', 'junotoys'),
			'password_empty'	=> esc_html__('The password can\'t be empty and shorter then 4 characters', 'junotoys'),
			'password_long'		=> esc_html__('Too long password', 'junotoys'),
			'password_not_equal'	=> esc_html__('The passwords in both fields are not equal', 'junotoys'),
			'registration_success'	=> esc_html__('Registration success! Please log in!', 'junotoys'),
			'registration_failed'	=> esc_html__('Registration failed!', 'junotoys'),
			'geocode_error'			=> esc_html__('Geocode was not successful for the following reason:', 'junotoys'),
			'googlemap_not_avail'	=> esc_html__('Google map API not available!', 'junotoys'),
			'editor_save_success'	=> esc_html__("Post content saved!", 'junotoys'),
			'editor_save_error'		=> esc_html__("Error saving post data!", 'junotoys'),
			'editor_delete_post'	=> esc_html__("You really want to delete the current post?", 'junotoys'),
			'editor_delete_post_header'	=> esc_html__("Delete post", 'junotoys'),
			'editor_delete_success'	=> esc_html__("Post deleted!", 'junotoys'),
			'editor_delete_error'	=> esc_html__("Error deleting post!", 'junotoys'),
			'editor_caption_cancel'	=> esc_html__('Cancel', 'junotoys'),
			'editor_caption_close'	=> esc_html__('Close', 'junotoys')
			);
		return $vars;
	}
}
?>