<?php $dropDown = new Woo_Feed_Dropdown();?>
<div class="wrap">
    <h2><?php echo _e('Add Option', 'woo-feed'); ?></h2>

    <form action="" name="feed" method="post">

        <table class="widefat fixed">
            <tbody>
            <tr>
                <td width="40%"><b><?php echo _e('Option Name', 'woo-feed'); ?> <span class="requiredIn">*</span></b></td>
                <td>
                    <select name="wpfp_option" id="providers" class="generalInput" required>
                        <?php echo $dropDown->woo_feed_get_wp_options(); ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button type="submit" class="button button-primary"><?php echo _e('Add Option'); ?></button>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>