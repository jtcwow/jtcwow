<?php if ( ! defined('URNA_THEME_DIR')) exit('No direct script access allowed');

$theme_primary = require_once( get_parent_theme_file_path( URNA_INC . '/class-primary-color.php') );

/*For example $main_color_skin 	= '.top-info > .widget'; */
$main_font_skin 	= '.btn,.tbay-footer .tbay-addon .tbay-addon-title ';
$main_color_skin 	= '.has-after:hover,button.btn-close:hover,.new-input + span:before,.new-input + label:before,.tbay-footer .tbay-addon-newsletter .input-group-btn:hover:before,.tbay-addon-social .social.style3 li a:hover,.woocommerce .products .star-rating span:before,.woocommerce .product .star-rating span:before,.woocommerce .star-rating span:before,.elements .show-all:hover';  
$main_bg_skin 		= '.has-after:after , .btn-theme-2,.tbay-addon:hover .owl-carousel > .slick-arrow:hover , .skin-jewelry .product-block.v14 .group-buttons > div a:hover , .skin-jewelry .show-all:hover , .product-block.v14 .group-buttons > div a.added,.product-block.v14 .group-buttons > div a.added + a.added_to_cart,.product-block.v14 .button-wishlist a:hover,.product-block.v14 .yith-wcwl-wishlistexistsbrowse.show a,.product-block.v14 .yith-wcwl-wishlistaddedbrowse.show a';
$main_border_skin 	= '.btn-theme-2 , .product-block.v14 .group-buttons > div a.added,.product-block.v14 .button-wishlist a:hover,.product-block.v14 .yith-wcwl-wishlistexistsbrowse.show a,.product-block.v14 .yith-wcwl-wishlistaddedbrowse.show a,.tbay-footer .container , .navbar-nav.megamenu';


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
$output['main_color_second'] = array( 
	'color' => urna_texttrim(''),
	'background-color' => urna_texttrim('.cart-dropdown .cart-icon .mini-cart-items, .top-wishlist .count_wishlist'),
	'border-color' => urna_texttrim('')
);

/*Custom Fonts*/
$output['primary-font'] = $main_font;
$output['secondary-font'] = '#tbay-header .navbar-nav.megamenu li > .dropdown-menu .widget .widgettitle,#tbay-header .navbar-nav.megamenu li > .dropdown-menu .tbay-addon-title , .tbay-homepage-demo #tbay-main-content .tbay-addon .tbay-addon-title,.tbay-homepage-demo #tbay-main-content .tbay-addon .tbay-addon-heading,.tbay-addon-tags .content a';

/*Custom Header*/
$output['header_bg'] 					= array(
	'background'=> urna_texttrim('#tbay-header ,#tbay-header .header-main')
);
$output['header_text_color'] 			= array('#tbay-header .top-contact .content,#tbay-header .top-contact .content .color-black');
$output['header_link_color'] 			= array('#tbay-header .canvas-menu-sidebar .btn-canvas-menu, .tbay-login >a, .top-wishlist a, .cart-dropdown .cart-icon, #tbay-search-form-canvas-v3 .search-open,#tbay-header .navbar-nav.megamenu > li > a');

$output['header_link_color_active'] = array( 
	'color' => urna_texttrim('#tbay-header .canvas-menu-sidebar .btn-canvas-menu:hover, .tbay-login >a:hover, .top-wishlist a:hover, .cart-dropdown .cart-icon:hover, #tbay-search-form-canvas-v3 .search-open:hover,#tbay-header .navbar-nav.megamenu > li > a:hover,#tbay-header .navbar-nav.megamenu > li:hover > a,#tbay-header .navbar-nav.megamenu > li.active > a'),
	'background-color' => urna_texttrim(''),
);

/*Custom Main Menu*/
$output['main_menu_bg'] 				= array(
	'background'=> urna_texttrim('#tbay-header .header-mainmenu')
);
$output['main_menu_link_color'] 		= array('#tbay-header .navbar-nav.megamenu > li > a');
$output['main_menu_link_color_active'] 	= array('#tbay-header .navbar-nav.megamenu > li > a:hover,#tbay-header .navbar-nav.megamenu > li:hover > a,#tbay-header .navbar-nav.megamenu > li.active > a');


/*Custom Footer*/
$output['footer_bg'] 					= array(
	'background'=> urna_texttrim('.tbay-footer')
);
$output['footer_heading_color'] 		= array('.tbay-footer .tbay-addon .tbay-addon-title');
$output['footer_text_color'] 			= array('.tbay-footer .vc_row:not(.tbay-copyright) .wpb_text_column p, .tbay-footer .tbay-copyright .wpb_text_column,.text-black');
$output['footer_link_color'] 			= array('.contact-info a, .tbay-footer .menu.treeview li > a, .tbay-addon-social .social.style3 > li a, .tbay-footer .tbay-copyright .tbay-addon-newsletter .input-group-btn:before,.tbay-footer a');
$output['footer_link_color_hover'] 		= array('.contact-info a:hover, .tbay-footer .menu.treeview li > a:hover, .tbay-addon-social .social.style3 > li a:hover, .tbay-footer .tbay-copyright .tbay-addon-newsletter .input-group-btn:hover:before, .tbay-footer ul.menu li.active a,.tbay-footer a:hover');

/*Custom Copyright*/
$output['copyright_bg'] 				= array(
	'background'=> urna_texttrim('.tbay-footer .tbay-copyright')
);
$output['copyright_text_color'] 		= array('.tbay-footer .tbay-copyright .wpb_text_column');
$output['copyright_link_color'] 		= array('.copyright a');
$output['copyright_link_color_hover'] 	= array('.copyright a:hover');


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
