<style>
	.box-section{
		margin: 5px 0px;
		z-index:1;
		border: 1px solid #ccc;
		background-color: #fff;
	}

	.box-section h3
	{
		text-align:center;
		margin:2px;
	}

	.sub-box
	{
		float: left;
		width: 31%;
		text-align: center;
		padding: 0px 5px;
	}
	
	.box-1 img {
		margin-top: 40px;
		width: 70%;
	}

	.box-2, .box-3 {
		text-align: -webkit-auto;
		margin-bottom: 10px;
	}

	.footer-box {
		clear: both;
	}
	
	#close_button {
		float:right;
		display:inline-block;
		padding:5px 5px;
		background:#ccc;
		box-shadow: none;
		cursor: pointer;
		border: 1px solid #ccc;
		z-index: 1;
	}
</style>

<script type="text/javascript">
	
	jQuery(document).ready(function(c) {
		jQuery('#close_button').on('click', function(c){
			jQuery('.box-section').fadeOut('slow', function(c){
				jQuery('.box-section').remove();
			});
		}); 
	});

</script>
<div class="box-section main-box">

	<div id='close_button'>[X]</div>

	<div class="sub-box box-1">
		
		<img src="https://www.pluginhive.com/wp-content/uploads/2019/09/pluginhive_logo.png">
		
		<h3>WooCommerce Multi Vendor Shipping Addon</h3>
		
	</div>

	<div class="sub-box box-2">
		
		<ul>
			<li> <b>Works with the following multi-vendor plugins:</b></li>
			<li>♦ Dokan Multivendor</li>
			<li>♦ WCFM Marketplace</li>
			<li>♦ WooCommerce Product Vendors</li>
			<li>♦ WooCommerce Vendors Pro</li>
			<li>♦ WC Marketplace</li>
		</ul>

	</div>

	<div class="sub-box box-3">
		
		<ul>
			<li> <b>Requires any one of the following shipping plugins:</b></li>
			<li>♦ <a rel="nofollow" href="https://www.pluginhive.com/product/woocommerce-fedex-shipping-plugin-with-print-label/" target="_blank">WooCommerce FedEx Shipping Plugin with Print Label</a></li>
			<li>♦ <a rel="nofollow" href="https://www.pluginhive.com/product/woocommerce-ups-shipping-plugin-with-print-label/" target="_blank">WooCommerce UPS Shipping Plugin with Print Label</a></li>
			<li>♦ <a rel="nofollow" href="https://www.pluginhive.com/product/multiple-carrier-shipping-plugin-woocommerce/" target="_blank">Multi-Carrier Shipping Plugin for WooCommerce</a></li>
		</ul>

		<div class="button-links">
			<a href="https://www.pluginhive.com/knowledge-base/category/woocommerce-multi-vendor-shipping-addon/" target="_blank" class="button button-primary">Documentation</a>
			<a href="https://www.pluginhive.com/support/" target="_blank" class="button button-primary">Contact Us</a>
		</div>

	</div>

	<div class="footer-box"></div>
</div>