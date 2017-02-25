<?php

/**
 * A class definition responsible for processing and mapping product according to feed rules and make the feed
 *
 * @link       https://webappick.com/
 * @since      1.0.0
 *
 * @package    Woo_Feed
 * @subpackage Woo_Feed/includes
 * @author     Ohidul Islam <wahid@webappick.com>
 */
class WF_Engine
{
    /**
     * This variable is responsible for mapping store attributes to merchant attribute
     *
     * @since   1.0.0
     * @var     array $mapping Map store attributes to merchant attribute
     * @access  private
     */
    private $mapping;

    /**
     * Store product information
     *
     * @since   1.0.0
     * @var     array $storeProducts
     * @access  public
     */
    private $storeProducts;

    /**
     * New product information
     *
     * @since   1.0.0
     * @var     array $products
     * @access  private
     */
    private $products;

    /**
     * Contain Feed Rules
     *
     * @since   1.0.0
     * @var     array $rules
     * @access  private
     */
    private $rules;

    public function __construct($Products, $rules)
    {
        $this->rules = $rules;
        $this->storeProducts = $Products;
        $productClass = new Woo_Feed_Products();
    }

    /**
     * Return Dynamic Category Mapping Values by Parent Product Id
     *
     * @param   string $mappingName Category Mapping Name
     * @param   int $parent Parent id of the product
     *
     * @return mixed
     */
    public function get_category_mapping_value($mappingName, $parent)
    {
        $getValue = unserialize(get_option($mappingName));
        $mapp = array_reverse($getValue['cmapping'], true);
        $categories = "";
        if (get_the_terms($parent, 'product_cat')) {
            $categories = array_reverse(get_the_terms($parent, 'product_cat'));
        }

        if (!empty($categories) && is_array($categories) && count($categories)) {
            foreach ($categories as $key => $category) {
                if (!empty($mapp[$category->term_id])) {
                    return $mapp[$category->term_id];
                }
            }
        }

        return false;
    }

    /**
     * Format price value
     *
     * @param string $name Attribute Name
     * @param int $conditionName condition
     * @param int $result price
     *
     * @return mixed
     */
    public function price_format($name, $conditionName, $result)
    {
        $plus = "+";
        $minus = "-";
        $percent = "%";

        if (strpos($name, 'price') !== false) {
            if (strpos($result, $plus) !== false && strpos($result, $percent) !== false) {
                $result = str_replace("+", "", $result);
                $result = str_replace("%", "", $result);
                if (is_numeric($result)) {
                    $result = $conditionName + (($conditionName * $result) / 100);
                }
            } elseif (strpos($result, $minus) !== false && strpos($result, $percent) !== false) {
                $result = str_replace("-", "", $result);
                $result = str_replace("%", "", $result);
                if (is_numeric($result)) {
                    $result = $conditionName - (($conditionName * $result) / 100);
                }
            } elseif (strpos($result, $plus) !== false) {
                $result = str_replace("+", "", $result);
                if (is_numeric($result)) {
                    $result = ($conditionName + $result);
                }
            } elseif (strpos($result, $minus) !== false) {
                $result = str_replace("-", "", $result);
                if (is_numeric($result)) {
                    $result = $conditionName - $result;
                }
            }
        }

        return $result;
    }

