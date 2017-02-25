<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.multidots.com/
 * @since      1.0.0
 *
 * @package    Woo_Ecommerce_Tracking_For_Google_And_Facebook
 * @subpackage Woo_Ecommerce_Tracking_For_Google_And_Facebook/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Ecommerce_Tracking_For_Google_And_Facebook
 * @subpackage Woo_Ecommerce_Tracking_For_Google_And_Facebook/public
 * @author     Multidots <wordpress@multidots.com>
 */
class Woo_Ecommerce_Tracking_For_Google_And_Facebook_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Ecommerce_Tracking_For_Google_And_Facebook_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Ecommerce_Tracking_For_Google_And_Facebook_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-ecommerce-tracking-for-google-and-facebook-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Ecommerce_Tracking_For_Google_And_Facebook_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Ecommerce_Tracking_For_Google_And_Facebook_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-ecommerce-tracking-for-google-and-facebook-public.js', array( 'jquery' ), $this->version, false );

	}
	
	/**
	 * Start session if not
	 *
	 * @since    1.0.0
	 */
	public function ecommerce_tracking_session_start(){
		global $woocommerce;
		if( !session_id() ) {
			session_start();
		}
	}
	
	/**
	 * Load Ecommerce Tracking code.
	 *
	 * @since    1.0.0
	 */
	public function woocommerce_load_ecommerce_tracking_code( $order ) {
		$website_url = site_url();
		$server_name = $_SERVER['SERVER_NAME'];
		$server_referer = $_SERVER['HTTP_REFERER'];
		$website_url = preg_replace('#^https?://#', '', $website_url);
		
		if ( $_SERVER['SERVER_NAME'] == $website_url && !empty( $_SERVER['HTTP_REFERER'] ) ) {
			?>
			<script type="text/javascript">
			ga('require', 'ecommerce', 'ecommerce.js'); // Load The Ecommerce Tracking Plugin
			
			// Transaction Details
			ga('ecommerce:addTransaction', {
			'id': '<?php echo $order->id;?>',
			'affiliation': '<?php echo get_option( "blogname" );?>',
			'revenue': '<?php echo $order->get_total();?>',
			'shipping': '<?php echo $order->get_total_shipping();?>',
			'tax': '<?php echo $order->get_total_tax();?>',
			'currency': '<?php echo get_woocommerce_currency();?>'
			});
			<?php
			//Item Details
			if ( sizeof( $order->get_items() ) > 0 ) {
				foreach( $order->get_items() as $item ) {
				 ?>
				ga('ecommerce:addItem', {
				'id': '<?php echo $order->id;?>',
				'name': '<?php echo $item['name'];?>',
				'sku': '<?php echo get_post_meta($item["product_id"], '_sku', true);?>',
				'category': '',
				'price': '<?php echo $item['line_subtotal'];?>',
				'quantity': '<?php echo $item['qty'];?>',
				'currency': '<?php echo get_woocommerce_currency();?>'
				});
				<?php
				}
			} ?>
			ga('ecommerce:send');
			</script>
			<?php
		}
	}
	
	/**
	 * Load Facebook Conversion Tracking code
	 *
	 * @since    1.0.0
	 */
	public function woo_ecommerce_fb_conversion_tracking( $orderid ) {

	    $order = new WC_Order( $orderid );
	    $currency = $order->get_order_currency();
	    $total = $order->get_total();

	    //Set Facebook Label
	    $ecommerce_tracking_settings_facebook_track_id = get_option('ecommerce_tracking_settings_facebook_track_id');
	   	
	    if ( isset( $_GET['key'] ) && !empty( $_GET['key'] ) && !empty( $ecommerce_tracking_settings_facebook_track_id ) ) {
		?>
	    	<!-- Facebook Pixel Code -->
			<script>
			!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
			n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
			n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
			t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
			document,'script','https://connect.facebook.net/en_US/fbevents.js');
			
			fbq('init', '<?php echo $ecommerce_tracking_settings_facebook_track_id; ?>');
			fbq('track', 'Purchase', {value: '<?php echo $order->get_total(); ?>', currency: '<?php echo $order->get_order_currency(); ?>'});</script>
			<noscript><img height="1" width="1" style="display:none"
			src="https://www.facebook.com/tr?id=<?php echo $ecommerce_tracking_settings_facebook_track_id; ?>&ev=Purchase&noscript=1"
			/></noscript>
			<!-- End Facebook Pixel Code -->
	    
	    <?php
	    
	    }
	}
	
	
	/**
	 * Load Google Conversion Tracking code
	 *
	 * @since    1.0.0
	 */
	public function woo_ecommerce_google_conversion_tracking( $orderid ) {

	    $order = new WC_Order( $orderid );
	
	    $currency = $order->get_order_currency();
	    $total = $order->get_total();
	    
	    //Set Google Conversion Label
	    $ecommerce_tracking_settings_google_conversion_label = get_option('ecommerce_tracking_settings_google_conversion_label');
	    if ( isset( $ecommerce_tracking_settings_google_conversion_label ) && !empty( $ecommerce_tracking_settings_google_conversion_label ) ) {
	    	$ecommerce_tracking_settings_google_conversion_label = $ecommerce_tracking_settings_google_conversion_label;
	    } else {
	    	$ecommerce_tracking_settings_google_conversion_label = 'xxxxxx';
	    }
	    
	   	//Set Google Conversion Code
	   	$ecommerce_tracking_settings_google_conversion_id = get_option('ecommerce_tracking_settings_google_conversion_id');
	    if ( isset( $ecommerce_tracking_settings_google_conversion_id ) && !empty( $ecommerce_tracking_settings_google_conversion_id ) ) {
	    	$ecommerce_tracking_settings_google_conversion_id = $ecommerce_tracking_settings_google_conversion_id;
	    } else {
	    	$ecommerce_tracking_settings_google_conversion_id = 'xxxxxx';
	    }
	    
	    
	    if ( isset( $_GET['key'] ) && !empty( $_GET['key'] ) ) { ?>
			<script type="text/javascript">
			/* <![CDATA[ */
			var google_conversion_id = '<?php echo $ecommerce_tracking_settings_google_conversion_id ?>';
			var google_conversion_language = "en";
			var google_conversion_format = "3";
			var google_conversion_color = "ffffff";
			var google_conversion_label = "<?php echo $ecommerce_tracking_settings_google_conversion_label ?>"; 
			var google_conversion_value = <?php echo $total?>; 
			var google_conversion_currency = "<?php echo $currency?>"; 
			var google_remarketing_only = false;
			/* ]]> */
			</script>
			<script type="text/javascript"  
			src="//www.googleadservices.com/pagead/conversion.js">
			</script>
			<noscript>
			<div style="display:inline;">
			<?php echo '<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/'.$ecommerce_tracking_settings_google_conversion_id.'/?value=' . $total . '&amp;currency_code=' . $currency . '&amp;label=' . $ecommerce_tracking_settings_google_conversion_label . '&amp;guid=ON&amp;script=0"/>' ?>
			</div>
			</noscript>
		
	    <?php
	    }
	}
	
	/**
	 * BN code added 
	 */
	function paypal_bn_code_filter_free_advance_tracking($paypal_args) {
		$paypal_args['bn'] = 'Multidots_SP';
		return $paypal_args;
	}
}