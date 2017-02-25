<?php

/**
 * Dynamic Attribute List View
 *
 * @link       https://webappick.com/
 * @since      1.0.0
 *
 * @package    Woo_Feed
 * @subpackage Woo_Feed/admin/partial
 * @author     Ohidul Islam <wahid@webappick.com>
 */

$myListTable = new Woo_Feed_DAttribute_list();

?>

<div class="wrap"><h2><?php echo __('Attribute List', 'woo-feed'); ?>
        <a href="<?php echo admin_url('admin.php?page=woo_feed_manage_attribute&action=add-attribute'); ?>"
           class="page-title-action"><?php echo __('Add New', 'woo-feed'); ?></a>
    </h2>

    <?php WPFP()->wpfp_help_section(); ?>

    <?php
    if (isset($_GET['wpf_message']) && $_GET['wpf_message'] === 'success') {
        echo "<div class='updated'><p>" . __(get_option('wpf_message'), 'woo-feed') . "</p></div>";
    } elseif (isset($_GET['wpf_message']) && $_GET['wpf_message'] === 'error') {
        echo "<div class='error'><p>" . __(get_option('wpf_message'), 'woo-feed') . "</p></div>";
    }
    $myListTable->prepare_items();
    ?>
    <form id="contact-filter" method="post">
        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php //$myListTable->search_box('search', 'search_id'); ?>
        <!-- Now we can render the completed list table -->
        <?php $myListTable->display() ?>
    </form>
</div>

<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('body').find(".single-dattribute-del").click(function () {
            if (confirm('<?php _e('Are You Sure to Delete ?','woo-feed');?>')) {
                var url = jQuery(this).attr('val');
                window.location.href = url;
            }
        });

        jQuery('#doaction').click(function () {
            if (confirm('<?php _e('Are You Sure to Delete ?','woo-feed'); ?>'))
                return true;
            else
                return false;
        });

        jQuery('#doaction2').click(function () {
            if (confirm('<?php _e('Are You Sure to Delete ?','woo-feed'); ?>'))
                return true;
            else
                return false;
        });
    });
</script>