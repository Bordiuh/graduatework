<?php
/**
 * Juno Toys Framework: Testimonial support
 *
 * @package	junotoys
 * @since	junotoys 1.0
 */

// Theme init
if (!function_exists('junotoys_testimonial_theme_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_testimonial_theme_setup', 1 );
	function junotoys_testimonial_theme_setup() {
	
		// Add item in the admin menu
		add_action('trx_utils_filter_override_options',		'junotoys_testimonial_add_override_options');

		// Save data from override options
		add_action('save_post',				'junotoys_testimonial_save_data');

		// Register shortcodes [trx_testimonials] and [trx_testimonials_item]
		add_action('junotoys_action_shortcodes_list',		'junotoys_testimonials_reg_shortcodes');
		if (function_exists('junotoys_exists_visual_composer') && junotoys_exists_visual_composer())
			add_action('junotoys_action_shortcodes_list_vc','junotoys_testimonials_reg_shortcodes_vc');

		// Meta box fields
		junotoys_storage_set('testimonial_override_options', array(
			'id' => 'testimonial-override-options',
			'title' => esc_html__('Testimonial Details', 'junotoys'),
			'page' => 'testimonial',
			'context' => 'normal',
			'priority' => 'high',
			'fields' => array(
				"testimonial_author" => array(
					"title" => esc_html__('Testimonial author',  'junotoys'),
					"desc" => wp_kses_data( __("Name of the testimonial's author", 'junotoys') ),
					"class" => "testimonial_author",
					"std" => "",
					"type" => "text"),
				"testimonial_position" => array(
					"title" => esc_html__("Author's position",  'junotoys'),
					"desc" => wp_kses_data( __("Position of the testimonial's author", 'junotoys') ),
					"class" => "testimonial_author",
					"std" => "",
					"type" => "text"),
				"testimonial_email" => array(
					"title" => esc_html__("Author's e-mail",  'junotoys'),
					"desc" => wp_kses_data( __("E-mail of the testimonial's author - need to take Gravatar (if registered)", 'junotoys') ),
					"class" => "testimonial_email",
					"std" => "",
					"type" => "text"),
				"testimonial_link" => array(
					"title" => esc_html__('Testimonial link',  'junotoys'),
					"desc" => wp_kses_data( __("URL of the testimonial source or author profile page", 'junotoys') ),
					"class" => "testimonial_link",
					"std" => "",
					"type" => "text")
				)
			)
		);
		
		// Add supported data types
		junotoys_theme_support_pt('testimonial');
		junotoys_theme_support_tx('testimonial_group');
		
	}
}




// Add override options
if (!function_exists('junotoys_testimonial_add_override_options')) {
    //add_action('trx_utils_filter_override_options', 'junotoys_testimonial_add_override_options');
    function junotoys_testimonial_add_override_options($boxes = array()) {
        $boxes[] = array_merge(junotoys_storage_get('testimonial_override_options'), array('callback' => 'junotoys_testimonial_show_override_options'));
        return $boxes;
    }
}


// Callback function to show fields in override options
if (!function_exists('junotoys_testimonial_show_override_options')) {
	function junotoys_testimonial_show_override_options() {
		global $post;

		// Use nonce for verification
		echo '<input type="hidden" name="override_options_testimonial_nonce" value="'.esc_attr(wp_create_nonce(admin_url())).'" />';
		
		$data = get_post_meta($post->ID, junotoys_storage_get('options_prefix').'_testimonial_data', true);
	
		$fields = junotoys_storage_get_array('testimonial_override_options', 'fields');
		?>
		<table class="testimonial_area">
		<?php
		if (is_array($fields) && count($fields) > 0) {
			foreach ($fields as $id=>$field) { 
				$meta = isset($data[$id]) ? $data[$id] : '';
				?>
				<tr class="testimonial_field <?php echo esc_attr($field['class']); ?>" valign="top">
					<td><label for="<?php echo esc_attr($id); ?>"><?php echo esc_attr($field['title']); ?></label></td>
					<td><input type="text" name="<?php echo esc_attr($id); ?>" id="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($meta); ?>" size="30" />
						<br><small><?php echo esc_attr($field['desc']); ?></small></td>
				</tr>
				<?php
			}
		}
		?>
		</table>
		<?php
	}
}


// Save data from override options
if (!function_exists('junotoys_testimonial_save_data')) {
	//Handler of add_action('save_post', 'junotoys_testimonial_save_data');
	function junotoys_testimonial_save_data($post_id) {
		// verify nonce
		if ( !wp_verify_nonce( junotoys_get_value_gp('override_options_testimonial_nonce'), admin_url() ) )
			return $post_id;

		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}

		// check permissions
		if ($_POST['post_type']!='testimonial' || !current_user_can('edit_post', $post_id)) {
			return $post_id;
		}

		$data = array();

		$fields = junotoys_storage_get_array('testimonial_override_options', 'fields');

		// Post type specific data handling
		if (is_array($fields) && count($fields) > 0) {
			foreach ($fields as $id=>$field) { 
				if (isset($_POST[$id])) 
					$data[$id] = stripslashes($_POST[$id]);
			}
		}

		update_post_meta($post_id, junotoys_storage_get('options_prefix').'_testimonial_data', $data);
	}
}




