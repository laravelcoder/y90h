<?php

/**
 * The file that defines the merchants attributes dropdown
 *
 * A class definition that includes attributes dropdown and functions used across the admin area.
 *
 * @link       https://webappick.com/
 * @since      1.0.0
 *
 * @package    Woo_Feed
 * @subpackage Woo_Feed/includes
 * @author     Ohidul Islam <wahid@webappick.com>
 */
class Woo_Feed_Dropdown
{

    public $cats = array();

    /**
     * Dropdown of Merchant List
     *
     * @param string $selected
     * @return string
     */
    public function merchantsDropdown($selected = "")
    {
        $attributes = new Woo_Feed_Default_Attributes();
        $str = "<option></option>";
        foreach ($attributes->merchants() as $key => $value) {
            if (substr($key, 0, 2) == "--") {
                $str .= "<optgroup label='$value'>";
            } elseif (substr($key, 0, 2) == "---") {
                $str .= "</optgroup>";
            } else {
                $sltd = "";
                if ($selected == $key)
                    $sltd = 'selected="selected"';
                $str .= "<option $sltd value='$key'>" . $value . "</option>";
            }

        }
        return $str;
    }

    /**
     * Dropdown of Google Attribute List
     *
     * @param string $selected
     * @return string
     */
    public function googleAttributesDropdown($selected = "")
    {
        $attributes = new Woo_Feed_Default_Attributes();
        $str = "<option></option>";
        foreach ($attributes->googleAttributes() as $key => $value) {
            if (substr($key, 0, 2) == "--") {
                $str .= "<optgroup label='$value'>";
            } elseif (substr($key, 0, 2) == "---") {
                $str .= "</optgroup>";
            } else {
                $sltd = "";
                if ($selected == $key)
                    $sltd = 'selected="selected"';
                $str .= "<option $sltd value='$key'>" . $value . "</option>";
            }

        }
        return $str;
    }

    /**
     * Dropdown of Facebook Attribute List
     *
     * @param string $selected
     * @return string
     */
    public function facebookAttributesDropdown($selected = "")
    {
        $attributes = new Woo_Feed_Default_Attributes();
        $str = "<option></option>";
        foreach ($attributes->googleAttributes() as $key => $value) {
            if (substr($key, 0, 2) == "--") {
                $str .= "<optgroup label='$value'>";
            } elseif (substr($key, 0, 2) == "---") {
                $str .= "</optgroup>";
            } else {
                $sltd = "";
                if ($selected == $key)
                    $sltd = 'selected="selected"';
                $str .= "<option $sltd value='$key'>" . $value . "</option>";
            }

        }
        return $str;
    }

    /**
     * Dropdown of Amazon Attribute List
     *
     * @param string $selected
     * @return string
     */
    public function amazonAttributesDropdown($selected = "")
    {
        $attributes = new Woo_Feed_Default_Attributes();
        $str = "<option></option>";
        foreach ($attributes->amazonAttributes() as $key => $value) {
            if (substr($key, 0, 2) == "--") {
                $str .= "<optgroup label='$value'>";
            } elseif (substr($key, 0, 2) == "---") {
                $str .= "</optgroup>";
            } else {
                $sltd = "";
                if ($selected == $key)
                    $sltd = 'selected="selected"';
                $str .= "<option $sltd value='$key'>" . $value . "</option>";
            }

        }
        return $str;
    }

    /**
     * Dropdown of Pricegraber Attribute List
     *
     * @param string $selected
     * @return string
     */
    public function priceGrabberAttributesDropdown($selected = "")
    {
        $attributes = new Woo_Feed_Default_Attributes();
        $str = "<option></option>";
        foreach ($attributes->priceGrabberAttribute() as $key => $value) {
            if (substr($key, 0, 2) == "--") {
                $str .= "<optgroup label='$value'>";
            } elseif (substr($key, 0, 2) == "---") {
                $str .= "</optgroup>";
            } else {
                $sltd = "";
                if ($selected == $key)
                    $sltd = 'selected="selected"';
                $str .= "<option $sltd value='$key'>" . $value . "</option>";
            }

        }
        return $str;
    }

