<?php
/**
 * Juno Toys Framework: Team support
 *
 * @package	junotoys
 * @since	junotoys 1.0
 */

// Theme init
if (!function_exists('junotoys_team_theme_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_team_theme_setup', 1 );
	function junotoys_team_theme_setup() {

		// Add item in the admin menu
		add_action('trx_utils_filter_override_options',						'junotoys_team_add_override_options');

		// Save data from override options
		add_action('save_post',								'junotoys_team_save_data');
		
		// Detect current page type, taxonomy and title (for custom post_types use priority < 10 to fire it handles early, than for standard post types)
		add_filter('junotoys_filter_get_blog_type',			'junotoys_team_get_blog_type', 9, 2);
		add_filter('junotoys_filter_get_blog_title',		'junotoys_team_get_blog_title', 9, 2);
		add_filter('junotoys_filter_get_current_taxonomy',	'junotoys_team_get_current_taxonomy', 9, 2);
		add_filter('junotoys_filter_is_taxonomy',			'junotoys_team_is_taxonomy', 9, 2);
		add_filter('junotoys_filter_get_stream_page_title',	'junotoys_team_get_stream_page_title', 9, 2);
		add_filter('junotoys_filter_get_stream_page_link',	'junotoys_team_get_stream_page_link', 9, 2);
		add_filter('junotoys_filter_get_stream_page_id',	'junotoys_team_get_stream_page_id', 9, 2);
		add_filter('junotoys_filter_query_add_filters',		'junotoys_team_query_add_filters', 9, 2);
		add_filter('junotoys_filter_detect_inheritance_key','junotoys_team_detect_inheritance_key', 9, 1);

		// Extra column for team members lists
		if (junotoys_get_theme_option('show_overriden_posts')=='yes') {
			add_filter('manage_edit-team_columns',			'junotoys_post_add_options_column', 9);
			add_filter('manage_team_posts_custom_column',	'junotoys_post_fill_options_column', 9, 2);
		}

		// Register shortcodes [trx_team] and [trx_team_item]
		add_action('junotoys_action_shortcodes_list',		'junotoys_team_reg_shortcodes');
		if (function_exists('junotoys_exists_visual_composer') && junotoys_exists_visual_composer())
			add_action('junotoys_action_shortcodes_list_vc','junotoys_team_reg_shortcodes_vc');

		// Meta box fields
		junotoys_storage_set('team_override_options', array(
			'id' => 'team-override-options',
			'title' => esc_html__('Team Member Details', 'junotoys'),
			'page' => 'team',
			'context' => 'normal',
			'priority' => 'high',
			'fields' => array(
				"team_member_position" => array(
					"title" => esc_html__('Position',  'junotoys'),
					"desc" => wp_kses_data( __("Position of the team member", 'junotoys') ),
					"class" => "team_member_position",
					"std" => "",
					"type" => "text"),
				"team_member_email" => array(
					"title" => esc_html__("E-mail",  'junotoys'),
					"desc" => wp_kses_data( __("E-mail of the team member - need to take Gravatar (if registered)", 'junotoys') ),
					"class" => "team_member_email",
					"std" => "",
					"type" => "text"),
				"team_member_link" => array(
					"title" => esc_html__('Link to profile',  'junotoys'),
					"desc" => wp_kses_data( __("URL of the team member profile page (if not this page)", 'junotoys') ),
					"class" => "team_member_link",
					"std" => "",
					"type" => "text"),
				"team_member_socials" => array(
					"title" => esc_html__("Social links",  'junotoys'),
					"desc" => wp_kses_data( __("Links to the social profiles of the team member", 'junotoys') ),
					"class" => "team_member_email",
					"std" => "",
					"type" => "social")
				)
			)
		);
		
		// Add supported data types
		junotoys_theme_support_pt('team');
		junotoys_theme_support_tx('team_group');
	}
}

if ( !function_exists( 'junotoys_team_settings_theme_setup2' ) ) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_team_settings_theme_setup2', 3 );
	function junotoys_team_settings_theme_setup2() {
		// Add post type 'team' and taxonomy 'team_group' into theme inheritance list
		junotoys_add_theme_inheritance( array('team' => array(
			'stream_template' => 'blog-team',
			'single_template' => 'single-team',
			'taxonomy' => array('team_group'),
			'taxonomy_tags' => array(),
			'post_type' => array('team'),
			'override' => 'custom'
			) )
		);
	}
}



// Add override options
if (!function_exists('junotoys_team_add_override_options')) {
    //add_action('trx_utils_filter_override_options', 'junotoys_team_add_override_options');
    function junotoys_team_add_override_options($boxes = array()) {
        $boxes[] = array_merge(junotoys_storage_get('team_override_options'), array('callback' => 'junotoys_team_show_override_options'));
        return $boxes;
    }
}


