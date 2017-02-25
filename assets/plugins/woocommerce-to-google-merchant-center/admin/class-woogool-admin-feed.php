<?php

class WooGool_Admin_Feed {
    
    private $offset_increment = 20;
    private $number_per_page = 5;
	
    /**
     * @var The single instance of the class
     * 
     */
    protected static $_instance = null;

    /**
     * Main woogool Instance
     *
     * @static
     * @see woogool()
     * @return woogool - Main instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    function __construct() {
        add_action( 'admin_init', array( $this, 'register_post_type' ) );
        //add_action( 'admin_init', array( $this, 'new_feed' ) );
        add_action( 'admin_init', array( $this, 'feed_delete' ) );
        add_action( 'admin_init', array( $this, 'check_categori_fetch' ) );
        //add_action( 'add_meta_boxes', array( $this, 'feed_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_post_meta' ), 10, 3 );
        add_action( 'template_redirect', array( $this, 'xml_download' ) );
    }

    function register_post_type() {
        register_post_type( 'woogool_feed', array(
            'label'               => __( 'Feed', 'hrm' ),
            'public'              => false,
            'show_in_admin_bar'   => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'show_in_admin_bar'   => false,
            'show_ui'             => false,
            'show_in_menu'        => false,
            'capability_type'     => 'post',
            'hierarchical'        => false,
            'rewrite'             => array('slug' => ''),
            'query_var'           => true,
            'supports'            => array('title', 'editor'),
        ));
    }

    function xml_download() {

        if ( ! isset( $_GET['woogool_feed_download'] ) || ! isset( $_GET['nonce'] ) ) {
            return;
        }

        $feed_id = isset( $_GET['feed_id'] ) ? intval( $_GET['feed_id'] ) : 0;

        if ( ! $feed_id ) {
            return;
        }

        $post_feed = get_post( $feed_id );

        global $wpdb, $wp_query, $post;

        // Don't cache feed under WP Super-Cache
        define( 'DONOTCACHEPAGE', TRUE );

        // Cater for large stores
        $wpdb->hide_errors();
        @set_time_limit( 0 );
        while ( ob_get_level() ) {
            @ob_end_clean();
        }

        header( 'Content-Type: application/xml; charset=UTF-8' );

        if ( $_GET['woogool_feed_download'] ) {
            header( 'Content-Disposition: attachment; filename="woogool_product_List.xml"' );  
        } else {
            header( 'Content-Disposition: inline; filename="woogool_product_List.xml"' );
        }

        
        $content  = "<?xml version='1.0' encoding='UTF-8'?>\n";
        $content .= "<rss version='2.0' xmlns:atom='http://www.w3.org/2005/Atom' xmlns:g='http://base.google.com/ns/1.0'>\n";
        $content .= "   <channel>\n";
        $content .= "       <atom:link href='".htmlspecialchars( home_url() )."' rel='self' type='application/rss+xml' />\n";
        $content .=         $post_feed->post_content;
        $content .= "  </channel>\n";
        $content .= '</rss>';

        echo $content;
        exit();
    }

    function new_feed( $post ) {

        $xml_content = '';
        $xml_content = $this->get_xml_content( $post, $xml_content, 0 );
        $this->insert_feed( $xml_content, $post );

        return array( 'offset' => $offset, 'continue' => false );
    }

    function xml_get_products( $post, $offset = 0 ) {
        $products = array();
        
        if ( $post['all_products'] == 'all' ) {   
            $products = woogool_get_products( $this->number_per_page, $offset );
            $products = wp_list_pluck( $products, 'ID' );

        } else if ( $post['all_products'] == 'individual' ) {   
            $products = $post['products'];

        } else if ( $post['all_products'] == 'category' ) {
            $tax_query['tax_query'] = array(
                array(
                    'taxonomy'         => 'product_cat',
                    'field'            => 'term_id',
                    'terms'            => $post['products_cat'],
                    'include_children' => false,
                    'operator'         => 'IN',
            ));

            $products = woogool_get_products( $this->number_per_page, $offset, $tax_query );
            $products = wp_list_pluck( $products, 'ID' );
        }

        return $products;
    }

    function get_xml_db_content( $post_id ) {
        $post = get_post( $post_id );
        return isset( $post->post_content ) ? $post->post_content : '';
    }

    function insert_feed( $xml_content, $post ) {

        $arg = array(
            'post_type'    => 'woogool_feed',
            'post_title'   => $post['post_title'],
            'post_content' => $xml_content,
            'post_status'  => 'publish'
        );

        $feed_id = isset( $post['id'] ) ? intval( $post['id'] ) : false;
        $post_id = false;

        if ( $feed_id ) {
            $arg['ID'] = $feed_id;
            $post_id = wp_update_post( $arg );
        } else {
            $post_id = wp_insert_post( $arg );
        }

        if ( $post_id ) {
            $this->update_feed_meta( $post_id, $post );
        }

        return $post_id;
    }

    function get_xml_head_with_content( $xml_content ) {

        $content = "<?xml version='1.0' encoding='UTF-8'?>\n";
        $content .= "<rss version='2.0' xmlns:atom='http://www.w3.org/2005/Atom' xmlns:g='http://base.google.com/ns/1.0'>\n";
        $content .= "   <channel>\n";
        $content .= "       <atom:link href='".htmlspecialchars( home_url() )."' rel='self' type='application/rss+xml' />\n";

        $content .= $xml_content;

        $content .= "  </channel>\n";
        $content .= '</rss>';
        return $content;
    }

    function update_feed_meta( $post_id, $post ) {

        $all_products = isset( $post['all_products'] ) ? $post['all_products'] : 0;
        update_post_meta( $post_id, '_all_products', $all_products );

        $products = isset( $post['products'] ) ? $post['products'] : array();
        update_post_meta( $post_id, '_products', $products );

        $products_cat = isset( $post['products_cat'] ) ? $post['products_cat'] : array();
        update_post_meta( $post_id, '_products_cat', $products_cat );

        $var_products = isset( $post['variable_products'] ) ? $post['variable_products'] : 'no';
        update_post_meta( $post_id, '_woogool_include_variable_products', $var_products );

        $google_product_category = isset( $post['google_product_category'] ) ? $post['google_product_category'] : '';
        update_post_meta( $post_id, '_google_product_category', $google_product_category );

        $product_type = isset( $post['product_type'] ) ? $post['product_type'] : '';
        update_post_meta( $post_id, '_product_type', $product_type );

        $availability = isset( $post['availability'] ) ? $post['availability'] : '';
        update_post_meta( $post_id, '_availability', $availability );

        $availability_date = isset( $post['availability_date'] ) ? $post['availability_date'] : '';
        update_post_meta( $post_id, '_availability_date', $availability_date );

        $condition = isset( $post['condition'] ) ? $post['condition'] : '';
        update_post_meta( $post_id, '_condition', $condition );

        $brand = isset( $post['brand'] ) ? $post['brand'] : '';
        update_post_meta( $post_id, '_brand', $brand );

        $mpn = isset( $post['mpn'] ) ? $post['mpn'] : '';
        update_post_meta( $post_id, '_mpn', $mpn );

        $gtin = isset( $post['gtin'] ) ? $post['gtin'] : '';
        update_post_meta( $post_id, '_gtin', $gtin );

        $gender = isset( $post['gender'] ) ? $post['gender'] : '';
        update_post_meta( $post_id, '_gender', $gender );

        $age_group = isset( $post['age_group'] ) ? $post['age_group'] : '';
        update_post_meta( $post_id, '_age_group', $age_group );

        $color = isset( $post['color'] ) ? $post['color'] : '';
        update_post_meta( $post_id, '_color', $color );

        $size = isset( $post['size'] ) ? $post['size'] : '';
        update_post_meta( $post_id, '_size', $size );

        $size_type = isset( $post['size_type'] ) ? $post['size_type'] : '';
        update_post_meta( $post_id, '_size_type', $size_type );

        $size_system = isset( $post['size_system'] ) ? $post['size_system'] : '';
        update_post_meta( $post_id, '_size_system', $size_system );

        $expiration_date = isset( $post['expiration_date'] ) ? $post['expiration_date'] : '';
        update_post_meta( $post_id, '_expiration_date', $expiration_date );

        $sale_price = isset( $post['sale_price'] ) ? $post['sale_price'] : 'no';
        update_post_meta( $post_id, '_sale_price', $sale_price );

        $sale_price_effective_date = isset( $post['sale_price_effective_date'] ) ? $post['sale_price_effective_date'] : 'no';
        update_post_meta( $post_id, '_sale_price_effective_date', $sale_price_effective_date );

        $custom_label_0 = isset( $post['custom_label_0'] ) ? $post['custom_label_0'] : '';
        update_post_meta( $post_id, '_custom_label_0', $custom_label_0 );

        $custom_label_1 = isset( $post['custom_label_1'] ) ? $post['custom_label_1'] : '';
        update_post_meta( $post_id, '_custom_label_1', $custom_label_1 );

        $custom_label_2 = isset( $post['custom_label_2'] ) ? $post['custom_label_2'] : '';
        update_post_meta( $post_id, '_custom_label_2', $custom_label_2 );

        $custom_label_3 = isset( $post['custom_label_3'] ) ? $post['custom_label_3'] : '';
        update_post_meta( $post_id, '_custom_label_3', $custom_label_3 );

        $custom_label_4 = isset( $post['custom_label_4'] ) ? $post['custom_label_4'] : '';
        update_post_meta( $post_id, '_custom_label_4', $custom_label_4 );

        $promotion_id = isset( $post['promotion_id'] ) ? $post['promotion_id'] : '';
        update_post_meta( $post_id, '_promotion_id', $promotion_id );

        $promotion_id = isset( $post['identifier_exists'] ) ? $post['identifier_exists'] : '';
        update_post_meta( $post_id, '_identifier_exists', $promotion_id );
    }

    function get_xml_content( $post, $content, $offset = 0 ) {
        
        $products = $this->xml_get_products( $post, $offset );
 
        if ( ! count( $products ) ) {
            return false;
        }

        $product_cat = get_option( 'woogool_google_product_type' );

        foreach ( $products as $key => $product_id ) {
            if ( $this->is_product_feed_disabled( $product_id ) ) {
                continue;
            }

            $wc_product  = wc_get_product( $product_id );
            $gtins        = get_post_meta( $product_id, 'woogool_gtin', true );
            
            if ( empty( $gtins ) ) {
                $gtins        = get_post_meta( $product_id, 'feedUPC', true );
            }
            
            $gtins        = explode( '&', $gtins );
            $product_gtin = array();

            foreach ( $gtins as $key => $gtin ) {
                $exp = explode( '=', $gtin );

                $gtin_pro_id = isset( $exp[0] ) ? intval( str_replace( ' ', '', $exp[0] ) ) : 0;
                $gtin_pro    = isset( $exp[1] ) ? str_replace( ' ', '', $exp[1] ) : 0;
                $product_gtin[$gtin_pro_id] = $gtin_pro;
            }
            
            $variation = false;

            if ( $wc_product->post->post_status != 'publish' ) {
                continue;
            }

            $enable_variable_product = ( isset( $post['variable_products'] ) && $post['variable_products'] == 'yes' ) ? true : false;

            if ( $wc_product->product_type == 'variable' && $enable_variable_product ) {
                $variable       = new WC_Product_Variable( $wc_product );
                
                $get_variations = $variable->get_available_variations();
                $get_attrs      = $variable->get_variation_attributes();
                
                if ( $get_variations ) {

                    $variation = true;
                    foreach ( $get_variations as $key => $attr ) {
                         
                        if ( ! $attr['variation_is_active'] || ! $attr['variation_is_visible'] ) {
                            continue;
                        }

                        $variation_content = $this->xml_for_product_variation( $post, $wc_product,  $attr, $get_attrs, $product_cat, $product_gtin );
                        $content           .= $variation_content;
                    }
                }
            }

            if ( $variation ) {
                continue;
            }

            $size_attr          = $this->get_size_attr( $post, $product_id, $wc_product );
            $color_attr         = $this->get_color_attr( $post, $product_id, $wc_product );
            $sale_price         = $wc_product->get_sale_price();
            $additional_images  = $this->get_additional_images( $wc_product );
            $currency           = get_woocommerce_currency();
            $post_title         = $wc_product->post->post_title;
            $description        = strip_tags( html_entity_decode( stripslashes( nl2br( $wc_product->post->post_content ) ) ) );
            $link               = $wc_product->get_permalink();
            $feed_image_url     = wp_get_attachment_url( $wc_product->get_image_id() );
            $condition          = $this->get_condition( $post, $product_id );
            $availability       = $this->get_availability( $post, $product_id );
            $category           = $this->get_category( $post, $product_id, $product_cat );
            $type               = $this->get_type( $post, $product_id,  $product_cat );
            $availability_date  = $this->get_availability_date( $post, $product_id );
            $availability_value = $this->get_availability_value( $availability_date );
            $sku_as_mpn         = $this->get_sku_as_mpn( $post, $product_id, $wc_product );
            $gender             = $this->get_gender( $post, $product_id );
            $age_group          = $this->get_age_group( $post, $product_id );
            $size_type          = $this->get_size_type( $post, $product_id );
            $size_system        = $this->get_size_system( $post, $product_id );
            $custom_label_0     = $this->get_custom_label_0( $post, $product_id );
            $custom_label_1     = $this->get_custom_label_1( $post, $product_id );
            $custom_label_2     = $this->get_custom_label_2( $post, $product_id );
            $custom_label_3     = $this->get_custom_label_3( $post, $product_id );
            $custom_label_4     = $this->get_custom_label_4( $post, $product_id );
            $promotion_id       = $this->get_promotion_id( $post, $product_id );
            $brand              = $this->get_brand( $post, $product_id );
            $identifier         = $this->get_identifier( $post, $product_id, $sku_as_mpn, $brand );
            $expiration_date    = $this->get_expiration_date( $post, $product_id );
            $gtin               = $this->get_gtin( $post, $product_id, $product_gtin );
            
            $price       = $wc_product->get_price() . ' ' . $currency;
            $description = preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F\x80-\x9F]/u', '', $description );
            $description = str_replace( ']]>', ']]]]><![CDATA[>', $description );
            $effective_date = $this->get_sale_price_effective_date( $post, $product_id ); 
            
            $sale_price = $wc_product->get_sale_price();
            
            if ( isset( $post['sale_price'] ) && $post['sale_price'] == 'yes' ) {
                $salse_price = ! empty( $sale_price ) ? $sale_price . ' ' . $currency : false;
            } else {
                $salse_price = false;
            }
            
            $content .= "       <item>\n";
            $content .= "           <g:id>$wc_product->id</g:id>\n";
            $content .= ( $wc_product->product_type == 'variable' && $enable_variable_product ) ? "           <g:item_group_id>$product_id</g:item_group_id>\n" : '';
            $content .= "           <title><![CDATA[$post_title]]></title>\n";
            $content .= "           <description><![CDATA[$description]]></description>\n";
            $content .= "           <link>$link</link>\n";
            $content .= "           <g:image_link>$feed_image_url</g:image_link>\n";
            $content .= "           <g:condition>$condition</g:condition>\n";
            $content .= "           <g:availability>$availability</g:availability>\n";
            $content .= "           <g:price>$price</g:price>\n";

            $content .= $category ?  "          <g:google_product_category>$category</g:google_product_category>\n" : '';
            $content .= $type ? "           <g:product_type>$type</g:product_type>\n" : '';
            $content .= $availability_date ? "          <g:availability_date>$availability_value</g:availability_date>\n" : '';
            $content .= ! empty( $sale_price ) ? "          <g:sale_price>$sale_price $currency</g:sale_price>\n" : '';
            $content .= $sku_as_mpn ? "         <g:mpn>$sku_as_mpn</g:mpn>\n" : '';
            $content .= $gender ? "         <g:gender>$gender</g:gender>\n" : '';
            $content .= $age_group ? "          <g:age_group>$age_group</g:age_group>\n" : '';
            $content .= $brand ? "          <g:brand>$brand</g:brand>\n" : '';
            $content .= $gtin ? "          <g:gtin>$gtin</g:gtin>\n" : '';
            $content .= $expiration_date ? "          <g:expiration_date>$expiration_date</g:expiration_date>\n" : '';
            $content .= $size_type ? "          <g:size_type>$size_type</g:size_type>\n" : '';
            $content .= $size_system ? "            <g:size_system>$size_system</g:size_system>\n" : '';
            $content .= $salse_price ? "         <g:sale_price>$salse_price</g:sale_price>\n" : '';
            $content .= $effective_date ? "         <g:sale_price_effective_date>$effective_date</g:sale_price_effective_date>\n" : ''; 
            $content .= $custom_label_0 ? "         <g:custom_label_0><![CDATA[$custom_label_0]]></g:custom_label_0>\n" : '';
            $content .= $custom_label_1 ? "         <g:custom_label_1><![CDATA[$custom_label_1]]></g:custom_label_1>\n" : '';
            $content .= $custom_label_2 ? "         <g:custom_label_2><![CDATA[$custom_label_2]]></g:custom_label_2>\n" : '';
            $content .= $custom_label_3 ? "         <g:custom_label_3><![CDATA[$custom_label_3]]></g:custom_label_3>\n" : '';
            $content .= $custom_label_4 ? "         <g:custom_label_4><![CDATA[$custom_label_4]]></g:custom_label_4>\n" : '';
            $content .= $promotion_id ? "           <g:promotion_id>$promotion_id</g:promotion_id>\n" : '';
            $content .= $color_attr ? "         <g:color>$color_attr</g:color>\n" : '';
            $content .= $size_attr ? "          <g:size>$size_attr</g:size>\n" : '';
            $content .= $identifier ? "         <g:identifier_exists>FALSE</g:identifier_exists>\n" : '';
            
            $additional_img_count = 1;
            
            foreach ( $additional_images as $image_url ) {
                // Google limit the number of additional images to 10
                if ( $additional_img_count == 10 ) {
                    
                    break;
                }

                $content .= "           <g:additional_image_link><![CDATA[$image_url]]></g:additional_image_link>\n";
                $additional_img_count++;
            }

            $content .= "       </item>\n";
        }

        return $content;
    }

    function xml_for_product_variation( $post, $wc_product,  $attr, $get_attrs, $product_cat, $product_gtin ) {

        $custom_attrs = array();
        $product_id         = $wc_product->id; 
        $variation_id       = $attr['variation_id'];
        $brand              = $this->get_brand( $post, $product_id );
        
        $size_attr          = isset( $attr['attributes']['attribute_size'] ) && ! empty( $attr['attributes']['attribute_size'] ) ? $attr['attributes']['attribute_size'] : false;
        if ( ! $size_attr ) {
            $size_attr      = isset( $attr['attributes']['attribute_pa_size'] ) && ! empty( $attr['attributes']['attribute_pa_size'] ) ? $attr['attributes']['attribute_pa_size'] : false;  
        }
        
        $color_attr         = isset( $attr['attributes']['attribute_color'] ) && ! empty( $attr['attributes']['attribute_color'] ) ? $attr['attributes']['attribute_color'] : false;
        if ( ! $color_attr ) {
            $color_attr      = isset( $attr['attributes']['attribute_pa_color'] ) && ! empty( $attr['attributes']['attribute_pa_color'] ) ? $attr['attributes']['attribute_pa_color'] : false;  
        }
        
        foreach ( $attr['attributes'] as $attr_name => $attr_val ) {
            if ( $attr_name == 'attribute_color' || $attr_name == 'attribute_size' ) {
                continue;
            }

            if ( $attr_name == 'attribute_pa_color' || $attr_name == 'attribute_pa_size' ) {
                continue;
            }

            $attr_rel_name = trim( str_replace('attribute_pa_', ' ', $attr_name ) );
            $attr_rel_name = trim( str_replace('attribute_', ' ', $attr_rel_name ) );

            $custom_attrs[$attr_rel_name] = $attr_val;
        }

        $currency           = get_woocommerce_currency();
        $sale_price         = ! empty( $attr['display_price'] ) ? $attr['display_price'] . ' ' . $currency : false;
        $post_title         = $wc_product->post->post_title;
        $description        = strip_tags( html_entity_decode( stripslashes( nl2br( $attr['variation_description'] ) ) ) );
        $link               = $wc_product->get_permalink();
        $feed_image_url     = ! empty( $attr['image_src'] ) ? $attr['image_src'] : false;
        $condition          = $this->get_condition( $post, $product_id );
        $availability       = $this->get_availability( $post, $product_id );
        $category           = $this->get_category( $post, $product_id, $product_cat );
        $type               = $this->get_type( $post, $product_id,  $product_cat );
        $availability_date  = $this->get_availability_date( $post, $product_id );
        $availability_value = $this->get_availability_value( $availability_date );
        $sku_as_mpn         = isset( $post['mpn'] ) && $attr['sku'] ? $attr['sku'] : false;
        $gender             = $this->get_gender( $post, $product_id );
        $age_group          = $this->get_age_group( $post, $product_id );
        $size_type          = $this->get_size_type( $post, $product_id );
        $size_system        = $this->get_size_system( $post, $product_id );
        $custom_label_0     = $this->get_custom_label_0( $post, $product_id );
        $custom_label_1     = $this->get_custom_label_1( $post, $product_id );
        $custom_label_2     = $this->get_custom_label_2( $post, $product_id );
        $custom_label_3     = $this->get_custom_label_3( $post, $product_id );
        $custom_label_4     = $this->get_custom_label_4( $post, $product_id );
        $promotion_id       = $this->get_promotion_id( $post, $product_id );
        $identifier         = $this->get_identifier( $post, $product_id, $sku_as_mpn, $brand );
        $expiration_date    = $this->get_expiration_date( $post, $product_id );
        $gtin               = $this->get_gtin( $post, $variation_id, $product_gtin );
        
        $price          = ! empty( $attr['display_regular_price'] ) ? $attr['display_regular_price'] . ' ' . $currency : false;
        $description    = preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F\x80-\x9F]/u', '', $description );
        $description    = str_replace( ']]>', ']]]]><![CDATA[>', $description );
        $effective_date = $this->get_sale_price_effective_date( $post, $product_id ); 
        
        if ( isset( $post['sale_price'] ) && $post['sale_price'] == 'yes' ) {
            $salse_price = ! empty( $attr['display_price'] ) ? $attr['display_price'] . ' ' . $currency : false;
        } else {
            $salse_price = false;
        }

        $content  = "       <item>\n";
        $content .= "           <g:id>$variation_id</g:id>\n";
        $content .= "           <g:item_group_id>$product_id</g:item_group_id>\n";
        $content .= "           <title><![CDATA[$post_title]]></title>\n";
        $content .= "           <description><![CDATA[$description]]></description>\n";
        $content .= "           <link>$link</link>\n";
        $content .= "           <g:image_link>$feed_image_url</g:image_link>\n";
        $content .= "           <g:condition>$condition</g:condition>\n";
        $content .= "           <g:availability>$availability</g:availability>\n";
        $content .= $price ? "           <g:price>$price</g:price>\n" : '';
        $content .= $category ?  "          <g:google_product_category>$category</g:google_product_category>\n" : '';
        $content .= $type ? "           <g:product_type>$type</g:product_type>\n" : '';
        $content .= $availability_date ? "          <g:availability_date>$availability_value</g:availability_date>\n" : '';
        $content .= ! empty( $sale_price ) ? "          <g:sale_price>$sale_price</g:sale_price>\n" : '';
        $content .= $sku_as_mpn ? "         <g:mpn>$sku_as_mpn</g:mpn>\n" : '';
        $content .= $gtin ? "         <g:gtin>$gtin</g:gtin>\n" : '';
        $content .= $gender ? "         <g:gender>$gender</g:gender>\n" : '';
        $content .= $age_group ? "          <g:age_group>$age_group</g:age_group>\n" : '';
        $content .= $brand ? "          <g:brand>$brand</g:brand>\n" : '';
        $content .= $expiration_date ? "          <g:expiration_date>$expiration_date</g:expiration_date>\n" : '';
        $content .= $size_type ? "          <g:size_type>$size_type</g:size_type>\n" : '';
        $content .= $size_system ? "            <g:size_system>$size_system</g:size_system>\n" : '';
        $content .= $salse_price ? "         <g:sale_price>$salse_price</g:sale_price>\n" : '';
        $content .= $effective_date ? "         <g:sale_price_effective_date>$effective_date</g:sale_price_effective_date>\n" : '';
        $content .= $custom_label_0 ? "         <g:custom_label_0><![CDATA[$custom_label_0]]></g:custom_label_0>\n" : '';
        $content .= $custom_label_1 ? "         <g:custom_label_1><![CDATA[$custom_label_1]]></g:custom_label_1>\n" : '';
        $content .= $custom_label_2 ? "         <g:custom_label_2><![CDATA[$custom_label_2]]></g:custom_label_2>\n" : '';
        $content .= $custom_label_3 ? "         <g:custom_label_3><![CDATA[$custom_label_3]]></g:custom_label_3>\n" : '';
        $content .= $custom_label_4 ? "         <g:custom_label_4><![CDATA[$custom_label_4]]></g:custom_label_4>\n" : '';
        $content .= $promotion_id ? "           <g:promotion_id>$promotion_id</g:promotion_id>\n" : '';
        $content .= $color_attr ? "         <g:color>$color_attr</g:color>\n" : '';
        $content .= $size_attr ? "          <g:size>$size_attr</g:size>\n" : '';
        $content .= $identifier ? "         <g:identifier_exists>FALSE</g:identifier_exists>\n" : '';

        foreach ( $custom_attrs as $attr_key => $attr_value ) {
            $content .= !empty( $attr_value ) ? "          <g:$attr_key type=\"string\">$attr_value</g:$attr_key>\n" : '';
        }

        $content .= "       </item>\n";

        return $content;
    }

    function get_gtin( $post, $product_id, $gtin ) {
        
        if ( ! isset( $post['gtin'] ) ) {
            return false;
        }
        
        if ( isset( $gtin[$product_id] ) && ! empty( $gtin[$product_id] ) ) {
            return $gtin[$product_id];
        }
        
        return false;
    }

    function get_sale_price_effective_date( $post, $product_id ) {
        $status = isset( $post['sale_price_effective_date'] ) && $post['sale_price_effective_date'] == 'yes' ? true : false;

        if( ! $status ) {
            return false;
        }

        $sale_price_dates_from  = ( $date = get_post_meta( $product_id, '_sale_price_dates_from', true ) ) ? $this->get_availability_value( date_i18n( 'Y-m-d', $date ) ) : false;
        
        if ( ! $sale_price_dates_from ) {
            return false;
        }
        $sale_price_dates_to    = ( $date = get_post_meta( $product_id, '_sale_price_dates_to', true ) ) ? $this->get_availability_value( date_i18n( 'Y-m-d', $date ) ) : false;

        if ( ! $sale_price_dates_to ) {
            return false;
        }

        return $sale_price_dates_from .'/'. $sale_price_dates_to;
    }

    function get_identifier( $post, $product_id, $sku_as_mpn, $brand ) {
        $identifier = false;

        if ( ! $sku_as_mpn && ! $brand ) {
            $identifier = isset( $post['identifier_exists'] ) ? true : false;   
        }

        return $identifier;
    }

    function get_condition( $post, $product_id ) {
    	//required attribute
        $condition_ind = get_post_meta( $product_id, '_condition', true );
        if ( $condition_ind == '-1' ) {
            $condition      = $post['condition'];
        } else {
            $condition  = empty( $condition_ind ) ? $post['condition'] : $condition_ind;
        }

        return $condition;
    }

    function get_availability( $post, $product_id ) {
    	//required attribute
        $avaibility_ind = get_post_meta( $product_id, '_availability', true );
        if ( $avaibility_ind == 'default' ) {
            $availability   = $post['availability'];
        } else {
            $availability   = $avaibility_ind;
        }

        return $availability;
    }

    function get_category( $post, $product_id, $product_cat ) {
    	$pro_cat_ind = get_post_meta( $product_id, '_google_product_category', true );
        if ( $pro_cat_ind == 'default' ) {
            $category = $post['google_product_category'] ? $product_cat[$post['google_product_category']] : false;
            $category = $category ? str_replace( "&", "&amp;", $category ) : false;
            $category = $category ? str_replace( ">", "&gt;", $category ) : false;
        } else {
            if ( empty( $pro_cat_ind ) ) {
                $category = false;
            } else {
                $category = $product_cat[$pro_cat_ind];
                $category = $category ? str_replace( "&", "&amp;", $category ) : false;
                $category = $category ? str_replace( ">", "&gt;", $category ) : false;
            }
        }

        return $category;
    }

    function get_type( $post, $product_id, $product_cat ) {
    	
    	$pro_typ_ind = get_post_meta( $product_id, '_product_type', true );
        
        if ( $pro_typ_ind == 'default' ) {
            $type = $post['product_type'] ? $product_cat[$post['product_type']] : false;
            $type = $type ? str_replace( "&", "&amp;", $type ) : false;
            $type = $type ? str_replace( ">", "&gt;", $type ) : false;
        } else {
            if ( empty( $pro_typ_ind ) ) {
                $type = false;
            } else {
                $type = $product_cat[$pro_typ_ind];
                $type = $type ? str_replace( "&", "&amp;", $type ) : false;
                $type = $type ? str_replace( ">", "&gt;", $type ) : false;
            }
        }

        return $type;
    }

    function get_availability_date( $post, $product_id ) {
    	$availability_date_ind = get_post_meta( $product_id, '_availability_date_default', true );
        if ( $availability_date_ind == 'default' ) {
            $availability_date = !empty( $post['availability_date'] ) ? $post['availability_date'] : false;
        } else {
            $availability_date = get_post_meta( $product_id, '_availability_date', true );
            $availability_date = empty( $availability_date ) ? false : $availability_date;
        }

        return $availability_date;
    }

    function get_expiration_date( $post, $product_id ) {
        $expiration_date_ind = get_post_meta( $product_id, '_expiration_date_default', true );
        if ( $expiration_date_ind == 'default' ) {
            $expiration_date = !empty( $post['expiration_date'] ) ? $post['expiration_date'] : false;
        } else {
            $expiration_date = get_post_meta( $product_id, '_expiration_date', true );
            $expiration_date = empty( $expiration_date ) ? false : $expiration_date;
        }

        return $expiration_date;
    }

    function get_availability_value( $availability_date ) {
    	$availability_value = '';

    	if ( $availability_date ) {
            $tz_offset = get_option( 'gmt_offset' );
            $availability_value = $availability_date.'T00:00:00' . sprintf( '%+03d', $tz_offset ) . '00';
        }

        return $availability_value;
    }

    function get_sku_as_mpn ( $post, $product_id, $wc_product ) {
    	$sku_ind = get_post_meta( $product_id, '_mpn_default', true );

        $sku = $wc_product->get_sku();
        $sku = ! empty( $sku ) ? $sku : false;

        if ( $sku_ind == 'default' ) {
            $mpn = isset( $post['mpn'] ) ? true : false;
            $sku_as_mpn    = $mpn ? $sku : false;
        } else {
            $sku_as_mpn = get_post_meta( $product_id, '_mpn', true );
            
            if ( empty( $sku_as_mpn ) ) {
                $sku_as_mpn = $sku ? $sku : false;
            } 
        }

        return $sku_as_mpn;
    }

    function get_gender( $post, $product_id ) {
    	$gender_ind = get_post_meta( $product_id, '_gender', true );
        if ( $gender_ind == 'default' ) {
            $gender = $post['gender'] == '-1' ? false : $post['gender'];
        } else {
            $gender = ( $gender_ind == '-1' ) ? false : $gender_ind;
        }

        return $gender;
    }

    function get_age_group( $post, $product_id ) {
    	$age_group_ind = get_post_meta( $product_id, '_age_group', true );
        if ( $age_group_ind == 'default' ) {
            $age_group = $post['age_group'] == '-1' ? false : $post['age_group'];
        } else {
            $age_group = ( $age_group_ind == '-1' ) ? false : $age_group_ind;
        }

        return $age_group;
    }

    function get_size_type( $post, $product_id ) {
    	$size_type_ind = get_post_meta( $product_id, '_size_type', true );
        if ( $size_type_ind == 'default' ) {
            $size_type = $post['size_type'] == '-1' ? false : $post['size_type'];
        } else {
            $size_type = ( $size_type_ind == '-1' ) ? false : $size_type_ind;
        }

        return $size_type;
    }

    function get_size_system( $post, $product_id ) {
    	$size_system_ind = get_post_meta( $product_id, '_size_system', true );
        if ( $size_system_ind == 'default' ) {
            $size_system = $post['size_system'] == '-1' ? false : $post['size_system'];
        } else {
            $size_system = ( $size_system_ind == '-1' ) ? false : $size_system_ind;
        }

        return $size_system;
    }

    function get_custom_label_0( $post, $product_id ) {
    	$custom_label_0_ind = get_post_meta( $product_id, '_custom_label_0_default', true );
        if ( $custom_label_0_ind == 'default' ) {
            $custom_label_0 = ! empty( $post['custom_label_0'] ) ? $post['custom_label_0'] : false;
        } else {
            $custom_label_0 = get_post_meta( $product_id, '_custom_label_0', true );
            $custom_label_0 = empty( $custom_label_0 ) ? false : $custom_label_0;
        }

        return $custom_label_0;
    }

    function get_custom_label_1( $post, $product_id ) {
    	$custom_label_1_ind = get_post_meta( $product_id, '_custom_label_1_default', true );
        if ( $custom_label_1_ind == 'default' ) {
            $custom_label_1 = ! empty( $post['custom_label_1'] ) ? $post['custom_label_1'] : false;
        } else {
            $custom_label_1 = get_post_meta( $product_id, '_custom_label_1', true );
            $custom_label_1 = empty( $custom_label_1 ) ? false : $custom_label_1;
        }

        return $custom_label_1;
    }

    function get_custom_label_2( $post, $product_id ) {
    	$custom_label_2_ind = get_post_meta( $product_id, '_custom_label_2_default', true );
        if ( $custom_label_2_ind == 'default' ) {
            $custom_label_2 = ! empty( $post['custom_label_2'] ) ? $post['custom_label_2'] : false;
        } else {
            $custom_label_2 = get_post_meta( $product_id, '_custom_label_2', true );
            $custom_label_2 = empty( $custom_label_2 ) ? false : $custom_label_2;
        }

        return $custom_label_2;
    }

    function get_custom_label_3( $post, $product_id ) {
    	$custom_label_3_ind = get_post_meta( $product_id, '_custom_label_3_default', true );
        if ( $custom_label_3_ind == 'default' ) {
            $custom_label_3 = ! empty( $post['custom_label_3'] ) ? $post['custom_label_3'] : false;
        } else {
            $custom_label_3 = get_post_meta( $product_id, '_custom_label_3', true );
            $custom_label_3 = empty( $custom_label_3 ) ? false : $custom_label_3;
        }

        return $custom_label_3;
    }

    function get_custom_label_4( $post, $product_id ) {
    	$custom_label_4_ind = get_post_meta( $product_id, '_custom_label_4_default', true );
        if ( $custom_label_4_ind == 'default' ) {
            $custom_label_4 = ! empty( $post['custom_label_4'] ) ? $post['custom_label_4'] : false;
        } else {
            $custom_label_4 = get_post_meta( $product_id, '_custom_label_4', true );
            $custom_label_4 = empty( $custom_label_4 ) ? false : $custom_label_4;
        }

        return $custom_label_4;
    }

    function get_promotion_id( $post, $product_id ) {
    	$promotion_id_ind = get_post_meta( $product_id, '_promotion_id_default', true );
        if ( $promotion_id_ind == 'default' ) {
            $promotion_id = ! empty( $post['promotion_id'] ) ? $post['promotion_id'] : false;
        } else {
            $promotion_id = get_post_meta( $product_id, '_promotion_id', true );
            $promotion_id = empty( $promotion_id ) ? false : $promotion_id;
        }

        return $promotion_id;
    }

    function get_brand( $post, $product_id ) {
	    $brand_ind = get_post_meta( $product_id, '_brand_default', true );
        if ( $brand_ind == 'default' ) {
            $brand = ! empty( $post['brand'] ) ? $post['brand'] : false;
        } else {
            $brand = get_post_meta( $product_id, '_brand', true );
            $brand = empty( $brand ) ? false : $brand;
        }

        return $brand;
    }

    function get_additional_images( $wc_product ) {
        $additional_images = array();
        foreach ( $wc_product->get_gallery_attachment_ids() as $key => $link_id ) {
            $additional_images[] =  wp_get_attachment_url( $link_id ); 
            if ( count( $additional_images ) > 9 ) {
                break;
            }
        }

        return $additional_images;
    }

    function get_size_attr( $post, $product_id, $wc_product ) {
    	$size = $wc_product->get_attribute('size');

    	if ( isset( $post['size'] ) && ! empty( $size ) ) {
            $size = str_replace(' ', '', $size );
            $size_attr = woogool_is_product_attribute_taxonomy( 'size', $wc_product ) ? str_replace( ',', '/', $size ) : str_replace( '|', '/', $size );
        } else {
            $size_attr = false;
        }

    	$size_ind = get_post_meta( $product_id, '_size_default', true );
        if ( $size_ind != 'default' ) {
            $size = get_post_meta( $product_id, '_size', true );
            if ( empty( $size ) ) {
                $size_attr = false;
            } else {
                $size = str_replace(' ', '', $size );
                $size_attr = str_replace( ',', '/', $size );
            }
        }

        return $size_attr;
    }

    function get_color_attr( $post, $product_id, $wc_product ) {

    	$color = $wc_product->get_attribute('color');
            
        if ( isset( $post['color'] ) && ! empty( $color ) ) {
            $color = str_replace(' ', '', $color );
            $color_attr = woogool_is_product_attribute_taxonomy( 'color', $wc_product ) ? str_replace( ',', '/', $color ) : str_replace( '|', '/', $color );
        } else {
            $color_attr = false;
        }

    	$color_ind = get_post_meta( $product_id, '_color_default', true );
        if ( $color_ind != 'default' ) {
            $color = get_post_meta( $product_id, '_color', true );
            if ( empty( $color ) ) {
                $color_attr = false;
            } else {
                $color = str_replace(' ', '', $color );
                $color_attr = str_replace( ',', '/', $color );
            }
        }

        return $color_attr;
    }

    function is_product_feed_disabled( $product_id ) {
    	if ( get_post_meta( $product_id, '_disabled_feed', true ) == 'disabled' ) {
    		return true;
    	}

    	return false;
    }

    function feed_delete() {
        if( ! isset( $_GET['page'] ) || ! isset( $_GET['woogool_tab'] ) || ! isset( $_GET['action'] ) ) {
            return;
        }

        if ( $_GET['page'] != 'product_woogool' || $_GET['woogool_tab'] != 'woogool_multiple' || $_GET['action'] != 'delete' ) {
            return;
        }

        $feed_id = isset( $_GET['feed_id'] ) ? intval( $_GET['feed_id'] ) : 0;

        if ( ! $feed_id ) {
            return;
        }

        wp_delete_post( $feed_id, true );

        $url_feed_list   = admin_url( 'edit.php?post_type=product&page=product_woogool&woogool_tab=woogool_multiple&woogool_sub_tab=feed-lists' );
        wp_redirect( $url_feed_list );
        exit();
    }

    function check_categori_fetch() {
        $feed_cat_fetch_time = get_option( 'woogool_google_product_type_fetch_time', false );
        if ( ! $feed_cat_fetch_time ) {
            $this->store_google_product_type();
            return;
        }

        $cat = get_option( 'woogool_google_product_type' );
        if ( ! $cat || ! count( $cat ) || empty( $cat ) ) {
            $this->store_google_product_type();
            return;
        }
        $minute_diff = woogool_get_minute_diff( current_time( 'mysql' ), $feed_cat_fetch_time );

        if ( $minute_diff > 600 ) {
            $this->store_google_product_type();
        }
    }

    function store_google_product_type() {
        $cat = woogool_get_google_product_type();
        $cat = $cat ? $cat : array();
        update_option( 'woogool_google_product_type', $cat );
        update_option( 'woogool_google_product_type_fetch_time', current_time( 'mysql' ) );
    }

    function feed_meta_box( $post_type ) {
        add_meta_box( 'woogool-feed-metabox-wrap', __( 'Feed Information' ), array( $this, 'woogool_meta_box_callback' ), $post_type, 'normal', 'core' );
    }

    function woogool_meta_box_callback( $post ) {
        if ( ! isset( $post->post_type ) ) {
            return;
        }
        
        if ( $post->post_type != 'product' ) {
            return;
        }
        $post_id = $post->ID;
        include_once WOOGOOL_PATH . '/views/single/product-meta.php';
    }

    function save_post_meta( $post_id, $post, $update ) {
        if ( $post->post_type != 'product' ) {
            return;
        }

        if ( ! isset( $_POST['woogool_sinlge_product_feed'] ) ) {
            return;
        }

        $disabled = isset( $_POST['disabled_feed'] ) ? $_POST['disabled_feed'] : 1;
        update_post_meta( $post_id, '_disabled_feed', $disabled );

        update_post_meta( $post_id, '_google_product_category', $_POST['google_product_category'] );
        update_post_meta( $post_id, '_product_type', $_POST['product_type'] );

        update_post_meta( $post_id, '_availability', $_POST['availability'] );

        $availability_date_default = isset( $_POST['availability_date_default'] ) ? $_POST['availability_date_default'] : '';
        update_post_meta( $post_id, '_availability_date_default', $availability_date_default );
        update_post_meta( $post_id, '_availability_date', $_POST['availability_date'] );

        update_post_meta( $post_id, '_condition', $_POST['condition'] );

        $brand_default = isset( $_POST['brand_default'] ) ? $_POST['brand_default'] : '';
        update_post_meta( $post_id, '_brand_default', $brand_default );
        update_post_meta( $post_id, '_brand', $_POST['brand'] );

        $mpn_default = isset( $_POST['mpn_default'] ) ? $_POST['mpn_default'] : '';
        update_post_meta( $post_id, '_mpn_default', $mpn_default );
        update_post_meta( $post_id, '_mpn', $_POST['mpn'] );

        update_post_meta( $post_id, '_gender', $_POST['gender'] );
        update_post_meta( $post_id, '_age_group', $_POST['age_group'] );

        $color_default = isset( $_POST['color_default'] ) ? $_POST['color_default'] : '';
        update_post_meta( $post_id, '_color_default', $color_default );
        update_post_meta( $post_id, '_color', $_POST['color'] );

        $size_default = isset( $_POST['size_default'] ) ? $_POST['size_default'] : '';
        update_post_meta( $post_id, '_size_default', $size_default );
        update_post_meta( $post_id, '_size', $_POST['size'] );

        update_post_meta( $post_id, '_size_type', $_POST['size_type'] );
        update_post_meta( $post_id, '_size_system', $_POST['size_system'] );

        $expiration_date_default = isset( $_POST['expiration_date_default'] ) ? $_POST['expiration_date_default'] : '';
        update_post_meta( $post_id, '_expiration_date_default', $expiration_date_default );
        update_post_meta( $post_id, '_expiration_date', $_POST['expiration_date'] );

        $custom_label_0_default = isset( $_POST['custom_label_0_default'] ) ? $_POST['custom_label_0_default'] : '';
        update_post_meta( $post_id, '_custom_label_0_default', $custom_label_0_default );
        update_post_meta( $post_id, '_custom_label_0', $_POST['custom_label_0'] );

        $custom_label_1_default = isset( $_POST['custom_label_1_default'] ) ? $_POST['custom_label_1_default'] : '';
        update_post_meta( $post_id, '_custom_label_1_default', $custom_label_1_default );
        update_post_meta( $post_id, '_custom_label_1', $_POST['custom_label_1'] );

        $custom_label_2_default = isset( $_POST['custom_label_2_default'] ) ? $_POST['custom_label_2_default'] : '';
        update_post_meta( $post_id, '_custom_label_2_default', $custom_label_2_default );
        update_post_meta( $post_id, '_custom_label_2', $_POST['custom_label_2'] );

        $custom_label_3_default = isset( $_POST['custom_label_3_default'] ) ? $_POST['custom_label_3_default'] : '';
        update_post_meta( $post_id, '_custom_label_3_default', $custom_label_3_default );
        update_post_meta( $post_id, '_custom_label_3', $_POST['custom_label_3'] );

        $custom_label_4_default = isset( $_POST['custom_label_4_default'] ) ? $_POST['custom_label_4_default'] : '';
        update_post_meta( $post_id, '_custom_label_4_default', $custom_label_4_default );
        update_post_meta( $post_id, '_custom_label_4', $_POST['custom_label_4'] );

        $promotion_id_default = isset( $_POST['promotion_id_default'] ) ? $_POST['promotion_id_default'] : '';
        update_post_meta( $post_id, '_promotion_id_default', $promotion_id_default );
        update_post_meta( $post_id, '_promotion_id', $_POST['promotion_id'] );
    }
}




