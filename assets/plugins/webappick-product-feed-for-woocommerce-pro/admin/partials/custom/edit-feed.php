<?php
$dropDown = new Woo_Feed_Dropdown();
$product = new Woo_Feed_Products();
$product->load_attributes();
?>
<div class="wrap" id="Feed">
<h2><?php echo _e('WooCommerce Product Feed', 'woo-feed'); ?></h2>

<form action="" name="feed" id="updatefeed"
      method="post">

<table class=" widefat fixed">
    <thead>
    <tr>
        <td colspan="2"><b><?php echo _e('Content Settings', 'woo-feed'); ?></b></td>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td width="30%"><b><?php echo _e('Template', 'woo-feed'); ?> <span class="requiredIn">*</span></b></td>
        <td>
            <select name="provider" id="provider" class="generalInput">
                <?php echo $dropDown->merchantsDropdown(esc_attr($feedRules['provider'])); ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><b><?php echo _e('File Name', 'woo-feed'); ?> <span class="requiredIn">*</span></b></td>
        <td><input name="filename"
                   value="<?php echo isset($feedRules['filename']) ? esc_attr($feedRules['filename']) : ''; ?>"
                   type="text"
                   class="generalInput"/></td>
    </tr>
    <tr>
        <td><b><?php echo _e('Feed Type', 'woo-feed'); ?> <span class="requiredIn">*</span></b></td>
        <td>
            <select name="feedType" id="feedType" class="generalInput">
                <option <?php echo ($feedRules['feedType'] == "xml") ? 'selected="selected"' : ''; ?> value="xml">
                    XML
                </option>
                <option <?php echo ($feedRules['feedType'] == "csv") ? 'selected="selected"' : ''; ?> value="csv">
                    CSV
                </option>
                <option <?php echo ($feedRules['feedType'] == "txt") ? 'selected="selected"' : ''; ?> value="txt">
                    TXT
                </option>
            </select>
        </td>
    </tr>
    <tr class="itemWrapper" <?php echo ($feedRules['feedType'] != "xml") ? 'style="display: none;"' : ''; ?> >
        <td><b><?php echo _e('Items Wrapper', 'woo-feed'); ?> <span class="requiredIn">*</span></b></td>
        <td><input name="itemsWrapper" type="text"
                   value="<?php echo ($feedRules['feedType'] == "xml") && isset($feedRules['itemsWrapper']) ? esc_attr($feedRules['itemsWrapper']) : 'products'; ?>"
                   class="generalInput" required="required"/>
        </td>
    </tr>
    <tr class="itemWrapper" <?php echo ($feedRules['feedType'] != "xml") ? 'style="display: none;"' : ''; ?>>
        <td><b><?php echo _e('Single Item Wrapper', 'woo-feed'); ?> <span class="requiredIn">*</span></b></td>
        <td><input name="itemWrapper" type="text"
                   value="<?php echo ($feedRules['feedType'] == "xml") && isset($feedRules['itemWrapper']) ? esc_attr($feedRules['itemWrapper']) : 'product'; ?>"
                   class="generalInput" required="required"/>
        </td>
    </tr>
    <tr class="wf_csvtxt" <?php echo ($feedRules['feedType'] == "xml") ? 'style="display: none;"' : ''; ?>>
        <td><b><?php echo _e('Delimiter', 'woo-feed'); ?> <span class="requiredIn">*</span></b></td>
        <td>
            <select name="delimiter" id="delimiter" class="generalInput">
                <option <?php echo isset($feedRules['delimiter']) && $feedRules['delimiter'] == "," ? 'selected="selected"' : ''; ?>
                    value=",">Comma
                </option>
                <option <?php echo isset($feedRules['delimiter']) && $feedRules['delimiter'] == "tab" ? 'selected="selected"' : ''; ?>
                    value="tab">Tab
                </option>
                <option <?php echo isset($feedRules['delimiter']) && $feedRules['delimiter'] == ":" ? 'selected="selected"' : ''; ?>
                    value=":">Colon
                </option>
                <option <?php echo isset($feedRules['delimiter']) && $feedRules['delimiter'] == " " ? 'selected="selected"' : ''; ?>
                    value=" ">Space
                </option>
                <option <?php echo isset($feedRules['delimiter']) && $feedRules['delimiter'] == "|" ? 'selected="selected"' : ''; ?>
                    value="|">Pipe
                </option>
                <option <?php echo isset($feedRules['delimiter']) && $feedRules['delimiter'] == ";" ? 'selected="selected"' : ''; ?>
                    value=";">Semi Colon
                </option>
            </select>
        </td>
    </tr>
    <tr class="wf_csvtxt" <?php echo ($feedRules['feedType'] == "xml") ? 'style="display: none;"' : ''; ?>>
        <td><b><?php echo _e('Enclosure', 'woo-feed'); ?> <span class="requiredIn">*</span></b></td>
        <td>
            <select name="enclosure" id="enclosure" class="generalInput">
                <option <?php echo isset($feedRules['enclosure']) && $feedRules['enclosure'] == " " ? 'selected="selected"' : ''; ?>
                    value=" ">None
                </option>
                <option <?php echo isset($feedRules['enclosure']) && $feedRules['enclosure'] == "double" ? 'selected="selected"' : ''; ?>
                    value='double'>"
                </option>
                <option <?php echo isset($feedRules['enclosure']) && $feedRules['enclosure'] == "single" ? 'selected="selected"' : ''; ?>
                    value="single">'
                </option>
            </select>
        </td>
    </tr>
    <tr>
        <td><b>Limit</b></td>
        <td><input type="number" name="Limit" class="generalInput" value="<?php echo isset($feedRules['Limit']) ? esc_attr($feedRules['Limit']) : ''; ?>"/></td>
    </tr>
    <tr>
        <td><b>Offset</b></td>
        <td><input type="number" name="Offset" class="generalInput" value="<?php echo isset($feedRules['Offset']) ? esc_attr($feedRules['Offset']) : ''; ?>"/></td>
    </tr>
    <tr>
        <td><b>Categories</b></td>
        <td>
            <select name="categories[]" class="wf_categories generalInput" multiple>
                <?php
                $categories = $dropDown->categories();
                if (count($categories)) {
                    $str = "";
                    foreach ($categories as $key => $value) {
                        $selected="";
                        if(in_array($key,$feedRules['categories'])){
                            $selected="selected='selected'";
                        }
                        $str .= "<option $selected value=$key>$value</option>";
                    }
                    echo $str;
                }
                ?>
            </select>
        </td>
    </tr>
    </tbody>