    /**
     * Get the value of a dynamic attribute
     *
     * @param $attributeName
     * @param $attributes
     *
     * @return mixed|string
     */
    public function get_dynamic_attribute_value($attributeName, $attributes)
    {
        $productClass = new Woo_Feed_Products();
        $getValue = unserialize(get_option($attributeName));
        $wfDAttributeName = $getValue['wfDAttributeName'];
        $wfDAttributeCode = $getValue['wfDAttributeCode'];
        $attribute = $getValue['attribute'];
        $condition = $getValue['condition'];
        $compare = $getValue['compare'];
        $type = $getValue['type'];
        $value_attribute = $getValue['value_attribute'];
        $value_pattern = $getValue['value_pattern'];
        $default_type = $getValue['default_type'];
        $default_value_attribute = $getValue['default_value_attribute'];
        $default_value_pattern = $getValue['default_value_pattern'];

        $result = "";
        if ($attributes['type'] == 'variation') {
            $id = $attributes['item_group_id'];
        } else {
            $id = $attributes['id'];
        }
        # Check If Attribute Code exist
        if ($wfDAttributeCode) {
            if (count($attribute)) {
                foreach ($attribute as $key => $name) {
                    if (!empty($name)) {
                        if (array_key_exists($name, $attributes)) {
                            $conditionName = $attributes[$name];
                        } else if (strpos($name, "wf_attr_") !== false) {
                            $conditionName = implode(',', wc_get_product_terms($id, str_replace("wf_attr_", "", $name), array('fields' => 'names')));
                        } else if (strpos($name, "wf_cattr_") !== false) {
                            $conditionName = $this->get_custom_attribute_value($name, $attributes['id'], $attributes['item_group_id']);
                        } else if (strpos($name, "wf_taxo_") !== false) {
                            $conditionName = $productClass->get_product_term_list($id, str_replace("wf_taxo_", "", $name));
                        } else if (strpos($name, "wf_cmapping_") !== false) {
                            $conditionName = $this->get_category_mapping_value($name, $attributes['item_group_id']);
                        } else if (strpos($name, "wf_dattribute_") !== false) {
                            $conditionName = $this->get_dynamic_attribute_value($name, $attributes);
                        } else if (strpos($name, "wf_option_") !== false) {
                            $optionName = str_replace('wf_option_', '', $name);
                            $optionValue = get_option($optionName);
                            $conditionName = $optionValue;
                        }
                        $conditionCompare = $compare[$key];
                        $conditionValue = "";
                        if (!empty($conditionCompare)) {
                            $conditionCompare = trim($conditionCompare);
                        }

                        if ($type[$key] == 'pattern') {
                            $conditionValue = $value_pattern[$key];
                        } else if ($type[$key] == 'attribute') {
                            if (array_key_exists($value_attribute[$key], $attributes)) {
                                $conditionValue = $attributes[$value_attribute[$key]];
                            } else if (strpos($value_attribute[$key], "wf_attr_") !== false) {
                                $conditionValue = implode(',', wc_get_product_terms($id, str_replace("wf_attr_", "", $value_attribute[$key]), array('fields' => 'names')));
                            } else if (strpos($value_attribute[$key], "wf_cattr_") !== false) {
                                $conditionValue = $this->get_custom_attribute_value($value_attribute[$key], $attributes['id'], $attributes['item_group_id']);
                            } else if (strpos($value_attribute[$key], "wf_taxo_") !== false) {
                                $conditionValue = $productClass->get_product_term_list($id, str_replace("wf_taxo_", "", $value_attribute[$key]));
                            } else if (strpos($value_attribute[$key], "wf_cmapping_") !== false) {
                                $conditionValue = $this->get_category_mapping_value($value_attribute[$key], $attributes['item_group_id']);
                            } else if (strpos($value_attribute[$key], "wf_dattribute_") !== false) {
                                $conditionValue = $this->get_dynamic_attribute_value($value_attribute[$key], $attributes);
                            } else if (strpos($value_attribute[$key], "wf_option_") !== false) {
                                $optionName = str_replace('wf_option_', '', $value_attribute[$key]);
                                $optionValue = get_option($optionName);
                                $conditionValue = $optionValue;
                            }

                        } elseif ($type[$key] == 'remove') {
                            $conditionValue = "";
                        }

                        switch ($condition[$key]) {
                            case "==":
                                if ($conditionName == $conditionCompare) {
                                    $result = $conditionValue;
                                    $result = $this->price_format($name, $conditionName, $result);
                                }
                                break;
                            case "!=":
                                if ($conditionName != $conditionCompare) {
                                    $result = $conditionValue;
                                    $result = $this->price_format($name, $conditionName, $result);
                                }
                                break;
                            case ">=":
                                if ($conditionName >= $conditionCompare) {
                                    $result = $conditionValue;
                                    $result = $this->price_format($name, $conditionName, $result);
                                }

                                break;
                            case "<=":
                                if ($conditionName <= $conditionCompare) {
                                    $result = $conditionValue;
                                    $result = $this->price_format($name, $conditionName, $result);
                                }
                                break;
                            case ">":
                                if ($conditionName > $conditionCompare) {
                                    $result = $conditionValue;
                                    $result = $this->price_format($name, $conditionName, $result);
                                }
                                break;
                            case "<":
                                if ($conditionName < $conditionCompare) {
                                    $result = $conditionValue;
                                    $result = $this->price_format($name, $conditionName, $result);
                                }
                                break;
                            case "contains":
                                if (strpos(strtolower($conditionName), strtolower($conditionCompare)) !== false) {
                                    $result = $conditionValue;
                                    $result = $this->price_format($name, $conditionName, $result);
                                }
                                break;
                            case "nContains":
                                if (strpos(strtolower($conditionName), strtolower($conditionCompare)) === false) {
                                    $result = $conditionValue;
                                    $result = $this->price_format($name, $conditionName, $result);
                                }
                                break;
                            default:
                                break;
                        }
                    }
                }
            }
        }


        if (empty($result)) {
            if ($default_type == 'pattern') {
                $result = $default_value_pattern;
            } else if ($default_type == 'attribute') {
                if (!empty($default_value_attribute)) {
                    if (array_key_exists($default_value_attribute, $attributes)) {
                        $result = $attributes[$default_value_attribute];
                    } else if (strpos($default_value_attribute, "wf_attr_") !== false) {
                        $result = implode(',', wc_get_product_terms($id, str_replace("wf_attr_", "", $default_value_attribute), array('fields' => 'names')));
                    } else if (strpos($default_value_attribute, "wf_cattr_") !== false) {
                        $result = $this->get_custom_attribute_value($default_value_attribute, $attributes['id'], $attributes['item_group_id']);
                    } else if (strpos($default_value_attribute, "wf_taxo_") !== false) {
                        $result = $productClass->get_product_term_list($id, str_replace("wf_taxo_", "", $default_value_attribute));
                    } else if (strpos($default_value_attribute, "wf_cmapping_") !== false) {
                        $result = $this->get_category_mapping_value($default_value_attribute, $attributes['item_group_id']);
                    } else if (strpos($default_value_attribute, "wf_dattribute_") !== false) {
                        $result = $this->get_dynamic_attribute_value($default_value_attribute, $attributes);
                    } else if (strpos($default_value_attribute, "wf_option_") !== false) {
                        $optionName = str_replace('wf_option_', '', $default_value_attribute);
                        $optionValue = get_option($optionName);
                        $result = $optionValue;
                    }
                }
            } elseif ($default_type == 'remove') {
                $result = "";
            }
        }
        return $result;
    }

