<?php include_once( GHOSTMONITOR_PIXEL_PATH . 'includes/gm-plugin-pixel-helper-functions.php' ); ?>

<h2>Ghostmonitor Pixel Settings</h2>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary gm-plugin-pixel-save" value="Save Settings">
</p>


<table class="form-table">
	<tbody>
	<tr>
		<th scope="row"><label for="gm_pixel_enabled">Enabled</label></th>
		<td>
			<fieldset>
				<label for="gm_pixel_enabled">
					<input name="gm_pixel_enabled" type="checkbox" id="gm_pixel_enabled" <?php echo get_option('gm_pixel_enabled') ? 'checked' : '' ?>>
					Enable GhostMonitor Pixel
				</label>
			</fieldset>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="gm_pixel_id">Your facebook pixel ID</label></th>
		<td>
			<input name="gm_pixel_id" type="text" id="gm_pixel_id" value="<?php echo get_option( 'gm_pixel_id' ) ?>" class="regular-text">
			<p class="description"><a href="http://blog.ghostmonitor.com/2016/01/08/how-to-find-my-facebook-pixel-id/">Where can I find my Pixel ID?</a></p>
		</td>
	</tr>
	</tbody>
</table>

<hr>

<h3>Woocommerce Events</h3>

<table class="form-table">
	<tbody class="gm-pixel-woocommerce-settings">
<?php
	$checked = maybe_unserialize( get_option( 'gm_pixel_woocommerce_settings' ) );
	$checkbox_names = array( 
		'anypage' => array(
			'label' => 'Any WooCommerce Page',
			'excerpt'  => 'ViewContent',
			'desc' => 'Track every visitor comes to WooCommerce site. It doesn’t track pages outside WooCommerce. <a href="http://blog.ghostmonitor.com/2016/01/05/tracking-facebook-pixel-events-on-woocommerce-by-ghostmonitor/">How to track all visitors including blog visitors.</a>',
		),
		'addtocart' => array(
			'label' => 'Add To Cart Page',
			'excerpt'  => 'AddToCart',
			'desc' => 'Track when items are added to a shopping cart (ex: click, landing page on Add to Cart button)',
		),
		'checkout' => array(
			'label' => 'Checkout Page',
			'excerpt'  => 'InitiateCheckout',
			'desc' => 'Track when people enter the checkout flow (ex: click, landing page on checkout button)',
		),
		'thankyou' => array(
			'label' => 'Thank you page (Made purchase)',
			'excerpt'  => 'Purchase',
			'desc' => 'Track purchases or checkout flow completions (ex: Landing on "Thank You" or confirmation page)',
		)
	);
	foreach($checkbox_names as $chk_name => $chk_desc):
?>
	<tr class="gm-pixel-woocommerce-setting">
		<th scope="row" class="titledesc">
			<label for="gm_pixel_woocommerce_setting_<?php echo $chk_name ?>"><?php echo $chk_desc['label'] ?></label>
		</th>
		<td class="gm-pixel-woocommerce-setting-input">
			<fieldset>
			<label for="gm_pixel_woocommerce_setting_<?php echo $chk_name ?>"></label>
				<input name="gm_pixel_woocommerce_setting_<?php echo $chk_name ?>" id="gm_pixel_woocommerce_setting_<?php echo $chk_name ?>" type="checkbox" page-type="<?php echo $chk_name ?>" <?php echo (bool)$checked[$chk_name] ? 'checked' : '' ?>>
				<?php echo $chk_desc['excerpt'] ?>
			</fieldset>
			<p class="description"><?php echo $chk_desc['desc'] ?></p>
		</td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>

<hr>

<h3>Page Settings</h3>

<table class="form-table">
	<tbody class="gm-pixel-page-settings">
	<?php
	$page_data = maybe_unserialize( get_option( 'gm_pixel_page_settings' ) );
	$pagenum = 0;
	do { ?>
	<tr id='gm_pixel_page_<?php echo $pagenum; ?>_row' style="border: 1px dashed black;">
		<th scope="row">
			<label for="gm_pixel_page_<?php echo $pagenum; ?>_url">New Page</label>
		</th>
		<td class="gm_pixel_page_setting">
			<input name="gm_pixel_page_<?php echo $pagenum; ?>_url" type="select" id="gm_pixel_page_<?php echo $pagenum; ?>_url" value="<?php echo gm_pixel_get_page_data($page_data, $pagenum, 'url'); ?>" class="pixel-page-url regular-text">
			<p class="description">Page URL</p>
			<hr>
			<select name="gm_pixel_page_<?php echo $pagenum ?>_event" id="gm_pixel_page_<?php echo $pagenum ?>_event" value="<?php $selected = gm_pixel_get_page_data($page_data, $pagenum, 'event'); echo $selected; ?>" class="pixel-page-event regular-text">
				<option value="">- Select Event -</option>
				<?php
				$events = array('PageView', 'ViewContent', 'Search', 'AddToCart', 'AddToWishlist', 'InitiateCheckout', 'AddPaymentInfo', 'Purchase', 'Lead', 'CompleteRegistration');
				foreach($events as $event) {
					if($selected === $event) {
						echo '<option value="' . $event . '" selected>' . $event . '</option>';
					} else {
						echo '<option value="' . $event . '">' . $event . '</option>';
					}
				} ?>
			</select>
			<p class="description">Event</p>
			<button class="button button-secondary" id="gm-plugin-pixel-delete-page-<?php echo $pagenum ?>" page-id=<?php echo $pagenum ?>>Delete Setting</button>
		</td>
	</tr>
	<?php
	++$pagenum;
	$ok = isset($page_data[$pagenum]);
	} while( $ok ) ?>
	</tbody>
</table>
<script>window.gmPixelPagenum = <?php echo $pagenum ?>;</script>

<button class="button button-secondary gm-plugin-pixel-new-page">New Page</button>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary gm-plugin-pixel-save" value="Save Settings">
</p>