    /**
     * Dropdown of Nextag Attribute List
     *
     * @param string $selected
     * @return string
     */
    public function nextagAttributesDropdown($selected = "")
    {
        $attributes = new Woo_Feed_Default_Attributes();
        $str = "<option></option>";
        foreach ($attributes->nextagAttribute() as $key => $value) {
            if (substr($key, 0, 2) == "--") {
                $str .= "<optgroup label='$value'>";
            } elseif (substr($key, 0, 2) == "---") {
                $str .= "</optgroup>";
            } else {
                $sltd = "";
                if ($selected == $key)
                    $sltd = 'selected="selected"';
                $str .= "<option $sltd value='$key'>" . $value . "</option>";
            }

        }
        return $str;
    }

    /**
     * Dropdown of kelkoo Attribute List
     *
     * @param string $selected
     * @return string
     */
    public function kelkooAttributesDropdown($selected = "")
    {
        $attributes = new Woo_Feed_Default_Attributes();
        $str = "<option></option>";
        foreach ($attributes->kelkooAttribute() as $key => $value) {
            if (substr($key, 0, 2) == "--") {
                $str .= "<optgroup label='$value'>";
            } elseif (substr($key, 0, 2) == "---") {
                $str .= "</optgroup>";
            } else {
                $sltd = "";
                if ($selected == $key)
                    $sltd = 'selected="selected"';
                $str .= "<option $sltd value='$key'>" . $value . "</option>";
            }

        }
        return $str;
    }

    /**
     * Dropdown of Shopzilla Attribute List
     *
     * @param string $selected
     * @return string
     */
    public function shopzillaAttributesDropdown($selected = "")
    {
        $attributes = new Woo_Feed_Default_Attributes();
        $str = "<option></option>";
        foreach ($attributes->shopzillaAttribute() as $key => $value) {
            if (substr($key, 0, 2) == "--") {
                $str .= "<optgroup label='$value'>";
            } elseif (substr($key, 0, 2) == "---") {
                $str .= "</optgroup>";
            } else {
                $sltd = "";
                if ($selected == $key)
                    $sltd = 'selected="selected"';
                $str .= "<option $sltd value='$key'>" . $value . "</option>";
            }

        }
        return $str;
    }

    /**
     * Dropdown of Shopping.com Attribute List
     *
     * @param string $selected
     * @return string
     */
    public function shoppingAttributesDropdown($selected = "")
    {
        $attributes = new Woo_Feed_Default_Attributes();
        $str = "<option></option>";
        foreach ($attributes->shoppingAttribute() as $key => $value) {
            if (substr($key, 0, 2) == "--") {
                $str .= "<optgroup label='$value'>";
            } elseif (substr($key, 0, 2) == "---") {
                $str .= "</optgroup>";
            } else {
                $sltd = "";
                if ($selected == $key)
                    $sltd = 'selected="selected"';
                $str .= "<option $sltd value='$key'>" . $value . "</option>";
            }

        }
        return $str;
    }

    /**
     * Dropdown of Shopmania Attribute List
     *
     * @param string $selected
     * @return string
     */
    public function shopmaniaAttributesDropdown($selected = "")
    {
        $attributes = new Woo_Feed_Default_Attributes();
        $str = "<option></option>";
        foreach ($attributes->shopmaniaAttribute() as $key => $value) {
            if (substr($key, 0, 2) == "--") {
                $str .= "<optgroup label='$value'>";
            } elseif (substr($key, 0, 2) == "---") {
                $str .= "</optgroup>";
            } else {
                $sltd = "";
                if ($selected == $key)
                    $sltd = 'selected="selected"';
                $str .= "<option $sltd value='$key'>" . $value . "</option>";
            }

        }
        return $str;
    }