    /**
     * Filter Products
     *
     * @return bool|array
     */
    public function filter_product($no, $attributes)
    {
        $productClass = new Woo_Feed_Products();
        # Filtering Variable
        $fAttributes = $this->rules['fattribute'];
        $condition = $this->rules['condition'];
        $filterCompare = $this->rules['filterCompare'];
        $filterType = $this->rules['filterType'];

        //foreach ($this->storeProducts as $no => $attributes) {
        if ($attributes['type'] == 'variation') {
            $id = $attributes['item_group_id'];
        } else {
            $id = $attributes['id'];
        }

        $countFilter = 0;
        $matched = 0;
        foreach ($fAttributes as $key => $check) {

            if (array_key_exists($check, $attributes)) {
                $conditionName = $attributes[$check];
            } else if (strpos($check, "wf_attr_") !== false) {
                $conditionName = implode(',', wc_get_product_terms($id, str_replace("wf_attr_", "", $check), array('fields' => 'names')));
            } else if (strpos($check, "wf_cattr_") !== false) {
                $conditionName = $this->get_custom_attribute_value($check, $attributes['id'], $attributes['item_group_id']);
            } else if (strpos($check, "wf_taxo_") !== false) {
                $conditionName = $productClass->get_product_term_list($id, str_replace("wf_taxo_", "", $check));
            } else if (strpos($check, "wf_cmapping_") !== false) {
                $conditionName = $this->get_category_mapping_value($check, $attributes['item_group_id']);
            } else if (strpos($check, "wf_dattribute_") !== false) {
                $conditionName = $this->get_dynamic_attribute_value($check, $attributes);
            } else if (strpos($check, "wf_option_") !== false) {
                $optionName = str_replace('wf_option_', '', $check);
                $optionValue = get_option($optionName);
                $conditionName = $optionValue;
            }

            $con = $condition[$key];
            $conditionCompare = $filterCompare[$key];

            switch ($con) {
                case "==":
                    if (strtolower($conditionName) == strtolower($conditionCompare)) {
                        $matched++;
                    }
                    break;
                case "!=":
                    if (strtolower($conditionName) != strtolower($conditionCompare)) {
                        $matched++;
                    }
                    break;
                case ">=":
                    if (strtolower($conditionName) >= strtolower($conditionCompare)) {
                        $matched++;
                    }
                    break;
                case "<=":
                    if (strtolower($conditionName) <= strtolower($conditionCompare)) {
                        $matched++;
                    }
                    break;
                case ">":
                    if (strtolower($conditionName) > strtolower($conditionCompare)) {
                        $matched++;
                    }
                    break;
                case "<":
                    if (strtolower($conditionName) < strtolower($conditionCompare)) {
                        $matched++;
                    }
                    break;
                case "contains":
                    if (strpos(strtolower($conditionName), strtolower($conditionCompare)) !== false) {
                        $matched++;
                    }
                    break;
                case "nContains":
                    if (strpos(strtolower($conditionName), strtolower($conditionCompare)) === false) {
                        $matched++;
                    }
                    break;
                default:
                    break;
            }
        }

        if ($filterType == 1 && $matched == 0) {
            //unset($this->storeProducts[$no]);
            return false;
        }

        if ($filterType == 2 && count($fAttributes) != $matched) {
            //unset($this->storeProducts[$no]);
            return false;
        }
        return true;
    }

