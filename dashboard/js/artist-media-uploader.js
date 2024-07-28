jQuery(document).ready(function ($) {
  var mediaUploader;

  $("#upload_image_button").click(function (e) {
    e.preventDefault();
    if (mediaUploader) {
      mediaUploader.open();
      return;
    }
    mediaUploader = wp.media.frames.file_frame = wp.media({
      title: "Choose Image",
      button: {
        text: "Choose Image",
      },
      multiple: false,
    });
    mediaUploader.on("select", function () {
      var attachment = mediaUploader.state().get("selection").first().toJSON();
      $("#artist_image").val(attachment.url);
      $("#artist_image_preview").attr("src", attachment.url).show();
    });
    mediaUploader.open();
  });
});
