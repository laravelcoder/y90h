<?php

/**
 * Leadgenix function overrides and additions
 */
//ANNOYANCES
show_admin_bar(false);
// remove junk from head
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'parent_post_rel_link', 10, 0);
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);

function remove_caption_padding($width) {
    return $width - 10; //wordpress default adds 10px width on image caption.
}

add_filter('img_caption_shortcode_width', 'remove_caption_padding');

//MAIL
add_filter('wp_mail_from', 'new_mail_from');
add_filter('wp_mail_from_name', 'new_mail_from_name');

function new_mail_from($old) {
    return 'no-reply@young90health.com';
}

function new_mail_from_name($old) {
    return 'Young 90 Health';
}

//WP-ADMIN STYLES
add_action('login_enqueue_scripts', 'my_login_stylesheet');
add_filter('login_headerurl', 'put_my_url');

function put_my_url() {
    return ('https://www.young90health.com/');
}

function my_login_stylesheet() {
    wp_enqueue_style('custom-login', get_stylesheet_directory_uri() . '/login.css');
}

//ENQUEUE SCRIPTS AND STYLES FOR FRONTEND
add_action('wp_print_styles', 'load_custom_scripts_sheets');

function load_custom_scripts_sheets() {
    wp_register_style('et-googleFonts', '//fonts.googleapis.com/css?family=Varela+Round|Montserrat:400,700');
    wp_enqueue_style('et-googleFonts');
    wp_enqueue_script('custom', get_stylesheet_directory_uri() . '/custom.js', array('jquery'), '1.0.1');
}

//MAKE SURE THE SHOP PAGE HAS SIDEBAR, I included in archive-product.php since Divi was adding a class after this.
/*
  add_filter('body_class', function($classes) {
  //if($_SERVER['REMOTE_ADDR']=='66.219.235.253'){print_r($classes);}
  if (in_array('post-type-archive-product', $classes)) {//shop page
  $classes = array_diff($classes, array('et_full_width_page'));
  array_push($classes,"et_right_sidebar");
  }
  //if($_SERVER['REMOTE_ADDR']=='66.219.235.253'){echo "<hr>";print_r($classes);echo "<hr>";}
  return $classes;
  });//end add filter
 */


//WOOCOMMERCE RESTRICT CA SHIPPING
function nf_restrict_shipping($available_methods) {
    global $woocommerce;
    $excluded_states = array('CA');

    if (in_array($woocommerce->customer->get_shipping_state(), $excluded_states)) {
        // Empty the $available_methods array
        $available_methods = array();
    }

    return $available_methods;
}

add_filter('woocommerce_package_rates', 'nf_restrict_shipping', 10);
add_filter('woocommerce_cart_no_shipping_available_html', 'change_shipping_message');
add_filter('woocommerce_no_shipping_available_html', 'change_shipping_message');

function change_shipping_message() {
    return '<span style="color:red">We are sorry, we cannot ship here. California residents <a href="/california-notice">read this</a>, or call us at 1-800-994-1601</span>';
}

//WOOCOMMERCE CUSTOM TRACKING CODE
add_action('woocommerce_thankyou', 'my_custom_tracking');

