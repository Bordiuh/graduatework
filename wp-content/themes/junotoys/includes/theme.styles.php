<?php
/**
 * Theme custom styles
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if (!function_exists('junotoys_action_theme_styles_theme_setup')) {
	add_action( 'junotoys_action_before_init_theme', 'junotoys_action_theme_styles_theme_setup', 1 );
	function junotoys_action_theme_styles_theme_setup() {
	
		// Add theme fonts in the used fonts list
		add_filter('junotoys_filter_used_fonts',			'junotoys_filter_theme_styles_used_fonts');
		// Add theme fonts (from Google fonts) in the main fonts list (if not present).
		add_filter('junotoys_filter_list_fonts',			'junotoys_filter_theme_styles_list_fonts');

		// Add theme stylesheets
		add_action('junotoys_action_add_styles',			'junotoys_action_theme_styles_add_styles');
		// Add theme inline styles
		add_filter('junotoys_filter_add_styles_inline',		'junotoys_filter_theme_styles_add_styles_inline');

		// Add theme scripts
		add_action('junotoys_action_add_scripts',			'junotoys_action_theme_styles_add_scripts');
		// Add theme scripts inline
		add_filter('junotoys_filter_localize_script',		'junotoys_filter_theme_styles_localize_script');

		// Add theme less files into list for compilation
		add_filter('junotoys_filter_compile_less',			'junotoys_filter_theme_styles_compile_less');


		/* Color schemes
		
		// Block's border and background
		bd_color		- border for the entire block
		bg_color		- background color for the entire block
		// Next settings are deprecated
		//bg_image, bg_image_position, bg_image_repeat, bg_image_attachment  - first background image for the entire block
		//bg_image2,bg_image2_position,bg_image2_repeat,bg_image2_attachment - second background image for the entire block
		
		// Additional accented colors (if need)
		accent2			- theme accented color 2
		accent2_hover	- theme accented color 2 (hover state)		
		accent3			- theme accented color 3
		accent3_hover	- theme accented color 3 (hover state)		
		
		// Headers, text and links
		text			- main content
		text_light		- post info
		text_dark		- headers
		text_link		- links
		text_hover		- hover links
		
		// Inverse blocks
		inverse_text	- text on accented background
		inverse_light	- post info on accented background
		inverse_dark	- headers on accented background
		inverse_link	- links on accented background
		inverse_hover	- hovered links on accented background
		
		// Input colors - form fields
		input_text		- inactive text
		input_light		- placeholder text
		input_dark		- focused text
		input_bd_color	- inactive border
		input_bd_hover	- focused borde
		input_bg_color	- inactive background
		input_bg_hover	- focused background
		
		// Alternative colors - highlight blocks, form fields, etc.
		alter_text		- text on alternative background
		alter_light		- post info on alternative background
		alter_dark		- headers on alternative background
		alter_link		- links on alternative background
		alter_hover		- hovered links on alternative background
		alter_bd_color	- alternative border
		alter_bd_hover	- alternative border for hovered state or active field
		alter_bg_color	- alternative background
		alter_bg_hover	- alternative background for hovered state or active field 
		// Next settings are deprecated
		//alter_bg_image, alter_bg_image_position, alter_bg_image_repeat, alter_bg_image_attachment - background image for the alternative block
		
		*/

		// Add color schemes
		junotoys_add_color_scheme('original', array(

			'title'					=> esc_html__('Original', 'junotoys'),
			
			// Whole block border and background
			'bd_color'				=> '#E4E7E8',
			'bg_color'				=> '#FFFFFF',
			
			//Accent
			'accent2'				=> '#2BB24C',
			'accent2_hover'			=> '#F5B120',
			'accent3'				=> '#00CAC9',
			'accent3_hover'			=> '#EA624C',
			
			// Headers, text and links colors
			'text'					=> '#969899',
			'text_light'			=> '#ACB4B6',
			'text_dark'				=> '#232A34',
			'text_link'				=> '#1F5967',
			'text_hover'			=> '#12AEE0',

			// Inverse colors
			'inverse_text'			=> '#ffffff',
			'inverse_light'			=> '#ffffff',
			'inverse_dark'			=> '#ffffff',
			'inverse_link'			=> '#ffffff',
			'inverse_hover'			=> '#ffffff',
		
			// Input fields
			'input_text'			=> '#8A8A8A',
			'input_light'			=> '#ACB4B6',
			'input_dark'			=> '#232A34',
			'input_bd_color'		=> '#DDDDDD',
			'input_bd_hover'		=> '#BBBBBB',
			'input_bg_color'		=> '#F7F7F7',
			'input_bg_hover'		=> '#F0F0F0',
		
			// Alternative blocks (submenu items, etc.)
			'alter_text'			=> '#969899',
			'alter_light'			=> '#ACB4B6',
			'alter_dark'			=> '#232A34',
			'alter_link'			=> '#1F5967',
			'alter_hover'			=> '#12AEE0',
			'alter_bd_color'		=> '#D3EDF5',
			'alter_bd_hover'		=> '#D3EDF5',
			'alter_bg_color'		=> '#F5F6F7',
			'alter_bg_hover'		=> '#DEEFF5',
			)
		);

		// Add color schemes
		junotoys_add_color_scheme('dark', array(

			'title'					=> esc_html__('Dark', 'junotoys'),
			
			// Whole block border and background
			'bd_color'				=> '#7D7D7D',
			'bg_color'				=> '#333333',
			
			//Accent
			'accent2'				=> '#2BB24C',
			'accent2_hover'			=> '#F5B120',
			'accent3'				=> '#00CAC9',
			'accent3_hover'			=> '#EA624C',
			
			// Headers, text and links colors
			'text'					=> '#FFFFFF',
			'text_light'			=> '#FFFFFF',
			'text_dark'				=> '#FFFFFF',
			'text_link'				=> '#1F5967',
			'text_hover'			=> '#12AEE0',

			// Inverse colors
			'inverse_text'			=> '#F0F0F0',
			'inverse_light'			=> '#E0E0E0',
			'inverse_dark'			=> '#FFFFFF',
			'inverse_link'			=> '#FFFFFF',
			'inverse_hover'			=> '#E5E5E5',
		
			// Input fields
			'input_text'			=> '#999999',
			'input_light'			=> '#AAAAAA',
			'input_dark'			=> '#D0D0D0',
			'input_bd_color'		=> '#909090',
			'input_bd_hover'		=> '#888888',
			'input_bg_color'		=> '#666666',
			'input_bg_hover'		=> '#505050',
		
			// Alternative blocks (submenu items, etc.)
			'alter_text'			=> '#999999',
			'alter_light'			=> '#AAAAAA',
			'alter_dark'			=> '#D0D0D0',
			'alter_link'			=> '#1F5967',
			'alter_hover'			=> '#12AEE0',
			'alter_bd_color'		=> '#909090',
			'alter_bd_hover'		=> '#888888',
			'alter_bg_color'		=> '#FFFFFF',
			'alter_bg_hover'		=> '#12AEE0',
			)
		);

	

		/* Font slugs:
		h1 ... h6	- headers
		p			- plain text
		link		- links
		info		- info blocks (Posted 15 May, 2015 by John Doe)
		menu		- main menu
		submenu		- dropdown menus
		logo		- logo text
		button		- button's caption
		input		- input fields
		*/

		// Add Custom fonts
		junotoys_add_custom_font('h1', array(
			'title'			=> esc_html__('Heading 1', 'junotoys'),
			'description'	=> '',
			'font-family'	=> 'Fredoka One',
			'font-size' 	=> '3.2em',
			'font-weight'	=> '400',
			'font-style'	=> '',
			'line-height'	=> '1.125em',
			'margin-top'	=> '0',
			'margin-bottom'	=> '0.4em'
			)
		);
		junotoys_add_custom_font('h2', array(
			'title'			=> esc_html__('Heading 2', 'junotoys'),
			'description'	=> '',
			'font-family'	=> 'Fredoka One',
			'font-size' 	=> '2.4em',
			'font-weight'	=> '400',
			'font-style'	=> '',
			'line-height'	=> '1.3em',
			'margin-top'	=> '0',
			'margin-bottom'	=> '0.9em'
			)
		);
		junotoys_add_custom_font('h3', array(
			'title'			=> esc_html__('Heading 3', 'junotoys'),
			'description'	=> '',
			'font-family'	=> 'Fredoka One',
			'font-size' 	=> '1.6em',
			'font-weight'	=> '400',
			'font-style'	=> '',
			'line-height'	=> '1.3em',
			'margin-top'	=> '0',
			'margin-bottom'	=> '0.4em'
			)
		);
		junotoys_add_custom_font('h4', array(
			'title'			=> esc_html__('Heading 4', 'junotoys'),
			'description'	=> '',
			'font-family'	=> 'Fredoka One',
			'font-size' 	=> '1.46em',
			'font-weight'	=> '400',
			'font-style'	=> '',
			'line-height'	=> '1.3em',
			'margin-top'	=> '0',
			'margin-bottom'	=> '0.6em'
			)
		);
		junotoys_add_custom_font('h5', array(
			'title'			=> esc_html__('Heading 5', 'junotoys'),
			'description'	=> '',
			'font-family'	=> 'Fredoka One',
			'font-size' 	=> '1.2em',
			'font-weight'	=> '400',
			'font-style'	=> '',
			'line-height'	=> '1.3em',
			'margin-top'	=> '0',
			'margin-bottom'	=> '0.5em'
			)
		);
		junotoys_add_custom_font('h6', array(
			'title'			=> esc_html__('Heading 6', 'junotoys'),
			'description'	=> '',
			'font-family'	=> '',
			'font-size' 	=> '1.066em',
			'font-weight'	=> '600',
			'font-style'	=> '',
			'line-height'	=> '1.55em',
			'margin-top'	=> '0',
			'margin-bottom'	=> '0.65em'
			)
		);
		junotoys_add_custom_font('p', array(
			'title'			=> esc_html__('Text', 'junotoys'),
			'description'	=> '',
			'font-family'	=> 'Ubuntu',
			'font-size' 	=> '15px',
			'font-weight'	=> '400',
			'font-style'	=> '',
			'line-height'	=> '1.6em',
			'margin-top'	=> '',
			'margin-bottom'	=> '1em'
			)
		);
		junotoys_add_custom_font('link', array(
			'title'			=> esc_html__('Links', 'junotoys'),
			'description'	=> '',
			'font-family'	=> '',
			'font-size' 	=> '',
			'font-weight'	=> '',
			'font-style'	=> ''
			)
		);
		junotoys_add_custom_font('info', array(
			'title'			=> esc_html__('Post info', 'junotoys'),
			'description'	=> '',
			'font-family'	=> '',
			'font-size' 	=> '12px',
			'font-weight'	=> '700',
			'font-style'	=> '',
			'line-height'	=> '1.2857em',
			'margin-top'	=> '',
			'margin-bottom'	=> '1.5em'
			)
		);
		junotoys_add_custom_font('menu', array(
			'title'			=> esc_html__('Main menu items', 'junotoys'),
			'description'	=> '',
			'font-family'	=> '',
			'font-size' 	=> '0.866em',
			'font-weight'	=> '700',
			'font-style'	=> '',
			'line-height'	=> '1.924em',
			'margin-top'	=> '0.5em',
			'margin-bottom'	=> '0.5em'
			)
		);
		junotoys_add_custom_font('submenu', array(
			'title'			=> esc_html__('Dropdown menu items', 'junotoys'),
			'description'	=> '',
			'font-family'	=> '',
			'font-size' 	=> '0.866em',
			'font-weight'	=> '700',
			'font-style'	=> '',
			'line-height'	=> '1.924em',
			'margin-top'	=> '',
			'margin-bottom'	=> ''
			)
		);
		junotoys_add_custom_font('logo', array(
			'title'			=> esc_html__('Logo', 'junotoys'),
			'description'	=> '',
			'font-family'	=> '',
			'font-size' 	=> '2.8571em',
			'font-weight'	=> '700',
			'font-style'	=> '',
			'line-height'	=> '0.75em',
			'margin-top'	=> '2em',
			'margin-bottom'	=> '2em'
			)
		);
		junotoys_add_custom_font('button', array(
			'title'			=> esc_html__('Buttons', 'junotoys'),
			'description'	=> '',
			'font-family'	=> 'Fredoka One',
			'font-size' 	=> '0.8em',
			'font-weight'	=> '',
			'font-style'	=> '',
			'line-height'	=> '1em'
			)
		);
		junotoys_add_custom_font('input', array(
			'title'			=> esc_html__('Input fields', 'junotoys'),
			'description'	=> '',
			'font-family'	=> '',
			'font-size' 	=> '',
			'font-weight'	=> '',
			'font-style'	=> '',
			'line-height'	=> '1.2857em'
			)
		);

	}
}





