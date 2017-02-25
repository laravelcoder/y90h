<?php

class Ghostmonitor_Pixel_Public {
	public function load_script($pixel_id, $event = false, $params='') {
		$http_params = '';
		if($params) {
			$http_params = '&' . http_build_query($params);
			$params = ', ' . json_encode($params);
		}
		return "
<!-- Facebook Pixel Code -->
	<script>
		!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
		n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
		n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
		t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
		document,'script','//connect.facebook.net/en_US/fbevents.js');
		// Insert Your Facebook Pixel ID below. 
		fbq('init', '$pixel_id');
		fbq('track', '$event'$params);
	</script>
<!-- Insert Your Facebook Pixel ID below. -->" . '
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=' . $pixel_id . '&ev=' . $event . $http_params . '&noscript=1"/></noscript>
<!-- End Facebook Pixel Code -->';
	}
	public function load_script_for_ajax() {
		?>
		<script>
			var selectors = '.ajax_add_to_cart';
			jQuery( selectors ).click(function() {
				fbq('track', 'AddToCart',{
					content_ids: this.dataset.product_id,
					content_type: 'product',
				});
			})
		</script>
	<?php
	}
}