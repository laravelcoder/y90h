<?php

/**
 * Category Mapping List View
 *
 * @link       https://webappick.com/
 * @since      1.0.0
 *
 * @package    Woo_Feed
 * @subpackage Woo_Feed/admin/partial
 * @author     Ohidul Islam <wahid@webappick.com>
 */

$myListTable = new Woo_Feed_Option_list();

?>

<div class="wrap"><h2><?php echo _e('Option List','woo-feed');?>
        <a href="<?php echo admin_url('admin.php?page=woo_feed_wp_options&action=add-option'); ?>"
           class="page-title-action"><?php echo _e('Add New Option','woo-feed');?></a>
    </h2>

    <?php WPFP()->wpfp_help_section(); ?>

    <?php
    if (isset($_GET['wpf_message']) && $_GET['wpf_message'] === 'success') {
        echo "<div class='updated'><p>" . __(get_option('wpf_message'), 'woo-feed') . "</p></div>";
    } elseif (isset($_GET['wpf_message']) && $_GET['wpf_message'] === 'error') {
        echo "<div id='message' class='error'><p>" . __(get_option('wpf_message'), 'woo-feed') . "</p></div>";
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
        jQuery('body').find(".single-option-delete").click(function () {
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