//------------------------------------------------------------------------------
// Theme fonts
//------------------------------------------------------------------------------

// Add theme fonts in the used fonts list
if (!function_exists('junotoys_filter_theme_styles_used_fonts')) {
	//Handler of add_filter('junotoys_filter_used_fonts', 'junotoys_filter_theme_styles_used_fonts');
	function junotoys_filter_theme_styles_used_fonts($theme_fonts) {
		$theme_fonts['Lato'] = 1;
		return $theme_fonts;
	}
}

// Add theme fonts (from Google fonts) in the main fonts list (if not present).
// To use custom font-face you not need add it into list in this function
// How to install custom @font-face fonts into the theme?
// All @font-face fonts are located in "theme_name/css/font-face/" folder in the separate subfolders for the each font. Subfolder name is a font-family name!
// Place full set of the font files (for each font style and weight) and css-file named stylesheet.css in the each subfolder.
// Create your @font-face kit by using Fontsquirrel @font-face Generator (http://www.fontsquirrel.com/fontface/generator)
// and then extract the font kit (with folder in the kit) into the "theme_name/css/font-face" folder to install
if (!function_exists('junotoys_filter_theme_styles_list_fonts')) {
	//Handler of add_filter('junotoys_filter_list_fonts', 'junotoys_filter_theme_styles_list_fonts');
	function junotoys_filter_theme_styles_list_fonts($list) {
		// Example:
		// if (!isset($list['Advent Pro'])) {
		//		$list['Advent Pro'] = array(
		//			'family' => 'sans-serif',																						// (required) font family
		//			'link'   => 'Advent+Pro:100,100italic,300,300italic,400,400italic,500,500italic,700,700italic,900,900italic',	// (optional) if you use Google font repository
		//			'css'    => junotoys_get_file_url('/css/font-face/Advent-Pro/stylesheet.css')									// (optional) if you use custom font-face
		//			);
		// }
		if (!isset($list['Lato']))	$list['Lato'] = array('family'=>'sans-serif');
		return $list;
	}
}



