<?php
add_shortcode('render_events', 'custom_event_renderer_shortcode');

function custom_event_renderer_shortcode($atts)
{
  // Check if the 'ids' query parameter is set
  if (isset($_GET['ids'])) {
    // Get the IDs from the query parameter
    $ids = explode(',', $_GET['ids']);

    // Fetch the posts
    $posts = get_posts(array(
      'post__in' => $ids,
      'post_status'    => array('publish', 'future'),
      'post_type' => 'event', // Change to your custom post type if needed
      'orderby' => 'post__in', // Preserve the order of IDs
    ));

    // Start output buffering
    ob_start();

    // Render the posts
    if (!empty($posts)) {
      foreach ($posts as $post) {
        setup_postdata($post);
?>
        <div class="event">
          <h2><?php echo get_the_title($post); ?></h2>
          <!-- if feature image exists -->
          <?php if (has_post_thumbnail($post)) : ?>
            <div class="featured-image" style="width: 100%;">
              <img src="<?php echo get_the_post_thumbnail_url($post); ?>" alt="<?php echo get_the_title($post); ?>" style="width: 100%;">
            </div>
          <?php endif; ?>
          <div class="content">
            <?php echo apply_filters('the_content', $post->post_content); ?>
          </div>
        </div>
<?php
      }
      wp_reset_postdata();
    } else {
      echo 'No posts found.';
    }

    // Return the buffered content
    return ob_get_clean();
  }

  return ''; // Return empty if no IDs are provided
}
