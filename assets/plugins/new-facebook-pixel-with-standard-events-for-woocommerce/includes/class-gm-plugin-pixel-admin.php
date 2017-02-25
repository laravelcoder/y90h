<?php

class Ghostmonitor_Pixel_Admin {
	
	protected $is_notice_displayed;
	
	public function __construct() {
		$this->is_notice_displayed = false;
		add_menu_page( 'WooCommerce Facebook Pixel', 'WooCommerce Facebook Pixel', 'manage_options', 'woocommerce-facebook-pixel', array( $this, 'add_admin_page' ) );
		add_action('admin_head-toplevel_page_woocommerce-facebook-pixel',array($this, 'gm_plugin_page'));
		$options = get_option( 'gm-plugin-notice' );
		if( ((strtotime('now') > $options['date']) &&  ($options['dismissed'] == 0)) ) {
			add_action( 'admin_notices', array( $this, 'gm_plugin_notice') );
			add_action( 'admin_enqueue_scripts', array( $this, 'gm_plugin_dismiss_script')  );
			$this->is_notice_displayed = true;
		}
	}

	public function add_admin_page() {
		include_once( GHOSTMONITOR_PIXEL_PATH . 'admin/gm-plugin-pixel-admin-menu.php' );
	}
	public function gm_plugin_notice() {
    	?>
		<div class="notice-info notice is-dismissible ghostmonitor-notice" style="background-color:#fef7f1">
			<p><strong>Suggested plugin:</strong> <a href="https://ghostmonitor.com/?src=wordpress">GhostMonitor Automated Cart Abandonment Campaign for WooCommerce</a> - Recover your abandoned carts now with 3 automated emails</p>
		</div>
		<?php
	}
	public function gm_plugin_dismiss_script() {
		wp_enqueue_script(  'gm_plugin_notice_dismiss',    GHOSTMONITOR_PIXEL_URL . 'admin/js/gm-plugin-notice-dismiss.js',    array('jquery'), false, true  );
	}
	public function gm_plugin_page() {
		if( !$this->is_notice_displayed ) {
			add_action( 'admin_notices', array( $this, 'gm_plugin_notice') );
		}
	}
}