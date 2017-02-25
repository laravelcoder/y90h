<?php
/**
 * Add New Dynamic Attribute View
 *
 * @link       https://webappick.com/
 * @since      1.0.0
 *
 * @package    Woo_Feed
 * @subpackage Woo_Feed/admin/partial
 * @author     Ohidul Islam <wahid@webappick.com>
 */

$dropDown = new Woo_Feed_Dropdown();
$product = new Woo_Feed_Products();
$value = "";
if (isset($_GET['action']) && isset($dAttributeOption)) {
    $option = get_option($dAttributeOption);
    $value = unserialize($option);
}
?>
<div class="wrap">
<h2><?php echo _e('Dynamic Attribute', 'woo-feed'); ?></h2>
<br/>

<form action="" name="feed" id="dynamic-attribute-form" method="post">

<table class="widefat fixed">
    <tbody>
    <tr>
        <td width="30%"><b><?php echo _e('Attribute Name', 'woo-feed'); ?><span class="requiredIn">*</span></b></td>
        <td>
            <input wftitle="Type Attribute Name" type="text" name="wfDAttributeName" required="required"
                   class="wfmasterTooltip"
                   value="<?php echo isset($value['wfDAttributeName']) ? $value['wfDAttributeName'] : ''; ?>"/>
        </td>
    </tr>
    <tr>
        <td><label for="wfDAttributeCode"></label><b><?php echo _e('Attribute Code', 'woo-feed'); ?><span
                    class="requiredIn">*</span></b></td>
        <td>
            <input id="wfDAttributeCode"
                   wftitle="Attribute Code should be unique and don't use space. Otherwise it will override the existing Attribute Code. Example: newPrice or new_price"
                   class="wfmasterTooltip" type="text" name="wfDAttributeCode"
                   value="<?php echo isset($value['wfDAttributeName']) ? $value['wfDAttributeCode'] : ''; ?>"
                   required="required"/>
        </td>
    </tr>

    </tbody>