function my_custom_tracking($order_id) {
    $order = new WC_Order($order_id);
    //$order->get_order_total()
    $total = $order->get_total();

    echo '
		<!-- Google Code for Sale Conversion Page -->
		<script type="text/javascript">
		/* <![CDATA[ */
		var google_conversion_id = 973430199;
		var google_conversion_language = "en";
		var google_conversion_format = "3";
		var google_conversion_color = "ffffff";
		var google_conversion_label = "Npe5CKnX6AkQt7uV0AM";
		var google_conversion_value = "' . $total . '";
		var google_conversion_currency = "USD";
		var google_remarketing_only = false;

		/* ]]> */
		</script>
		<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
		</script>
		<noscript>
		<div style="display:inline;">
		<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/950216969/?value=' . $total . '&amp;currency_code=USD&amp;label=QwBpCLGOklwQidKMxQM&amp;guid=ON&amp;script=0"/>
		</div>
		</noscript>
		
		<script>
		ga(\'ecommerce:addTransaction\', {
		  \'id\': \'' . $order->get_order_number() . '\', // Transaction ID. Required.
		  \'affiliation\': \'Young 90 Health\',   // Affiliation or store name.
		  \'revenue\': \'' . $total . '\',
		});
		ga(\'ecommerce:send\');
		</script>
		<script>(function(w,d,t,r,u){var f,n,i;w[u]=w[u]||[],f=function(){var o={ti:"4077224"};o.q=w[u],w[u]=new UET(o),w[u].push("pageLoad")},n=d.createElement(t),n.src=r,n.async=1,n.onload=n.onreadystatechange=function(){var s=this.readyState;s&&s!=="loaded"&&s!=="complete"||(f(),n.onload=n.onreadystatechange=null)},i=d.getElementsByTagName(t)[0],i.parentNode.insertBefore(n,i)})(window,document,"script","//bat.bing.com/bat.js","uetq");</script><noscript><img src="//bat.bing.com/action/0?ti=4077224&Ver=2" height="0" width="0" style="display:none; visibility: hidden;" /></noscript>
		
		
		<script type="text/javascript"> var sa_values = { "site":16076 };  function saLoadScript(src) { var js = window.document.createElement("script"); js.src = src; js.type = "text/javascript"; document.getElementsByTagName("head")[0].appendChild(js); } var d = new Date(); if (d.getTime() - 172800000 > 1443036321000) saLoadScript("//www.shopperapproved.com/thankyou/rate/16076.js"); else saLoadScript("//direct.shopperapproved.com/thankyou/rate/16076.js?d=" + d.getTime()); </script>
		';
}

//WOOCOMMERCE SHOW ADD TO CART ON SHOP PAGES
add_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_add_to_cart');


//WOOCOMMERCE ADD SEARCH AND CATEGORIES TO PRODUCT ARCHIVES
remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10);

//remove_action('woocommerce_before_shop_loop','woocommerce_result_count',20);
//remove_action('woocommerce_before_shop_loop','woocommerce_catalog_ordering',30);
//add_action( 'woocommerce_before_shop_loop', 'nf_search_categories', 20 );
//add_action('woocommerce_after_shop_loop','woocommerce_result_count',20);
function nf_search_categories() {
    $taxonomy = 'product_cat';
    $orderby = 'name';
    $show_count = 0;      // 1 for yes, 0 for no
    $pad_counts = 0;      // 1 for yes, 0 for no
    $hierarchical = 1;      // 1 for yes, 0 for no  
    $title = '';
    $empty = 0;
    $args = array(
        'taxonomy' => $taxonomy,
        'orderby' => $orderby,
        'show_count' => $show_count,
        'pad_counts' => $pad_counts,
        'hierarchical' => $hierarchical,
        'title_li' => $title,
        'hide_empty' => $empty
    );
    $all_categories = get_categories($args);
//print_r($all_categories);
    $catdrop = '<select name="product_cat" class="dropdown_product_cat">
<option value="" selected="selected">Select a category</option>';
    foreach ($all_categories as $cat) {
        //print_r($cat);
        if ($cat->category_parent == 0) {
            $category_id = $cat->term_id;
        }
        $catdrop.='<option value="' . get_term_link($cat->slug, 'product_cat') . '">' . $cat->name . '</option>';
    }
    $catdrop.='</select>';


    //$one = woocommerce_product_dropdown_categories();
    $one = $catdrop;
    $two = get_product_search_form(false);
    $three = woocommerce_catalog_ordering();
    echo sprintf(
            '<div class="one_third">
			%1$s
		</div>
		<div class="one_third">
			%2$s
		</div>
		<div class="one_third last">
			%3$s
		</div>', $one, $two, $three
    );

    //echo $output;
}

add_action('woocommerce_after_shop_loop', 'woocommerce_result_count', 20);



//WOOCOMMERCE 24 ITEMS PER PAGE
add_filter('loop_shop_per_page', create_function('$cols', 'return 24;'), 20);
add_filter('loop_shop_columns', 'wc_loop_shop_columns', 1, 10);

