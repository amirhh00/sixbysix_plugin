<?php
function register_artist_post_type()
{
  $labels = array(
    'name'               => _x('Artists', 'post type general name', 'textdomain'),
    'singular_name'      => _x('Artist', 'post type singular name', 'textdomain'),
    'menu_name'          => _x('Artists', 'admin menu', 'textdomain'),
    'name_admin_bar'     => _x('Artist', 'add new on admin bar', 'textdomain'),
    'add_new'            => _x('Add New', 'artist', 'textdomain'),
    'add_new_item'       => __('Add New Artist', 'textdomain'),
    'new_item'           => __('New Artist', 'textdomain'),
    'edit_item'          => __('Edit Artist', 'textdomain'),
    'view_item'          => __('View Artist', 'textdomain'),
    'all_items'          => __('All Artists', 'textdomain'),
    'search_items'       => __('Search Artists', 'textdomain'),
    'parent_item_colon'  => __('Parent Artists:', 'textdomain'),
    'not_found'          => __('No artists found.', 'textdomain'),
    'not_found_in_trash' => __('No artists found in Trash.', 'textdomain')
  );

  $args = array(
    'labels'             => $labels,
    'public'             => true,
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => false, // Do not show in main menu
    'query_var'          => true,
    'rewrite'            => array('slug' => 'artist'),
    'capability_type'    => 'post',
    'has_archive'        => true,
    'hierarchical'       => false,
    'menu_position'      => null,
    'supports'           => array('title', 'editor', 'thumbnail')
  );

  register_post_type('artist', $args);
}

add_action('init', 'register_artist_post_type');

function add_artist_submenu()
{
  add_submenu_page(
    'sixonesix-settings', // Parent slug
    'All Artists',        // Page title
    'All Artists',        // Menu title
    'manage_options',     // Capability
    'edit.php?post_type=artist' // Menu slug
  );

  add_submenu_page(
    'sixonesix-settings', // Parent slug
    'Add New Artist',     // Page title
    'Add New Artist',     // Menu title
    'manage_options',     // Capability
    'post-new.php?post_type=artist' // Menu slug
  );
}

add_action('admin_menu', 'add_artist_submenu');

// Add thumbnail column to the artist post type list table
function add_artist_columns($columns)
{
  $columns = array(
    'cb' => '<input type="checkbox" />',
    'title' => __('Title', 'textdomain'),
    'thumbnail' => __('Thumbnail', 'textdomain'),
    'date' => __('Date', 'textdomain')
  );
  return $columns;
}

add_filter('manage_edit-artist_columns', 'add_artist_columns');

function display_artist_thumbnail_column($column, $post_id)
{
  if ($column == 'thumbnail') {
    $thumbnail = get_the_post_thumbnail($post_id, array(50, 50));
    echo $thumbnail ? $thumbnail : __('No Thumbnail', 'textdomain');
  }
}

add_action('manage_artist_posts_custom_column', 'display_artist_thumbnail_column', 10, 2);
