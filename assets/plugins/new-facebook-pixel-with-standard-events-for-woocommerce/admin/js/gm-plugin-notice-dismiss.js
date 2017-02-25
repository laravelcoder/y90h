jQuery(document).on( 'click', '.ghostmonitor-notice .notice-dismiss', function() {

    jQuery.ajax({
        url: ajaxurl,
        data: {
            action: 'gm_plugin_notice_dismiss'
        }
    })
})