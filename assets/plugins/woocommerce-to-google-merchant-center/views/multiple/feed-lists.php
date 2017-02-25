<?php
$feeds =  array(); //woogool_get_feeds();

$url_feed_download   = home_url( '?woogool_feed_download=true' );

?>
<table class="widefat">
	<thead>
		<tr>
			<th><?php _e( 'Feed Name', 'woogool' ); ?></th>
			<th><?php _e( 'Feed Preview Link', 'woogool' ); ?></th>
			<th><?php _e( 'Feed Download', 'woogool' ); ?></th>
			<th><?php _e( 'Edit', 'woogool' ); ?></th>
			<th><?php _e( 'Delete', 'woogool' ); ?></th>
		<tr>
	</thead>
	<tbody>
	<?php
		foreach ( $feeds as $key => $feed_obj ) {
			$url_feed_download = wp_nonce_url( home_url( '?woogool_feed_download=1&feed_id=' . $feed_obj->ID ), 'woogool_feed_download', 'nonce' );
			$url_feed_preview  = wp_nonce_url( home_url( '?woogool_feed_download=0&feed_id=' . $feed_obj->ID ), 'woogool_feed_download', 'nonce' );
			$edit_url          = admin_url( 'edit.php?post_type=product&page=product_woogool&woogool_tab=woogool_multiple&woogool_sub_tab=new_feed&action=edit&feed_id=' . $feed_obj->ID );
			$delete_url        = admin_url( 'edit.php?post_type=product&page=product_woogool&woogool_tab=woogool_multiple&woogool_sub_tab=feed-lists&action=delete&feed_id=' . $feed_obj->ID );
			?>

			<tr>
				<td><?php echo $feed_obj->post_title; ?></td>
				<td><a href="<?php echo $url_feed_preview; ?>" target="_blank"><?php _e( 'View', 'woogool' ); ?></a></td>
				<td><a href="<?php echo $url_feed_download; ?>"><?php echo $url_feed_download; ?></a></td>
				<td><a href="<?php echo $edit_url; ?>"><?php _e( 'Edit', 'woogool' ); ?></a></td>
				<td><a href="<?php echo $delete_url; ?>"><?php _e( 'Delete', 'woogool' ); ?></a></td>
			</tr>

			<?php
		}
	?>
	<?php 
	if ( ! count( $feeds ) ) {
		?>
		<tr>
			<td collspan="5"><?php _e( 'No Feed Found!','woogool' ); ?></td>
		</tr>

		<?php

	}
	?>
	</tbody>
</table>
<a style="top: 20px;" class="page-title-action" target="_blank" href="http://mishubd.com/product/woogoo/"><?php _e('Update pro version for getting this feature', 'woogool'); ?></a>

