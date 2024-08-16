<?php
function register_menu_post_type()
{
  $labels = array(
    'name' => 'Menu Items',
    'singular_name' => 'Menu Item',
    'add_new' => 'Add New',
    'add_new_item' => 'Add New Menu Item',
    'edit_item' => 'Edit Menu Item',
    'new_item' => 'New Menu Item',
    'all_items' => 'All Menu Items',
    'view_item' => 'View Menu Item',
    'search_items' => 'Search Menu Items',
    'not_found' => 'No Menu Items found',
    'not_found_in_trash' => 'No Menu Items found in Trash',
    'menu_name' => 'Menu Items'
  );

  $args = array(
    'labels' => $labels,
    'public' => true,
    'has_archive' => true,
    'rewrite' => array('slug' => 'menu-items'),
    'supports' => array('title', 'page-attributes'), // Support title and page attributes for ordering
    'show_in_rest' => false,
    'menu_icon' => 'dashicons-food',
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => false, // Do not show in main menu
    'query_var'          => true,
    'capability_type'    => 'post',
    'hierarchical'       => true, // Enable ordering
    'menu_position'      => null,
  );

  register_post_type('menu_item', $args);
}
add_action('init', 'register_menu_post_type');

function add_menu_submenu()
{
  add_submenu_page(
    'sixonesix-settings', // Parent slug
    'All Menu Items',     // Page title
    'All Menu Items',     // Menu title
    'manage_options',     // Capability
    'edit.php?post_type=menu_item' // Menu slug
  );

  add_submenu_page(
    'sixonesix-settings', // Parent slug
    'Add New Menu Item',  // Page title
    'Add New Menu Item',  // Menu title
    'manage_options',     // Capability
    'post-new.php?post_type=menu_item' // Menu slug
  );
}
add_action('admin_menu', 'add_menu_submenu');

// Add meta boxes for custom fields
function add_menu_item_meta_boxes()
{
  add_meta_box(
    'menu_item_details',
    'Menu Item Details',
    'render_menu_item_meta_box',
    'menu_item',
    'normal',
    'high'
  );
}
add_action('add_meta_boxes', 'add_menu_item_meta_boxes');

function render_menu_item_meta_box($post)
{
  // Retrieve current values
  $days_available = get_post_meta($post->ID, 'days_available', true);
  $time_available = get_post_meta($post->ID, 'time_available', true);
  $link = get_post_meta($post->ID, 'link', true);
  $button_text = get_post_meta($post->ID, 'button_text', true);

  // Nonce field for security
  wp_nonce_field('save_menu_item_meta_box_data', 'menu_item_meta_box_nonce');

  $output = <<<HTML
  <label for="days_available">Days Available:</label>
  <input type="text" id="days_available" name="days_available" value="$days_available" size="25" />

  <label for="time_available">Time Available:</label>
  <input type="text" id="time_available" name="time_available" value="$time_available" size="25" />

  <label for="link">Link:</label>
  <input type="text" id="link" name="link" value="$link" size="25" />

  <!-- a new one for button text -->
  <label for="button_text">Button Text:</label>
  <input type="text" id="button_text" name="button_text" value="$button_text" size="25" />

HTML;

  echo $output;
}

function save_menu_item_meta_box_data($post_id)
{
  // Check nonce for security
  if (!isset($_POST['menu_item_meta_box_nonce']) || !wp_verify_nonce($_POST['menu_item_meta_box_nonce'], 'save_menu_item_meta_box_data')) {
    return;
  }

  // Check if not an autosave
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  // Check user permissions
  if (!current_user_can('edit_post', $post_id)) {
    return;
  }

  // Save custom fields
  if (isset($_POST['days_available'])) {
    update_post_meta($post_id, 'days_available', sanitize_text_field($_POST['days_available']));
  }

  if (isset($_POST['time_available'])) {
    update_post_meta($post_id, 'time_available', sanitize_text_field($_POST['time_available']));
  }

  if (isset($_POST['link'])) {
    update_post_meta($post_id, 'link', sanitize_text_field($_POST['link']));
  }
  if (isset($_POST['button_text'])) {
    update_post_meta($post_id, 'button_text', sanitize_text_field($_POST['button_text']));
  }
}
add_action('save_post', 'save_menu_item_meta_box_data');
