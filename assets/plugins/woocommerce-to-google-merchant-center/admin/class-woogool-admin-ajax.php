<?php
/**
 * Ajax request handelar
 */
class WooGool_Admin_ajax {
    function __construct() {
        add_action( 'wp_ajax_new_merchant', array( $this, 'new_merchant' ) );
        add_action( 'wp_ajax_delete_product', array( $this, 'delete_product' ) );
        add_action( 'wp_ajax_woogool_merchant_form', array( $this, 'google_merchant' ) );
        add_action( 'wp_ajax_change_product', array( $this, 'change_product' ) );
        add_action( 'wp_ajax_woogool-new-feed', array( $this, 'new_feed' ) );
        add_action( 'wp_ajax_woogool-new-feed-continue', array( $this, 'new_feed_continue' ) );
    }

    function new_feed_continue() {
        check_ajax_referer( 'woogool_nonce' );
        $postdata = $_POST['form_data']['postdata'];

        $res                 = WooGool_Admin_Feed::instance()->new_feed( $postdata );
        $postdata['id']      = isset( $res['id'] ) ? intval( $res['id'] ) : 0;
        $postdata['offset']  = $res['offset']; 
        $postdata['continue']= $continue = isset( $res['continue'] ) ? $res['continue'] : false;
        $redirect_url        = woogool_subtab_menu_url( 'woogool_multiple', 'feed-lists' );

        wp_send_json_success( array( 'postdata' => $postdata, 'continue' => $continue, 'redirect' => $redirect_url ) );
    }

    function new_feed() {
        check_ajax_referer( 'woogool_nonce' );
        parse_str( $_POST['form_data'], $postdata );
        $res                 = WooGool_Admin_Feed::instance()->new_feed( $postdata );
        $postdata['id']      = isset( $res['id'] ) ? intval( $res['id'] ) : 0;
        $postdata['offset']  = $res['offset']; 
        $postdata['continue']= $continue = isset( $res['continue'] ) ? $res['continue'] : false;
        $redirect_url        = woogool_subtab_menu_url( 'woogool_multiple', 'feed-lists' );

        wp_send_json_success( array( 'postdata' => $postdata, 'continue' => $continue, 'redirect' => $redirect_url ) );
    }

    /**
     * From dropdown menu change product and get form value from the product metadata
     * @return array
     */
    function change_product() {
        $product_id = $_POST['product_id'];
        $url = admin_url( 'edit.php?post_type=product&page=product_woogool&woogool_tab=woogool_single&woogool_sub_tab=new_product&product_id=' . $product_id );
        wp_send_json_success( array( 'url' => $url ) );
    }

    /**
     * Delete product
     * @return json
     */
    function delete_product() {
    	check_ajax_referer( 'woogool_nonce' );
    	$merchant_id = $_POST['merchant_id'];
    	$merchant_product_id = $_POST['merchant_product_id'];
    	$product_id = $_POST['post_id'];
    	$client = woogool_get_client();

        if ( !$client ) {
            wp_send_json_error( array( 'url' => admin_url( 'edit.php?post_type=product&page=product_woogool' ) ) );
        }

        $shoppinContent = new Google_Service_ShoppingContent($client);
        try {
            $product_status = $shoppinContent->products->get( $merchant_id, $merchant_product_id );

            $shoppinContent->products->delete( $merchant_id, $merchant_product_id );
            delete_post_meta( $product_id, 'merchant_status' );
            delete_post_meta( $product_id, 'merchant_product_id' );
            delete_post_meta( $product_id, 'merchant_id' );
            wp_send_json_success(array( 'success_msg' => __( 'Product delete successfully from google merchant center', 'woogool' ) ));
        }  catch( Google_Service_Exception $e ) {
            if ( strpos( $e->getMessage(), 'item not found' ) === false ) {

            } else {
                delete_post_meta( $product_id, 'merchant_status' );
                delete_post_meta( $product_id, 'merchant_product_id' );
                delete_post_meta( $product_id, 'merchant_id' );
                wp_send_json_success(array( 'success_msg' => __( 'Product delete successfully from google merchant center', 'woogool' ) ));
            }
        }

    }

    function new_merchant() {
        check_ajax_referer( 'woogool_nonce' );
        ob_start();
        woogool_Admin_Merchant::merchatn_form();
        wp_send_json_success( array( 'append_data' => ob_get_clean() ) );
    }

    /**
     * Insert product to merchant center
     * @return json
     */
    function google_merchant() {

        if ( !isset( $_POST['product_id'] ) ) {
            wp_send_json_error( array( 'error_code' => 400, 'error_msg' => __( 'Product id required', 'woogool' ) ) );
        }
        update_post_meta( $_POST['product_id'], 'woogool_product', $_POST );
        update_user_meta( get_current_user_id(), 'woogool_product_id', $_POST['product_id'] );

        $client = woogool_get_client();


        $postdatas = $_POST;
        $product_id = $_POST['product_id'];
        unset($postdatas['woogool_submit']);
        unset($postdatas['action']);
        unset($postdatas['product_id']);

        if ( ! $client ) {
            wp_send_json_error( array( 'authentication_fail' => true ) );
        }

        $shoppinContent = new Google_Service_ShoppingContent($client);

        try {

			$submited_products = get_option( 'woogool_submited_products', 0 );

            if ( $submited_products >= 5 ) {
                $url = 'http://mishubd.com/product/woogoo/';
                $notice = sprintf( 'You have to purchase this plugin (WooGool) to submit more than 5 products. <a href="%s" target="_blank">Purchase Link</a>', $url );
                wp_send_json_error( array( 'error_code' => 'unknown', 'error_msg' => $notice ) );
            }

			$merchant_id       = get_user_meta( get_current_user_id(), 'merchant_account_id', true );
			$product           = $shoppinContent->products->insert( $merchant_id, new Google_Service_ShoppingContent_Product( $postdatas ) );
            
            if ( $product ) {
                update_post_meta( $product_id, 'merchant_status', 'yes' );
                update_post_meta( $product_id, 'merchant_product_id', $product->id );
                update_post_meta( $product_id, 'merchant_id', $merchant_id );

                $new_submited_products = $submited_products + 1;

                update_option( 'woogool_submited_products', $new_submited_products );

            }

            wp_send_json_success(array('success_msg' => __( 'Update successfully! Please check your merchant account', 'woogool' ) ));

        } catch( Google_ServiceException $e ) {
            wp_send_json_error( array( 'error_code' => $e->getCode(), 'error_msg' => $e->getMessage() ) );
        } catch (Google_Exception $e) {
            wp_send_json_error( array( 'error_code' => $e->getCode(), 'error_msg' => $e->getMessage() ) );
        }

    }
}