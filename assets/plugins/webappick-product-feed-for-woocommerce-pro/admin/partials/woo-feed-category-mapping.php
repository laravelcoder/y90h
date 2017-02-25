<?php
/**
 * Add New Category Mapping View
 *
 * @link       https://webappick.com/
 * @since      1.0.0
 *
 * @package    Woo_Feed
 * @subpackage Woo_Feed/admin/partial
 * @author     Ohidul Islam <wahid@webappick.com>
 */

function categories($child = 0, $par = "", $value = "")
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

    if (!empty($par)) {
        $par = $par . " > ";
    }
    $all_categories = get_categories($args);

    foreach ($all_categories as $cat) {
        $category_id = $cat->term_id;

        if ($child == 0) {
            $class = "treegrid-parent category-mapping";
        } else {
            $class = "treegrid-parent-$child category-mapping";
        }
        ?>
        <tr class="treegrid-1 ">
            <td><i></i><?php echo $par . $cat->name; ?></td>
            <td><input class="<?php echo $class; ?> woo-feed-mapping-input" data-provide="typeahead" autocomplete="off"
                       type="text" name="<?php echo "cmapping[" . $category_id . "]"; ?>"
                       placeholder="<?php echo $par . $cat->name; ?>"
                       classVal="<?php echo $category_id; ?>"
                       value="<?php echo is_array($value) ? $value['cmapping'][$category_id] : ''; ?>"/>
            </td>
        </tr>
        <?php
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
            categories($category_id, $nextParent, $value);
        } else {
        }
    }

}


$dropDown = new Woo_Feed_Dropdown();
$value = "";
if (isset($_GET['action']) && isset($mappingOption)) {
    $option = get_option($mappingOption);
    $value = unserialize($option);
}
?>
<div class="wrap">
    <h2><?php echo _e('Category Mapping', 'woo-feed'); ?></h2>

    <form action="" name="feed" id="category-mapping-form" method="post">

        <table class=" widefat fixed" id="cmTable"
               val="<?php echo plugins_url('woo-feed/admin/partials/woo-feed-search-category.php'); ?>">

            <tbody>
            <tr>
                <td width="30%"><b><?php echo _e('Merchant', 'woo-feed'); ?> <span class="requiredIn">*</span></b></td>
                <td>
                    <select name="mappingprovider" id="providers" class="generalInput" required>
                        <?php echo $dropDown->merchantsDropdown($value['mappingprovider']); ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><b><?php echo _e('Mapping Name', 'woo-feed'); ?><span class="requiredIn">*</span></b></td>
                <td><input required value="<?php echo is_array($value) ? $value['mappingname'] : ''; ?>"
                           name="mappingname" wftitle="Mapping Name should be unique and don't use space. Otherwise it will override the existing Category Mapping. Example: myMappingName or my_mapping_name"
                           type="text" class="generalInput wfmasterTooltip"/>
                </td>
            </tr>

            </tbody>
        </table>
        <br/>
        <table class="table tree widefat fixed ">
            <thead>
            <tr>
                <th><?php echo _e('Local Category', 'woo-feed'); ?></th>
                <th><?php echo _e('Merchant Category', 'woo-feed'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php categories(0, '', $value); ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2">
                    <button type="submit" id="submit"
                            name="<?php echo isset($_GET['action']) ? $_GET['action'] : ''; ?>"
                            class="button button-large button-primary"><?php echo _e('Save Mapping', 'woo-feed'); ?>
                    </button>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
