(function( $ ) {
	$(window).load(function () {
		$('span.enable_ecommerce_tracking_disctiption_tab').click( function( event ) {
			event.preventDefault();
			var data = $(this);
			$(this).next('p.description').toggle();
			//$('span.advance_extra_flate_rate_disctiption_tab').next('p.description').toggle();
			
		});	
		
	});

})( jQuery );
