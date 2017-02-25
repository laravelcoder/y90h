jQuery(document).ready(function($) {
  jQuery('.gm-plugin-pixel-save').on( 'click', function() {
    var data = {
      action: 'gm_plugin_pixel_save',
      gm_pixel_id: jQuery('#gm_pixel_id').val(),
      gm_pixel_enabled: Number(jQuery('#gm_pixel_enabled').is(':checked')),
      nonce: gmPluginPixelSaveNonce.nonce,
    };

    var pageSettings = jQuery('.gm_pixel_page_setting').map(function(i) {
        _this = jQuery(this);
        var obj = {};
        obj['url'] = _this.find('.pixel-page-url').val();
        obj['event'] = _this.find('.pixel-page-event').val();
        return obj;
    }).get();

    var wooSettings = {};

    jQuery('[id^="gm_pixel_woocommerce_setting_"]').map(function(i) {
        _this = jQuery(this);
        var obj = {};
        obj['page'] = _this.attr('page-type');
        obj['checked'] = Number(_this.is(':checked'));
        return obj;
    }).get().forEach(function(elem) {
      wooSettings[elem.page] = elem.checked;
    });

    pageSettings = JSON.stringify(pageSettings);
    wooSettings = JSON.stringify(wooSettings);

    jQuery.extend(data, {gm_pixel_page_settings: pageSettings});
    jQuery.extend(data, {gm_pixel_woocommerce_settings: wooSettings});

    jQuery.post(ajaxurl, data, function(response) {
      alert(response);
    });
  });
});