function wc_loop_shop_columns($number_columns) {
    return 3;
}

//CHANGE VIEW CART TEXT AND GO STRAIGHT TO CHECKOUT (BYPASS CART) - BAD IDEA IMO
function my_text_strings($translated_text, $text, $domain) {
    switch ($translated_text) {
        case 'View Cart' :
            $translated_text = __('Checkout', 'woocommerce');
            break;
    }
    return $translated_text;
}

add_filter('gettext', 'my_text_strings', 20, 3);
add_filter('add_to_cart_redirect', 'redirect_to_checkout');

function redirect_to_checkout() {
    global $woocommerce;
    $checkout_url = $woocommerce->cart->get_checkout_url();
    return $checkout_url;
}

//WOOCOMMERCE CUSTOM SHORTCODE FOR THE PRODUCT GRID
function naturesfix_product($atts) {
    global $woocommerce;
    $a = shortcode_atts(array(
        'sku' => '0000'
            ), $atts);
    $prodid = wc_get_product_id_by_sku($a['sku']);
    $product = wc_get_product($prodid);
    if (!$product || !is_object($product)) {
        $output = '<div class="nfproduct">
				<div class="nfleft"></div>
				<div class="nfright">
					<h4>No Product Found</h4>
					<div class="nfproductprice">id:' . $prodid . '</div>
					<div class="nfaddtocart"></div>
				</div>
				<div class="clear"></div>
			</div> <!-- nfproduct -->'
        ;
        return $output;
    }
    $title = get_the_title($prodid);
    $description = $product->post->post_excerpt;
    $image = $product->get_image();
    $price = '$' . number_format($product->price, 2);
    $addto = do_shortcode('[add_to_cart id="' . $prodid . '"]');
    $permalink = $product->get_permalink();
    $output = sprintf(
            '<div class="nfproduct">
			<div class="nfleft"><a href="%5$s">%2$s</a></div>
			<div class="nfright">
				<h4><a href="%5$s">%1$s</a></h4>
				<div class="nfproductprice">%4$s</div>
				<div class="nfaddtocart">%3$s</div>
			</div>
			<div class="clear"></div>
		</div> <!-- nfproduct -->', $title, $image, $addto, $price, $permalink
    );

    return $output;
}

add_shortcode('nfproduct', 'naturesfix_product');

function naturesfix_pak_product($atts) {
    global $woocommerce;
    $a = shortcode_atts(array(
        'sku' => '0000'
            ), $atts);
    $prodid = wc_get_product_id_by_sku($a['sku']);
    $product = wc_get_product($prodid);
    if (!$product || !is_object($product)) {
        $output = '<div class="nfpakproduct" style="margin-top:10px;width:180px">
			<div></div>
			<div class="wp-caption">
				<p class="wp-caption-text">No Product Found</p>
				<div class="nfpakproductprice">id: ' . $prodid . '</div>
			</div>
			<div class="et_blurb_fade"></div>
			<div class="nfpakadd"></div>
			<div class="clear"></div>
		</div> <!-- nfpakproduct -->'
        ;
        return $output;
    }
    $title = get_the_title($prodid);
    $description = $product->post->post_excerpt;
    $image = $product->get_image('shop_thumbnail');
    $price = '$' . number_format($product->price, 2);
    $addto = do_shortcode('[add_to_cart id="' . $prodid . '"]');
    $permalink = $product->get_permalink();
    $output = sprintf(
            '<div class="nfpakproduct" style="margin-top:10px;width:180px">
			<div><a href="%5$s">%2$s</a></div>
			<div class="wp-caption">
				<p class="wp-caption-text">%1$s</a></p>
				<div class="nfpakproductprice"><a href="%5$s">%4$s</div>
			</div>
			<div class="et_blurb_fade"></div>
			<div class="nfpakadd">%3$s</div>
			<div class="clear"></div>
		</div> <!-- nfpakproduct -->', $title, $image, $addto, $price, $permalink
    );

    return $output;
}