</table>
<br/>
<table class="widefat fixed mtable2 sorted_table" width="80%" id="table-1">
    <thead>
    <tr>
        <th></th>
        <th><?php echo _e('Attributes', 'woo-feed'); ?></th>
        <th><?php echo _e('Condition', 'woo-feed'); ?></th>
        <th></th>
        <th><?php echo _e('Output Type', 'woo-feed'); ?></th>
        <th><?php echo _e('Value', 'woo-feed'); ?></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <tr style="display:none;" class="daRow">
        <td>
            <i class="wf_sortedtable dashicons dashicons-menu"></i>
        </td>
        <td>
            <select name="attribute[]" id="" disabled required class="wf_attributes fsrow">
                <?php echo $product->attributeDropdown(); ?>
            </select>
        </td>
        <td>
            <select name="condition[]" disabled class="fsrow">
                <option value="=="><?php echo _e('is / equal', 'woo-feed'); ?></option>
                <option value="!="><?php echo _e('is not / not equal', 'woo-feed'); ?></option>
                <option value=">="><?php echo _e('equals or greater than', 'woo-feed'); ?></option>
                <option value=">"><?php echo _e('greater than', 'woo-feed'); ?></option>
                <option value="<="><?php echo _e('equals or less than', 'woo-feed'); ?></option>
                <option value="<"><?php echo _e('less than', 'woo-feed'); ?></option>
                <option value="contains"><?php echo _e('contains', 'woo-feed'); ?></option>
                <option value="nContains"><?php echo _e('does not contain', 'woo-feed'); ?></option>
            </select>
        </td>
        <td>
            <input type="text" name="compare[]" disabled class="fsrow"/>
        </td>
        <td>
            <select name="type[]" class="dType fsrow" disabled>
                <option value="attribute"><?php echo _e('Attribute', 'woo-feed'); ?></option>
                <option value="pattern"><?php echo _e('Pattern', 'woo-feed'); ?></option>
            </select>
        </td>
        <td>
            <select name="value_attribute[]" id="" disabled style="width: 160px;" class="value_attribute fsrow">
                <?php echo $product->attributeDropdown(); ?>
            </select>
            <input type="text" name="value_pattern[]" disabled id="" style="display: none;width: 160px;"
                   class="value_pattern fsrow"/>
        </td>
        <td>
            <span class="delRow dashicons dashicons-trash"></span>
        </td>
    </tr>
    <?php
    if (isset($value['default_value_attribute'])) {
        $default_type = $value['default_type'];
        $default_value_attribute = $value['default_value_attribute'];
        $default_value_pattern = $value['default_value_pattern'];
    }
    if (isset($value['attribute'])) {
        $attributes = $value['attribute'];
        $condition = $value['condition'];
        $compare = $value['compare'];
        $type = $value['type'];
        $value_attribute = $value['value_attribute'];
        $value_pattern = $value['value_pattern'];

        foreach ($attributes as $key => $attribute) {
            ?>

            <tr class="daRow">
                <td>
                    <i class="wf_sortedtable dashicons dashicons-menu"></i>
                </td>
                <td>
                    <select name="attribute[]" id="" required class="wf_attributes">
                        <?php echo $product->attributeDropdown($attributes[$key]); ?>
                    </select>
                </td>
                <td>
                    <select name="condition[]" class="">
                        <option <?php echo isset($condition[$key]) && $condition[$key] == "==" ? 'selected="selected"' : ''; ?>
                            value="=="><?php echo _e('is / equal', 'woo-feed'); ?>
                        </option>
                        <option <?php echo isset($condition[$key]) && $condition[$key] == "!=" ? 'selected="selected"' : ''; ?>
                            value="!="><?php echo _e('is not / not equal', 'woo-feed'); ?>
                        </option>
                        <option <?php echo isset($condition[$key]) && $condition[$key] == ">=" ? 'selected="selected"' : ''; ?>
                            value=">="><?php echo _e('equals or greater than', 'woo-feed'); ?>
                        </option>
                        <option <?php echo isset($condition[$key]) && $condition[$key] == ">" ? 'selected="selected"' : ''; ?>
                            value=">"><?php echo _e('greater than', 'woo-feed'); ?>
                        </option>
                        <option <?php echo isset($condition[$key]) && $condition[$key] == "<=" ? 'selected="selected"' : ''; ?>
                            value="<="><?php echo _e('equals or less than', 'woo-feed'); ?>
                        </option>
                        <option <?php echo isset($condition[$key]) && $condition[$key] == "<" ? 'selected="selected"' : ''; ?>
                            value="<"><?php echo _e('less than', 'woo-feed'); ?>
                        </option>
                        <option <?php echo isset($condition[$key]) && $condition[$key] == "contains" ? 'selected="selected"' : ''; ?>
                            value="contains"><?php echo _e('contains', 'woo-feed'); ?>
                        </option>
                        <option <?php echo isset($condition[$key]) && $condition[$key] == "nContains" ? 'selected="selected"' : ''; ?>
                            value="nContains"><?php echo _e('does not contain', 'woo-feed'); ?>
                        </option>
                    </select>
                </td>
                <td>
                    <input type="text" value="<?php echo isset($compare[$key]) ? $compare[$key] : ''; ?>"
                           name="compare[]" class=""/>
                </td>
                <td>
                    <select name="type[]" class="dType">
                        <option <?php echo isset($type[$key]) && $type[$key] == "attribute" ? 'selected="selected"' : ''; ?>
                            value="attribute"><?php echo _e('Attribute', 'woo-feed'); ?>
                        </option>
                        <option <?php echo isset($type[$key]) && $type[$key] == "pattern" ? 'selected="selected"' : ''; ?>
                            value="pattern"><?php echo _e('Pattern', 'woo-feed'); ?>
                        </option>
                    </select>
                </td>
                <td>
                    <select name="value_attribute[]" id="" class="value_attribute"
                            style="width: 160px;<?php if ($type[$key] != "attribute") echo 'display:none'; ?>">
                        <?php echo $product->attributeDropdown($value_attribute[$key]); ?>
                    </select>
                    <input type="text" name="value_pattern[]" id=""
                           value="<?php echo isset($value_pattern[$key]) ? $value_pattern[$key] : ''; ?>"
                           style="<?php if ($type[$key] != "pattern") echo 'display:none'; ?>;width: 160px;"
                           class="value_pattern"/>

                </td>
                <td>
                    <span class="delRow dashicons dashicons-trash"></span>
                </td>
            </tr>

        <?php
        }
    }
    ?>

    </tbody>
    <tfoot>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td align="right">Default</td>
        <td>
            <select name="default_type" class="dType">
                <option <?php echo isset($default_type) && $default_type == "attribute" ? 'selected="selected"' : ''; ?>
                    value="attribute"><?php echo _e('Attribute', 'woo-feed'); ?>
                </option>
                <option <?php echo isset($default_type) && $default_type == "pattern" ? 'selected="selected"' : ''; ?>
                    value="pattern"><?php echo _e('Pattern', 'woo-feed'); ?>
                </option>
            </select>
        </td>
        <td>
            <select name="default_value_attribute" id=""
                    style="width: 160px;<?php if (isset($default_type) && $default_type != "attribute") echo 'display:none'; ?>"
                    class="value_attribute">
                <?php echo $product->attributeDropdown(isset($default_value_attribute) ? $default_value_attribute : ""); ?>
            </select>
            <input type="text" name="default_value_pattern" id=""
                   value="<?php echo isset($default_value_pattern) ? $default_value_pattern : ''; ?>"
                   style="<?php
                   if (isset($default_type) && $default_type != "pattern") {
                       echo 'display:none';
                   } else if ($_GET['action'] == 'add-attribute') {
                       echo 'display:none';
                   } ?>
                       ;width: 160px;"
                   class="value_pattern"/>
        </td>
        <td></td>
    </tr>
    <tr>
        <td>
            <button type="button" class="button-small button-primary" id="wf_newCon">
                <?php echo _e('Add Condition', 'woo-feed'); ?>
            </button>
        </td>
        <td colspan="6">

        </td>
    </tr>
    </tfoot>
</table>
<table class=" widefat fixed">
    <tr>
        <td align="right">
            <button type="submit" id="wf_submit" class="wfbtn"
                    name="<?php echo isset($_GET['action']) ? $_GET['action'] : ''; ?>">
                <?php echo _e('Save', 'woo-feed'); ?>
            </button>
        </td>
    </tr>
</table>
</form>
</div>