//------------------------------------------------------------------------------
// Theme stylesheets
//------------------------------------------------------------------------------

// Add theme.less into list files for compilation
if (!function_exists('junotoys_filter_theme_styles_compile_less')) {
	//Handler of add_filter('junotoys_filter_compile_less', 'junotoys_filter_theme_styles_compile_less');
	function junotoys_filter_theme_styles_compile_less($files) {
		if (file_exists(junotoys_get_file_dir('css/theme.less'))) {
		 	$files[] = junotoys_get_file_dir('css/theme.less');
		}
		return $files;	
	}
}

// Add theme stylesheets
if (!function_exists('junotoys_action_theme_styles_add_styles')) {
	//Handler of add_action('junotoys_action_add_styles', 'junotoys_action_theme_styles_add_styles');
	function junotoys_action_theme_styles_add_styles() {
		// Add stylesheet files only if LESS supported
		if ( junotoys_get_theme_setting('less_compiler') != 'no' ) {
			wp_enqueue_style( 'junotoys-theme-style', junotoys_get_file_url('css/theme.css'), array(), null );
			wp_add_inline_style( 'junotoys-theme-style', junotoys_get_inline_css() );
		}
	}
}

// Add theme inline styles
if (!function_exists('junotoys_filter_theme_styles_add_styles_inline')) {
	//Handler of add_filter('junotoys_filter_add_styles_inline', 'junotoys_filter_theme_styles_add_styles_inline');
	function junotoys_filter_theme_styles_add_styles_inline($custom_style) {
		// Todo: add theme specific styles in the $custom_style to override
		//       rules from style.css and shortcodes.css
		// Example:
		//		$scheme = junotoys_get_custom_option('body_scheme');
		//		if (empty($scheme)) $scheme = 'original';
		//		$clr = junotoys_get_scheme_color('text_link');
		//		if (!empty($clr)) {
		// 			$custom_style .= '
		//				a,
		//				.bg_tint_light a,
		//				.top_panel .content .search_wrap.search_style_default .search_form_wrap .search_submit,
		//				.top_panel .content .search_wrap.search_style_default .search_icon,
		//				.search_results .post_more,
		//				.search_results .search_results_close {
		//					color:'.esc_attr($clr).';
		//				}
		//			';
		//		}

		// Submenu width
		$menu_width = junotoys_get_theme_option('menu_width');
		if (!empty($menu_width)) {
			$custom_style .= "
				/* Submenu width */
				.menu_side_nav > li ul,
				.menu_main_nav > li ul {
					width: ".intval($menu_width)."px;
				}
				.menu_side_nav > li > ul ul,
				.menu_main_nav > li > ul ul {
					left:".intval($menu_width+4)."px;
				}
				.menu_side_nav > li > ul ul.submenu_left,
				.menu_main_nav > li > ul ul.submenu_left {
					left:-".intval($menu_width+1)."px;
				}
			";
		}
	
		// Logo height
		$logo_height = junotoys_get_custom_option('logo_height');
		if (!empty($logo_height)) {
			$custom_style .= "
				/* Logo header height */
				.sidebar_outer_logo .logo_main,
				.top_panel_wrap .logo_main,
				.top_panel_wrap .logo_fixed {
					height:".intval($logo_height)."px;
				}
			";
		}
	
		// Logo top offset
		$logo_offset = junotoys_get_custom_option('logo_offset');
		if (!empty($logo_offset)) {
			$custom_style .= "
				/* Logo header top offset */
				.top_panel_wrap .logo {
					margin-top:".intval($logo_offset)."px;
				}
			";
		}

		// Logo footer height
		$logo_height = junotoys_get_theme_option('logo_footer_height');
		if (!empty($logo_height)) {
			$custom_style .= "
				/* Logo footer height */
				.contacts_wrap .logo img {
					height:".intval($logo_height)."px;
				}
			";
		}

		// Custom css from theme options
		$custom_style .= junotoys_get_custom_option('custom_css');

		return $custom_style;	
	}
}