    /**
     * Configure the feed according to the rules
     * @return array
     */
    public function mapProductsByRules()
    {
        $productClass = new Woo_Feed_Products();
        $attributes = $this->rules['attributes'];
        $prefix = $this->rules['prefix'];
        $suffix = $this->rules['suffix'];
        $outputType = $this->rules['output_type'];
        $limit = $this->rules['limit'];
        $merchantAttributes = $this->rules['mattributes'];
        $type = $this->rules['type'];
        $default = $this->rules['default'];
        $feedType = $this->rules['feedType'];

        $wf_attr = array();
        $wf_cattr = array();
        $wf_taxo = array();
        $wf_dattribute = array();
        $wf_cmapping = array();
        $wf_option = array();

        # Map Merchant Attributes and Woo Attributes
        $countAttr = 0;
        update_option('wpf_progress', 'Mapping Attributes');

        if (count($merchantAttributes)) {
            foreach ($merchantAttributes as $key => $attr) {
                if ($type[$key] == 'attribute') {
                    $this->mapping[$attr]['value'] = $attributes[$key];
                    $this->mapping[$attr]['suffix'] = $suffix[$key];
                    $this->mapping[$attr]['prefix'] = $prefix[$key];
                    $this->mapping[$attr]['type'] = $outputType[$key];
                    $this->mapping[$attr]['limit'] = $limit[$key];
                } else if ($type[$key] == 'pattern') {
                    $this->mapping[$attr]['value'] = "wf_pattern_$default[$key]";
                    $this->mapping[$attr]['suffix'] = $suffix[$key];
                    $this->mapping[$attr]['prefix'] = $prefix[$key];
                    $this->mapping[$attr]['type'] = $outputType[$key];
                    $this->mapping[$attr]['limit'] = $limit[$key];
                }
                $countAttr++;
            }
        }

        # Process Dynamic Attributes and Category Mapping
        if (count($this->mapping)) {
            foreach ($this->mapping as $mkey => $attr) {
                if (strpos($attr['value'], 'wf_cmapping_') !== false) {
                    $wf_cmapping[] = $attr['value'];
                }

                if (strpos($attr['value'], 'wf_option_') !== false) {
                    $wf_option[] = $attr['value'];
                }

                if (strpos($attr['value'], 'wf_dattribute_') !== false) {
                    $wf_dattribute[] = $attr['value'];
                }

                if (strpos($attr['value'], 'wf_attr_') !== false) {
                    $wf_attr[] = $attr['value'];
                }

                if (strpos($attr['value'], 'wf_cattr_') !== false) {
                    $wf_cattr[] = $attr['value'];
                }

                if (strpos($attr['value'], 'wf_taxo_') !== false) {
                    $wf_taxo[] = $attr['value'];
                }
            }

            # Init Woo Attributes, Custom Attributes and Taxonomies
            if (count($this->storeProducts)) {
                $i = 0;
                foreach ($this->storeProducts as $key => $value) {
                    if ($value['type'] == 'variation') {
                        $id = $value['item_group_id'];
                    } else {
                        $id = $value['id'];
                    }

                    # Get Dynamic Attribute Values
                    if (count($wf_dattribute)) {
                        // print_r($wf_dattribute);
                        foreach ($wf_dattribute as $wf_dattribute_key => $wf_dattribute_value) {
                            $dAttribute = $this->get_dynamic_attribute_value($wf_dattribute_value, $value);
                            $this->storeProducts[$key][$wf_dattribute_value] = $dAttribute;
                            // print_r($dAttribute);
                        }
                    }

                    # Get Category Mapping Values
                    if (count($wf_cmapping)) {
                        foreach ($wf_cmapping as $wf_cmapping_key => $wf_cmapping_value) {
                            $parent = $value['item_group_id'];
                            $category = $this->get_category_mapping_value($wf_cmapping_value, $parent);
                            $this->storeProducts[$key][$wf_cmapping_value] = $category;
                        }
                    }

                    # Get WP Option Value
                    if (count($wf_option)) {
                        foreach ($wf_option as $wf_option_key => $wf_option_value) {
                            $optionName = str_replace('wf_option_', '', $wf_option_value);
                            $optionValue = get_option($optionName);
                            $this->storeProducts[$key][$wf_option_value] = $optionValue;
                        }
                    }

                    # Get Woo Attributes
                    if (count($wf_attr)) {
                        foreach ($wf_attr as $attr_key => $attr_value) {
                            $this->storeProducts[$key][$attr_value] = implode(',', wc_get_product_terms($id, str_replace("wf_attr_", "", $attr_value), array('fields' => 'names')));
                        }
                    }

                    # Get Custom Attributes
                    if (count($wf_cattr)) {
                        foreach ($wf_cattr as $cattr_key => $cattr_value) {
                            if ($cattr_value == 'wf_cattr_upc') {
                                $get_upc = $productClass->getUPCForVariableProducts($value['id'], 'wf_cattr_upc');
                                if (is_array($get_upc) && array_key_exists($value['id'], $get_upc)) {
                                    $this->storeProducts[$key][$cattr_value] = $get_upc[$value['item_group_id']];
                                } else {
                                    $this->storeProducts[$key][$cattr_value] = $productClass->getAttributeValue($value['id'], str_replace("wf_cattr_", "", $cattr_value));
                                }
                            } else {
                                $this->storeProducts[$key][$cattr_value] = $productClass->getAttributeValue($value['id'], str_replace("wf_cattr_", "", $cattr_value));
                            }
                        }
                    }

                    # Get Taxonomies
                    if (count($wf_taxo)) {
                        foreach ($wf_taxo as $taxo_key => $taxo_value) {
                            $this->storeProducts[$key][$taxo_value] = $productClass->get_product_term_list($value['id'], str_replace("wf_taxo_", "", $taxo_value));
                        }
                    }
                    # Make the product array according to mapping rules
                    $this->productArrayByMapping($key, $i);
                    $i++;
                }
                # Reindex Final Products
                $this->short_final_products();
            }
        }
        return $this->products;
    }

