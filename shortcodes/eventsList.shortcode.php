<?php

add_shortcode('eventsl', 'display_custom_posts');

function display_custom_posts()
{
  // Get query parameters
  $post_ids = isset($_GET['post_id']) ? explode(',', $_GET['post_id']) : null;
  $posts_per_page = isset($_GET['posts_per_page']) ? intval($_GET['posts_per_page']) : 5;
  $pageNumber = isset($_GET['page']) ? intval($_GET['page']) : 1;

  // Set up query arguments
  $args = array(
    'post_type' => 'event',
    'post_status'    => array('publish', 'future'),
    'posts_per_page' => $posts_per_page,
    'paged' => $pageNumber,
  );

  if ($post_ids) {
    $args['post__in'] = $post_ids;
  }

  // Execute the query
  $query = new WP_Query($args);
  $output = '<div class="custom-posts">';

  if ($query->have_posts()) {
    while ($query->have_posts()) {
      $query->the_post();
      $post = $query->post;
      $featured_image = get_the_post_thumbnail_url($post->ID, 'full');
      $output .= <<<HTML
      <div class="event">
        <h2><a href="{$post->guid}">{$post->post_title}</a></h2>
        <img src="{$featured_image}" alt="{$post->post_title}">
        <div>{$post->post_content}</div>
      </div>
      HTML;
    }
  } else {
    $output .= 'No posts found.';
  }

  $output .= '</div>';
  // $output .= '<pre>' . print_r($args, true) . '</pre>';
  // $output .= '<pre>' . print_r($post, true) . '</pre>';

  wp_reset_postdata();

  return $output;
}
