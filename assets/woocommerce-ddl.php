<?php
/*
Plugin Name: Phillips Data Layer
Plugin URI: http://www.affordableprogrammer.com
Description: Adds a data layer that conforms to the W3C spec
Author: Phillip Madsen
Author URI: http://www.affordableprogrammer.com
Version: 0.1
License: GPLv3
*/


/* Check if WooCommerce plugin is active before continuing */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	global $datalayer;
	$datalayer = array();

	function datalayer_single_product_details() {
		global $post, $datalayer;

		$product = get_product( $post->ID );

		$datalayer['page']['pageInfo']['pageName'] = esc_js($post->post_title);
		$datalayer['page']['category']['pageType'] = 'product';

		$datalayer['product'][0]['productInfo']['productID'] = $post->ID;
		$datalayer['product'][0]['productInfo']['productName'] = esc_js($post->post_title);
		$datalayer['product'][0]['productInfo']['description'] = esc_js($post->post_content);
		$datalayer['product'][0]['productInfo']['productURL'] = esc_js(get_permalink());

		$product_image_id = $product->get_image_id();
		if ($product_image_id) {
			$datalayer['product'][0]['productInfo']['productThumbnail'] = esc_js(datalayer_get_image_src($product_image_id, 'shop_thumbnail'));
			$datalayer['product'][0]['productInfo']['productImage'] = esc_js(datalayer_get_image_src($product_image_id, 'full'));
		}

		$sku = $product->get_sku();
		if (strlen($sku) > 0) {
			$datalayer['product'][0]['productInfo']['sku'] = $sku;
		}

		$product_categories = get_the_terms($post->ID, 'product_cat');
		$first_cat = array_shift($product_categories);

		$datalayer['product'][0]['category']['primaryCategory'] = esc_js($first_cat->name);
		$datalayer['product'][0]['price']['price'] = $product->get_price();

		if ($product->is_on_sale()) {
			$datalayer['product'][0]['price']['regular_unit_price'] = $product->get_regular_price();
		}

		if ($product->get_stock_quantity()) {
			$datalayer['product'][0]['attributes']['stock'] = $product->get_stock_quantity();
		}
	}
	add_action('woocommerce_after_single_product', 'datalayer_single_product_details');

	function datalayer_cart_details() {
		global $woocommerce, $datalayer;

		$datalayer['page']['category']['pageType'] = 'basket';

		$datalayer['cart']['price']['basePrice'] = preg_replace("/&#?[a-z0-9]+;/i","", strip_tags($woocommerce->cart->get_cart_subtotal()));
		$datalayer['cart']['price']['cartTotal'] = preg_replace("/&#?[a-z0-9]+;/i","",strip_tags($woocommerce->cart->get_cart_total()));

		$shipping = $woocommerce->cart->get_cart_shipping_total();
		if (strpos($shipping,'Free') !== FALSE) {
			$shipping = '0.00';
		}
		$datalayer['cart']['price']['shipping'] = $shipping;

		$datalayer['cart']['item'] = array();

		$cart_contents = $woocommerce->cart->get_cart();
		foreach ($cart_contents as $item) {
			$product = get_product($item['product_id']);
			$item_details = array();

			$item_details['productInfo']['productID'] = $item['product_id'];
			$item_details['productInfo']['productName'] = esc_js($item['data']->post->post_title);
			$item_details['productInfo']['description'] = esc_js($item['data']->post->post_content);
			$item_details['quantity'] = $item['quantity'];
			$item_details['price']['basePrice'] = $item['line_subtotal']/$item['quantity'];
			$item_details['price']['priceWithTax'] = $item['line_total']/$item['quantity'];

			$product_image_id = $product->get_image_id();
			if ($product_image_id) {
				$item_details['productInfo']['productThumbnail'] = esc_js(datalayer_get_image_src($product_image_id, 'shop_thumbnail'));
				$item_details['productInfo']['productImage'] = esc_js(datalayer_get_image_src($product_image_id, 'full'));
			}

			$sku = $product->get_sku();
			if (strlen($sku) > 0) {
				$item_details['productInfo']['sku'] = $sku;
			}

			array_push($datalayer['cart']['item'], $item_details);
		}
	}
	add_action('woocommerce_after_cart', 'datalayer_cart_details');

	function datalayer_purchase_confirmation() {
		global $datalayer;
		$datalayer['page']['category']['pageType'] = 'confirmation';
	}
	add_action('woocommerce_thankyou', 'datalayer_purchase_confirmation');

	function datalayer_checkout() {
		global $datalayer, $order;
		$datalayer['page']['category']['pageType'] = 'checkout';
		$datalayer['transaction']['transactionID'] = $order->get_order_number();
	}
	add_action('woocommerce_after_checkout_form', 'datalayer_checkout');

	function datalayer_user_info() {
		if (is_user_logged_in()) {
			//if the user is logged in
			global $current_user, $post, $datalayer;

			$datalayer['user'][0]['profile'][0]['profileInfo']['email'] = $current_user->user_email;
		}
		datalayer_print_datalayer_variable();
	}
	add_action('wp_footer', 'datalayer_user_info');

	function datalayer_get_image_src($img_id, $img_size='full') {
		$image_details = wp_get_attachment_image_src($img_id, $img_size);
		return $image_details[0];
	}

	function datalayer_print_datalayer_variable() {
		global $datalayer;

		echo '<script>',"\n";
		echo 'window.digitalData = window.digitalData || {};',"\n";
		echo 'window.digitalData = ',json_encode($datalayer),"\n";
		echo 'condole.log('. json_encode($datalayer). ')';
		echo 'dataLayer = [];';
		echo 'dataLayer.push('. json_encode($datalayer). ');';
		echo '</script>',"\n";
	}
}