//------------------------------------------------------------------------------
// Theme scripts
//------------------------------------------------------------------------------

// Add theme scripts
if (!function_exists('junotoys_action_theme_styles_add_scripts')) {
	//Handler of add_action('junotoys_action_add_scripts', 'junotoys_action_theme_styles_add_scripts');
	function junotoys_action_theme_styles_add_scripts() {
		if (junotoys_get_theme_option('show_theme_customizer') == 'yes' && file_exists(junotoys_get_file_dir('js/theme.customizer.js')))
			wp_enqueue_script( 'junotoys-theme_styles-customizer-script', junotoys_get_file_url('js/theme.customizer.js'), array(), null, true );
	}
}

// Add theme scripts inline
if (!function_exists('junotoys_filter_theme_styles_localize_script')) {
	//Handler of add_filter('junotoys_filter_localize_script',		'junotoys_filter_theme_styles_localize_script');
	function junotoys_filter_theme_styles_localize_script($vars) {
		if (empty($vars['theme_font']))
			$vars['theme_font'] = junotoys_get_custom_font_settings('p', 'font-family');
		$vars['theme_color'] = junotoys_get_scheme_color('text_dark');
		$vars['theme_bg_color'] = junotoys_get_scheme_color('bg_color');
		return $vars;
	}
}
?>