// Callback function to show fields in override options
if (!function_exists('junotoys_team_show_override_options')) {
	function junotoys_team_show_override_options() {
		global $post;

		$data = get_post_meta($post->ID, junotoys_storage_get('options_prefix').'_team_data', true);
		$fields = junotoys_storage_get_array('team_override_options', 'fields');
		?>
		<input type="hidden" name="override_options_team_nonce" value="<?php echo esc_attr(wp_create_nonce(admin_url())); ?>" />
		<table class="team_area">
		<?php
		if (is_array($fields) && count($fields) > 0) {
			foreach ($fields as $id=>$field) { 
				$meta = isset($data[$id]) ? $data[$id] : '';
				?>
				<tr class="team_field <?php echo esc_attr($field['class']); ?>" valign="top">
					<td><label for="<?php echo esc_attr($id); ?>"><?php echo esc_attr($field['title']); ?></label></td>
					<td>
						<?php
						if ($id == 'team_member_socials') {
							$socials_type = junotoys_get_theme_setting('socials_type');
							$social_list = junotoys_get_theme_option('social_icons');
							if (is_array($social_list) && count($social_list) > 0) {
								foreach ($social_list as $soc) {
									if ($socials_type == 'icons') {
										$parts = explode('-', $soc['icon'], 2);
										$sn = isset($parts[1]) ? $parts[1] : $soc['icon'];
									} else {
										$sn = basename($soc['icon']);
										$sn = junotoys_substr($sn, 0, junotoys_strrpos($sn, '.'));
										if (($pos=junotoys_strrpos($sn, '_'))!==false)
											$sn = junotoys_substr($sn, 0, $pos);
									}   
									$link = isset($meta[$sn]) ? $meta[$sn] : '';
									?>
									<label for="<?php echo esc_attr(($id).'_'.($sn)); ?>"><?php echo esc_attr(junotoys_strtoproper($sn)); ?></label><br>
									<input type="text" name="<?php echo esc_attr($id); ?>[<?php echo esc_attr($sn); ?>]" id="<?php echo esc_attr(($id).'_'.($sn)); ?>" value="<?php echo esc_attr($link); ?>" size="30" /><br>
									<?php
								}
							}
						} else {
							?>
							<input type="text" name="<?php echo esc_attr($id); ?>" id="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($meta); ?>" size="30" />
							<?php
						}
						?>
						<br><small><?php echo esc_attr($field['desc']); ?></small>
					</td>
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
if (!function_exists('junotoys_team_save_data')) {
	//Handler of add_action('save_post', 'junotoys_team_save_data');
	function junotoys_team_save_data($post_id) {
		// verify nonce
		if ( !wp_verify_nonce( junotoys_get_value_gp('override_options_team_nonce'), admin_url() ) )
			return $post_id;

		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}

		// check permissions
		if ($_POST['post_type']!='team' || !current_user_can('edit_post', $post_id)) {
			return $post_id;
		}

		$data = array();

		$fields = junotoys_storage_get_array('team_override_options', 'fields');

		// Post type specific data handling
		if (is_array($fields) && count($fields) > 0) {
			foreach ($fields as $id=>$field) {
				if (isset($_POST[$id])) {
					if (is_array($_POST[$id]) && count($_POST[$id]) > 0) {
						foreach ($_POST[$id] as $sn=>$link) {
							$_POST[$id][$sn] = stripslashes($link);
						}
						$data[$id] = $_POST[$id];
					} else {
						$data[$id] = stripslashes($_POST[$id]);
					}
				}
			}
		}

		update_post_meta($post_id, junotoys_storage_get('options_prefix').'_team_data', $data);
	}
}



// Return true, if current page is team member page
if ( !function_exists( 'junotoys_is_team_page' ) ) {
	function junotoys_is_team_page() {
		$is = in_array(junotoys_storage_get('page_template'), array('blog-team', 'single-team'));
		if (!$is) {
			if (!junotoys_storage_empty('pre_query'))
				$is = junotoys_storage_call_obj_method('pre_query', 'get', 'post_type')=='team' 
						|| junotoys_storage_call_obj_method('pre_query', 'is_tax', 'team_group') 
						|| (junotoys_storage_call_obj_method('pre_query', 'is_page') 
								&& ($id=junotoys_get_template_page_id('blog-team')) > 0 
								&& $id==junotoys_storage_get_obj_property('pre_query', 'queried_object_id', 0)
							);
			else
				$is = get_query_var('post_type')=='team' || is_tax('team_group') || (is_page() && ($id=junotoys_get_template_page_id('blog-team')) > 0 && $id==get_the_ID());
		}
		return $is;
	}
}

// Filter to detect current page inheritance key
if ( !function_exists( 'junotoys_team_detect_inheritance_key' ) ) {
	//Handler of add_filter('junotoys_filter_detect_inheritance_key',	'junotoys_team_detect_inheritance_key', 9, 1);
	function junotoys_team_detect_inheritance_key($key) {
		if (!empty($key)) return $key;
		return junotoys_is_team_page() ? 'team' : '';
	}
}

// Filter to detect current page slug
if ( !function_exists( 'junotoys_team_get_blog_type' ) ) {
	//Handler of add_filter('junotoys_filter_get_blog_type',	'junotoys_team_get_blog_type', 9, 2);
	function junotoys_team_get_blog_type($page, $query=null) {
		if (!empty($page)) return $page;
		if ($query && $query->is_tax('team_group') || is_tax('team_group'))
			$page = 'team_category';
		else if ($query && $query->get('post_type')=='team' || get_query_var('post_type')=='team')
			$page = $query && $query->is_single() || is_single() ? 'team_item' : 'team';
		return $page;
	}
}

// Filter to detect current page title
if ( !function_exists( 'junotoys_team_get_blog_title' ) ) {
	//Handler of add_filter('junotoys_filter_get_blog_title',	'junotoys_team_get_blog_title', 9, 2);
	function junotoys_team_get_blog_title($title, $page) {
		if (!empty($title)) return $title;
		if ( junotoys_strpos($page, 'team')!==false ) {
			if ( $page == 'team_category' ) {
				$term = get_term_by( 'slug', get_query_var( 'team_group' ), 'team_group', OBJECT);
				$title = $term->name;
			} else if ( $page == 'team_item' ) {
				$title = junotoys_get_post_title();
			} else {
				$title = esc_html__('All team', 'junotoys');
			}
		}

		return $title;
	}
}

// Filter to detect stream page title
if ( !function_exists( 'junotoys_team_get_stream_page_title' ) ) {
	//Handler of add_filter('junotoys_filter_get_stream_page_title',	'junotoys_team_get_stream_page_title', 9, 2);
	function junotoys_team_get_stream_page_title($title, $page) {
		if (!empty($title)) return $title;
		if (junotoys_strpos($page, 'team')!==false) {
			if (($page_id = junotoys_team_get_stream_page_id(0, $page=='team' ? 'blog-team' : $page)) > 0)
				$title = junotoys_get_post_title($page_id);
			else
				$title = esc_html__('All team', 'junotoys');				
		}
		return $title;
	}
}

// Filter to detect stream page ID
if ( !function_exists( 'junotoys_team_get_stream_page_id' ) ) {
	//Handler of add_filter('junotoys_filter_get_stream_page_id',	'junotoys_team_get_stream_page_id', 9, 2);
	function junotoys_team_get_stream_page_id($id, $page) {
		if (!empty($id)) return $id;
		if (junotoys_strpos($page, 'team')!==false) $id = junotoys_get_template_page_id('blog-team');
		return $id;
	}
}

// Filter to detect stream page URL
if ( !function_exists( 'junotoys_team_get_stream_page_link' ) ) {
	//Handler of add_filter('junotoys_filter_get_stream_page_link',	'junotoys_team_get_stream_page_link', 9, 2);
	function junotoys_team_get_stream_page_link($url, $page) {
		if (!empty($url)) return $url;
		if (junotoys_strpos($page, 'team')!==false) {
			$id = junotoys_get_template_page_id('blog-team');
			if ($id) $url = get_permalink($id);
		}
		return $url;
	}
}

// Filter to detect current taxonomy
if ( !function_exists( 'junotoys_team_get_current_taxonomy' ) ) {
	//Handler of add_filter('junotoys_filter_get_current_taxonomy',	'junotoys_team_get_current_taxonomy', 9, 2);
	function junotoys_team_get_current_taxonomy($tax, $page) {
		if (!empty($tax)) return $tax;
		if ( junotoys_strpos($page, 'team')!==false ) {
			$tax = 'team_group';
		}
		return $tax;
	}
}

// Return taxonomy name (slug) if current page is this taxonomy page
if ( !function_exists( 'junotoys_team_is_taxonomy' ) ) {
	//Handler of add_filter('junotoys_filter_is_taxonomy',	'junotoys_team_is_taxonomy', 9, 2);
	function junotoys_team_is_taxonomy($tax, $query=null) {
		if (!empty($tax))
			return $tax;
		else 
			return $query && $query->get('team_group')!='' || is_tax('team_group') ? 'team_group' : '';
	}
}

// Add custom post type and/or taxonomies arguments to the query
if ( !function_exists( 'junotoys_team_query_add_filters' ) ) {
	//Handler of add_filter('junotoys_filter_query_add_filters',	'junotoys_team_query_add_filters', 9, 2);
	function junotoys_team_query_add_filters($args, $filter) {
		if ($filter == 'team') {
			$args['post_type'] = 'team';
		}
		return $args;
	}
}





// ---------------------------------- [trx_team] ---------------------------------------


if ( !function_exists( 'junotoys_sc_team' ) ) {
	function junotoys_sc_team($atts, $content=null){	
		if (junotoys_in_shortcode_blogger()) return '';
		extract(junotoys_html_decode(shortcode_atts(array(
			// Individual params
			"style" => "team-3",
			"slider" => "no",
			"controls" => "no",
			"slides_space" => 0,
			"interval" => "",
			"autoheight" => "no",
			"align" => "",
			"custom" => "no",
			"ids" => "",
			"cat" => "",
			"count" => 3,
			"columns" => 3,
			"offset" => "",
			"orderby" => "title",
			"order" => "asc",
			"title" => "",
			"subtitle" => "",
			"description" => "",
			"link_caption" => esc_html__('Learn more', 'junotoys'),
			"link" => '',
			"scheme" => '',
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

		if (empty($id)) $id = "sc_team_".str_replace('.', '', mt_rand());
		if (empty($width)) $width = "100%";
		if (!empty($height) && junotoys_param_is_on($autoheight)) $autoheight = "no";
		if (empty($interval)) $interval = mt_rand(5000, 10000);

		$css .= ($css ? ';' : '') . junotoys_get_css_position_from_values($top, $right, $bottom, $left);

		$ws = junotoys_get_css_dimensions_from_values($width);
		$hs = junotoys_get_css_dimensions_from_values('', $height);
		$css .= ($hs) . ($ws);

		$count = max(1, (int) $count);
		$columns = max(1, min(12, (int) $columns));
		if (junotoys_param_is_off($custom) && $count < $columns) $columns = $count;

		junotoys_storage_set('sc_team_data', array(
			'id' => $id,
            'style' => $style,
            'columns' => $columns,
            'counter' => 0,
            'slider' => $slider,
            'css_wh' => $ws . $hs
            )
        );

		if (junotoys_param_is_on($slider)) junotoys_enqueue_slider('swiper');
	
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'_wrap"' : '') 
						. ' class="sc_team_wrap'
						. ($scheme && !junotoys_param_is_off($scheme) && !junotoys_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '') 
						.'">'
					. '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
						. ' class="sc_team sc_team_style_'.esc_attr($style)
							. ' ' . esc_attr(junotoys_get_template_property($style, 'container_classes'))
							. (!empty($class) ? ' '.esc_attr($class) : '')
							. ($align!='' && $align!='none' ? ' align'.esc_attr($align) : '')
						.'"'
						. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
						. (!junotoys_param_is_off($animation) ? ' data-animation="'.esc_attr(junotoys_get_animation_classes($animation)).'"' : '')
					. '>'
					. (!empty($subtitle) ? '<h6 class="sc_team_subtitle sc_item_subtitle">' . trim(junotoys_strmacros($subtitle)) . '</h6>' : '')
					. (!empty($title) ? '<h2 class="sc_team_title sc_item_title">' . trim(junotoys_strmacros($title)) . '</h2>' : '')
					. (!empty($description) ? '<div class="sc_team_descr sc_item_descr">' . trim(junotoys_strmacros($description)) . '</div>' : '')
					. (junotoys_param_is_on($slider) 
						? ('<div class="sc_slider_swiper swiper-slider-container'
										. ' ' . esc_attr(junotoys_get_slider_controls_classes($controls))
										. (junotoys_param_is_on($autoheight) ? ' sc_slider_height_auto' : '')
										. ($hs ? ' sc_slider_height_fixed' : '')
										. '"'
									. (!empty($width) && junotoys_strpos($width, '%')===false ? ' data-old-width="' . esc_attr($width) . '"' : '')
									. (!empty($height) && junotoys_strpos($height, '%')===false ? ' data-old-height="' . esc_attr($height) . '"' : '')
									. ((int) $interval > 0 ? ' data-interval="'.esc_attr($interval).'"' : '')
									. ($slides_space > 0 ? ' data-slides-space="' . esc_attr($slides_space) . '"' : '')
									. ($columns > 1 ? ' data-slides-per-view="' . esc_attr($columns) . '"' : '')
									. ' data-slides-min-width="250"'
								. '>'
							. '<div class="slides swiper-wrapper">')
						: ($columns > 1 // && junotoys_get_template_property($style, 'need_columns')
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
				'post_type' => 'team',
				'post_status' => 'publish',
				'posts_per_page' => $count,
				'ignore_sticky_posts' => true,
				'order' => $order=='asc' ? 'asc' : 'desc',
			);
		
			if ($offset > 0 && empty($ids)) {
				$args['offset'] = $offset;
			}
		
			$args = junotoys_query_add_sort_order($args, $orderby, $order);
			$args = junotoys_query_add_posts_and_cats($args, $ids, 'team', $cat, 'team_group');
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
					"columns_count" => $columns,
					'slider' => $slider,
					'tag_id' => $id ? $id . '_' . $post_number : '',
					'tag_class' => '',
					'tag_animation' => '',
					'tag_css' => '',
					'tag_css_wh' => $ws . $hs
				);
				$post_data = junotoys_get_post_data($args);
				$post_meta = get_post_meta($post_data['post_id'], junotoys_storage_get('options_prefix').'_team_data', true);
				$thumb_sizes = junotoys_get_thumb_sizes(array('layout' => $style));
				$args['position'] = $post_meta['team_member_position'];
				$args['link'] = !empty($post_meta['team_member_link']) ? $post_meta['team_member_link'] : $post_data['post_link'];
				$args['email'] = $post_meta['team_member_email'];
				$args['photo'] = $post_data['post_thumb'];
				$mult = junotoys_get_retina_multiplier();
				if (empty($args['photo']) && !empty($args['email'])) $args['photo'] = get_avatar($args['email'], $thumb_sizes['w']*$mult);
				$args['socials'] = '';
				$soc_list = $post_meta['team_member_socials'];
				if (is_array($soc_list) && count($soc_list)>0) {
					$soc_str = '';
					foreach ($soc_list as $sn=>$sl) {
						if (!empty($sl))
							$soc_str .= (!empty($soc_str) ? '|' : '') . ($sn) . '=' . ($sl);
					}
					if (!empty($soc_str))
						$args['socials'] = junotoys_do_shortcode('[trx_socials size="medium" socials="'.esc_attr($soc_str).'"][/trx_socials]');
				}
	
				$output .= junotoys_show_post_layout($args, $post_data);
			}
			wp_reset_postdata();
		}

		if (junotoys_param_is_on($slider)) {
			$output .= '</div>'
				. '<div class="sc_slider_controls_wrap"><a class="sc_slider_prev" href="#"></a><a class="sc_slider_next" href="#"></a></div>'
				. '<div class="sc_slider_pagination_wrap"></div>'
				. '</div>';
		} else if ($columns > 1) {// && junotoys_get_template_property($style, 'need_columns')) {
			$output .= '</div>';
		}

		$output .= (!empty($link) ? '<div class="sc_team_button sc_item_button">'.junotoys_do_shortcode('[trx_button link="'.esc_url($link).'" icon="icon-right"]'.esc_html($link_caption).'[/trx_button]').'</div>' : '')
					. '</div><!-- /.sc_team -->'
				. '</div><!-- /.sc_team_wrap -->';
	
		// Add template specific scripts and styles
		do_action('junotoys_action_blog_scripts', $style);
	
		return apply_filters('junotoys_shortcode_output', $output, 'trx_team', $atts, $content);
	}
	junotoys_require_shortcode('trx_team', 'junotoys_sc_team');
}


if ( !function_exists( 'junotoys_sc_team_item' ) ) {
	function junotoys_sc_team_item($atts, $content=null) {
		if (junotoys_in_shortcode_blogger()) return '';
		extract(junotoys_html_decode(shortcode_atts( array(
			// Individual params
			"user" => "",
			"member" => "",
			"name" => "",
			"position" => "",
			"photo" => "",
			"email" => "",
			"link" => "",
			"socials" => "",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => ""
		), $atts)));
	
		junotoys_storage_inc_array('sc_team_data', 'counter');
	
		$id = $id ? $id : (junotoys_storage_get_array('sc_team_data', 'id') ? junotoys_storage_get_array('sc_team_data', 'id') . '_' . junotoys_storage_get_array('sc_team_data', 'counter') : '');
	
		$descr = trim(chop(do_shortcode($content)));
	
		$thumb_sizes = junotoys_get_thumb_sizes(array('layout' => junotoys_storage_get_array('sc_team_data', 'style')));
	
		if (!empty($socials)) $socials = junotoys_do_shortcode('[trx_socials size="tiny" shape="round" socials="'.esc_attr($socials).'"][/trx_socials]');
	
		if (!empty($user) && $user!='none' && ($user_obj = get_user_by('login', $user)) != false) {
			$meta = get_user_meta($user_obj->ID);
			if (empty($email))		$email = $user_obj->data->user_email;
			if (empty($name))		$name = $user_obj->data->display_name;
			if (empty($position))	$position = isset($meta['user_position'][0]) ? $meta['user_position'][0] : '';
			if (empty($descr))		$descr = isset($meta['description'][0]) ? $meta['description'][0] : '';
			if (empty($socials))	$socials = junotoys_show_user_socials(array('author_id'=>$user_obj->ID, 'echo'=>false));
		}
	
		if (!empty($member) && $member!='none' && ($member_obj = (intval($member) > 0 ? get_post($member, OBJECT) : get_page_by_title($member, OBJECT, 'team'))) != null) {
			if (empty($name))		$name = $member_obj->post_title;
			if (empty($descr))		$descr = $member_obj->post_excerpt;
			$post_meta = get_post_meta($member_obj->ID, junotoys_storage_get('options_prefix').'_team_data', true);
			if (empty($position))	$position = $post_meta['team_member_position'];
			if (empty($link))		$link = !empty($post_meta['team_member_link']) ? $post_meta['team_member_link'] : get_permalink($member_obj->ID);
			if (empty($email))		$email = $post_meta['team_member_email'];
			if (empty($photo)) 		$photo = wp_get_attachment_url(get_post_thumbnail_id($member_obj->ID));
			if (empty($socials)) {
				$socials = '';
				$soc_list = $post_meta['team_member_socials'];
				if (is_array($soc_list) && count($soc_list)>0) {
					$soc_str = '';
					foreach ($soc_list as $sn=>$sl) {
						if (!empty($sl))
							$soc_str .= (!empty($soc_str) ? '|' : '') . ($sn) . '=' . ($sl);
					}
					if (!empty($soc_str))
						$socials = junotoys_do_shortcode('[trx_socials size="tiny" shape="round" socials="'.esc_attr($soc_str).'"][/trx_socials]');
				}
			}
		}
		if (empty($photo)) {
			$mult = junotoys_get_retina_multiplier();
			if (!empty($email)) $photo = get_avatar($email, $thumb_sizes['w']*$mult);
		} else {
			if ($photo > 0) {
				$attach = wp_get_attachment_image_src( $photo, 'full' );
				if (isset($attach[0]) && $attach[0]!='')
					$photo = $attach[0];
			}
			$photo = junotoys_get_resized_image_tag($photo, $thumb_sizes['w'], $thumb_sizes['h']);
		}
		$post_data = array(
			'post_title' => $name,
			'post_excerpt' => $descr
		);
		$args = array(
			'layout' => junotoys_storage_get_array('sc_team_data', 'style'),
			'number' => junotoys_storage_get_array('sc_team_data', 'counter'),
			'columns_count' => junotoys_storage_get_array('sc_team_data', 'columns'),
			'slider' => junotoys_storage_get_array('sc_team_data', 'slider'),
			'show' => false,
			'descr'  => 0,
			'tag_id' => $id,
			'tag_class' => $class,
			'tag_animation' => $animation,
			'tag_css' => $css,
			'tag_css_wh' => junotoys_storage_get_array('sc_team_data', 'css_wh'),
			'position' => $position,
			'link' => $link,
			'email' => $email,
			'photo' => $photo,
			'socials' => $socials
		);
		$output = junotoys_show_post_layout($args, $post_data);

		return apply_filters('junotoys_shortcode_output', $output, 'trx_team_item', $atts, $content);
	}
	junotoys_require_shortcode('trx_team_item', 'junotoys_sc_team_item');
}
// ---------------------------------- [/trx_team] ---------------------------------------



// Add [trx_team] and [trx_team_item] in the shortcodes list
if (!function_exists('junotoys_team_reg_shortcodes')) {
	//Handler of add_filter('junotoys_action_shortcodes_list',	'junotoys_team_reg_shortcodes');
	function junotoys_team_reg_shortcodes() {
		if (junotoys_storage_isset('shortcodes')) {

			$users = junotoys_get_list_users();
			$members = junotoys_get_list_posts(false, array(
				'post_type'=>'team',
				'orderby'=>'title',
				'order'=>'asc',
				'return'=>'title'
				)
			);
			$team_groups = junotoys_get_list_terms(false, 'team_group');
			$team_styles = junotoys_get_list_templates('team');
			$controls	 = junotoys_get_list_slider_controls();

			junotoys_sc_map_after('trx_tabs', array(

				// Team
				"trx_team" => array(
					"title" => esc_html__("Team", 'junotoys'),
					"desc" => wp_kses_data( __("Insert team in your page (post)", 'junotoys') ),
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
							"desc" => wp_kses_data( __("How many columns use to show team members", 'junotoys') ),
							"value" => 3,
							"min" => 2,
							"max" => 5,
							"step" => 1,
							"type" => "spinner"
						),
						"scheme" => array(
							"title" => esc_html__("Color scheme", 'junotoys'),
							"desc" => wp_kses_data( __("Select color scheme for this block", 'junotoys') ),
							"value" => "",
							"type" => "checklist",
							"options" => junotoys_get_sc_param('schemes')
						),
						"align" => array(
							"title" => esc_html__("Alignment", 'junotoys'),
							"desc" => wp_kses_data( __("Alignment of the team block", 'junotoys') ),
							"divider" => true,
							"value" => "",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => junotoys_get_sc_param('align')
						),
						"custom" => array(
							"title" => esc_html__("Custom", 'junotoys'),
							"desc" => wp_kses_data( __("Allow get team members from inner shortcodes (custom) or get it from specified group (cat)", 'junotoys') ),
							"divider" => true,
							"value" => "no",
							"type" => "switch",
							"options" => junotoys_get_sc_param('yes_no')
						),
						"cat" => array(
							"title" => esc_html__("Categories", 'junotoys'),
							"desc" => wp_kses_data( __("Select categories (groups) to show team members. If empty - select team members from any category (group) or from IDs list", 'junotoys') ),
							"dependency" => array(
								'custom' => array('no')
							),
							"divider" => true,
							"value" => "",
							"type" => "select",
							"style" => "list",
							"multiple" => true,
							"options" => junotoys_array_merge(array(0 => esc_html__('- Select category -', 'junotoys')), $team_groups)
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
							"value" => "title",
							"type" => "select",
							"options" => junotoys_get_sc_param('sorting')
						),
						"order" => array(
							"title" => esc_html__("Post order", 'junotoys'),
							"desc" => wp_kses_data( __("Select desired posts order", 'junotoys') ),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => "asc",
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
						"link" => array(
							"title" => esc_html__("Button URL", 'junotoys'),
							"desc" => wp_kses_data( __("Link URL for the button at the bottom of the block", 'junotoys') ),
							"value" => "",
							"type" => "text"
						),
						"link_caption" => array(
							"title" => esc_html__("Button caption", 'junotoys'),
							"desc" => wp_kses_data( __("Caption for the button at the bottom of the block", 'junotoys') ),
							"value" => "",
							"type" => "text"
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
						"name" => "trx_team_item",
						"title" => esc_html__("Member", 'junotoys'),
						"desc" => wp_kses_data( __("Team member", 'junotoys') ),
						"container" => true,
						"params" => array(
							"user" => array(
								"title" => esc_html__("Registerd user", 'junotoys'),
								"desc" => wp_kses_data( __("Select one of registered users (if present) or put name, position, etc. in fields below", 'junotoys') ),
								"value" => "",
								"type" => "select",
								"options" => $users
							),
							"member" => array(
								"title" => esc_html__("Team member", 'junotoys'),
								"desc" => wp_kses_data( __("Select one of team members (if present) or put name, position, etc. in fields below", 'junotoys') ),
								"value" => "",
								"type" => "select",
								"options" => $members
							),
							"link" => array(
								"title" => esc_html__("Link", 'junotoys'),
								"desc" => wp_kses_data( __("Link on team member's personal page", 'junotoys') ),
								"divider" => true,
								"value" => "",
								"type" => "text"
							),
							"name" => array(
								"title" => esc_html__("Name", 'junotoys'),
								"desc" => wp_kses_data( __("Team member's name", 'junotoys') ),
								"divider" => true,
								"dependency" => array(
									'user' => array('is_empty', 'none'),
									'member' => array('is_empty', 'none')
								),
								"value" => "",
								"type" => "text"
							),
							"position" => array(
								"title" => esc_html__("Position", 'junotoys'),
								"desc" => wp_kses_data( __("Team member's position", 'junotoys') ),
								"dependency" => array(
									'user' => array('is_empty', 'none'),
									'member' => array('is_empty', 'none')
								),
								"value" => "",
								"type" => "text"
							),
							"email" => array(
								"title" => esc_html__("E-mail", 'junotoys'),
								"desc" => wp_kses_data( __("Team member's e-mail", 'junotoys') ),
								"dependency" => array(
									'user' => array('is_empty', 'none'),
									'member' => array('is_empty', 'none')
								),
								"value" => "",
								"type" => "text"
							),
							"photo" => array(
								"title" => esc_html__("Photo", 'junotoys'),
								"desc" => wp_kses_data( __("Team member's photo (avatar)", 'junotoys') ),
								"dependency" => array(
									'user' => array('is_empty', 'none'),
									'member' => array('is_empty', 'none')
								),
								"value" => "",
								"readonly" => false,
								"type" => "media"
							),
							"socials" => array(
								"title" => esc_html__("Socials", 'junotoys'),
								"desc" => wp_kses_data( __("Team member's socials icons: name=url|name=url... For example: facebook=http://facebook.com/myaccount|twitter=http://twitter.com/myaccount", 'junotoys') ),
								"dependency" => array(
									'user' => array('is_empty', 'none'),
									'member' => array('is_empty', 'none')
								),
								"value" => "",
								"type" => "text"
							),
							"_content_" => array(
								"title" => esc_html__("Description", 'junotoys'),
								"desc" => wp_kses_data( __("Team member's short description", 'junotoys') ),
								"divider" => true,
								"rows" => 4,
								"value" => "",
								"type" => "textarea"
							),
							"id" => junotoys_get_sc_param('id'),
							"class" => junotoys_get_sc_param('class'),
							"animation" => junotoys_get_sc_param('animation'),
							"css" => junotoys_get_sc_param('css')
						)
					)
				)

			));
		}
	}
}


// Add [trx_team] and [trx_team_item] in the VC shortcodes list
if (!function_exists('junotoys_team_reg_shortcodes_vc')) {
	//Handler of add_filter('junotoys_action_shortcodes_list_vc',	'junotoys_team_reg_shortcodes_vc');
	function junotoys_team_reg_shortcodes_vc() {

		$users = junotoys_get_list_users();
		$members = junotoys_get_list_posts(false, array(
			'post_type'=>'team',
			'orderby'=>'title',
			'order'=>'asc',
			'return'=>'title'
			)
		);
		$team_groups = junotoys_get_list_terms(false, 'team_group');
		$team_styles = junotoys_get_list_templates('team');
		$controls	 = junotoys_get_list_slider_controls();

		// Team
		vc_map( array(
				"base" => "trx_team",
				"name" => esc_html__("Team", 'junotoys'),
				"description" => wp_kses_data( __("Insert team members", 'junotoys') ),
				"category" => esc_html__('Content', 'junotoys'),
				'icon' => 'icon_trx_team',
				"class" => "trx_sc_columns trx_sc_team",
				"content_element" => true,
				"is_container" => true,
				"show_settings_on_create" => true,
				"as_parent" => array('only' => 'trx_team_item'),
				"params" => array(
					array(
						"param_name" => "scheme",
						"heading" => esc_html__("Color scheme", 'junotoys'),
						"description" => wp_kses_data( __("Select color scheme for this block", 'junotoys') ),
						"class" => "",
						"value" => array_flip(junotoys_get_sc_param('schemes')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "align",
						"heading" => esc_html__("Alignment", 'junotoys'),
						"description" => wp_kses_data( __("Alignment of the team block", 'junotoys') ),
						"class" => "",
						"value" => array_flip(junotoys_get_sc_param('align')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "custom",
						"heading" => esc_html__("Custom", 'junotoys'),
						"description" => wp_kses_data( __("Allow get team members from inner shortcodes (custom) or get it from specified group (cat)", 'junotoys') ),
						"class" => "",
						"value" => array("Custom members" => "yes" ),
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
						"description" => wp_kses_data( __("Select category to show team members. If empty - select team members from any category (group) or from IDs list", 'junotoys') ),
						"group" => esc_html__('Query', 'junotoys'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => array_flip(junotoys_array_merge(array(0 => esc_html__('- Select category -', 'junotoys')), $team_groups)),
						"type" => "dropdown"
					),
					array(
						"param_name" => "columns",
						"heading" => esc_html__("Columns", 'junotoys'),
						"description" => wp_kses_data( __("How many columns use to show team members", 'junotoys') ),
						"group" => esc_html__('Query', 'junotoys'),
						"admin_label" => true,
						"class" => "",
						"value" => "3",
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
						"std" => "title",
						"class" => "",
						"value" => array_flip(junotoys_get_sc_param('sorting')),
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
						"std" => "asc",
						"class" => "",
						"value" => array_flip(junotoys_get_sc_param('ordering')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "ids",
						"heading" => esc_html__("Team member's IDs list", 'junotoys'),
						"description" => wp_kses_data( __("Comma separated list of team members's ID. If set - parameters above (category, count, order, etc.)  are ignored!", 'junotoys') ),
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
						"param_name" => "link",
						"heading" => esc_html__("Button URL", 'junotoys'),
						"description" => wp_kses_data( __("Link URL for the button at the bottom of the block", 'junotoys') ),
						"group" => esc_html__('Captions', 'junotoys'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "link_caption",
						"heading" => esc_html__("Button caption", 'junotoys'),
						"description" => wp_kses_data( __("Caption for the button at the bottom of the block", 'junotoys') ),
						"group" => esc_html__('Captions', 'junotoys'),
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
				'default_content' => '
					[trx_team_item user="' . esc_html__( 'Member 1', 'junotoys' ) . '"][/trx_team_item]
					[trx_team_item user="' . esc_html__( 'Member 2', 'junotoys' ) . '"][/trx_team_item]
					[trx_team_item user="' . esc_html__( 'Member 4', 'junotoys' ) . '"][/trx_team_item]
				',
				'js_view' => 'VcTrxColumnsView'
			) );
			
			
		vc_map( array(
				"base" => "trx_team_item",
				"name" => esc_html__("Team member", 'junotoys'),
				"description" => wp_kses_data( __("Team member - all data pull out from it account on your site", 'junotoys') ),
				"show_settings_on_create" => true,
				"class" => "trx_sc_collection trx_sc_column_item trx_sc_team_item",
				"content_element" => true,
				"is_container" => true,
				'icon' => 'icon_trx_team_item',
				"as_child" => array('only' => 'trx_team'),
				"as_parent" => array('except' => 'trx_team'),
				"params" => array(
					array(
						"param_name" => "user",
						"heading" => esc_html__("Registered user", 'junotoys'),
						"description" => wp_kses_data( __("Select one of registered users (if present) or put name, position, etc. in fields below", 'junotoys') ),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($users),
						"type" => "dropdown"
					),
					array(
						"param_name" => "member",
						"heading" => esc_html__("Team member", 'junotoys'),
						"description" => wp_kses_data( __("Select one of team members (if present) or put name, position, etc. in fields below", 'junotoys') ),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($members),
						"type" => "dropdown"
					),
					array(
						"param_name" => "link",
						"heading" => esc_html__("Link", 'junotoys'),
						"description" => wp_kses_data( __("Link on team member's personal page", 'junotoys') ),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "name",
						"heading" => esc_html__("Name", 'junotoys'),
						"description" => wp_kses_data( __("Team member's name", 'junotoys') ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "position",
						"heading" => esc_html__("Position", 'junotoys'),
						"description" => wp_kses_data( __("Team member's position", 'junotoys') ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "email",
						"heading" => esc_html__("E-mail", 'junotoys'),
						"description" => wp_kses_data( __("Team member's e-mail", 'junotoys') ),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "photo",
						"heading" => esc_html__("Member's Photo", 'junotoys'),
						"description" => wp_kses_data( __("Team member's photo (avatar)", 'junotoys') ),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					array(
						"param_name" => "socials",
						"heading" => esc_html__("Socials", 'junotoys'),
						"description" => wp_kses_data( __("Team member's socials icons: name=url|name=url... For example: facebook=http://facebook.com/myaccount|twitter=http://twitter.com/myaccount", 'junotoys') ),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					junotoys_get_vc_param('id'),
					junotoys_get_vc_param('class'),
					junotoys_get_vc_param('animation'),
					junotoys_get_vc_param('css')
				),
				'js_view' => 'VcTrxColumnItemView'
			) );
			
		class WPBakeryShortCode_Trx_Team extends JUNOTOYS_VC_ShortCodeColumns {}
		class WPBakeryShortCode_Trx_Team_Item extends JUNOTOYS_VC_ShortCodeCollection {}

	}
}
?>