<?php if ( ! defined('URNA_THEME_DIR')) exit('No direct script access allowed');

$theme_primary = require_once( get_parent_theme_file_path( URNA_INC . '/class-primary-color.php') );

/*For example $main_color_skin 	= '.top-info > .widget'; */
$main_font_skin 	= '.btn';
$main_color_skin 	= '.has-after:hover,button.btn-close:hover,.new-input + span:before,.new-input + label:before,.woocommerce-currency-switcher-form .SumoSelect > .CaptionCont > span:hover,.tbay-login > a:focus,#track-order a:hover , .top-flashsale a,.navbar-nav > li > a:hover,.navbar-nav > li > a:focus,.navbar-nav > li > a.active,.recent-view h3:hover,.category-inside-title:hover,.category-inside-title:focus,.contact-info li.contact,.tbay-addon-tags .tag-img .content a:hover , .become-vendor .tbay-addon-features.style-3 .ourservice-heading,.cart-dropdown .cart-icon i:hover,body.tbay-homepage-demo .category-inside-title';  
$main_bg_skin 		= '.has-after:after , .btn-theme-2,.category-inside .tbay-vertical > li > a:hover,.tbay-footer .menu.treeview li > a:hover:before';
$main_border_skin 	= '.btn-theme-2,.product-block.v10:hover .image,.product-block.v10:hover .image.has-slider-gallery > a';


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

/*Theme color second*/
$output['main_color_second'] = array( 
	'color' => urna_texttrim('.tbay-to-top a:hover , .become-vendor .tbay-addon-features .ourservice-heading , .become-vendor .tbay-addon .tbay-addon-title,.become-vendor .tbay-addon .tbay-addon-heading,.testimonials-body .description'),
	'background-color' => urna_texttrim('.tbay-addon .owl-carousel > .slick-arrow:hover,#tbay-main-content .product-block .group-buttons > div a:hover , #tbay-header,#tbay-header .header-main,.tparrows.revo-tbay:hover , .tbay-to-top a:hover , .become-vendor .tbay-addon-features.style-3 .tbay-addon-content , .wcv-dashboard-navigation ul,.wcvendors-pro-dashboard-wrapper .wcv-navigation ul.menu'),	
	'border-color' => urna_texttrim('.tbay-addon .owl-carousel > .slick-arrow:hover,#tbay-main-content .product-block .group-buttons > div a:hover,#tbay-main-content .product-block .group-buttons > div:last-child a:hover') 
);


/*Custom Fonts*/
$output['primary-font'] = $main_font;

/*Custom Header*/
$output['header_bg'] 					= array(
	'background'=> urna_texttrim('#tbay-header, #tbay-header .header-main')
);
$output['header_text_color'] 			= array('#tbay-header .cart-dropdown .subtotal .woocommerce-Price-amount,#tbay-header p');
$output['header_link_color'] 			= array('#tbay-header .woocommerce-currency-switcher-form .SumoSelect > .CaptionCont > span,#tbay-header .woocommerce-currency-switcher-form .SumoSelect > .CaptionCont > label, #tbay-header .tbay-login >a,#tbay-header .top-wishlist a,#tbay-header .cart-dropdown .cart-icon i,#tbay-header .tbay-custom-language .select-button, #tbay-header .category-inside-title, .navbar-nav.megamenu > li > a, #tbay-header #track-order a,#tbay-header .recent-view h3');

$output['header_link_color_active'] = array( 
	'color' => urna_texttrim('#tbay-header .woocommerce-currency-switcher-form .SumoSelect > .CaptionCont:hover > span, #tbay-header .tbay-login >a:hover,#tbay-header .top-wishlist a:hover,#tbay-header .cart-dropdown:hover .cart-icon i,#tbay-header .tbay-custom-language .select-button:hover,#tbay-header .tbay-custom-language li:hover .select-button,#tbay-header .navbar-nav.megamenu > li:hover > a,#tbay-header .navbar-nav.megamenu > li > a:hover,#tbay-header #track-order a:hover,#tbay-header .recent-view h3:hover,#tbay-header .navbar-nav.megamenu > li.active > a,#tbay-header .category-inside-title:hover,#tbay-header .category-inside-title:focus,.recent-view h3:hover:after'),
	'background-color' => urna_texttrim(''),
);


/*Custom Main Menu*/
$output['main_menu_bg'] 				= array(
	'background'=> urna_texttrim('#tbay-header .tbay-mainmenu')
);
$output['main_menu_link_color'] 		= array('#tbay-header .navbar-nav.megamenu > li > a');
$output['main_menu_link_color_active'] 	= array('#tbay-header .navbar-nav.megamenu > li > a:hover,#tbay-header .navbar-nav.megamenu > li:hover > a,#tbay-header .navbar-nav.megamenu > li.active > a');


/*Custom Footer*/
$output['footer_bg'] 					= array(
	'background'=> urna_texttrim('.tbay-footer')
);
$output['footer_heading_color'] 		= array('.tbay-footer .tbay-addon .tbay-addon-title');
$output['footer_text_color'] 			= array('.tbay-footer .tbay-addon .tbay-addon-title .subtitle, .contact-info li, .copyright');
$output['footer_link_color'] 			= array('.tbay-footer .menu li > a, .copyright a,.contact-info li a');
$output['footer_link_color_hover'] 		= array(
	'color' => urna_texttrim('.contact-info li a:hover, .tbay-footer ul.menu li > a:hover, .copyright a:hover, .tbay-footer ul.menu li.active > a'),
	'background-color' => urna_texttrim('.tbay-footer .menu.treeview li > a:hover:before'),
);

/*Custom Copyright*/
$output['copyright_bg'] 				= array(
	'background'=> urna_texttrim('.tbay-footer .tbay-copyright')
);
$output['copyright_text_color'] 		= array('.copyright');
$output['copyright_link_color'] 		= array('.tbay-copyright .none-menu .menu li a, .copyright a');
$output['copyright_link_color_hover'] 	= array('.tbay-copyright .none-menu .menu li a:hover, .copyright a:hover');

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