    /**
     * Dropdown of Bing.com Attribute List
     *
     * @param string $selected
     * @return string
     */
    public function bingAttributesDropdown($selected = "")
    {
        $attributes = new Woo_Feed_Default_Attributes();
        $str = "<option></option>";
        foreach ($attributes->bingAttribute() as $key => $value) {
            if (substr($key, 0, 2) == "--") {
                $str .= "<optgroup label='$value'>";
            } elseif (substr($key, 0, 2) == "---") {
                $str .= "</optgroup>";
            } else {
                $sltd = "";
                if ($selected == $key)
                    $sltd = 'selected="selected"';
                $str .= "<option $sltd value='$key'>" . $value . "</option>";
            }

        }
        return $str;
    }

    /**
     * Dropdown of become.com Attribute List
     *
     * @param string $selected
     * @return string
     */
    public function becomeAttributesDropdown($selected = "")
    {
        $attributes = new Woo_Feed_Default_Attributes();
        $str = "<option></option>";
        foreach ($attributes->becomeAttribute() as $key => $value) {
            if (substr($key, 0, 2) == "--") {
                $str .= "<optgroup label='$value'>";
            } elseif (substr($key, 0, 2) == "---") {
                $str .= "</optgroup>";
            } else {
                $sltd = "";
                if ($selected == $key)
                    $sltd = 'selected="selected"';
                $str .= "<option $sltd value='$key'>" . $value . "</option>";
            }

        }
        return $str;
    }

    /**
     * Dropdown of connexity.com Attribute List
     *
     * @param string $selected
     * @return string
     */
    public function connexityAttributesDropdown($selected = "")
    {
        $attributes = new Woo_Feed_Default_Attributes();
        $str = "<option></option>";
        foreach ($attributes->becomeAttribute() as $key => $value) {
            if (substr($key, 0, 2) == "--") {
                $str .= "<optgroup label='$value'>";
            } elseif (substr($key, 0, 2) == "---") {
                $str .= "</optgroup>";
            } else {
                $sltd = "";
                if ($selected == $key)
                    $sltd = 'selected="selected"';
                $str .= "<option $sltd value='$key'>" . $value . "</option>";
            }

        }
        return $str;
    }

    public function woo_feed_get_wp_options()
    {
        global $wpdb;
        $var1 = "_transient";
        $var2 = "_site_transient";
        $sql1 = $wpdb->prepare("SELECT * FROM $wpdb->options WHERE option_name NOT LIKE %s AND option_name NOT LIKE %s;", $var1 . "%", $var2 . "%");
        $result1 = $wpdb->get_results($sql1);
        $str = "<option></option>";
        if (count($result1)) {
            foreach ($result1 as $key => $value) {
                $str .= "<option value=" . $value->option_id . "-" . $value->option_name . " > $value->option_name</option > ";
            }
        }
        return $str;
    }


    public function categories($child = 0, $par = "", $value = "")
    {

        $taxonomy = 'product_cat';
        $orderby = 'term_group';
        $show_count = 1;      // 1 for yes, 0 for no
        $pad_counts = 1;      // 1 for yes, 0 for no
        $hierarchical = 1;      // 1 for yes, 0 for no
        $title = '';
        $empty = 0;

        $args = array(
            'taxonomy' => $taxonomy,
            'parent' => $child,
            'orderby' => $orderby,
            'show_count' => $show_count,
            'pad_counts' => $pad_counts,
            'hierarchical' => $hierarchical,
            'title_li' => $title,
            'hide_empty' => $empty
        );


        $all_categories = get_categories($args);

        foreach ($all_categories as $cat) {
            $category_id = $cat->term_id;

            if ($child == 0) {
                $class = "treegrid-parent ";
            } else {
                $class = "treegrid-parent-$child ";
            }



            $this->cats[$cat->slug] = $cat->name ;

            $nextParent = $par . $cat->name;
            $args2 = array(
                'taxonomy' => $taxonomy,
                'parent' => $category_id,
                'orderby' => $orderby,
                'show_count' => $show_count,
                'pad_counts' => $pad_counts,
                'hierarchical' => $hierarchical,
                'title_li' => $title,
                'hide_empty' => $empty
            );
            $sub_cats = get_categories($args2);
            if ($sub_cats) {
                $this->categories($category_id, $nextParent, $value);
            } else {
            }
        }
        return $this->cats;
    }
}