    public function productArrayByMapping($key)
    {
        $value = $this->storeProducts[$key];
        # Make Product feed array according to mapping
        $totalProduct = count($this->storeProducts);
        $count = 1;
        # Filter products by condition
        if (isset($this->rules['fattribute']) && count($this->rules['fattribute'])) {
            update_option('wpf_progress', 'Filtering Products');
            if (!$this->filter_product($key, $value)) {
                return false;
            }
        }
        $i = 0;
        //update_option('wpf_progress', "Processed $count Products Out of $totalProduct");
        foreach ($this->mapping as $attr => $rules) {
            if (array_key_exists($rules['value'], $value)) {
                $output = $value[$rules['value']];
                if (!empty($output)) {
                    foreach ($rules['type'] as $key22 => $value22) {
                        # Format Output According to output type
                        if ($value22 == 2) { # Strip Tags
                            $output = strip_tags(html_entity_decode($output));
                        } elseif ($value22 == 3) { # UTF-8 Encode
                            $output = utf8_encode($output);
                        } elseif ($value22 == 4) { # htmlentities
                            $output = htmlentities($output, ENT_QUOTES, 'UTF-8');
                        } elseif ($value22 == 5) { # Integer
                            $output = absint($output);
                        } elseif ($value22 == 6) { # Price
                            $output = number_format($output, 2, '.', '');
                        } elseif ($value22 == 7) { # Delete Space
                            $output = trim($output);
                        } elseif ($value22 == 8) { # CDATA
                            $output = '<![CDATA[' . $output . ']]>';
                        }
                    }

                    # Format Output According to output limit
                    if (!empty($rules['limit']) && is_numeric($rules['limit']) && strpos($output, "<![CDATA[") !== false) {
                        $output = str_replace(array("<![CDATA[", "]]>"), array("", ""), $output);
                        $output = substr($output, 0, $rules['limit']);
                        $output = '<![CDATA[' . $output . ']]>';
                    } elseif (!empty($rules['limit']) && is_numeric($rules['limit'])) {
                        $output = substr($output, 0, $rules['limit']);
                    }

                    # Prefix and Suffix Assign
                    if (strpos($output, "<![CDATA[") !== false) {
                        $output = str_replace(array("<![CDATA[", "]]>"), array("", ""), $output);
                        $output = $rules['prefix'] . $output . " " . $rules['suffix'];
                        $output = '<![CDATA[' . $output . ']]>';
                    } else {
                        $output = $rules['prefix'] . " " . $output . " " . $rules['suffix'];
                    }
                } else {
                    $output = "";
                }

                $attr = trim($attr);
                $this->products[$key][$attr] = $output;
            } else {
                if (!empty($this->rules['default'][$i])) {
                    $output = str_replace("wf_pattern_", "", $rules['value']);
                    if (!empty($output)) {
                        # Format Output According to output type
                        foreach ($rules['type'] as $key22 => $value22) {
                            if ($value22 == 2) { # Strip Tags
                                $output = strip_tags(html_entity_decode($output));
                            } elseif ($value22 == 3) { # UTF-8 Encode
                                $output = utf8_encode($output);
                            } elseif ($value22 == 4) { # htmlentities
                                $output = htmlentities($output, ENT_QUOTES, 'UTF-8');
                            } elseif ($value22 == 5) { # Integer
                                $output = absint($output);
                            } elseif ($value22 == 6) { # Price
                                $output = number_format($output, 2, '.', '');
                            } elseif ($value22 == 7) { # Delete Space
                                $output = trim($output);
                            } elseif ($value22 == 8) { # CDATA
                                $output = '<![CDATA[' . $output . ']]>';
                            }
                        }

                        # Format Output According to output limit
                        if (!empty($rules['limit']) && is_numeric($rules['limit']) && strpos($output, "<![CDATA[") !== false) {
                            $output = str_replace(array("<![CDATA[", "]]>"), array("", ""), $output);
                            $output = substr($output, 0, $rules['limit']);
                            $output = '<![CDATA[' . $output . ']]>';
                        } elseif (!empty($rules['limit']) && is_numeric($rules['limit'])) {
                            $output = substr($output, 0, $rules['limit']);
                        }

                        # Prefix and Suffix Assign
                        if (strpos($output, "<![CDATA[") !== false) {
                            $output = str_replace(array("<![CDATA[", "]]>"), array("", ""), $output);
                            $output = $rules['prefix'] . $output . " " . $rules['suffix'];
                            $output = '<![CDATA[' . $output . ']]>';
                        } else {
                            $output = $rules['prefix'] . " " . $output . " " . $rules['suffix'];
                        }
                    }
                    $attr = trim($attr);
                    $this->products[$key][$attr] = $output;
                } else {
                    $attr = trim($attr);
                    $this->products[$key][$attr] = "";
                }
            }
            $i++;
        }
        $count++;
        return true;
    }

