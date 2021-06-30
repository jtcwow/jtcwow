=== Plugin Name ===
Contributors: (this should be a list of wordpress.org userid's)
Donate link: http://central.tech
Tags: comments, spam
Requires at least: 3.0.1
Tested up to: 3.4
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Here is a short description of the plugin.  This should be no more than 150 characters.  No markup here.

== Description ==

This is the long description.  No limit, and you can use Markdown (as well as in the following sections).

For backwards compatibility, if this section is missing, the full length of the short description will be used, and
Markdown parsed.

A few notes about the sections above:

*   "Contributors" is a comma separated list of wp.org/wp-plugins.org usernames
*   "Tags" is a comma separated list of tags that apply to the plugin
*   "Requires at least" is the lowest version that the plugin will work on
*   "Tested up to" is the highest version that you've *successfully used to test the plugin*. Note that it might work on
higher versions... this is just the highest one you've verified.
*   Stable tag should indicate the Subversion "tag" of the latest stable version, or "trunk," if you use `/trunk/` for
stable.

    Note that the `readme.txt` of the stable tag is the one that is considered the defining one for the plugin, so
if the `/trunk/readme.txt` file says that the stable tag is `4.3`, then it is `/tags/4.3/readme.txt` that'll be used
for displaying information about the plugin.  In this situation, the only thing considered from the trunk `readme.txt`
is the stable tag pointer.  Thus, if you develop in trunk, you can update the trunk `readme.txt` to reflect changes in
your in-development version, without having that information incorrectly disclosed about the current stable version
that lacks those changes -- as long as the trunk's `readme.txt` points to the correct stable tag.

    If no stable tag is provided, it is assumed that trunk is stable, but you should specify "trunk" if that's where
you put the stable version, in order to eliminate any doubt.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `jtcwow.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('plugin_name_hook'); ?>` in your templates

== Frequently Asked Questions ==

= A question that someone might have =

An answer to that question.

= What about foo bar? =

Answer to foo bar dilemma.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 1.0 =
* A change since the previous version.
* Another change.

= 0.5 =
* List versions from most recent at top to oldest at bottom.

== Upgrade Notice ==

= 1.0 =
Upgrade notices describe the reason a user should upgrade.  No more than 300 characters.

= 0.5 =
This version fixes a security related bug.  Upgrade immediately.

== Arbitrary section ==

You may provide arbitrary sections, in the same format as the ones above.  This may be of use for extremely complicated
plugins where more information needs to be conveyed that doesn't fit into the categories of "description" or
"installation."  Arbitrary sections will be shown below the built-in sections outlined above.

== A brief Markdown Example ==

Ordered list:

1. Some feature
1. Another feature
1. Something else about the plugin

Unordered list:

* something
* something else
* third thing

Here's a link to [WordPress](http://wordpress.org/ "Your favorite software") and one to [Markdown's Syntax Documentation][markdown syntax].
Titles are optional, naturally.

[markdown syntax]: http://daringfireball.net/projects/markdown/syntax
            "Markdown is what the parser uses to process much of the readme file"

Markdown uses email style notation for blockquotes and I've been told:
> Asterisks for *emphasis*. Double it up  for **strong**.

`<?php code(); // goes in backticks ?>`

== Edit other core plugins ==
* wp-content\plugins\wc-frontend-manager\controllers\orders\wcfm-controller-wcfmmarketplace-orders.php @317
    Find
    $wcfm_orders_json_arr[$index][] = '<a href="#" class="show_order_items">' . sprintf( _n( '%d item', '%d items', $order->order_item_count, 'wc-frontend-manager' ), $order->order_item_count ) . '</a>' . $order_item_details;
    Replace
    $order_item = '';
    foreach( $the_order->get_items() as $item_id => $item ) {

        $product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );
        $is_visible        = $product && $product->is_visible();
        $product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );

        $order_item .= "<a href=\"{$product_permalink}\">{$product->get_image( array( 50, 50 ) )}</a>";

        $order_item .= apply_filters( 'woocommerce_order_item_name', $product_permalink ? sprintf( '<p><a href="%s">%s</a>', $product_permalink, $item['name'] ) : $item['name'], $item, $is_visible );

        break;
    }
    $wcfm_orders_json_arr[$index][] = "<div>{$order_item}</div>";

* wp-content\plugins\wc-frontend-manager\views\products-manager\wcfm-view-product-manage-tabs.php @100
    Find
    <div class="page_collapsible products_manage_shipping <?php echo $wcfm_pm_block_class_shipping . ' ' . $wcfm_wpml_edit_disable_element; ?> <?php echo apply_filters( 'wcfm_pm_block_custom_class_shipping', '' ); ?>" id="wcfm_products_manage_form_shipping_head"><label class="wcfmfa fa-truck"></label><?php _e('Shipping', 'wc-frontend-manager'); ?><span></span></div>
    Replace
	<div class="page_collapsible products_manage_shipping <?php echo $wcfm_pm_block_class_shipping . ' ' . $wcfm_wpml_edit_disable_element; ?> <?php echo apply_filters( 'wcfm_pm_block_custom_class_shipping', '' ); ?>" id="wcfm_products_manage_form_shipping_head"><label class="wcfmfa fa-truck"></label><?php _e('Package Size', 'wc-frontend-manager'); ?><span></span></div>

* wp-content\plugins\wc-frontend-manager-affiliate\core\class-wcfmaf-frontend.php
    Find
    wcfm_aff_log( "WCFMAF Vendor Order Commission Generate:: Order => " . $order_id . " Vendor => " . $vendor_id . " Affiliate => "  . $wcfm_affiliate . " Rule => " . json_encode( $commission ) );
    Replace
    wcfm_aff_log( "WCFMAF Vendor Order Commission Generate:: Order => " . $order_id . " Vendor => " . $vendor_id . " Affiliate => "  . $vendor_affiliate . " Rule => " . json_encode( $commission ) );

    Find
    $commission_tax = apply_filters( 'wcfmmp_commission_deducted_tax', $commission_tax, $vendor_id, $product_id, $order_id, $total_commission, $order_commission_rule );
    Replace
    $commission_tax = apply_filters( 'wcfmmp_commission_deducted_tax', $commission_tax, $vendor_id, $product_id, $variation_id, $order_id, $total_commission, $order_commission_rule );