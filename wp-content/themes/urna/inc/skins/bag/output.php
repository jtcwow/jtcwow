<?php if ( ! defined('URNA_THEME_DIR')) exit('No direct script access allowed');

$theme_primary = require_once( get_parent_theme_file_path( URNA_INC . '/class-primary-color.php') );

/*For example $main_color_skin 	= '.top-info > .widget'; */
$main_font_skin 	= '.btn';
$main_color_skin 	= '.has-after:hover,button.btn-close:hover,.new-input + span:before,.new-input + label:before,.top-newsletter .textwidget a , .top-newsletter .input-group-btn:after,.textwidget p > a.active,#tbay-header .navbar-nav > li.active > a, #tbay-header .navbar-nav > li:hover > a,#tbay-header .navbar-nav > li:focus > a,#tbay-header .navbar-nav > li > a:active,#tbay-header .navbar-nav > li > a:hover,#tbay-header .navbar-nav > li > a:focus,#tbay-header .navbar-nav > li > a:hover:after ';  
$main_bg_skin 		= '.has-after:after,.tbay-addon-categoriestabs .show-all:hover,.tbay-search-form form .input-group-addon .button-search,.tbay-to-top a:hover , .btn-theme-2 ';
$main_border_skin 	= '.btn-theme-2,.tbay-addon-categoriestabs .show-all:hover,.tbay-to-top a:hover ';

$main_font 				= $theme_primary['main_font']; 
$main_color 			= $theme_primary['color']; 
$main_bg 				= $theme_primary['background'];
$main_border 			= $theme_primary['border'];
$main_top_border 		= $theme_primary['border-top-color'];
$main_right_border 		= $theme_primary['border-right-color'];
$main_bottom_border 	= $theme_primary['border-bottom-color'];
$main_left_border 		= $theme_primary['border-left-color'];

if( !empty($main_font_skin) ) {
	$main_font 	= $main_font . ',' . $main_font_skin; 
}
if( !empty($main_color_skin) ) {
	$main_color 	= $main_color . ',' . $main_color_skin; 
}
if( !empty($main_bg_skin) ) {
	$main_bg 	= $main_bg. ',' .$main_bg_skin; 
}
if( !empty($main_border_skin) ) {
	$main_border 	= $main_border. ',' .$main_border_skin; 
}


/**
 * ------------------------------------------------------------------------------------------------
 * Prepare CSS selectors for theme settions (colors, borders, typography etc.)
 * ------------------------------------------------------------------------------------------------
 */

$output = array();
$output['topbar_bg'] = $output['topbar_text_color'] = $output['topbar_link_color'] = $output['topbar_link_color_hover'] = array();

/*CustomMain color*/
$output['main_color'] = array( 
	'color' => urna_texttrim($main_color),
	'background-color' => urna_texttrim($main_bg),
	'border-color' => urna_texttrim($main_border),
);
if( !empty($main_top_border) ) {

	$bordertop = array(
		'border-top-color' => urna_texttrim($main_top_border),
	);

	$output['main_color'] = array_merge($output['main_color'],$bordertop);
}
if( !empty($main_right_border) ) {
	
	$borderright = array(
		'border-right-color' => urna_texttrim($main_right_border),
	);

	$output['main_color'] = array_merge($output['main_color'],$borderright);
}
if( !empty($main_bottom_border) ) {
	
	$borderbottom = array(
		'border-bottom-color' => urna_texttrim($main_bottom_border),
	);

	$output['main_color'] = array_merge($output['main_color'],$borderbottom);
}
if( !empty($main_left_border) ) {
	
	$borderleft = array(
		'border-left-color' => urna_texttrim($main_left_border),
	);

	$output['main_color'] = array_merge($output['main_color'],$borderleft);
}

/*Custom Fonts*/
$output['primary-font'] = $main_font;

/*Custom Header*/
$output['header_bg'] 					= array(
	'background'=> urna_texttrim('#tbay-header .sidebar-header-main')
);
$output['header_text_color'] 			= array('.top-newsletter .copyright, .top-newsletter .widget-title');
$output['header_link_color'] 			= array('#tbay-header .navbar-nav > li > a, .top-newsletter .copyright a');

$output['header_link_color_active'] = array( 
	'color' => urna_texttrim('.navbar-nav > li > a:hover:after, #tbay-header .navbar-nav > li > a:hover, #tbay-header .navbar-nav > li:hover > a,#tbay-header .navbar-nav > li.active > a,.top-newsletter .copyright a:hover'),
	'background-color' => urna_texttrim(''),
);

/*Custom Main Menu*/
$output['main_menu_bg'] 				= array(
	'background'=> urna_texttrim('#tbay-header .tbay-mainmenu')
);
$output['main_menu_link_color'] 		= array('#tbay-header .navbar-nav > li > a,.navbar-nav > li > a:after');
$output['main_menu_link_color_active'] 	= array('#tbay-header .navbar-nav > li > a:hover:after,#tbay-header .navbar-nav > li > a:hover, #tbay-header .navbar-nav > li:hover > a,#tbay-header .navbar-nav > li.active > a');


/*Custom Footer*/
$output['footer_bg'] 					= array(
	'background'=> urna_texttrim('.tbay-footer')
);
$output['footer_heading_color'] 		= array('.tbay-footer .tbay-addon .tbay-addon-title');
$output['footer_text_color'] 			= array('.tbay-footer .tbay-addon-text-heading .subtitle, .contact-info li');
$output['footer_link_color'] 			= array('.tbay-footer .menu li > a, .social.style3 li a');
$output['footer_link_color_hover'] 		= array('.tbay-footer .menu li > a:hover, .social.style3 li a:hover');

/*Custom Copyright*/
$output['copyright_bg'] 				= array(
	'background'=> urna_texttrim('')
);
$output['copyright_text_color'] 		= array('');
$output['copyright_link_color'] 		= array('');
$output['copyright_link_color_hover'] 	= array('');

/*Background hover*/
$output['background_hover']  	= $theme_primary['background_hover'];
/*Tablet*/
$output['tablet_color'] 	 	= $theme_primary['tablet_color'];
$output['tablet_background'] 	= $theme_primary['tablet_background'];
$output['tablet_border'] 		= $theme_primary['tablet_border'];
/*Mobile*/
$output['mobile_color'] 		= $theme_primary['mobile_color'];
$output['mobile_background'] 	= $theme_primary['mobile_background'];
$output['mobile_border'] 		= $theme_primary['mobile_border'];

return apply_filters( 'urna_get_output', $output);
