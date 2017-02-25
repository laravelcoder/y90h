jQuery(document).ready(function($) {
  jQuery('.gm-plugin-pixel-new-page').on( 'click', function() {
    pagenum = window.gmPixelPagenum;
    var row = '<tr id="gm_pixel_page_' + pagenum + '_row" style="border: 1px dashed black;"><th scope="row"><label for="gm_pixel_page_' + pagenum + '_url">New Page</label></th> \
        <td class="gm_pixel_page_setting"> \
            <input name="gm_pixel_page_' + pagenum + '_url" type="select" id="gm_pixel_page_' + pagenum + '_url" class="pixel-page-url regular-text"> \
            <p class="description">Page URL</p> \
            <hr> \
            <select name="gm_pixel_page_' + pagenum + '_event" id="gm_pixel_page_' + pagenum + '_event" class="pixel-page-event regular-text"> \
                <option value="">- Select Event -</option> \
                <option value="PageView">PageView</option> \
                <option value="ViewContent">ViewContent</option> \
                <option value="Search">Search</option> \
                <option value="AddToCart">AddToCart</option> \
                <option value="AddToWishlist">AddToWishlist</option> \
                <option value="InitiateCheckout">InitiateCheckout</option> \
                <option value="AddPaymentInfo">AddPaymentInfo</option> \
                <option value="Purchase">Purchase</option> \
                <option value="Lead">Lead</option> \
                <option value="CompleteRegistration">CompleteRegistration</option> \
            </select> \
            <p class="description">Event</p> \
            <button class="button button-secondary" id="gm-plugin-pixel-delete-page-' + pagenum + '" page-id=' + pagenum + '>Delete Setting</button> \
        </td></tr>';

    jQuery('.gm-pixel-page-settings').append(row);
    ++window.gmPixelPagenum;
  });
});