jQuery(document).ready(function ($) {
  $("body").on("click", ".select-image", function (e) {
    e.preventDefault();

    var button = $(this),
      custom_uploader = wp
        .media({
          title: "Select Image",
          library: {
            type: "image",
          },
          button: {
            text: "Use this image",
          },
          multiple: false,
        })
        .on("select", function () {
          var attachment = custom_uploader.state().get("selection").first().toJSON();
          button.next(".image-data").val(attachment.url);
          button.siblings(".image-preview").html('<img src="' + attachment.url + '" style="max-width: 100px; max-height: 100px;">');
        })
        .open();
  });
});