add_shortcode('nfpakproduct', 'naturesfix_pak_product');

function naturesfix_pak_groupedproduct($atts) {
    global $woocommerce;
    $a = shortcode_atts(array(
        'id' => ''
            ), $atts);
    $prodid = $a['id'];
    $product = wc_get_product($prodid);
    if (!$product || !is_object($product)) {
        $output = '<div class="nfproduct">
				<div class="nfleft"></div>
				<div class="nfright">
					<h4>No Product Found</h4>
					<div class="nfproductprice">id:' . $prodid . '</div>
					<div class="nfaddtocart"></div>
				</div>
				<div class="clear"></div>
			</div> <!-- nfproduct -->'
        ;
        return $output;
    }
    //$children = $product->get_children();
    $title = get_the_title($prodid);
    $description = $product->post->post_excerpt;
    $image = $product->get_image();
    $price = $product->get_price_html();
    $addto = do_shortcode('[add_to_cart id="' . $prodid . '"]');
    $output = sprintf(
            '<div class="nfproduct">
			<div class="nfleft">%2$s</div>
			<div class="nfright">
				<h4>%1$s</h4>
				<div class="nfproductprice">%4$s</div>
				<div class="nfaddtocart">%3$s</div>
			</div>
			<div class="clear"></div>
		</div> <!-- nfproduct -->', $title, $image, $addto, $price
    );
    /* if($_SERVER['REMOTE_ADDR']=='66.219.235.253'){
      $ret='<pre>';
      $ret.=print_r($product->post);
      $ret.='<pre>';
      return $ret;
      } */
    return $output;
}

add_shortcode('nfgroupedproduct', 'naturesfix_pak_groupedproduct');


//CUSTOM MODULE FOR PAGE BUILDER 
//OVERRIDE THE BLURB MODULE
add_action('pre_get_posts', 'lgx_overwrite_shortcode');

function lgx_overwrite_shortcode() {

    remove_shortcode('et_pb_blurb');
    add_shortcode('et_pb_blurb', 'et_pb_blurb_leadgenix'); //already added in Divi functions
}

//end overwrite shortcode.

