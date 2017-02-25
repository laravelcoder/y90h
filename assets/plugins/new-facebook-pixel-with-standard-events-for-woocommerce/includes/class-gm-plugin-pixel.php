<?php

class Ghostmonitor_Pixel {
	
	private $add_to_cart;
	private $product_id;
	public function __construct() {
		$this->add_to_cart = false;
		add_action( 'admin_menu', array( $this, 'init' ) );
		add_action( 'woocommerce_add_to_cart', array($this, 'added_to_cart'),10,2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );
		add_action( 'wp_ajax_gm_plugin_pixel_save', array( $this, 'gm_plugin_pixel_save' ) );
		add_action( 'wp_ajax_gm_plugin_pixel_delete', array( $this, 'gm_plugin_pixel_delete' ) );
		add_action( 'wp_footer', array($this, 'load_public_script') );
		add_action( 'woocommerce_thankyou', array($this, 'load_public_script') );
		add_action( 'wp_ajax_gm_plugin_notice_dismiss', array( $this, 'gm_dismiss_notice') );
	}

	public function init() {
		include_once(GHOSTMONITOR_PIXEL_PATH . 'includes/class-gm-plugin-pixel-admin.php');
		new Ghostmonitor_Pixel_Admin();
	}
	public function added_to_cart($cart_item_key,$product_id) {
		$this->add_to_cart = true;
		$this->product_id = $product_id;
	}
	public function load_public_script( $order_id = false ) {
		if( ! get_option( 'gm_pixel_enabled' ) ) {
			return;
		}
		$page_settings = get_option( 'gm_pixel_page_settings' );
		$woo_settings  = get_option( 'gm_pixel_woocommerce_settings' );
		$pixel_id      = get_option( 'gm_pixel_id' );
		
		if ( !$page_settings && !$pixel_id && !$woo_settings) {
			return;
		}
		$page_settings = maybe_unserialize( $page_settings );
		$woo_settings  = maybe_unserialize( $woo_settings );

		include_once( GHOSTMONITOR_PIXEL_PATH . 'includes/class-gm-plugin-pixel-public.php' );
		$gm_pixel_public = new Ghostmonitor_Pixel_Public();

		if ( function_exists('is_woocommerce') && function_exists('is_checkout') && function_exists('is_cart') && !empty($woo_settings) ) {
			if( (bool)$woo_settings['addtocart'] && is_woocommerce() ) {
				if( !$this->product_id ) {
					global $post;
					$this->product_id = $post->ID;
				}
				if( $this->add_to_cart && is_product() ) {
					echo $gm_pixel_public->load_script( $pixel_id, 'AddToCart', array( 
						'content_ids' => $this->product_id,
						'content_type' => 'product'
					));
				}
				elseif ( is_product() ) {
					echo $gm_pixel_public->load_script( $pixel_id, 'ViewContent', array( 
						'content_ids' => $this->product_id,
						'content_type' => 'product'
					));
				} else {
					echo $gm_pixel_public->load_script( $pixel_id, 'PageView' );
				}
				if( get_option( 'woocommerce_enable_ajax_add_to_cart' ) ) {
					$gm_pixel_public->load_script_for_ajax();	
				}
					
				return;
			}
			elseif( $order_id != false && (bool)$woo_settings['thankyou'] ) {
				$order = new WC_Order_Factory();
				$order = $order->get_order($order_id);
				$value = $order->calculate_totals();
				$currency = $order->get_order_currency();
				echo $gm_pixel_public->load_script( $pixel_id, 'Purchase', array('value' => $value, 'currency' => $currency) );
				return;
			}
			elseif(is_checkout() && (bool)$woo_settings['checkout'] && strpos( $_SERVER['REQUEST_URI'], 'order-received' ) === false ) {
				echo $gm_pixel_public->load_script( $pixel_id, 'InitiateCheckout' );
				return;
			}
			elseif( ( is_woocommerce() || is_checkout() || is_cart() ) && (bool)$woo_settings['anypage']) {
				echo $gm_pixel_public->load_script( $pixel_id, 'PageView');
				return;
			}
		}

		usort($page_settings, function ($a, $b) {
			return mb_strlen($b['url']) - mb_strlen($a['url']);
		});

		$request_uri = ( ( ( !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ) || $_SERVER['SERVER_PORT'] == 443 ) ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$matched_event = false;
		foreach ($page_settings as $elem) {
			if (!empty($elem['url']) && strpos($request_uri, $elem['url']) !== false) {
				$matched_event = $elem['event'];
				break;
			}
		}

		if( $matched_event === false ) {
			return;
		}

		echo $gm_pixel_public->load_script( $pixel_id, $matched_event );
	}

	public function load_admin_scripts($hook) {
		if ( 'toplevel_page_woocommerce-facebook-pixel' != $hook ) {
			return;
		}

		wp_register_script( 'gm_plugin_pixel_save',   GHOSTMONITOR_PIXEL_URL . 'admin/js/gm-plugin-pixel-admin-save.js',   array('jquery'), false, true );
		wp_register_script( 'gm_plugin_pixel_delete', GHOSTMONITOR_PIXEL_URL . 'admin/js/gm-plugin-pixel-admin-delete.js', array('jquery'), false, true );
		wp_register_script( 'gm_plugin_pixel_new',    GHOSTMONITOR_PIXEL_URL . 'admin/js/gm-plugin-pixel-admin-new.js',    array('jquery'), false, true );

		wp_localize_script(
			'gm_plugin_pixel_save', 
			'gmPluginPixelSaveNonce', 
			array(
				'nonce' => wp_create_nonce( 'gm_plugin_pixel_save_nonce' ),
			)
		);

		wp_enqueue_script( 'gm_plugin_pixel_save' );
		wp_enqueue_script( 'gm_plugin_pixel_delete' );
		wp_enqueue_script( 'gm_plugin_pixel_new' );
	}

	public function gm_plugin_pixel_save() {
		check_ajax_referer( 'gm_plugin_pixel_save_nonce', 'nonce', true );

		foreach( $_POST as $key => $val) {
			if( $key === 'gm_pixel_id' && ( ! empty( $val ) ) ) {
				update_option($key, trim($val), true);
			}
			if( $key === 'gm_pixel_enabled' && ( (int)$key === 0 || (int)$key === 1 ) ) {
				update_option($key, trim($val), true);
			}
			if( $key === 'gm_pixel_page_settings' && ( ! empty( $val ) ) ) {
				$page_settings = json_decode(stripslashes(html_entity_decode(trim($val))), true);
				update_option('gm_pixel_page_settings', maybe_serialize($page_settings), true);
			}
			if( $key === 'gm_pixel_woocommerce_settings'  && ( ! empty( $val ) ) ) {
				$woocommerce_settings = json_decode(stripslashes(html_entity_decode(trim($val))), true);
				update_option('gm_pixel_woocommerce_settings', maybe_serialize($woocommerce_settings), true);
			}
		}

		echo 'Settings Saved!';

		wp_die();
	}

	public function gm_plugin_pixel_delete() {
		check_ajax_referer( 'gm_plugin_pixel_save_nonce', 'nonce', true );

		$page_settings = maybe_unserialize( get_option( 'gm_pixel_page_settings' ) );

		if( !isset($_POST['gm_pixel_page_url']) || !isset($_POST['gm_pixel_page_event']) ) {
			wp_die('Error: no `url` or `event` parameter!');
		}

		foreach( $page_settings as $key => $ps ) {
			if ( $ps['url'] ==  $_POST['gm_pixel_page_url'] && $ps['event'] == $_POST['gm_pixel_page_event'] ) {
				unset($page_settings[$key]);
				break;
			}
		}

		$page_settings = array_values($page_settings);

		update_option('gm_pixel_page_settings', maybe_serialize($page_settings), true);

		echo 'Page deleted!';

		wp_die();
	}
	public function gm_dismiss_notice() {
		$options = get_option( 'gm-plugin-notice' );
		$options['dismissed'] = 1;
		update_option( 'gm-plugin-notice', $options);
		wp_die();
	}
}
