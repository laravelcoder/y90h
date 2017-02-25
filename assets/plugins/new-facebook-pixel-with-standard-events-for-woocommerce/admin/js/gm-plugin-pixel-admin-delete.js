jQuery(document).ready(function($) {
  jQuery('body').on( 'click', '[id^="gm-plugin-pixel-delete-page-"]', function() {
    var page = jQuery(this).attr('page-id');
    var data = {
      action: 'gm_plugin_pixel_delete',
      nonce: gmPluginPixelSaveNonce.nonce,
      gm_pixel_page_url: jQuery('#gm_pixel_page_' + page + '_row').find('.pixel-page-url').val(),
      gm_pixel_page_event: jQuery('#gm_pixel_page_' + page + '_row').find('.pixel-page-event').val()
    };

    jQuery.post(ajaxurl, data, function(response) {
      return;
    });

    var removable = jQuery('#gm_pixel_page_' + page + '_row');
    removable.fadeOut(500, function() { removable.remove(); });
  });
});