    public function short_final_products()
    {
        # Reindex product array key
        if (count($this->products)) {
            update_option('wpf_progress', "Shorting Products");
            $array = array();
            $ij = 0;
            foreach ($this->products as $key => $item) {
                $array[$ij] = $item;
                unset($this->products[$key]);
                $ij++;
            }
            return $this->products = $array;
        }
        return false;
    }

    /**
     * Get Custom Attribute value
     *
     * @param $cattr_value
     * @param $id
     * @param $parentId
     * @return mixed
     */
    public function get_custom_attribute_value($cattr_value, $id, $parentId)
    {
        $productClass = new Woo_Feed_Products();
        if ($cattr_value == 'wf_cattr_upc') {
            $get_upc = $productClass->getUPCForVariableProducts($id, 'wf_cattr_upc');
            if (is_array($get_upc) && array_key_exists($id, $get_upc)) {
                return $get_upc[$parentId];
            } else {
                return $productClass->getAttributeValue($id, str_replace("wf_cattr_", "", $cattr_value));
            }
        } else {
            return $productClass->getAttributeValue($id, str_replace("wf_cattr_", "", $cattr_value));
        }
    }

    /**
     * Responsible to make XML feed header
     *
     * @param $wrapper
     * @param string $extraHeader
     *
     * @return string
     */
    public function get_feed_header($wrapper, $extraHeader = "")
    {
        $output = '<?xml version="1.0" encoding="UTF-8" ?>
<' . $wrapper . '>';
        $output .= "\n";
        if (!empty($extraHeader)) {
            $output .= $extraHeader;
            $output .= "\n";
        }

        return $output;
    }

