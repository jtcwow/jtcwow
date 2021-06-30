<?php if ( ! defined('URNA_THEME_DIR')) exit('No direct script access allowed');

$theme_primary = require_once( get_parent_theme_file_path( URNA_INC . '/class-primary-color.php') );

/*For example $main_color_skin 	= '.top-info > .widget'; */

$main_font_skin 	= '.btn,.woocs_price_code del,.woocommerce-grouped-product-list-item__price del,.yith-wfbt-submit-block .price_text > span.total_price del,.woocommerce-Price-amount del,.woocommerce-order-received .woocommerce-order table.shop_table .woocommerce-Price-amount del , #tbay-main-content .tbay-addon .tbay-addon-title .subtitle';
$main_color_skin 	= '.has-after:hover,button.btn-close:hover,.new-input + span:before,.new-input + label:before,#tbay-search-form-canvas.v4 button:hover,#tbay-search-form-canvas.v4 button:hover i,#tbay-search-form-canvas.v4 .sidebar-canvas-search .sidebar-content .select-category .optWrapper .options li:hover label , #tbay-search-form-canvas.v4 .autocomplete-suggestions > div .suggestion-group:hover .suggestion-title,
#tbay-header .tbay-mainmenu a:hover,#tbay-header .tbay-mainmenu a:focus,#tbay-header .tbay-mainmenu a.active , .navbar-nav > li:focus > a,.navbar-nav > li:hover > a,.navbar-nav > li.active > a, #tbay-header .top-wishlist .wishlist-icon:hover,#tbay-header .top-wishlist .wishlist-icon:focus,#tbay-header .top-wishlist .wishlist:hover,#tbay-header .top-wishlist .wishlist:focus';  
$main_bg_skin 		= '.has-after:after , .btn-theme-2,.tbay-addon-categories .cat-name,.tbay-addon-categoriestabs .show-all:hover,.tbay-to-top a:hover,.cart-dropdown > a:hover,.tbay-login > a:hover';
$main_border_skin 	= '.btn-theme-2,.tbay-addon-categoriestabs .show-all:hover,.cart-dropdown > a:hover,.tbay-login > a:hover';
$main_border_top_skin 	= '#tbay-search-form-canvas.v4 .tbay-loading:after';


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
if( !empty($main_border_top_skin) ) {
	$main_top_border 	= $main_top_border. ',' .$main_border_top_skin; 
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

$main_bg_array 		= explode(",", $main_bg);
$main_border_array  = explode(",", $main_border);

$main_border_same	=	array_intersect($main_bg_array,$main_border_array);


$main_border_second = implode(",", array_diff($main_border_array,$main_border_same));


/*Theme color second*/
$output['main_color_second'] = array( 
	'color' => urna_texttrim('#tbay-header .tbay-mainmenu a:hover,#tbay-header .tbay-mainmenu a:focus,#tbay-header .tbay-mainmenu a.active , .navbar-nav > li:focus > a,.navbar-nav > li:hover > a,.navbar-nav > li.active > a , .navbar-nav > li .dropdown-menu .tbay-addon ul:not(.entry-meta-list) li:focus > a,.navbar-nav > li .dropdown-menu .tbay-addon ul:not(.entry-meta-list) li:hover > a,.navbar-nav > li .dropdown-menu .tbay-addon ul:not(.entry-meta-list) li.active > a , .tbay-addon-product-tabs .nav-tabs > li.active > a,.tbay-addon-product-tabs .nav-tabs > li.active > a:hover,.tbay-addon-product-tabs .nav-tabs > li.active > a:focus,.tbay-addon-product-tabs .nav-tabs > li:hover > a,.tbay-addon-product-tabs .nav-tabs > li:hover > a:hover,.tbay-addon-product-tabs .nav-tabs > li:hover > a:focus,.tbay-addon-categoriestabs .nav-tabs > li.active > a,.tbay-addon-categoriestabs .nav-tabs > li.active > a:hover,.tbay-addon-categoriestabs .nav-tabs > li.active > a:focus,.tbay-addon-categoriestabs .nav-tabs > li:hover > a,.tbay-addon-categoriestabs .nav-tabs > li:hover > a:hover,.tbay-addon-categoriestabs .nav-tabs > li:hover > a:focus , .show-all:hover,.entry-title a:hover,.product-block .name a:hover'),
	'background-color' => urna_texttrim('.top-bar , .tbay-footer'),
	'border-color' => urna_texttrim('')
);

/*Theme color third*/
$output['main_color_third'] = array( 
	'color' => urna_texttrim('.btn-link:hover,.btn-link:focus , .tbay-addon-blog.carousel .post .readmore,.tbay-addon-blog.grid .post .readmore,.product-block.v3 .group-buttons > div a:hover,.product-block.v3 .add-cart a.added + a.added_to_cart,.product-block.v3 .yith-wcwl-wishlistexistsbrowse.show a,.product-block.v3 .yith-wcwl-wishlistaddedbrowse.show a,.product-block.v3 .group-buttons > div a.added,.product-block.v3 .group-buttons > div a:hover:before'),
	'background-color' => urna_texttrim('.elements .vc_row .tbay-addon-flash-sales .flash-sales-date .times > div span , .tbay-addon-products .progress-bar , .flash-sales-date .times > div,.tbay-addon-blog.carousel .post .readmore:before,.tbay-addon-blog.grid .post .readmore:before,.top-cart .cart-dropdown .cart-icon .mini-cart-items'),
	'border-color' => urna_texttrim('')
);

/*Custom Fonts*/
$output['primary-font'] = $main_font;
$output['secondary-font'] = '.woocs_price_code,.woocommerce-grouped-product-list-item__price,.yith-wfbt-submit-block .price_text > span.total_price, .woocommerce-Price-amount,.woocommerce-order-received .woocommerce-order table.shop_table .woocommerce-Price-amount,.tbay-addon-categories .cat-name , .tbay-addon-blog.carousel .post .entry-title,.tbay-addon-blog.grid .post .entry-title,.woocommerce .product-block span.onsale .saled,.woocommerce .product-block span.onsale .featured , .tbay-homepage-demo #tbay-main-content .tbay-addon .tbay-addon-title,.tbay-homepage-demo #tbay-main-content .tbay-addon .tbay-addon-heading';

/*Custom Header*/
$output['header_bg'] 					= array(
	'background'=> urna_texttrim('#tbay-header .header-main,.top-bar')
);
$output['header_text_color'] 			= array('#tbay-header .header-main p,.top-contact span,.top-contact,#tbay-header .tbay-custom-language .select-button,.woocommerce-currency-switcher-form .SumoSelect > .CaptionCont > span');
$output['header_link_color'] 			= array('.track-order a,.track-order a,.cart-dropdown > a, .tbay-login > a,#tbay-header .top-wishlist .wishlist-icon,#tbay-header .navbar-nav > li > a');

$output['header_link_color_active'] = array( 
	'color' => urna_texttrim('.top-bar .track-order a:hover,.top-bar .track-order a:focus,.top-bar .woocommerce-currency-switcher-form .SumoSelect > .CaptionCont:hover label i:after,#tbay-header .top-wishlist .wishlist-icon:hover,#tbay-header .navbar-nav > li > a:hover,#tbay-header .navbar-nav > li:hover > a,#tbay-header .navbar-nav > li.active > a'),
	'background-color' => urna_texttrim(''),
);

/*Custom Top Bar color*/

$output['topbar_bg'] 					= array(
	'background'=> urna_texttrim('#tbay-header .top-bar')
);
$output['topbar_text_color'] 			= array('.top-contact span,.topbar p,.top-bar .top-contact,#tbay-header .tbay-custom-language .select-button, .woocommerce-currency-switcher-form .SumoSelect > .CaptionCont > span');
$output['topbar_link_color'] 			= array('.tbay-custom-language .select-button,.tbay-custom-language .select-button::after,.woocommerce-currency-switcher-form .SumoSelect > .CaptionCont,.woocommerce-currency-switcher-form .SumoSelect > .CaptionCont > label i:after,.track-order a,#tbay-header .top-wishlist .wishlist-icon, #tbay-header .top-wishlist .wishlist');

$output['topbar_link_color_hover'] = array( 
	'color' => urna_texttrim('.top-bar .tbay-custom-language li:hover .select-button,.top-bar .tbay-custom-language li:hover .select-button:after,.top-bar .woocommerce-currency-switcher-form .SumoSelect > .CaptionCont:hover,.top-bar .track-order a:hover,.top-bar .track-order a:focus,.top-bar .woocommerce-currency-switcher-form .SumoSelect > .CaptionCont:hover label i:after,#tbay-header .top-wishlist .wishlist-icon:hover'),
	'background-color' => urna_texttrim(''),
);

/*Custom Main Menu*/
$output['main_menu_bg'] 				= array(
	'background'=> urna_texttrim('#tbay-header .tbay-mainmenu')
);
$output['main_menu_link_color'] 		= array('#tbay-header .navbar-nav > li > a');
$output['main_menu_link_color_active'] 	= array('#tbay-header .navbar-nav > li > a:hover,#tbay-header .navbar-nav > li:hover > a,#tbay-header .navbar-nav > li.active > a');


/*Custom Footer*/
$output['footer_bg'] 					= array(
	'background'=> urna_texttrim('.tbay-footer')
);
$output['footer_heading_color'] 		= array('.tbay-footer .tbay-addon.tbay-addon-newsletter .tbay-addon-title,.tbay-footer .tbay-addon:not(.tbay-addon-newsletter) .tbay-addon-title,.text-white');
$output['footer_text_color'] 			= array('.tbay-footer .tbay-copyright p,.tbay-footer p,.tbay-footer .contact-info li');
$output['footer_link_color'] 			= array('.tbay-footer .menu li > a,.tbay-footer a,.tbay-copyright .wpb_text_column a,#tbay-footer .contact-info li a');
$output['footer_link_color_hover'] 		= array('#tbay-footer .menu li > a:hover,#tbay-footer .menu li:hover > a,#tbay-footer .menu li > a:focus,#tbay-footer .menu li.active > a,#tbay-footer a:hover,#tbay-footer .contact-info li a:hover,.tbay-copyright .wpb_text_column a:hover');

/*Custom Copyright*/
$output['copyright_bg'] 				= array(
	'background'=> urna_texttrim('.tbay-footer .tbay-copyright')
);
$output['copyright_text_color'] 		= array('.tbay-footer .tbay-copyright p');
$output['copyright_link_color'] 		= array('.tbay-footer .tbay-copyright a');
$output['copyright_link_color_hover'] 	= array('#tbay-footer .tbay-copyright a:hover');

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