function et_pb_blurb_leadgenix($atts, $content = '') {
    $et_accent_color = et_get_option('accent_color', '#7EBEC5');

    extract(shortcode_atts(array(
        'module_id' => '',
        'module_class' => '',
        'title' => '',
        'url' => '',
        'image' => '',
        'url_new_window' => 'off',
        'alt' => '',
        'background_layout' => 'light',
        'text_orientation' => 'center',
        'animation' => 'top',
        'icon_placement' => 'top',
        'font_icon' => '',
        'use_icon' => 'off',
        'use_circle' => 'off',
        'use_circle_border' => 'off',
        'icon_color' => $et_accent_color,
        'circle_color' => $et_accent_color,
        'circle_border_color' => $et_accent_color,
                    ), $atts
    ));

    if (is_rtl() && 'left' === $text_orientation) {
        $text_orientation = 'right';
    }

    if (is_rtl() && 'left' === $icon_placement) {
        $icon_placement = 'right';
    }

    if ('' !== $title && '' !== $url)
        $title = sprintf('<a href="%1$s"%3$s>%2$s</a>', esc_url($url), esc_html($title), ( 'on' === $url_new_window ? ' target="_blank"' : '')
        );

    if ('' !== $title)
        $title = "<h4>{$title}</h4>";

    $imagesrc = esc_attr($image);
    $imagesize = getimagesize($imagesrc);
    $imageheight = $imagesize[1];

    if ('' !== $image || '' !== $font_icon) {
        if ('off' === $use_icon) {
            $image = sprintf(
                    '<img src="%1$s" alt="%2$s" class="et-waypoint%3$s" />', esc_attr($image), esc_attr($alt), esc_attr(" et_pb_animation_{$animation}")
            );
        } else {
            $icon_style = sprintf('color: %1$s;', esc_attr($icon_color));

            if ('on' === $use_circle) {
                $icon_style .= sprintf(' background-color: %1$s;', esc_attr($circle_color));

                if ('on' === $use_circle_border) {
                    $icon_style .= sprintf(' border-color: %1$s;', esc_attr($circle_border_color));
                }
            }

            $image = sprintf(
                    '<span class="et-pb-icon et-waypoint%2$s%3$s%4$s" style="%5$s">%1$s</span>', esc_attr($font_icon), esc_attr(" et_pb_animation_{$animation}"), ( 'on' === $use_circle ? ' et-pb-icon-circle' : ''), ( 'on' === $use_circle && 'on' === $use_circle_border ? ' et-pb-icon-circle-border' : ''), $icon_style
            );
        }

        $image = sprintf(
                '<div class="et_pb_main_blurb_image">%1$s</div>', ( '' !== $url ? sprintf(
                                '<a href="%1$s"%3$s>%2$s</a>', esc_url($url), $image, ( 'on' === $url_new_window ? ' target="_blank"' : '')
                        ) : $image
                )
        );
    }

    $class = " et_pb_bg_layout_{$background_layout} et_pb_text_align_{$text_orientation}";

    $button = ( '' !== $url) ? sprintf('<a href="%1$s" class="et_pb_promo_button">%2$s</a>', esc_url($url), esc_attr($alt)) : '';

    $output = sprintf(
            //'<div%5$s class="et_pb_blurb%4$s%6$s%7$s" style="background:url(%2$s) no-repeat center; min-height:%9$spx">
            '<a href="%10$s"><div%5$s class="et_pb_blurb%4$s%6$s%7$s">
			<div class="et_pb_blurb_img"><img src="%2$s" alt="'.$alt.'"/></div><!--comment out if doing background style.-->
			<div class="et_blurb_fade"><!--fade div--></div>
			<div class="et_pb_blurb_container" style="cursor:pointer;" onclick="window.location=\'%10$s\'">
				<div class="et_pb_blurb_title">%3$s</div>
				<div class="et_pb_blurb_content">
					%1$s
				</div> <!-- .et_pb_blurb_content -->
				%8$s
			</div>
		</div></a> <!-- .et_pb_blurb -->', do_shortcode(et_pb_fix_shortcodes($content)), $imagesrc, $title, esc_attr($class), ( '' !== $module_id ? sprintf(' id="%1$s"', esc_attr($module_id)) : ''), ( '' !== $module_class ? sprintf(' %1$s', esc_attr($module_class)) : ''), sprintf(' et_pb_blurb_position_%1$s', esc_attr($icon_placement)), $button, $imageheight, esc_url($url)
    );

    return $output;
}

//SHOW PRODUCT ID FOR WOOCOMMERCE PRODUCTS
/**
 * Load Custom Product Columns
 *
 * @access      public
 * @since       1.0 
 * @return      
 */
function woo_product_extra_columns($columns) {
    $newcolumns = array(
        "cb" => "<input type  = \"checkbox\" />",
        "product_ID" => esc_html__('ID', 'woocommerce'),
    );
    $columns = array_merge($newcolumns, $columns);
    return $columns;
}

add_filter("manage_edit-product_columns", "woo_product_extra_columns");

/**
 * Charge Product Columns Content
 *
 * @access      public
 * @since       1.0 
 * @return      
 */
function woo_product_extra_columns_content($column) {
    global $post;

    $product_id = $post->ID;

    switch ($column) {
        case "product_ID":
            echo $product_id;
            break;
    }
}

add_action("manage_posts_custom_column", "woo_product_extra_columns_content");
add_filter( 'woocommerce_quantity_input_args', 'jk_woocommerce_quantity_input_args', 10, 2 );

function jk_woocommerce_quantity_input_args( $args, $product ) {
	if ( is_singular( 'product' ) ) {
		$args['input_value'] 	= 1;	// Starting value (we only want to affect product pages, not cart)
	}
	$args['max_value'] 	= 80; 	// Maximum value
	$args['min_value'] 	= 1;   	// Minimum value
	$args['step'] 		= 1;    // Quantity steps
	return $args;
}