jQuery(document).ready(function ($) {

  $('#myprefix_media_manager').click(function (e) {
    e.preventDefault();
    var image_frame;
    if (image_frame) {
      image_frame.open();
    }
    // Define image_frame as wp.media object
    image_frame = wp.media({
      title: 'Select Media',
      multiple: true,
      library: {
        type: 'image',
      }
    });

    image_frame.on('close', function () {
      // On close, get selections and save to the hidden input
      // plus other AJAX stuff to refresh the image preview
      var selection = image_frame.state().get('selection');
      let attachment_ids = selection.map(attachment => attachment['id']);
      $('#js_gallery_attachment_ids').val(attachment_ids.join(','));
      Refresh_Image(attachment_ids);
    });

    image_frame.on('open', function () {
      // On open, get the id from the hidden input
      // and select the appropiate images in the media manager
      var selection = image_frame.state().get('selection');
      var ids = $('#js_gallery_attachment_ids').val().split(',');
      ids.forEach(function (id) {
        var attachment = wp.media.attachment(id);
        attachment.fetch();
        selection.add(attachment ? [attachment] : []);
      });

    });

    image_frame.open();
  });

});

// Ajax request to refresh the image preview
function Refresh_Image(ids) {
  var data = {
    action: 'myprefix_get_image',
    ids
  };

  jQuery.get(ajaxurl, data, function (response) {
    if (response.success === true) {
      jQuery('.image-container').html(response.data.images.join(''));
    }
  });
}