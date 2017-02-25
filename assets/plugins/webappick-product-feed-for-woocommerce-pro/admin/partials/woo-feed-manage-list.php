<?php

/**
 * Feed List View
 *
 * @link       https://webappick.com/
 * @since      1.0.0
 *
 * @package    Woo_Feed
 * @subpackage Woo_Feed/admin/partial
 * @author     Ohidul Islam <wahid@webappick.com>
 */

$myListTable = new Woo_Feed_Manage_list();

?>

<div class="wrap"><h2><?php echo _e('Manage Feed', 'woo-feed'); ?>
        <a href="<?php echo admin_url('admin.php?page=webappick-product-feed-for-woocommerce-pro/admin/class-woo-feed-admin.php'); ?>"
           class="page-title-action"><?php echo _e('New Feed', 'woo-feed'); ?></a>
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
    <table class=" widefat fixed">
        <thead>
        <tr>
            <th><b><?php echo _e('Auto Update Feed Interval'); ?></b></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <form action="" method="post">
                    <b><?php echo _e('Interval'); ?></b>&nbsp;&nbsp;&nbsp;
                    <select name="wf_schedule" id="wf_schedule">
                        <?php $interval = get_option('wf_schedule'); ?>
                        <option <?php echo  ($interval && $interval == "604800") ? "selected" : ""; ?> value="604800">1 Week</option>
                        <option <?php echo  ($interval && $interval == "86400") ? "selected" : ""; ?> value="86400">24 Hours</option>
                        <option <?php echo  ($interval && $interval == "43200") ? "selected" : ""; ?> value="43200">12 Hours</option>
                        <option <?php echo  ($interval && $interval == "21600") ? "selected" : ""; ?> value="21600">6 Hours</option>
                        <option <?php echo  ($interval && $interval == "3600") ? "selected" : ""; ?> value="3600">1 Hour</option>
                        <option <?php echo  ($interval && $interval == "900") ? "selected" : ""; ?> value="900">15 Minutes</option>
                        <option <?php echo  ($interval && $interval == "300") ? "selected" : ""; ?> value="300">5 Minutes</option>
                    </select>&nbsp;&nbsp;&nbsp;
                    <button type="submit" class="button button-primary"><?php echo _e('Update Interval'); ?></button>
                </form>
            </td>
        </tr>
        </tbody>
    </table>
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
        jQuery('body').find(".single-feed-delete").click(function () {
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