</table>
<br/>

<ul class="wf_tabs">
<li>
    <input type="radio" name="wf_tabs" id="tab1" checked/>
    <label class="wf-tab-name" for="tab1">Feed Config</label>

    <div id="wf-tab-content1" class="wf-tab-content">
        <table class="table tree widefat fixed sorted_table mtable" width="100%" id="table-1">
            <thead>
            <tr>
                <th></th>
                <th><?php echo ucfirst($provider); ?> <?php echo _e('Attributes', 'woo-feed'); ?></th>
                <th><?php echo _e('Prefix', 'woo-feed'); ?></th>
                <th><?php echo _e('Type', 'woo-feed'); ?></th>
                <th><?php echo _e('Value', 'woo-feed'); ?></th>
                <th><?php echo _e('Suffix', 'woo-feed'); ?></th>
                <th><?php echo _e('Output Type', 'woo-feed'); ?></th>
                <th><?php echo _e('Output Limit', 'woo-feed'); ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (count($feedRules['mattributes']) > 0) {
                $mAttributes = $feedRules['mattributes'];
                $wooAttributes = $feedRules['attributes'];
                $type = $feedRules['type'];
                $default = $feedRules['default'];
                $prefix = $feedRules['prefix'];
                $suffix = $feedRules['suffix'];
                $outputType = $feedRules['output_type'];
                $limit = $feedRules['limit'];
                $counter = 0;
                foreach ($mAttributes as $merchant => $mAttribute) {

                    ?>
                    <tr>
                        <td>
                            <i class="wf_sortedtable dashicons dashicons-menu"></i>
                        </td>
                        <td>
                            <input type="text" name="mattributes[]" value="<?php echo esc_attr($mAttribute); ?>"
                                   required
                                   class="wf_mattributes"/>
                        </td>
                        <td>
                            <input type="text" name="prefix[]" value="<?php echo esc_html($prefix[$merchant]); ?>"
                                   autocomplete="off"
                                   class="wf_ps"/>
                        </td>
                        <td>
                            <select name="type[]" id="" class="attr_type wfnoempty">
                                <option <?php echo ($type[$merchant] == "attribute") ? 'selected="selected"' : ''; ?>
                                    value="attribute"><?php echo _e('Attribute', 'woo-feed'); ?>
                                </option>
                                <option <?php echo ($type[$merchant] == "pattern") ? 'selected="selected"' : ''; ?>
                                    value="pattern"><?php echo _e('Pattern', 'woo-feed'); ?>
                                </option>
                            </select>

                        </td>
                        <td>
                            <select <?php echo ($type[$merchant] == "attribute") ? '' : 'style=" display: none;"'; ?>
                                name="attributes[]" id=""
                                class="wf_attr wf_attributes">
                                <?php echo $product->attributeDropdown(esc_attr($wooAttributes[$merchant])); ?>
                            </select>

                            <input <?php echo ($type[$merchant] == "pattern") ? '' : 'style=" display: none;"'; ?>
                                autocomplete="off"
                                class="wf_default wf_attributes" type="text" name="default[]"
                                value="<?php echo esc_attr($default[$merchant]); ?>"/>

                        </td>
                        <td>
                            <input type="text" name="suffix[]" value="<?php echo esc_html($suffix[$merchant]); ?>"
                                   autocomplete="off"
                                   class="wf_ps"/>
                        </td>

                        <td>
                            <select name="output_type[<?php echo $counter; ?>][]" id=""
                                    class="outputType wfnoempty" <?php echo (count($outputType[$counter]) > 1) ? 'multiple="multiple"' : ''; ?>>
                                <option <?php echo (in_array('1', $outputType[$counter])) ? 'selected="selected"' : ''; ?>
                                    value="1">Default
                                </option>
                                <option <?php echo (in_array('2', $outputType[$counter])) ? 'selected="selected"' : ''; ?>
                                    value="2">Strip Tags
                                </option>
                                <option <?php echo (in_array('3', $outputType[$counter])) ? 'selected="selected"' : ''; ?>
                                    value="3">UTF-8 Encode
                                </option>
                                <option <?php echo (in_array('4', $outputType[$counter])) ? 'selected="selected"' : ''; ?>
                                    value="4">htmlentities
                                </option>
                                <option <?php echo (in_array('5', $outputType[$counter])) ? 'selected="selected"' : ''; ?>
                                    value="5">Integer
                                </option>
                                <option <?php echo (in_array('6', $outputType[$counter])) ? 'selected="selected"' : ''; ?>
                                    value="6">Price
                                </option>
                                <option <?php echo (in_array('7', $outputType[$counter])) ? 'selected="selected"' : ''; ?>
                                    value="7">Remove Space
                                </option>
                                <option <?php echo (in_array('8', $outputType[$counter])) ? 'selected="selected"' : ''; ?>
                                    value="8">CDATA
                                </option>
                            </select>
                            <i class="dashicons dashicons-editor-expand expandType"></i>
                            <i style="display: none;"
                               class="dashicons dashicons-editor-contract contractType"></i>
                        </td>
                        <td>
                            <input type="text" name="limit[]" value="<?php echo esc_attr($limit[$merchant]); ?>"
                                   autocomplete="off"
                                   class="wf_ps"/>
                        </td>
                        <td>
                            <i class="delRow dashicons dashicons-trash"></i>
                        </td>
                    </tr>

                    <?php
                    $counter++;
                }
            }
            ?>

            </tbody>
            <tfoot>
            <tr>
                <td colspan="3">
                    <button type="button" class="button-small button-primary" id="wf_newRow">
                        <?php echo _e('Add New Row', 'woo-feed'); ?>
                    </button>
                </td>
                <td colspan="6">

                </td>
            </tr>
            </tfoot>
        </table>
        <table class=" widefat fixed">
            <tr>
                <td align="left" class="">
                    <div class="makeFeedResponse"></div>
                    <div class="makeFeedComplete"></div>
                </td>
                <td align="right">
                    <button name="<?php echo isset($_GET['action']) ? $_GET['action'] : ''; ?>"
                            type="submit" id="submit"
                            class="wfbtn updatefeed" >
                        <?php echo _e('Update and Generate Feed', 'woo-feed'); ?>
                    </button>
                </td>
            </tr>
        </table>
    </div>