    /**
     * Responsible to make XML feed body
     * @var array $items Product array
     * @return string
     */
    public function get_feed()
    {
        $feed = "";
        $itemsWrapper = $this->rules['itemsWrapper'];
        $itemWrapper = $this->rules['itemWrapper'];
        $extraheader = $this->rules['extraHeader'];
        $feed .= $this->get_feed_header($itemsWrapper, $extraheader);
        if (count($this->storeProducts)) {
            foreach ($this->storeProducts as $each => $products) {
                $feed .= "      <" . $itemWrapper . ">";
                foreach ($products as $key => $value) {
                    if (!empty($value)) {
                        $feed .= $value;
                    }
                }
                $feed .= "\n      </" . $itemWrapper . ">\n";
            }
            $feed .= $this->get_feed_footer($itemsWrapper);

            return $feed;
        }

        return false;
    }

    /**
     * Responsible to make XML feed footer
     * @var $wrapper
     * @return string
     */
    public function get_feed_footer($wrapper)
    {
        $footer = "  </$wrapper>";

        return $footer;
    }


    /**
     * Responsible to make TXT feed
     * @return string
     */
    public function get_txt_feed()
    {
        if (count($this->storeProducts)) {

            if ($this->rules['delimiter'] == 'tab') {
                $delimiter = "\t";
            } else {
                $delimiter = $this->rules['delimiter'];
            }

            if (!empty($this->rules['enclosure'])) {
                $enclosure = $this->rules['enclosure'];
                if ($enclosure == 'double') {
                    $enclosure = '"';
                } elseif ($enclosure == 'single') {
                    $enclosure = "'";
                } else {
                    $enclosure = "";
                }
            } else {
                $enclosure = "";
            }


            if (count($this->storeProducts)) {
                $headers = array_keys($this->storeProducts[0]);
                $feed[] = $headers;
                foreach ($this->storeProducts as $no => $product) {
                    $row = array();
                    foreach ($headers as $key => $header) {
                        $row[] = isset($product[$header]) ? $product[$header] : "";
                    }
                    $feed[] = $row;
                }
                $str = "";
                foreach ($feed as $fields) {
                    $str .= $enclosure . implode("$enclosure$delimiter$enclosure", $fields) . $enclosure . "\n";
                }
                return $str;
            }
        }

        return false;
    }

    /**
     * Responsible to make CSV feed
     * @return string
     */
    public function get_csv_feed()
    {
        if (count($this->storeProducts)) {
            $headers = array_keys($this->storeProducts[0]);
            $feed[] = $headers;
            foreach ($this->storeProducts as $no => $product) {
                $row = array();
                foreach ($headers as $key => $header) {
                    $row[] = isset($product[$header]) ? $product[$header] : "";;
                }
                $feed[] = $row;
            }

            return $feed;
        }
        return false;
    }
}