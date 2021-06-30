<?php if ( ! defined('URNA_THEME_DIR')) exit('No direct script access allowed');

$theme_primary = require_once( get_parent_theme_file_path( URNA_INC . '/class-primary-color.php') );

/*For example $main_color_skin 	= '.top-info > .widget'; */
$main_font_skin 	= '.btn';
$main_color_skin 	= '.has-after:hover,button.btn-close:hover,.new-input + span:before,.new-input + label:before,.sidebar-content-wrapper .product-categories li a:hover,.product-block.v3 .group-buttons > div a:hover,.product-block.v3 .group-buttons > div a.compare.added'; 
$main_bg_skin 		= '.has-after:after,.canvas-menu-sidebar > a i:hover,.sidebar-content-wrapper .close-canvas-menu > i:hover,.sidebar-content-wrapper .close-canvas-menu > i:focus , .tbay-homepage-demo .tbay-addon-products:not(.tbay-addon-vertical) .show-all,.navbar-nav > li > a:before , .btn-theme-2,.tbay-to-top a:hover';
$main_border_skin 	= '.btn-theme-2,.tbay-to-top a:hover';


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

/*Custom Top Bar color*/
$output['topbar_bg'] 					= array(
	'background'=> urna_texttrim('.topbar')
);
$output['topbar_text_color'] 			= array('');
$output['topbar_link_color'] 			= array('');

$output['topbar_link_color_hover'] = array( 
	'color' => urna_texttrim(''),
	'background-color' => urna_texttrim(''),
);

/*Custom Header*/
$output['header_bg'] 					= array(
	'background'=> urna_texttrim('#tbay-header .header-main')
);
$output['header_text_color'] 			= array('#tbay-header .header-main p');
$output['header_link_color'] 			= array('#tbay-header .navbar-nav > li > a, #tbay-header .tbay-search-form .button-search.icon, #tbay-header .tbay-login > a i, #tbay-header .top-wishlist i, #tbay-header .cart-dropdown .cart-icon i');

$output['header_link_color_active'] = array( 
	'color' => urna_texttrim('#tbay-header .navbar-nav > li > a:hover,#tbay-header .navbar-nav > li:hover > a,#tbay-header .navbar-nav > li.active > a, #tbay-header .tbay-search-form .button-search.icon:hover, #tbay-header .tbay-login > a i:hover, #tbay-header .top-wishlist i:hover, #tbay-header .cart-dropdown .cart-icon i:hover'),
	'background-color' => urna_texttrim('.canvas-menu-sidebar > a i:hover, .navbar-nav > li.active > a:before, .navbar-nav > li:hover > a:before'),
);

/*Custom Main Menu*/
$output['main_menu_bg'] 				= array(
	'background'=> urna_texttrim('#tbay-header .tbay-mainmenu')
);
$output['main_menu_link_color'] 		= array('#tbay-header .navbar-nav > li > a');
$output['main_menu_link_color_active'] 	= array(
	'color' => urna_texttrim('#tbay-header .navbar-nav > li > a:hover,#tbay-header .navbar-nav > li:hover > a,#tbay-header .navbar-nav > li.active > a, #tbay-header .tbay-search-form .button-search.icon:hover, #tbay-header .tbay-login > a i:hover, #tbay-header .top-wishlist i:hover, #tbay-header .cart-dropdown .cart-icon i:hover'),
	'background-color' => urna_texttrim('.canvas-menu-sidebar > a i:hover, .navbar-nav > li.active > a:before, .navbar-nav > li:hover > a:before'),
);


/*Custom Footer*/
$output['footer_bg'] 					= array(
	'background'=> urna_texttrim('.tbay-footer,.tbay-copyright')
);
$output['footer_heading_color'] 		= array('.tbay-footer .tbay-addon .tbay-addon-title, .tbay-footer .tbay-addon .tbay-addon-heading');
$output['footer_text_color'] 			= array('.contact-info li .text-contact, .text-contact, .contact-info i,.contact-info li .text-black.text-black');
$output['footer_link_color'] 			= array('.tbay-footer .wpb_text_column a, .tbay-footer .menu li > a, .social.style3 li a');
$output['footer_link_color_hover'] 		= array('.tbay-footer .wpb_text_column a:hover,.social.style3 li a:hover,.tbay-footer .menu li > a:hover,.tbay-footer .menu li:hover > a,.tbay-footer .menu li.active > a');

/*Custom Copyright*/
$output['copyright_bg'] 				= array(
	'background'=> urna_texttrim('.tbay-footer .tbay-copyright')
);
$output['copyright_text_color'] 		= array('.tbay-copyright .text-contact');
$output['copyright_link_color'] 		= array('.tbay-footer .tbay-copyright .wpb_text_column a, .tbay-footer .tbay-copyright .menu li > a');
$output['copyright_link_color_hover'] 	= array('.tbay-copyright .menu.treeview li a:hover,.tbay-footer .tbay-copyright .wpb_text_column a:hover');


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