// ---------------------------------- [trx_testimonials] ---------------------------------------


if (!function_exists('junotoys_sc_testimonials')) {
	function junotoys_sc_testimonials($atts, $content=null){	
		if (junotoys_in_shortcode_blogger()) return '';
		extract(junotoys_html_decode(shortcode_atts(array(
			// Individual params
			"style" => "testimonials-1",
			"columns" => 1,
			"slider" => "yes",
			"slides_space" => 0,
			"controls" => "no",
			"interval" => "",
			"autoheight" => "no",
			"align" => "",
			"custom" => "no",
			"ids" => "",
			"cat" => "",
			"count" => "3",
			"offset" => "",
			"orderby" => "date",
			"order" => "desc",
			"scheme" => "",
			"bg_color" => "",
			"bg_image" => "",
			"bg_overlay" => "",
			"bg_texture" => "",
			"title" => "",
			"subtitle" => "",
			"description" => "",
			"visible" => "yes",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"width" => "",
			"height" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
	
		if (empty($id)) $id = "sc_testimonials_".str_replace('.', '', mt_rand());
		if (empty($width)) $width = "100%";
		if (!empty($height) && junotoys_param_is_on($autoheight)) $autoheight = "no";
		if (empty($interval)) $interval = mt_rand(5000, 10000);
	
		if ($bg_image > 0) {
			$attach = wp_get_attachment_image_src( $bg_image, 'full' );
			if (isset($attach[0]) && $attach[0]!='')
				$bg_image = $attach[0];
		}
	
		if ($bg_overlay > 0) {
			if ($bg_color=='') $bg_color = junotoys_get_scheme_color('bg');
			$rgb = junotoys_hex2rgb($bg_color);
		}
		
		$css .= ($css ? ';' : '') . junotoys_get_css_position_from_values($top, $right, $bottom, $left);

		$ws = junotoys_get_css_dimensions_from_values($width);
		$hs = junotoys_get_css_dimensions_from_values('', $height);
		$css .= ($hs) . ($ws);

		$count = max(1, (int) $count);
		$columns = max(1, min(12, (int) $columns));
		if (junotoys_param_is_off($custom) && $count < $columns) $columns = $count;
		
		junotoys_storage_set('sc_testimonials_data', array(
			'id' => $id,
            'style' => $style,
            'columns' => $columns,
            'counter' => 0,
            'slider' => $slider,
            'css_wh' => $ws . $hs
            )
        );

		if (junotoys_param_is_on($slider)) junotoys_enqueue_slider('swiper');
	
		$output = ($bg_color!='' || $bg_image!='' || $bg_overlay>0 || $bg_texture>0 || junotoys_strlen($bg_texture)>2 || ($scheme && !junotoys_param_is_off($scheme) && !junotoys_param_is_inherit($scheme))
					? '<div class="sc_testimonials_wrap sc_section'
							. ($scheme && !junotoys_param_is_off($scheme) && !junotoys_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '') 
							. '"'
						.' style="'
							. ($bg_color !== '' && $bg_overlay==0 ? 'background-color:' . esc_attr($bg_color) . ';' : '')
							. ($bg_image !== '' ? 'background-image:url(' . esc_url($bg_image) . ');' : '')
							. '"'
						. (!junotoys_param_is_off($animation) ? ' data-animation="'.esc_attr(junotoys_get_animation_classes($animation)).'"' : '')
						. '>'
						. '<div class="sc_section_overlay'.($bg_texture>0 ? ' texture_bg_'.esc_attr($bg_texture) : '') . '"'
								. ' style="' . ($bg_overlay>0 ? 'background-color:rgba('.(int)$rgb['r'].','.(int)$rgb['g'].','.(int)$rgb['b'].','.min(1, max(0, $bg_overlay)).');' : '')
									. (junotoys_strlen($bg_texture)>2 ? 'background-image:url('.esc_url($bg_texture).');' : '')
									. '"'
									. ($bg_overlay > 0 ? ' data-overlay="'.esc_attr($bg_overlay).'" data-bg_color="'.esc_attr($bg_color).'"' : '')
									. '>' 
					: '')
				. '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_testimonials sc_testimonials_style_'.esc_attr($style)
 					. ' ' . esc_attr(junotoys_get_template_property($style, 'container_classes'))
					. (!empty($class) ? ' '.esc_attr($class) : '')
					. ($visible == 'no' ? ' no_visible_items' : '')
					. ($align!='' && $align!='none' ? ' align'.esc_attr($align) : '')
					. '"'
				. ($bg_color=='' && $bg_image=='' && $bg_overlay==0 && ($bg_texture=='' || $bg_texture=='0') && !junotoys_param_is_off($animation) ? ' data-animation="'.esc_attr(junotoys_get_animation_classes($animation)).'"' : '')
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
			. '>'
			. (!empty($subtitle) ? '<h6 class="sc_testimonials_subtitle sc_item_subtitle">' . trim(junotoys_strmacros($subtitle)) . '</h6>' : '')
			. (!empty($title) ? '<h2 class="sc_testimonials_title sc_item_title">' . trim(junotoys_strmacros($title)) . '</h2>' : '')
			. (!empty($description) ? '<div class="sc_testimonials_descr sc_item_descr">' . trim(junotoys_strmacros($description)) . '</div>' : '')
			. (junotoys_param_is_on($slider) 
				? ('<div class="sc_slider_swiper swiper-slider-container'
								. ' ' . esc_attr(junotoys_get_slider_controls_classes($controls))
								. (junotoys_param_is_on($autoheight) ? ' sc_slider_height_auto' : '')
								. ($hs ? ' sc_slider_height_fixed' : '')
								. '"'
							. (!empty($width) && junotoys_strpos($width, '%')===false ? ' data-old-width="' . esc_attr($width) . '"' : '')
							. (!empty($height) && junotoys_strpos($height, '%')===false ? ' data-old-height="' . esc_attr($height) . '"' : '')
							. ((int) $interval > 0 ? ' data-interval="'.esc_attr($interval).'"' : '')
							. ($columns > 1 ? ' data-slides-per-view="' . esc_attr($columns) . '"' : '')
							. ($slides_space > 0 ? ' data-slides-space="' . esc_attr($slides_space) . '"' : '')
							. ' data-slides-min-width="250"'
						. '>'
					. '<div class="slides swiper-wrapper">')
				: ($columns > 1 
					? '<div class="sc_columns columns_wrap">' 
					: '')
				);
	
		$content = do_shortcode($content);
			
		if (junotoys_param_is_on($custom) && $content) {
			$output .= $content;
		} else {
			global $post;
		
			if (!empty($ids)) {
				$posts = explode(',', $ids);
				$count = count($posts);
			}
			
			$args = array(
				'post_type' => 'testimonial',
				'post_status' => 'publish',
				'posts_per_page' => $count,
				'ignore_sticky_posts' => true,
				'order' => $order=='asc' ? 'asc' : 'desc',
			);
		
			if ($offset > 0 && empty($ids)) {
				$args['offset'] = $offset;
			}
		
			$args = junotoys_query_add_sort_order($args, $orderby, $order);
			$args = junotoys_query_add_posts_and_cats($args, $ids, 'testimonial', $cat, 'testimonial_group');
	
			$query = new WP_Query( $args );
	
			$post_number = 0;
				
			while ( $query->have_posts() ) { 
				$query->the_post();
				$post_number++;
				$args = array(
					'layout' => $style,
					'show' => false,
					'number' => $post_number,
					'posts_on_page' => ($count > 0 ? $count : $query->found_posts),
					"descr" => junotoys_get_custom_option('post_excerpt_maxlength'.($columns > 1 ? '_masonry' : '')),
					"orderby" => $orderby,
					'content' => false,
					'terms_list' => false,
					'columns_count' => $columns,
					'slider' => $slider,
					'tag_id' => $id ? $id . '_' . $post_number : '',
					'tag_class' => '',
					'tag_animation' => '',
					'tag_css' => '',
					'tag_css_wh' => $ws . $hs
				);
				$post_data = junotoys_get_post_data($args);
				$post_data['post_content'] = wpautop($post_data['post_content']);	// Add <p> around text and paragraphs. Need separate call because 'content'=>false (see above)
				$post_meta = get_post_meta($post_data['post_id'], junotoys_storage_get('options_prefix').'_testimonial_data', true);
				$thumb_sizes = junotoys_get_thumb_sizes(array('layout' => $style));
				$args['author'] = $post_meta['testimonial_author'];
				$args['position'] = $post_meta['testimonial_position'];
				$args['link'] = !empty($post_meta['testimonial_link']) ? $post_meta['testimonial_link'] : '';	//$post_data['post_link'];
				$args['email'] = $post_meta['testimonial_email'];
				$args['photo'] = $post_data['post_thumb'];
				$mult = junotoys_get_retina_multiplier();
				if (empty($args['photo']) && !empty($args['email'])) $args['photo'] = get_avatar($args['email'], $thumb_sizes['w']*$mult);
				$output .= junotoys_show_post_layout($args, $post_data);
			}
			wp_reset_postdata();
		}
	
		if (junotoys_param_is_on($slider)) {
			$output .= '</div>'
				. '<div class="sc_slider_controls_wrap"><a class="sc_slider_prev" href="#"></a><a class="sc_slider_next" href="#"></a></div>'
				. '<div class="sc_slider_pagination_wrap"></div>'
				. '</div>';
		} else if ($columns > 1) {
			$output .= '</div>';
		}

		$output .= '</div>'
					. ($bg_color!='' || $bg_image!='' || $bg_overlay>0 || $bg_texture>0 || junotoys_strlen($bg_texture)>2 || ($scheme && !junotoys_param_is_off($scheme) && !junotoys_param_is_inherit($scheme))
						?  '</div></div>'
						: '');
	
		// Add template specific scripts and styles
		do_action('junotoys_action_blog_scripts', $style);

		return apply_filters('junotoys_shortcode_output', $output, 'trx_testimonials', $atts, $content);
	}
	junotoys_require_shortcode('trx_testimonials', 'junotoys_sc_testimonials');
}
	
	
if (!function_exists('junotoys_sc_testimonials_item')) {
	function junotoys_sc_testimonials_item($atts, $content=null){	
		if (junotoys_in_shortcode_blogger()) return '';
		extract(junotoys_html_decode(shortcode_atts(array(
			// Individual params
			"author" => "",
			"position" => "",
			"link" => "",
			"photo" => "",
			"email" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
		), $atts)));

		junotoys_storage_inc_array('sc_testimonials_data', 'counter');
	
		$id = $id ? $id : (junotoys_storage_get_array('sc_testimonials_data', 'id') ? junotoys_storage_get_array('sc_testimonials_data', 'id') . '_' . junotoys_storage_get_array('sc_testimonials_data', 'counter') : '');
	
		$thumb_sizes = junotoys_get_thumb_sizes(array('layout' => junotoys_storage_get_array('sc_testimonials_data', 'style')));

		if (empty($photo)) {
			if (!empty($email))
				$mult = junotoys_get_retina_multiplier();
				$photo = get_avatar($email, $thumb_sizes['w']*$mult);
		} else {
			if ($photo > 0) {
				$attach = wp_get_attachment_image_src( $photo, 'full' );
				if (isset($attach[0]) && $attach[0]!='')
					$photo = $attach[0];
			}
			$photo = junotoys_get_resized_image_tag($photo, $thumb_sizes['w'], $thumb_sizes['h']);
		}

		$post_data = array(
			'post_content' => do_shortcode($content)
		);
		$args = array(
			'layout' => junotoys_storage_get_array('sc_testimonials_data', 'style'),
			'number' => junotoys_storage_get_array('sc_testimonials_data', 'counter'),
			'columns_count' => junotoys_storage_get_array('sc_testimonials_data', 'columns'),
			'slider' => junotoys_storage_get_array('sc_testimonials_data', 'slider'),
			'show' => false,
			'descr'  => 0,
			'tag_id' => $id,
			'tag_class' => $class,
			'tag_animation' => '',
			'tag_css' => $css,
			'tag_css_wh' => junotoys_storage_get_array('sc_testimonials_data', 'css_wh'),
			'author' => $author,
			'position' => $position,
			'link' => $link,
			'email' => $email,
			'photo' => $photo
		);
		$output = junotoys_show_post_layout($args, $post_data);

		return apply_filters('junotoys_shortcode_output', $output, 'trx_testimonials_item', $atts, $content);
	}
	junotoys_require_shortcode('trx_testimonials_item', 'junotoys_sc_testimonials_item');
}
// ---------------------------------- [/trx_testimonials] ---------------------------------------



// Add [trx_testimonials] and [trx_testimonials_item] in the shortcodes list
if (!function_exists('junotoys_testimonials_reg_shortcodes')) {
	//Handler of add_filter('junotoys_action_shortcodes_list',	'junotoys_testimonials_reg_shortcodes');
	function junotoys_testimonials_reg_shortcodes() {
		if (junotoys_storage_isset('shortcodes')) {

			$testimonials_groups = junotoys_get_list_terms(false, 'testimonial_group');
			$testimonials_styles = junotoys_get_list_templates('testimonials');
			$controls = junotoys_get_list_slider_controls();

			junotoys_sc_map_before('trx_title', array(
			
				// Testimonials
				"trx_testimonials" => array(
					"title" => esc_html__("Testimonials", 'junotoys'),
					"desc" => wp_kses_data( __("Insert testimonials into post (page)", 'junotoys') ),
					"decorate" => true,
					"container" => false,
					"params" => array(
						"title" => array(
							"title" => esc_html__("Title", 'junotoys'),
							"desc" => wp_kses_data( __("Title for the block", 'junotoys') ),
							"value" => "",
							"type" => "text"
						),
						"subtitle" => array(
							"title" => esc_html__("Subtitle", 'junotoys'),
							"desc" => wp_kses_data( __("Subtitle for the block", 'junotoys') ),
							"value" => "",
							"type" => "text"
						),
						"description" => array(
							"title" => esc_html__("Description", 'junotoys'),
							"desc" => wp_kses_data( __("Short description for the block", 'junotoys') ),
							"value" => "",
							"type" => "textarea"
						),
						"columns" => array(
							"title" => esc_html__("Columns", 'junotoys'),
							"desc" => wp_kses_data( __("How many columns use to show testimonials", 'junotoys') ),
							"value" => 1,
							"min" => 1,
							"max" => 6,
							"step" => 1,
							"type" => "spinner"
						),
						"slider" => array(
							"title" => esc_html__("Slider", 'junotoys'),
							"desc" => wp_kses_data( __("Use slider to show testimonials", 'junotoys') ),
							"value" => "yes",
							"type" => "switch",
							"options" => junotoys_get_sc_param('yes_no')
						),
						"controls" => array(
							"title" => esc_html__("Controls", 'junotoys'),
							"desc" => wp_kses_data( __("Slider controls style and position", 'junotoys') ),
							"dependency" => array(
								'slider' => array('yes')
							),
							"divider" => true,
							"value" => "",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $controls
						),
						"slides_space" => array(
							"title" => esc_html__("Space between slides", 'junotoys'),
							"desc" => wp_kses_data( __("Size of space (in px) between slides", 'junotoys') ),
							"dependency" => array(
								'slider' => array('yes')
							),
							"value" => 0,
							"min" => 0,
							"max" => 100,
							"step" => 10,
							"type" => "spinner"
						),
						"interval" => array(
							"title" => esc_html__("Slides change interval", 'junotoys'),
							"desc" => wp_kses_data( __("Slides change interval (in milliseconds: 1000ms = 1s)", 'junotoys') ),
							"dependency" => array(
								'slider' => array('yes')
							),
							"value" => 7000,
							"step" => 500,
							"min" => 0,
							"type" => "spinner"
						),
						"autoheight" => array(
							"title" => esc_html__("Autoheight", 'junotoys'),
							"desc" => wp_kses_data( __("Change whole slider's height (make it equal current slide's height)", 'junotoys') ),
							"dependency" => array(
								'slider' => array('yes')
							),
							"value" => "yes",
							"type" => "switch",
							"options" => junotoys_get_sc_param('yes_no')
						),
						"visible" => array(
							"title" => esc_html__("Show other items", 'junotoys'),
							"desc" => wp_kses_data( __("Make not current items visible", 'junotoys') ),
							"dependency" => array(
								'slider' => array('yes')
							),
							"value" => "yes",
							"type" => "switch",
							"options" => junotoys_get_sc_param('yes_no')
						),
						"align" => array(
							"title" => esc_html__("Alignment", 'junotoys'),
							"desc" => wp_kses_data( __("Alignment of the testimonials block", 'junotoys') ),
							"divider" => true,
							"value" => "",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => junotoys_get_sc_param('align')
						),
						"custom" => array(
							"title" => esc_html__("Custom", 'junotoys'),
							"desc" => wp_kses_data( __("Allow get testimonials from inner shortcodes (custom) or get it from specified group (cat)", 'junotoys') ),
							"divider" => true,
							"value" => "no",
							"type" => "switch",
							"options" => junotoys_get_sc_param('yes_no')
						),
						"cat" => array(
							"title" => esc_html__("Categories", 'junotoys'),
							"desc" => wp_kses_data( __("Select categories (groups) to show testimonials. If empty - select testimonials from any category (group) or from IDs list", 'junotoys') ),
							"dependency" => array(
								'custom' => array('no')
							),
							"divider" => true,
							"value" => "",
							"type" => "select",
							"style" => "list",
							"multiple" => true,
							"options" => junotoys_array_merge(array(0 => esc_html__('- Select category -', 'junotoys')), $testimonials_groups)
						),
						"count" => array(
							"title" => esc_html__("Number of posts", 'junotoys'),
							"desc" => wp_kses_data( __("How many posts will be displayed? If used IDs - this parameter ignored.", 'junotoys') ),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => 3,
							"min" => 1,
							"max" => 100,
							"type" => "spinner"
						),
						"offset" => array(
							"title" => esc_html__("Offset before select posts", 'junotoys'),
							"desc" => wp_kses_data( __("Skip posts before select next part.", 'junotoys') ),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => 0,
							"min" => 0,
							"type" => "spinner"
						),
						"orderby" => array(
							"title" => esc_html__("Post order by", 'junotoys'),
							"desc" => wp_kses_data( __("Select desired posts sorting method", 'junotoys') ),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => "date",
							"type" => "select",
							"options" => junotoys_get_sc_param('sorting')
						),
						"order" => array(
							"title" => esc_html__("Post order", 'junotoys'),
							"desc" => wp_kses_data( __("Select desired posts order", 'junotoys') ),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => "desc",
							"type" => "switch",
							"size" => "big",
							"options" => junotoys_get_sc_param('ordering')
						),
						"ids" => array(
							"title" => esc_html__("Post IDs list", 'junotoys'),
							"desc" => wp_kses_data( __("Comma separated list of posts ID. If set - parameters above are ignored!", 'junotoys') ),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => "",
							"type" => "text"
						),
						"scheme" => array(
							"title" => esc_html__("Color scheme", 'junotoys'),
							"desc" => wp_kses_data( __("Select color scheme for this block", 'junotoys') ),
							"value" => "",
							"type" => "checklist",
							"options" => junotoys_get_sc_param('schemes')
						),
						"bg_color" => array(
							"title" => esc_html__("Background color", 'junotoys'),
							"desc" => wp_kses_data( __("Any background color for this section", 'junotoys') ),
							"value" => "",
							"type" => "color"
						),
						"bg_image" => array(
							"title" => esc_html__("Background image URL", 'junotoys'),
							"desc" => wp_kses_data( __("Select or upload image or write URL from other site for the background", 'junotoys') ),
							"readonly" => false,
							"value" => "",
							"type" => "media"
						),
						"bg_overlay" => array(
							"title" => esc_html__("Overlay", 'junotoys'),
							"desc" => wp_kses_data( __("Overlay color opacity (from 0.0 to 1.0)", 'junotoys') ),
							"min" => "0",
							"max" => "1",
							"step" => "0.1",
							"value" => "0",
							"type" => "spinner"
						),
						"bg_texture" => array(
							"title" => esc_html__("Texture", 'junotoys'),
							"desc" => wp_kses_data( __("Predefined texture style from 1 to 11. 0 - without texture.", 'junotoys') ),
							"min" => "0",
							"max" => "11",
							"step" => "1",
							"value" => "0",
							"type" => "spinner"
						),
						"width" => junotoys_shortcodes_width(),
						"height" => junotoys_shortcodes_height(),
						"top" => junotoys_get_sc_param('top'),
						"bottom" => junotoys_get_sc_param('bottom'),
						"left" => junotoys_get_sc_param('left'),
						"right" => junotoys_get_sc_param('right'),
						"id" => junotoys_get_sc_param('id'),
						"class" => junotoys_get_sc_param('class'),
						"animation" => junotoys_get_sc_param('animation'),
						"css" => junotoys_get_sc_param('css')
					),
					"children" => array(
						"name" => "trx_testimonials_item",
						"title" => esc_html__("Item", 'junotoys'),
						"desc" => wp_kses_data( __("Testimonials item (custom parameters)", 'junotoys') ),
						"container" => true,
						"params" => array(
							"author" => array(
								"title" => esc_html__("Author", 'junotoys'),
								"desc" => wp_kses_data( __("Name of the testimonmials author", 'junotoys') ),
								"value" => "",
								"type" => "text"
							),
							"link" => array(
								"title" => esc_html__("Link", 'junotoys'),
								"desc" => wp_kses_data( __("Link URL to the testimonmials author page", 'junotoys') ),
								"value" => "",
								"type" => "text"
							),
							"email" => array(
								"title" => esc_html__("E-mail", 'junotoys'),
								"desc" => wp_kses_data( __("E-mail of the testimonmials author (to get gravatar)", 'junotoys') ),
								"value" => "",
								"type" => "text"
							),
							"photo" => array(
								"title" => esc_html__("Photo", 'junotoys'),
								"desc" => wp_kses_data( __("Select or upload photo of testimonmials author or write URL of photo from other site", 'junotoys') ),
								"value" => "",
								"type" => "media"
							),
							"_content_" => array(
								"title" => esc_html__("Testimonials text", 'junotoys'),
								"desc" => wp_kses_data( __("Current testimonials text", 'junotoys') ),
								"divider" => true,
								"rows" => 4,
								"value" => "",
								"type" => "textarea"
							),
							"id" => junotoys_get_sc_param('id'),
							"class" => junotoys_get_sc_param('class'),
							"css" => junotoys_get_sc_param('css')
						)
					)
				)

			));
		}
	}
}


// Add [trx_testimonials] and [trx_testimonials_item] in the VC shortcodes list
if (!function_exists('junotoys_testimonials_reg_shortcodes_vc')) {
	//Handler of add_filter('junotoys_action_shortcodes_list_vc',	'junotoys_testimonials_reg_shortcodes_vc');
	function junotoys_testimonials_reg_shortcodes_vc() {

		$testimonials_groups = junotoys_get_list_terms(false, 'testimonial_group');
		$testimonials_styles = junotoys_get_list_templates('testimonials');
		$controls			 = junotoys_get_list_slider_controls();
			
		// Testimonials			
		vc_map( array(
				"base" => "trx_testimonials",
				"name" => esc_html__("Testimonials", 'junotoys'),
				"description" => wp_kses_data( __("Insert testimonials slider", 'junotoys') ),
				"category" => esc_html__('Content', 'junotoys'),
				'icon' => 'icon_trx_testimonials',
				"class" => "trx_sc_columns trx_sc_testimonials",
				"content_element" => true,
				"is_container" => true,
				"show_settings_on_create" => true,
				"as_parent" => array('only' => 'trx_testimonials_item'),
				"params" => array(
					array(
						"param_name" => "slider",
						"heading" => esc_html__("Slider", 'junotoys'),
						"description" => wp_kses_data( __("Use slider to show testimonials", 'junotoys') ),
						"admin_label" => true,
						"group" => esc_html__('Slider', 'junotoys'),
						"class" => "",
						"std" => "yes",
						"value" => array_flip((array)junotoys_get_sc_param('yes_no')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "controls",
						"heading" => esc_html__("Controls", 'junotoys'),
						"description" => wp_kses_data( __("Slider controls style and position", 'junotoys') ),
						"admin_label" => true,
						"group" => esc_html__('Slider', 'junotoys'),
						'dependency' => array(
							'element' => 'slider',
							'value' => 'yes'
						),
						"class" => "",
						"std" => "no",
						"value" => array_flip($controls),
						"type" => "dropdown"
					),
					array(
						"param_name" => "slides_space",
						"heading" => esc_html__("Space between slides", 'junotoys'),
						"description" => wp_kses_data( __("Size of space (in px) between slides", 'junotoys') ),
						"admin_label" => true,
						"group" => esc_html__('Slider', 'junotoys'),
						'dependency' => array(
							'element' => 'slider',
							'value' => 'yes'
						),
						"class" => "",
						"value" => "0",
						"type" => "textfield"
					),
					array(
						"param_name" => "interval",
						"heading" => esc_html__("Slides change interval", 'junotoys'),
						"description" => wp_kses_data( __("Slides change interval (in milliseconds: 1000ms = 1s)", 'junotoys') ),
						"group" => esc_html__('Slider', 'junotoys'),
						'dependency' => array(
							'element' => 'slider',
							'value' => 'yes'
						),
						"class" => "",
						"value" => "7000",
						"type" => "textfield"
					),
					array(
						"param_name" => "autoheight",
						"heading" => esc_html__("Autoheight", 'junotoys'),
						"description" => wp_kses_data( __("Change whole slider's height (make it equal current slide's height)", 'junotoys') ),
						"group" => esc_html__('Slider', 'junotoys'),
						'dependency' => array(
							'element' => 'slider',
							'value' => 'yes'
						),
						"class" => "",
						"value" => array("Autoheight" => "yes" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "visible",
						"heading" => esc_html__("Show other items", 'junotoys'),
						"description" => wp_kses_data( __("Make not current items visible", 'junotoys') ),
						"group" => esc_html__('Slider', 'junotoys'),
						"class" => "",
						"std" => "yes",
						"value" => array_flip((array)junotoys_get_sc_param('yes_no')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "align",
						"heading" => esc_html__("Alignment", 'junotoys'),
						"description" => wp_kses_data( __("Alignment of the testimonials block", 'junotoys') ),
						"class" => "",
						"value" => array_flip((array)junotoys_get_sc_param('align')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "custom",
						"heading" => esc_html__("Custom", 'junotoys'),
						"description" => wp_kses_data( __("Allow get testimonials from inner shortcodes (custom) or get it from specified group (cat)", 'junotoys') ),
						"class" => "",
						"value" => array("Custom slides" => "yes" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "title",
						"heading" => esc_html__("Title", 'junotoys'),
						"description" => wp_kses_data( __("Title for the block", 'junotoys') ),
						"admin_label" => true,
						"group" => esc_html__('Captions', 'junotoys'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "subtitle",
						"heading" => esc_html__("Subtitle", 'junotoys'),
						"description" => wp_kses_data( __("Subtitle for the block", 'junotoys') ),
						"group" => esc_html__('Captions', 'junotoys'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "description",
						"heading" => esc_html__("Description", 'junotoys'),
						"description" => wp_kses_data( __("Description for the block", 'junotoys') ),
						"group" => esc_html__('Captions', 'junotoys'),
						"class" => "",
						"value" => "",
						"type" => "textarea"
					),
					array(
						"param_name" => "cat",
						"heading" => esc_html__("Categories", 'junotoys'),
						"description" => wp_kses_data( __("Select categories (groups) to show testimonials. If empty - select testimonials from any category (group) or from IDs list", 'junotoys') ),
						"group" => esc_html__('Query', 'junotoys'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => array_flip(junotoys_array_merge(array(0 => esc_html__('- Select category -', 'junotoys')), $testimonials_groups)),
						"type" => "dropdown"
					),
					array(
						"param_name" => "columns",
						"heading" => esc_html__("Columns", 'junotoys'),
						"description" => wp_kses_data( __("How many columns use to show testimonials", 'junotoys') ),
						"group" => esc_html__('Query', 'junotoys'),
						"admin_label" => true,
						"class" => "",
						"value" => "1",
						"type" => "textfield"
					),
					array(
						"param_name" => "count",
						"heading" => esc_html__("Number of posts", 'junotoys'),
						"description" => wp_kses_data( __("How many posts will be displayed? If used IDs - this parameter ignored.", 'junotoys') ),
						"group" => esc_html__('Query', 'junotoys'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => "3",
						"type" => "textfield"
					),
					array(
						"param_name" => "offset",
						"heading" => esc_html__("Offset before select posts", 'junotoys'),
						"description" => wp_kses_data( __("Skip posts before select next part.", 'junotoys') ),
						"group" => esc_html__('Query', 'junotoys'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => "0",
						"type" => "textfield"
					),
					array(
						"param_name" => "orderby",
						"heading" => esc_html__("Post sorting", 'junotoys'),
						"description" => wp_kses_data( __("Select desired posts sorting method", 'junotoys') ),
						"group" => esc_html__('Query', 'junotoys'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"std" => "date",
						"class" => "",
						"value" => array_flip((array)junotoys_get_sc_param('sorting')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "order",
						"heading" => esc_html__("Post order", 'junotoys'),
						"description" => wp_kses_data( __("Select desired posts order", 'junotoys') ),
						"group" => esc_html__('Query', 'junotoys'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"std" => "desc",
						"class" => "",
						"value" => array_flip((array)junotoys_get_sc_param('ordering')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "ids",
						"heading" => esc_html__("Post IDs list", 'junotoys'),
						"description" => wp_kses_data( __("Comma separated list of posts ID. If set - parameters above are ignored!", 'junotoys') ),
						"group" => esc_html__('Query', 'junotoys'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "scheme",
						"heading" => esc_html__("Color scheme", 'junotoys'),
						"description" => wp_kses_data( __("Select color scheme for this block", 'junotoys') ),
						"group" => esc_html__('Colors and Images', 'junotoys'),
						"class" => "",
						"value" => array_flip((array)junotoys_get_sc_param('schemes')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "bg_color",
						"heading" => esc_html__("Background color", 'junotoys'),
						"description" => wp_kses_data( __("Any background color for this section", 'junotoys') ),
						"group" => esc_html__('Colors and Images', 'junotoys'),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "bg_image",
						"heading" => esc_html__("Background image URL", 'junotoys'),
						"description" => wp_kses_data( __("Select background image from library for this section", 'junotoys') ),
						"group" => esc_html__('Colors and Images', 'junotoys'),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					array(
						"param_name" => "bg_overlay",
						"heading" => esc_html__("Overlay", 'junotoys'),
						"description" => wp_kses_data( __("Overlay color opacity (from 0.0 to 1.0)", 'junotoys') ),
						"group" => esc_html__('Colors and Images', 'junotoys'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "bg_texture",
						"heading" => esc_html__("Texture", 'junotoys'),
						"description" => wp_kses_data( __("Texture style from 1 to 11. Empty or 0 - without texture.", 'junotoys') ),
						"group" => esc_html__('Colors and Images', 'junotoys'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					junotoys_vc_width(),
					junotoys_vc_height(),
					junotoys_get_vc_param('margin_top'),
					junotoys_get_vc_param('margin_bottom'),
					junotoys_get_vc_param('margin_left'),
					junotoys_get_vc_param('margin_right'),
					junotoys_get_vc_param('id'),
					junotoys_get_vc_param('class'),
					junotoys_get_vc_param('animation'),
					junotoys_get_vc_param('css')
				),
				'js_view' => 'VcTrxColumnsView'
		) );
			
			
		vc_map( array(
				"base" => "trx_testimonials_item",
				"name" => esc_html__("Testimonial", 'junotoys'),
				"description" => wp_kses_data( __("Single testimonials item", 'junotoys') ),
				"show_settings_on_create" => true,
				"class" => "trx_sc_collection trx_sc_column_item trx_sc_testimonials_item",
				"content_element" => true,
				"is_container" => true,
				'icon' => 'icon_trx_testimonials_item',
				"as_child" => array('only' => 'trx_testimonials'),
				"as_parent" => array('except' => 'trx_testimonials'),
				"params" => array(
					array(
						"param_name" => "author",
						"heading" => esc_html__("Author", 'junotoys'),
						"description" => wp_kses_data( __("Name of the testimonmials author", 'junotoys') ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "link",
						"heading" => esc_html__("Link", 'junotoys'),
						"description" => wp_kses_data( __("Link URL to the testimonmials author page", 'junotoys') ),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "email",
						"heading" => esc_html__("E-mail", 'junotoys'),
						"description" => wp_kses_data( __("E-mail of the testimonmials author", 'junotoys') ),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "photo",
						"heading" => esc_html__("Photo", 'junotoys'),
						"description" => wp_kses_data( __("Select or upload photo of testimonmials author or write URL of photo from other site", 'junotoys') ),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					junotoys_get_vc_param('id'),
					junotoys_get_vc_param('class'),
					junotoys_get_vc_param('css')
				),
				'js_view' => 'VcTrxColumnItemView'
		) );
			
		class WPBakeryShortCode_Trx_Testimonials extends JUNOTOYS_VC_ShortCodeColumns {}
		class WPBakeryShortCode_Trx_Testimonials_Item extends JUNOTOYS_VC_ShortCodeCollection {}
		
	}
}
?>