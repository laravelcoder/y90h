<?php

/**
 * This is used to store all the information about wooCommerce store products
 *
 * @since      1.0.0
 * @package    Woo_Feed
 * @subpackage Woo_Feed/includes
 * @author     Ohidul Islam <wahid@webappick.com>
 */
class Woo_Feed_Products
{

    /**
     * Contain all parent product information for the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $parent Contain all parent product information for the plugin.
     */
    public $parent;

    /**
     * Contain all child product information for the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $parent Contain all child product information for the plugin.
     */
    public $child;

    /**
     * The parent id of current product.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $parentID The current product's Parent ID.
     */
    public $parentID;
    /**
     * The child id of current product.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $parentID The current product's child ID.
     */
    public $childID;

    /**
     * The Variable that contain all products.
     *
     * @since    1.0.0
     * @access   private
     * @var      array $productsList Products list array.
     */
    public $productsList;


    /**
     * Get WooCommerce Products
     *
     * @param $limit
     * @param $offset
     * @param $categories
     * @return array
     */
    public function woo_feed_get_visible_product($limit, $offset,$categories)
    {
        $limit = !empty($limit) && is_numeric($limit) ? absint($limit) : '-1';
        $offset = !empty($offset) && is_numeric($offset) ? absint($offset) : '0';
        $arg = array(
            'post_type' => array('product', 'product_variation'),
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => $limit,
            'orderby' => 'date',
            'order' => 'desc',
            'offset' => $offset,
        );
        if (is_array($categories) && !empty($categories[0])) {
            $i = 0;
            $arg['tax_query']['relation'] = "OR";
            foreach ($categories as $key => $value) {
                if (!empty($value)) {
                    $arg['tax_query'][$i]["taxonomy"] = "product_cat";
                    $arg['tax_query'][$i]["field"] = "slug";
                    $arg['tax_query'][$i]["terms"] = $value;
                    $i++;
                }
            }
        }

        # Query Database for products
        $loop = new WP_Query($arg);
        $i = 0;

        while ($loop->have_posts()) : $loop->the_post();

            $this->childID = get_the_ID();
            $this->parentID = wp_get_post_parent_id($this->childID);

            global $product;
            $type1 = "";
            //if(is_object($product)){
            if (is_object($product) && $product->is_type('simple')) {
                # No variations to product
                $type1 = "simple";
            } elseif (is_object($product) && $product->is_type('variable')) {
                # Product has variations
                $type1 = "variable";
            } elseif (is_object($product) && $product->is_type('grouped')) {
                $type1 = "grouped";
            } elseif (is_object($product) && $product->is_type('external')) {
                $type1 = "external";
            } elseif (is_object($product) && $product->is_downloadable()) {
                $type1 = "downloadable";
            } elseif (is_object($product) && $product->is_virtual()) {
                $type1 = "virtual";
            }

            $post = get_post($this->parentID);
            # Get upc value for variable products and make an array
            $get_upc = $this->getUPCForVariableProducts($this->parentID, 'upc');

            if (get_post_type() == 'product_variation') {
                if ($this->parentID != 0) {
                    $mainImage = wp_get_attachment_url($product->get_image_id());
                    $link = $product->get_permalink($this->childID);
                    if (substr(trim($link), 0, 4) === "http" && substr(trim($mainImage), 0, 4) === "http") {

                        $this->productsList[$i]['id'] = $this->childID;
                        $this->productsList[$i]['variation_type'] = "child";
                        $this->productsList[$i]['item_group_id'] = $this->parentID;
                        $this->productsList[$i]['sku'] = $product->get_sku();
                        $this->productsList[$i]['parent_sku'] = $this->getAttributeValue($this->parentID, "_sku");
                        $this->productsList[$i]['title'] = $product->get_title();
                        $this->productsList[$i]['description'] = $post->post_content;

                        # Short Description to variable description
                        $vDesc = $this->getAttributeValue($this->childID, "_variation_description");
                        if (!empty($vDesc)) {
                            $this->productsList[$i]['short_description'] = $vDesc;
                        } else {
                            $this->productsList[$i]['short_description'] = $post->post_excerpt;
                        }

                        $this->productsList[$i]['product_type'] = $this->get_product_term_list($post->ID, 'product_cat', "", ">");// $this->categories($this->parentID);//TODO
                        $this->productsList[$i]['link'] = $link;
                        $this->productsList[$i]['image'] = $this->get_formatted_url($mainImage);

                        # Featured Image
                        if (has_post_thumbnail($post->ID)):
                            $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'single-post-thumbnail');
                            $this->productsList[$i]['feature_image'] = $this->get_formatted_url($image[0]);
                        else:
                            $this->productsList[$i]['feature_image'] = $this->get_formatted_url($mainImage);
                        endif;

                        # Additional Images
                        $images = $this->additionalImages($product->get_gallery_attachment_ids());
                        if ($images and is_array($images)) {
                            foreach ($images as $key => $value) {
                                if ($value != $this->productsList[$i]['image']) {
                                    $this->productsList[$i]["image_$key"] = $this->get_formatted_url($value);
                                }
                            }
                        }

                        $this->productsList[$i]['condition'] = "New";
                        $this->productsList[$i]['type'] = $product->get_type();
                        $this->productsList[$i]['visibility'] = $product->visibility;
                        $this->productsList[$i]['rating_total'] = $product->get_rating_count();
                        $this->productsList[$i]['rating_average'] = $product->get_average_rating();
                        $this->productsList[$i]['tags'] = $this->get_product_term_list($post->ID, 'product_tag');
                        $this->productsList[$i]['shipping'] = $product->get_shipping_class();

                        $this->productsList[$i]['availability'] = $this->availability($product->stock_status);
                        $this->productsList[$i]['quantity'] = $this->get_quantity($this->childID, "_stock");
                        $this->productsList[$i]['sale_price_sdate'] = $this->get_date($this->childID, "_sale_price_dates_from");
                        $this->productsList[$i]['sale_price_edate'] = $this->get_date($this->childID, "_sale_price_dates_to");
                        $this->productsList[$i]['price'] = ($product->get_regular_price()) ? $product->get_regular_price() : false;
//                        $this->productsList[$i]['price_with_tax'] = ($product->is_taxable()) ? $product->get_price_including_tax() : false;
//                        $this->productsList[$i]['price_without_tax'] = ($product->is_taxable()) ? $product->get_price_excluding_tax() : false;
                        $this->productsList[$i]['sale_price'] = ($product->get_sale_price()) ? $product->get_sale_price() : false;
                        $this->productsList[$i]['weight'] = ($product->get_weight()) ? $product->get_weight() : false;
                        $this->productsList[$i]['width'] = ($product->get_width()) ? $product->get_width() : false;
                        $this->productsList[$i]['height'] = ($product->get_height()) ? $product->get_height() : false;
                        $this->productsList[$i]['length'] = ($product->get_length()) ? $product->get_length() : false;

                        # Sale price effective date
                        $from = $this->sale_price_effective_date($this->childID, '_sale_price_dates_from');
                        $to = $this->sale_price_effective_date($this->childID, '_sale_price_dates_to');
                        if (!empty($from) && !empty($to)) {
                            $from = date('Y-m-d\TH:iO', strtotime($from));
                            $to = date('Y-m-d\TH:iO', strtotime($to));
                            $this->productsList[$i]['sale_price_effective_date'] = "$from" . "/" . "$to";
                        } else {
                            $this->productsList[$i]['sale_price_effective_date'] = "";
                        }
                    }
                }
            } elseif (get_post_type() == 'product') {
                if ($type1 == 'simple') {
                    $mainImage = wp_get_attachment_url($product->get_image_id());
                    $link = get_permalink($post->ID);

                    if (substr(trim($link), 0, 4) === "http" && substr(trim($mainImage), 0, 4) === "http") {

                        $this->productsList[$i]['id'] = $product->id;
                        $this->productsList[$i]['variation_type'] = "simple";
                        $this->productsList[$i]['title'] = $product->get_title();
                        $this->productsList[$i]['description'] = $post->post_content;

                        $this->productsList[$i]['short_description'] = $post->post_excerpt;
                        $this->productsList[$i]['product_type'] = $this->get_product_term_list($post->ID, 'product_cat', "", ">");// $this->categories($this->parentID);//TODO
                        $this->productsList[$i]['link'] = $link;
                        $this->productsList[$i]['image'] = $this->get_formatted_url($mainImage);

                        # Featured Image
                        if (has_post_thumbnail($post->ID)):
                            $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'single-post-thumbnail');
                            $this->productsList[$i]['feature_image'] = $this->get_formatted_url($image[0]);
                        else:
                            $this->productsList[$i]['feature_image'] = $this->get_formatted_url($mainImage);
                        endif;

                        # Additional Images
                        $images = $this->additionalImages($product->get_gallery_attachment_ids());
                        if ($images and is_array($images)) {
                            foreach ($images as $key => $value) {
                                if ($value != $this->productsList[$i]['image']) {
                                    $this->productsList[$i]["image_$key"] = $this->get_formatted_url($value);
                                }
                            }
                        }

                        $this->productsList[$i]['condition'] = "New";
                        $this->productsList[$i]['type'] = $product->get_type();
                        $this->productsList[$i]['visibility'] = $product->visibility;
                        $this->productsList[$i]['rating_total'] = $product->get_rating_count();
                        $this->productsList[$i]['rating_average'] = $product->get_average_rating();
                        $this->productsList[$i]['tags'] = $this->get_product_term_list($post->ID, 'product_tag');

                        $this->productsList[$i]['item_group_id'] = $product->id;
                        $this->productsList[$i]['sku'] = $product->get_sku();

                        $this->productsList[$i]['availability'] = $this->availability($product->stock_status);
                        $this->productsList[$i]['quantity'] = $this->get_quantity($product->id, "_stock");
                        $this->productsList[$i]['sale_price_sdate'] = $this->get_date($product->id, "_sale_price_dates_from");
                        $this->productsList[$i]['sale_price_edate'] = $this->get_date($product->id, "_sale_price_dates_to");
                        $this->productsList[$i]['price'] = ($product->get_regular_price()) ? $product->get_regular_price() : false;
//                        $this->productsList[$i]['price_with_tax'] = ($product->is_taxable()) ? $product->get_price_including_tax() : false;
//                        $this->productsList[$i]['price_without_tax'] = ($product->is_taxable()) ? $product->get_price_excluding_tax() : false;
                        $this->productsList[$i]['sale_price'] = ($product->get_sale_price()) ? $product->get_sale_price() : false;
                        $this->productsList[$i]['weight'] = ($product->get_weight()) ? $product->get_weight() : false;
                        $this->productsList[$i]['width'] = ($product->get_width()) ? $product->get_width() : false;
                        $this->productsList[$i]['height'] = ($product->get_height()) ? $product->get_height() : false;
                        $this->productsList[$i]['length'] = ($product->get_length()) ? $product->get_length() : false;

                        # Sale price effective date
                        $from = $this->sale_price_effective_date($product->id, '_sale_price_dates_from');
                        $to = $this->sale_price_effective_date($product->id, '_sale_price_dates_to');
                        if (!empty($from) && !empty($to)) {
                            $from = date('Y-m-d\TH:iO', $from);
                            $to = date('Y-m-d\TH:iO', $to);
                            $this->productsList[$i]['sale_price_effective_date'] = "$from" . "/" . "$to";
                        } else {
                            $this->productsList[$i]['sale_price_effective_date'] = "";
                        }
                    }
                } else if ($type1 == 'variable' && $product->has_child()) {
                    $mainImage = wp_get_attachment_url($product->get_image_id());
                    $link = get_permalink($post->ID);

                    if (substr(trim($link), 0, 4) === "http" && substr(trim($mainImage), 0, 4) === "http") {

                        $this->productsList[$i]['id'] = $product->id;
                        $this->productsList[$i]['variation_type'] = "parent";
                        $this->productsList[$i]['title'] = $product->get_title();
                        $this->productsList[$i]['description'] = $post->post_content;

                        $this->productsList[$i]['short_description'] = $post->post_excerpt;
                        $this->productsList[$i]['product_type'] = $this->get_product_term_list($post->ID, 'product_cat', "", ">");// $this->categories($this->parentID);//TODO
                        $this->productsList[$i]['link'] = $link;

                        $this->productsList[$i]['image'] = $this->get_formatted_url($mainImage);

                        # Featured Image
                        if (has_post_thumbnail($post->ID)):
                            $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'single-post-thumbnail');
                            $this->productsList[$i]['feature_image'] = $this->get_formatted_url($image[0]);
                        else:
                            $this->productsList[$i]['feature_image'] = $this->get_formatted_url($mainImage);
                        endif;

                        # Additional Images
                        $images = $this->additionalImages($product->get_gallery_attachment_ids());
                        if ($images and is_array($images)) {
                            foreach ($images as $key => $value) {
                                if ($value != $this->productsList[$i]['image']) {
                                    $this->productsList[$i]["image_$key"] = $this->get_formatted_url($value);
                                }
                            }
                        }

                        $this->productsList[$i]['condition'] = "New";
                        $this->productsList[$i]['type'] = $product->get_type();
                        $this->productsList[$i]['visibility'] = $product->visibility;
                        $this->productsList[$i]['rating_total'] = $product->get_rating_count();
                        $this->productsList[$i]['rating_average'] = $product->get_average_rating();
                        $this->productsList[$i]['tags'] = $this->get_product_term_list($post->ID, 'product_tag');

                        $this->productsList[$i]['item_group_id'] = $product->id;
                        $this->productsList[$i]['sku'] = $product->get_sku();

                        $this->productsList[$i]['availability'] = $this->availability($product->stock_status);
                        $this->productsList[$i]['quantity'] = $this->get_quantity($product->id, "_stock");
                        $this->productsList[$i]['sale_price_sdate'] = $this->get_date($product->id, "_sale_price_dates_from");
                        $this->productsList[$i]['sale_price_edate'] = $this->get_date($product->id, "_sale_price_dates_to");
                        $this->productsList[$i]['price'] = ($product->max_variation_price) ? $product->max_variation_price : false;
                        //$this->productsList[$i]['price_with_tax'] = ($product->is_taxable()) ? $product->get_price_including_tax() : false;
                        $this->productsList[$i]['sale_price'] = ($product->min_variation_price) ? $product->min_variation_price : false;
                        $this->productsList[$i]['weight'] = ($product->get_weight()) ? $product->get_weight() : false;
                        $this->productsList[$i]['width'] = ($product->get_width()) ? $product->get_width() : false;
                        $this->productsList[$i]['height'] = ($product->get_height()) ? $product->get_height() : false;
                        $this->productsList[$i]['length'] = ($product->get_length()) ? $product->get_length() : false;

                        # Sale price effective date
                        $from = $this->sale_price_effective_date($product->id, '_sale_price_dates_from');
                        $to = $this->sale_price_effective_date($product->id, '_sale_price_dates_to');
                        if (!empty($from) && !empty($to)) {
                            $from = date('Y-m-d\TH:iO', $from);
                            $to = date('Y-m-d\TH:iO', $to);
                            $this->productsList[$i]['sale_price_effective_date'] = "$from" . "/" . "$to";
                        } else {
                            $this->productsList[$i]['sale_price_effective_date'] = "";
                        }
                    }
                }
            }
            $i++;

        endwhile;
        wp_reset_query();
        return $this->productsList;
    }

    /**
     * Get formatted image url
     *
     * @param $url
     * @return bool|string
     */
    public function get_formatted_url($url = "")
    {
        if (!empty($url)) {
            if (substr(trim($url), 0, 4) === "http" || substr(trim($url), 0, 3) === "ftp" || substr(trim($url), 0, 4) === "sftp") {
                return rtrim($url, "/");
            } else {
                $base = get_site_url();
                $url = $base . $url;
                return rtrim($url, "/");
            }
        }
        return $url;
    }

    /**
     * Get formatted product date
     *
     * @param $id
     * @param $name
     * @return bool|string
     */
    public function get_date($id, $name)
    {
        $date = $this->getAttributeValue($id, $name);
        if ($date) {
            return date("Y-m-d", $date);
        }
        return false;
    }

    /**
     * Get formatted product quantity
     *
     * @param $id
     * @param $name
     * @return bool|mixed
     */
    public function get_quantity($id, $name)
    {
        $qty = $this->getAttributeValue($id, $name);
        if ($qty) {
            return $qty + 0;
        }
        return false;
    }

    /**
     * Retrieve a post's terms as a list with specified format.
     *
     * @since 2.5.0
     *
     * @param int $id Post ID.
     * @param string $taxonomy Taxonomy name.
     * @param string $before Optional. Before list.
     * @param string $sep Optional. Separate items using this.
     * @param string $after Optional. After list.
     *
     * @return string|false|WP_Error A list of terms on success, false if there are no terms, WP_Error on failure.
     */
    function get_product_term_list($id, $taxonomy, $before = '', $sep = ',', $after = '')
    {
        $terms = get_the_terms($id, $taxonomy);

        if (is_wp_error($terms)) {
            return $terms;
        }

        if (empty($terms)) {
            return false;
        }

        $links = array();

        foreach ($terms as $term) {
            $links[] = $term->name;
        }

        return $before . join($sep, $links) . $after;
    }

    /** Return additional image URLs
     *
     * @param array $imgIds
     *
     * @return array
     */
    public function additionalImages($imgIds)
    {
        $images = array();
        if (count($imgIds)) {
            foreach ($imgIds as $key => $value) {
                if ($key < 9) {
                    $images[$key] = wp_get_attachment_url($value);
                }
            }

            return $images;
        }

        return false;
    }

    /**
     * Give space to availability text
     *
     * @param bool $param
     *
     * @return string
     */
    public function availability($param = false)
    {
        if ($param) {
            if ($param == 'instock') {
                return "in stock";
            } elseif ($param == 'outofstock') {
                return "out of stock";
            }
        }
        return "out of stock";
    }

    /**
     * Get Product Attribute Value
     *
     * @param $id
     * @param $name
     *
     * @return mixed
     */
    public function getAttributeValue($id, $name)
    {
        return get_post_meta($id, $name, true);
    }

    /**
     * Get UPC value for a product
     *
     * @param $id
     * @param $name
     *
     * @return array|mixed
     */
    public function getUPCForVariableProducts($id, $name)
    {
        $getValue = get_post_meta($id, $name, true);
        $UPCs = array();
        if (strpos($getValue, ';') !== false) {
            $products = explode(";", $getValue);
            foreach ($products as $key => $value) {
                $product = explode('=', $value);
                $UPCs[$product[0]] = $product[1];
            }
            return $UPCs;
        } else {
            return $getValue;
        }
    }

    /**
     * Get Sale price effective date for google
     *
     * @param $id
     * @param $name
     * @return string
     */
    public function sale_price_effective_date($id, $name)
    {
        return ($date = $this->getAttributeValue($id, $name)) ? date_i18n('Y-m-d', $date) : "";
    }


    /**
     * Get All Default WooCommerce Attributes
     * @return bool|array
     */
    public function getAllAttributes()
    {
        global $wpdb;

        //Load the main attributes
        $sql = '
			SELECT attribute_name as name, attribute_type as type
			FROM ' . $wpdb->prefix . 'woocommerce_attribute_taxonomies';
        $data = $wpdb->get_results($sql);
        if (count($data)) {
            foreach ($data as $key => $value) {
                $info["wf_attr_pa_" . $value->name] = $value->name;
            }
            return $info;
        }
        return false;
    }


    /**
     * Get All Custom Attributes
     * @return array|bool
     */
    public function getAllCustomAttributes()
    {
        global $wpdb;
        $info = array();
        //Load the main attributes
        $sql = "SELECT meta_key as name, meta_value as type
			FROM " . $wpdb->prefix . "postmeta" . "  group by meta_key";
        $data = $wpdb->get_results($sql);
        if (count($data)) {
            foreach ($data as $key => $value) {
                //if (substr($value->name, 0, 1) !== "_") { //&& substr($value->name, 0, 13) !== "attribute_pa_"
                $info["wf_cattr_" . $value->name] = $value->name;
                //}
            }
            return $info;
        }
        return false;
    }

    /**
     * Get All Taxonomy
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getAllTaxonomy($name = "color")
    {
        global $wpdb;
        //Load the taxonomies
        $info = false;

        $sql = "SELECT taxo.taxonomy, terms.name, terms.slug FROM $wpdb->term_taxonomy taxo
			LEFT JOIN $wpdb->terms terms ON (terms.term_id = taxo.term_id) GROUP BY taxo.taxonomy";
        $data = $wpdb->get_results($sql);
        if (count($data)) {
            foreach ($data as $key => $value) {
                $info["wf_taxo_" . $value->taxonomy] = $value->taxonomy;
            }
        }

        return $info;
    }

    /**
     * Get Category Mappings
     * @return bool|array
     */
    public function getCustomCategoryMappedAttributes()
    {
        global $wpdb;

        //Load Custom Category Mapped Attributes
        $var = "wf_cmapping_";
        $sql = $wpdb->prepare("SELECT * FROM $wpdb->options WHERE option_name LIKE %s;", $var . "%");
        $data = $wpdb->get_results($sql);
        if (count($data)) {
            foreach ($data as $key => $value) {
                $info[$key] = $value->option_name;
            }

            return $info;
        }

        return false;
    }

    /**
     * Get Dynamic Attribute List
     * @return bool|array
     */
    public function dynamicAttributes()
    {
        global $wpdb;

        # Load Custom Category Mapped Attributes
        $var = "wf_dattribute_";
        $sql = $wpdb->prepare("SELECT * FROM $wpdb->options WHERE option_name LIKE %s;", $var . "%");
        $data = $wpdb->get_results($sql);
        if (count($data)) {
            foreach ($data as $key => $value) {
                $info[$key] = $value->option_name;
            }
            return $info;
        }
        return false;
    }

    public function load_attributes()
    {
        # Get All WooCommerce Attributes
        $vAttributes = $this->getAllAttributes();
        update_option("wpfw_vAttributes", $vAttributes);

        # Get All Custom Attributes
        $customAttributes = $this->getAllCustomAttributes();
        update_option("wpfw_customAttributes", $customAttributes);

        # Get All Custom Taxonomies
        $customTaxonomy = $this->getAllTaxonomy();
        update_option("wpfw_customTaxonomy", $customTaxonomy);

        # Dynamic Attribute List
        $dynamicAttributes = $this->dynamicAttributes();
        update_option("wpfw_dynamicAttributes", $dynamicAttributes);

        # Category Mapping List
        $categoryMapping = $this->getCustomCategoryMappedAttributes();
        update_option("wpfw_categoryMapping", $categoryMapping);
    }

    /**
     * Local Attribute List to map product value with merchant attributes
     *
     * @param string $selected
     *
     * @return string
     */
    public function attributeDropdown($selected = "")
    {
        $attributes = array(
            "id" => "Product Id",
            "title" => "Product Title",
            "description" => "Product Description",
            "short_description" => "Product Short Description",
            "product_type" => "Product Local Category",
            "link" => "Product URL",
            "condition" => "Condition",
            "item_group_id" => "Parent Id [Group Id]",
            "sku" => "SKU",
            "parent_sku" => "Parent SKU",
            "availability" => "Availability",
            "quantity" => "Quantity",
            "price" => "Regular Price",
            "sale_price" => "Sale Price",
            "sale_price_sdate" => "Sale Start Date",
            "sale_price_edate" => "Sale End Date",
            "weight" => "Weight",
            "width" => "Width",
            "height" => "Height",
            "length" => "Length",
            "type" => "Product Type",
            "variation_type" => "Variation Type",
            "visibility" => "Visibility",
            "rating_total" => "Total Rating",
            "rating_average" => "Average Rating",
            "tags" => "Tags",
            "sale_price_effective_date" => "Sale Price Effective Date",

        );

        $images = array(
            "image" => "Main Image",
            "feature_image" => "Featured Image",
            "image_1" => "Additional Image 1",
            "image_2" => "Additional Image 2",
            "image_3" => "Additional Image 3",
            "image_4" => "Additional Image 4",
            "image_5" => "Additional Image 5",
            "image_6" => "Additional Image 6",
            "image_7" => "Additional Image 7",
            "image_8" => "Additional Image 8",
            "image_9" => "Additional Image 9",
            "image_10" => "Additional Image 10",
        );

        # Primary Attributes
        $str = "<option></option>";
        $sltd = "";
        $str .= "<optgroup label='Primary Attributes'>";
        foreach ($attributes as $key => $value) {
            $sltd = "";
            if ($selected == $key) {
                $sltd = 'selected="selected"';
            }
            $str .= "<option $sltd value='$key'>" . $value . "</option>";
        }
        $str .= "</optgroup>";

        # Additional Images
        if ($images) {
            $str .= "<optgroup label='Image Attributes'>";
            foreach ($images as $key => $value) {
                $sltd = "";
                if ($selected == $key) {
                    $sltd = 'selected="selected"';
                }
                $str .= "<option $sltd value='$key'>" . $value . "</option>";
            }
            $str .= "</optgroup>";
        }

        # Get All WooCommerce Attributes
        //$vAttributes = $this->getAllAttributes();
        $vAttributes = get_option("wpfw_vAttributes");
        if ($vAttributes) {
            $str .= "<optgroup label='Product Attributes'>";
            foreach ($vAttributes as $key => $value) {
                $sltd = "";
                if ($selected == $key) {
                    $sltd = 'selected="selected"';
                }
                $str .= "<option $sltd value='$key'>" . $value . "</option>";
            }
            $str .= "</optgroup>";
        }

        # Get All Custom Attributes
        //$customAttributes = $this->getAllCustomAttributes();
        $customAttributes = get_option("wpfw_customAttributes");
        if ($customAttributes) {
            $str .= "<optgroup label='Variation & Custom Attributes'>";
            foreach ($customAttributes as $key => $value) {
                $sltd = "";
                if ($selected == $key) {
                    $sltd = 'selected="selected"';
                }
                $str .= "<option $sltd value='$key'>" . $value . "</option>";
            }
            $str .= "</optgroup>";
        }


        if (get_option('wpfp_activated') && get_option('wpfp_activated') == "Activated") {

            # Get All Custom Taxonomies
            //$customTaxonomy = $this->getAllTaxonomy();
            $customTaxonomy = get_option("wpfw_customTaxonomy");
            if ($customTaxonomy) {
                $str .= "<optgroup label='Custom Taxonomies'>";
                foreach ($customTaxonomy as $key => $value) {
                    $sltd = "";
                    if ($selected == $key) {
                        $sltd = 'selected="selected"';
                    }
                    $str .= "<option $sltd value='$key'>" . $value . "</option>";
                }
                $str .= "</optgroup>";
            }

            # Get all saved option
            $wp_options = get_option("wpfp_option");
            if ($wp_options && count($wp_options)) {
                $str .= "<optgroup label='WP Options'>";
                foreach ($wp_options as $key => $option) {
                    $name = $option['option_name'];
                    $newName = str_replace('wf_option_', '', $name);
                    if ($selected == $name) {
                        $sltd = 'selected="selected"';
                    }
                    $str .= "<option $sltd value=$name>" . $newName . "</option>";
                }
                $str .= "</optgroup>";
            }

            # Dynamic Attribute List
            //$dynamicAttributes = $this->dynamicAttributes();
            $dynamicAttributes = get_option("wpfw_dynamicAttributes");
            if (count($dynamicAttributes) and is_array($dynamicAttributes)) {
                $str .= "<optgroup label='Dynamic Attributes'>";
                foreach ($dynamicAttributes as $key => $value) {
                    $sltd = "";
                    $newValue = str_replace('wf_dattribute_', '', $value);
                    if ($selected == $value) {
                        $sltd = 'selected="selected"';
                    }
                    $str .= "<option $sltd value='$value'>" . $newValue . "</option>";
                }
                $str .= "</optgroup>";
            }

            # Category Mapping List
            //$categoryMapping = $this->getCustomCategoryMappedAttributes();
            $categoryMapping = get_option("wpfw_categoryMapping");
            if (count($categoryMapping) and is_array($categoryMapping)) {
                $str .= "<optgroup label='Category Mapping Attributes'>";
                foreach ($categoryMapping as $key => $value) {
                    $sltd = "";
                    $newValue = str_replace('wf_cmapping_', '', $value);
                    if ($selected == $value) {
                        $sltd = 'selected="selected"';
                    }
                    $str .= "<option $sltd value='$value'>" . $newValue . "</option>";
                }
                $str .= "</optgroup>";
            }
        }
        return $str;
    }
}