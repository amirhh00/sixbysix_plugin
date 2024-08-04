jQuery(document).ready(function ($) {
  var $wp_inline_edit = inlineEditPost.edit;
  inlineEditPost.edit = function (id) {
    $wp_inline_edit.apply(this, arguments);

    var post_id = 0;
    if (typeof id == "object") {
      post_id = parseInt(this.getId(id));
    }

    if (post_id > 0) {
      var $edit_row = $("#edit-" + post_id);
      var $post_row = $("#post-" + post_id);
      var thumbnail = $post_row.find(".column-thumbnail img").attr("src");

      if (thumbnail) {
        $edit_row.find('input[name="artist_thumbnail"]').val(thumbnail);
        $edit_row.find("#artist_thumbnail_preview").attr("src", thumbnail).show();
      }
    }
  };

  $("#bulk_edit").on("click", function () {
    var $bulk_row = $("#bulk-edit");
    var $post_ids = $bulk_row.find("#bulk-titles").children();
    var post_ids = [];

    $post_ids.each(function () {
      post_ids.push(
        $(this)
          .attr("id")
          .replace(/^(ttle)/i, "")
      );
    });

    var thumbnail = $bulk_row.find('input[name="artist_thumbnail"]').val();

    $.ajax({
      url: ajaxurl,
      type: "POST",
      data: {
        action: "save_bulk_edit",
        post_ids: post_ids,
        artist_thumbnail: thumbnail,
      },
    });
  });

  // Media uploader
  var mediaUploader;
  $(document).on("click", "#artist_thumbnail_button", function (e) {
    e.preventDefault();
    var $button = $(this);
    var $input = $button.siblings('input[name="artist_thumbnail"]');
    var $preview = $button.siblings("#artist_thumbnail_preview");

    if (mediaUploader) {
      mediaUploader.open();
      return;
    }

    mediaUploader = wp.media.frames.file_frame = wp.media({
      title: "Choose Thumbnail",
      button: {
        text: "Choose Thumbnail",
      },
      multiple: false,
    });

    mediaUploader.on("select", function () {
      var attachment = mediaUploader.state().get("selection").first().toJSON();
      $input.val(attachment.url);
      $preview.attr("src", attachment.url).show();
    });

    mediaUploader.open();
  });
});
