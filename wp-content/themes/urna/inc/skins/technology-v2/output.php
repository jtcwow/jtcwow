<?php if ( ! defined('URNA_THEME_DIR')) exit('No direct script access allowed');

$theme_primary = require_once( get_parent_theme_file_path( URNA_INC . '/class-primary-color.php') );

/*For example $main_color_skin 	= '.top-info > .widget'; */
$main_font_skin 	= '.btn';
$main_color_skin 	= '.has-after:hover,button.btn-close:hover,.new-input + span:before,.new-input + label:before,#tbay-main-content .tbay-addon-products .tbay-addon-title ~ .show-all:hover,#tbay-main-content .tbay-addon-categories .tbay-addon-title ~ .show-all:hover, .product-block.v5 .add-cart a.added + a.added_to_cart';  
$main_bg_skin 		= '.has-after:after , .btn-theme-2 , #tbay-header,#tbay-header .header-main,#tbay-header .header-mainmenu';
$main_border_skin 	= '.btn-theme-2,.tparrows.revo-tbay:hover';


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


/*CustomMain color*/
$output['main_color'] = array( 
	'color' => urna_texttrim($main_color),
	'background-color' => urna_texttrim($main_bg),
	'border-color' => urna_texttrim($main_border)
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

/*Theme color second*/
$output['main_color_second'] = array( 
	'color' => urna_texttrim('.product-block.v5 .yith-wcwl-wishlistexistsbrowse.show a, .product-block.v5 .yith-wcwl-wishlistaddedbrowse.show a'),
	'background-color' => urna_texttrim('.tbay-search-form .button-group , .tbay-search-form .button-search,.top-wishlist .count_wishlist,.cart-dropdown .cart-icon .mini-cart-items , .tbay-addon.tbay-addon-newsletter .input-group-btn,.tbay-addon ul.list-tags li a:hover , #custom-register input.submit_button,#custom-login input.submit_button,#custom-register input.submit_button:hover,#custom-login input.submit_button:hover , .product-block.v5 .group-buttons > div a:hover,#tbay-main-content .tbay-addon:not(.tbay-addon-vertical):not(.vertical):not(.relate-blog).tbay-addon-flash-sales:not(.tbay-addon-grid) .tbay-addon-content .owl-carousel:before,#tbay-main-content .tbay-addon:not(.tbay-addon-vertical):not(.vertical):not(.relate-blog).tbay-addon-flash-sales:not(.tbay-addon-grid) .tbay-addon-content .owl-carousel:after,#tbay-main-content .tbay-addon:not(.tbay-addon-vertical):not(.vertical):not(.relate-blog).product-countdown:not(.tbay-addon-grid) .tbay-addon-content .owl-carousel:before,#tbay-main-content .tbay-addon:not(.tbay-addon-vertical):not(.vertical):not(.relate-blog).product-countdown:not(.tbay-addon-grid) .tbay-addon-content .owl-carousel:after'),
	'border-color' => urna_texttrim('.tbay-addon ul.list-tags li a:hover , #custom-register input.submit_button,#custom-login input.submit_button,#custom-register input.submit_button:hover,#custom-login input.submit_button:hover , .product-block.v5 .group-buttons > div a:hover,#tbay-main-content .tbay-addon:not(.tbay-addon-vertical):not(.vertical):not(.relate-blog).tbay-addon-flash-sales:not(.tbay-addon-grid) .tbay-addon-content,#tbay-main-content .tbay-addon:not(.tbay-addon-vertical):not(.vertical):not(.relate-blog).product-countdown:not(.tbay-addon-grid) .tbay-addon-content')
);


/*Custom Fonts*/
$output['primary-font'] = $main_font;

//*Custom Header*/
$output['header_bg'] 					= array(
	'background'=> urna_texttrim('#tbay-header, #tbay-header .header-main, .topbar:before,#tbay-header .header-mainmenu')
);
$output['header_text_color'] 			= array('#tbay-header p,#tbay-header .top-cart .text-cart,#tbay-header .top-cart .text-cart .woocommerce-Price-amount');
$output['header_link_color'] 			= array('#track-order a, .tbay-login .account-button, .topbar-right i, .topbar .social a, .topbar .tbay-custom-language .select-button, .topbar .tbay-custom-language .select-button:after, .topbar .woocommerce-currency-switcher-form .SumoSelect > .CaptionCont > span, .topbar .woocommerce-currency-switcher-form .SumoSelect > .CaptionCont label i:after, .category-inside-title, .navbar-nav.megamenu>li>a, .recent-view h3,#tbay-header .yith-compare-header a, #tbay-header .cart-dropdown .cart-icon, #tbay-header .top-wishlist a,#tbay-header .tbay-search-form .button-search.icon');

$output['header_link_color_active'] = array( 
	'color' => urna_texttrim('.topbar .tbay-login .account-button:hover, #track-order a:hover,.topbar .social a:hover, .topbar-right a:hover i, .topbar  .tbay-custom-language li:hover .select-button, .topbar .tbay-custom-language .select-button:hover, .topbar .tbay-custom-language li:hover .select-button:after, .topbar .woocommerce-currency-switcher-form .SumoSelect>.CaptionCont:hover label i:after, .topbar .woocommerce-currency-switcher-form .SumoSelect > .CaptionCont > span:hover, #tbay-header .yith-compare-header a:hover, #tbay-header .cart-dropdown .cart-icon:hover, #tbay-header .top-wishlist a:hover, #tbay-header .category-inside-title:hover, #tbay-header .category-inside-title:focus, #tbay-header .navbar-nav > li:hover > a, #tbay-header .navbar-nav > li:focus > a, #tbay-header .navbar-nav > li.active > a, .recent-view h3:hover,#tbay-header .tbay-search-form .button-search.icon:hover')
);

/*Custom Top Bar color*/
$output['topbar_bg'] 					= array(
	'background'=> urna_texttrim('.topbar, .topbar:before')
);
$output['topbar_text_color'] 			= array('.topbar p');
$output['topbar_link_color'] 			= array('#track-order a, .tbay-login .account-button, .topbar-right i, .topbar a, .topbar .tbay-custom-language .select-button, .topbar .tbay-custom-language .select-button:after, .topbar .woocommerce-currency-switcher-form .SumoSelect > .CaptionCont > span, .topbar .woocommerce-currency-switcher-form .SumoSelect > .CaptionCont label i:after, .topbar .social a');

$output['topbar_link_color_hover'] = array( 
	'color' => urna_texttrim('.topbar .tbay-login .account-button:hover, #track-order a:hover,.topbar a:hover, .topbar-right a:hover i, .topbar  .tbay-custom-language li:hover .select-button, .topbar .tbay-custom-language .select-button:hover, .topbar .tbay-custom-language li:hover .select-button:after, .topbar .woocommerce-currency-switcher-form .SumoSelect>.CaptionCont:hover label i:after, .topbar .woocommerce-currency-switcher-form .SumoSelect > .CaptionCont > span:hover, .topbar .social a:hover')
);

/*Custom Main Menu*/
$output['main_menu_bg'] 				= array(
	'background'=> urna_texttrim('#tbay-header .header-mainmenu .tbay-mainmenu')
);
$output['main_menu_link_color'] 		= array('#tbay-header .navbar-nav > li > a');
$output['main_menu_link_color_active'] 	= array('#tbay-header .navbar-nav > li > a:hover,#tbay-header .navbar-nav > li.active > a,#tbay-header .navbar-nav > li:hover > a');


/*Custom Footer*/
$output['footer_bg'] 					= array(
	'background'=> urna_texttrim('.tbay-footer')
);
$output['footer_heading_color'] 		= array('.tbay-addon .tbay-addon-title');
$output['footer_text_color'] 			= array('.tbay-addon .tbay-addon-title .subtitle, .contact-info li, .copyright');
$output['footer_link_color'] 			= array('.contact-info a, .tbay-footer .menu li > a, .copyright a');
$output['footer_link_color_hover'] 		= array('.contact-info a:hover, .tbay-footer .menu li > a:hover,.tbay-footer .menu li:hover > a,.tbay-footer .menu li.active > a, .copyright a:hover');

/*Custom Copyright*/
$output['copyright_bg'] 				= array(
	'background'=> urna_texttrim('.tbay-footer .tbay-copyright')
);
$output['copyright_text_color'] 		= array('.copyright');
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
