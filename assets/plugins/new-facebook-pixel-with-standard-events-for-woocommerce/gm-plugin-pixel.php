<?php
/*
Plugin Name: New Facebook Pixel with Standard Events for WooCommerce
Description: We also struggled setting up the new Facebook Pixels on our WooCommerce sites. We wanted to create a dynamic and scalable solution so we built our WordPress plugin. After a few weeks, we realized that there is a demand on a free plugin which helps you tracking your events on autopilot. So we decided to make available for everyone, for free. There are no hidden fees, no monthly fees, this plugin is forever free, all features included. The old Facebook Pixel will be dead in 2016, so a plugin like this will be inevitable for WooCommerce users. 
Plugin URI: https://ghostmonitor.com
Author: GhostMonitor
Author URI: https://ghostmonitor.com
Version: 1.4.0
License: GPLv3
*/

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

define( 'GHOSTMONITOR_PIXEL_PATH', plugin_dir_path( __FILE__ ) );
define( 'GHOSTMONITOR_PIXEL_URL', plugin_dir_url( __FILE__ ) );

require GHOSTMONITOR_PIXEL_PATH . 'includes/class-gm-plugin-pixel.php';

function run_gm_plugin_pixel() {
	new Ghostmonitor_Pixel();
}

function gm_pixel_activation() {
	$options = array( 
		'date' => strtotime('+3 days'),
		'dismissed' => 0
	);
	update_option( 'gm-plugin-notice', $options);
}
register_activation_hook( __FILE__, 'gm_pixel_activation' );

run_gm_plugin_pixel();