</li>
<?php if (get_option('wpfp_activated') && get_option('wpfp_activated') == "Activated") { ?>
    <li>
        <input type="radio" name="wf_tabs" id="tab2"/>
        <label class="wf-tab-name" for="tab2"><?php echo _e('Filter', 'woo-feed'); ?></label>

        <div id="wf-tab-content2" class="wf-tab-content">
            <table class="table tree widefat fixed sorted_table mtable" width="100%" id="table-filter">
                <thead>
                <tr>
                    <th></th>
                    <th><?php echo _e('Attributes', 'woo-feed'); ?></th>
                    <th><?php echo _e('Condition', 'woo-feed'); ?></th>
                    <th><?php echo _e('Value', 'woo-feed'); ?></th>
                    <th></th>
                </tr>
                <tr style="border-bottom: 2px solid #ccc">
                    <td><?php echo _e('Filter', 'woo-feed'); ?></td>
                    <td colspan="4">
                        <select name="filterType" class="">
                            <option <?php echo ($feedRules['filterType'] == "2") ? 'selected="selected"' : ''; ?>
                                value="2">
                                <?php echo _e('Together', 'woo-feed'); ?>
                            </option>
                            <option <?php echo ($feedRules['filterType'] == "1") ? 'selected="selected"' : ''; ?>
                                value="1">
                                <?php echo _e('Individually', 'woo-feed'); ?>
                            </option>
                        </select>
                    </td>
                </tr>
                </thead>
                <tbody>

                <tr style="display:none;" class="daRow">
                    <td>
                        <i class="wf_sortedtable dashicons dashicons-menu"></i>
                    </td>
                    <td>
                        <select name="fattribute[]" id="" disabled required class="fsrow">
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
                        <input type="text" name="filterCompare[]" disabled autocomplete="off" class="fsrow"/>
                    </td>
                    <td>
                        <i class="delRow dashicons dashicons-trash"></i>
                    </td>
                </tr>
                <?php
                //echo "<pre>";print_r($feedRules['fattribute']);
                if (isset($feedRules['fattribute']) && count($feedRules['fattribute'])) {
                    foreach ($feedRules['fattribute'] as $fkey => $fvalue) {
                        if (!empty($fvalue)) {
                            $condition = $feedRules['condition'];
                            $filterCompare = $feedRules['filterCompare'];
                            ?>
                            <tr class="daRow">
                                <td>
                                    <i class="wf_sortedtable dashicons dashicons-menu"></i>
                                </td>
                                <td>
                                    <select name="fattribute[]" id="" required class="fsrow">
                                        <?php echo $product->attributeDropdown(esc_attr($fvalue)); ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="condition[]" class="">
                                        <option <?php echo $condition[$fkey] == "==" ? 'selected="selected"' : ''; ?>
                                            value="=="><?php echo _e('is / equal', 'woo-feed'); ?>
                                        </option>
                                        <option <?php echo $condition[$fkey] == "!=" ? 'selected="selected"' : ''; ?>
                                            value="!="><?php echo _e('is not / not equal', 'woo-feed'); ?>
                                        </option>
                                        <option <?php echo $condition[$fkey] == ">=" ? 'selected="selected"' : ''; ?>
                                            value=">="><?php echo _e('equals or greater than', 'woo-feed'); ?>
                                        </option>
                                        <option <?php echo $condition[$fkey] == ">" ? 'selected="selected"' : ''; ?>
                                            value=">"><?php echo _e('greater than', 'woo-feed'); ?>
                                        </option>
                                        <option <?php echo $condition[$fkey] == "<=" ? 'selected="selected"' : ''; ?>
                                            value="<="><?php echo _e('equals or less than', 'woo-feed'); ?>
                                        </option>
                                        <option <?php echo $condition[$fkey] == "<" ? 'selected="selected"' : ''; ?>
                                            value="<"><?php echo _e('less than', 'woo-feed'); ?>
                                        </option>
                                        <option <?php echo $condition[$fkey] == "contains" ? 'selected="selected"' : ''; ?>
                                            value="contains"><?php echo _e('contains', 'woo-feed'); ?>
                                        </option>
                                        <option <?php echo $condition[$fkey] == "nContains" ? 'selected="selected"' : ''; ?>
                                            value="nContains"><?php echo _e('does not contain', 'woo-feed'); ?>
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text"
                                           value="<?php echo isset($filterCompare[$fkey]) ? esc_attr($filterCompare[$fkey]) : ""; ?>"
                                           name="filterCompare[]" autocomplete="off" class="fsrow"/>
                                </td>
                                <td>
                                    <i class="delRow dashicons dashicons-trash"></i>
                                </td>
                            </tr>
                        <?php
                        }
                    }
                }
                ?>

                </tbody>
                <tfoot>
                <tr>
                    <td>
                        <button type="button" class="button-small button-primary" id="wf_newFilter">
                            <?php echo _e('Add New Condition', 'woo-feed'); ?>
                        </button>
                    </td>
                    <td colspan="4">

                    </td>
                </tr>
                </tfoot>
            </table>
            <table class=" widefat fixed">
                <tr>
                    <td align="left" class="">
                        <div class="makeFeedResponse"></div>
                        <div class="makeFeedComplete"></div>
                    </td>
                    <td align="right">
                        <button name="<?php echo isset($_GET['action']) ? $_GET['action'] : ''; ?>"
                                type="submit" id="submit"
                                class="wfbtn updatefeed">
                            <?php echo _e('Update and Generate Feed', 'woo-feed'); ?>
                        </button>
                    </td>
                </tr>
            </table>
        </div>
    </li>
<?php } ?>
<li>
    <input type="radio" name="wf_tabs" id="tab3"/>
    <label class="wf-tab-name" for="tab3"><?php echo _e('FTP', 'woo-feed'); ?></label>

    <div id="wf-tab-content3" class="wf-tab-content">
        <table class="table widefat fixed mtable" width="100%">
            <tbody>
            <tr>
                <td><?php echo _e('Enabled', 'woo-feed'); ?></td>
                <td>
                    <select name="ftpenabled" id="">
                        <option <?php echo ($feedRules['ftpenabled'] == "0") ? 'selected="selected"' : ''; ?>
                            value="0"><?php echo _e('Disabled', 'woo-feed'); ?>
                        </option>
                        <option <?php echo ($feedRules['ftpenabled'] == "1") ? 'selected="selected"' : ''; ?>
                            value="1"><?php echo _e('Enabled', 'woo-feed'); ?>
                        </option>
                    </select>
                </td>
            </tr>

            <tr>
                <td><?php echo _e('Host Name', 'woo-feed'); ?></td>
                <td><input type="text" value="<?php echo esc_attr($feedRules['ftphost']); ?>" name="ftphost"
                           autocomplete="off"/></td>
            </tr>
            <tr>
                <td><?php echo _e('User Name', 'woo-feed'); ?></td>
                <td><input type="text" value="<?php echo esc_attr($feedRules['ftpuser']); ?>" name="ftpuser"
                           autocomplete="off"/></td>
            </tr>
            <tr>
                <td><?php echo _e('Password', 'woo-feed'); ?></td>
                <td><input type="password" value="<?php echo esc_attr($feedRules['ftppassword']); ?>"
                           name="ftppassword" autocomplete="off"/></td>
            </tr>
            <tr>
                <td><?php echo _e('Path', 'woo-feed'); ?></td>
                <td><input type="text" value="<?php echo esc_attr($feedRules['ftppath']); ?>" name="ftppath"
                           autocomplete="off"/></td>
            </tr>

            </tbody>
        </table>
        <table class=" widefat fixed">
            <tr>
                <td align="left" class="">
                    <div class="makeFeedResponse"></div>
                    <div class="makeFeedComplete"></div>
                </td>
                <td align="right">
                    <button name="<?php echo isset($_GET['action']) ? $_GET['action'] : ''; ?>"
                            type="submit" id="submit"
                            class="wfbtn updatefeed">
                        <?php echo _e('Update and Generate Feed', 'woo-feed'); ?>
                    </button>
                </td>
            </tr>
        </table>
    </div>
</li>

</ul>
</form>
</div>