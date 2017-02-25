<?php
/*
Plugin Name: Autoship with credit card
Plugin URI: http://fudugo.com
Description: Autoship with credit card
Author: Fudugo
Author URI: http://fudugo.com
*/

// Hook for adding admin menus
add_action('admin_menu', 'mt_add_credit');

// action function for above hook
function mt_add_credit() {

    // Add a new top-level menu (ill-advised):
    add_menu_page(__('Autoship with free shipping','autoship-credit'), __('Autoship with free shipping','autoship-credit'), 'manage_options', 'autoship-credit-details', 'autoship_details' );
}

function autoship_details()
{
global $wpdb;
include('autoship-details.php');	
}
?>