<?php
function register_events_post_type()
{
  $labels = array(
    'name'               => _x('Events', 'post type general name', 'textdomain'),
    'singular_name'      => _x('Event', 'post type singular name', 'textdomain'),
    'menu_name'          => _x('Events', 'admin menu', 'textdomain'),
    'name_admin_bar'     => _x('Event', 'add new on admin bar', 'textdomain'),
    'add_new'            => _x('Add New', 'event', 'textdomain'),
    'add_new_item'       => __('Add New Event', 'textdomain'),
    'new_item'           => __('New Event', 'textdomain'),
    'edit_item'          => __('Edit Event', 'textdomain'),
    'view_item'          => __('View Event', 'textdomain'),
    'all_items'          => __('All Events', 'textdomain'),
    'search_items'       => __('Search Events', 'textdomain'),
    'parent_item_colon'  => __('Parent Events:', 'textdomain'),
    'not_found'          => __('No Events found.', 'textdomain'),
    'not_found_in_trash' => __('No Events found in Trash.', 'textdomain')
  );

  $args = array(
    'labels'             => $labels,
    'public'             => true,
    'show_in_rest'       => true,
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => false,
    'query_var'          => true,
    'rewrite'            => array('slug' => 'event'),
    'capability_type'    => 'post',
    'capabilities' => array(
      'create_posts' => 'edit_posts',
      'edit_posts' => 'edit_posts',
      'edit_post' => 'edit_post',
      'edit_others_posts' => 'edit_posts',
      'publish_posts' => 'edit_posts',
      'delete_others_posts' => 'edit_posts',
    ),
    'map_meta_cap' => true,
    'has_archive'        => true,
    'hierarchical'       => false,
    'menu_position'      => null,
    'supports'           => array('title', 'editor', 'thumbnail')
  );

  register_post_type('event', $args);
}

add_action('init', 'register_events_post_type');
function add_event_submenu()
{
  add_submenu_page(
    'sixonesix-settings', // Parent slug
    'All Events',        // Page title
    '🗒️All Events',        // Menu title
    'edit_posts',     // Capability
    'edit.php?post_type=event' // Menu slug
  );

  add_submenu_page(
    'sixonesix-settings', // Parent slug
    'Add New Event',     // Page title
    '   +Add New Eventt',     // Menu title
    'edit_posts',     // Capability
    'post-new.php?post_type=event' // Menu slug
  );
}

add_action('admin_menu', 'add_event_submenu');


function add_artists_meta_box()
{
  add_meta_box(
    'artists_meta_box', // ID of the meta box
    'Artists', // Title of the meta box
    'display_artists_meta_box', // Callback function to display the meta box
    'event', // Post type where the meta box will appear
    'normal', // Context where the meta box will appear
    'default' // Priority of the meta box
  );
}
add_action('add_meta_boxes', 'add_artists_meta_box');

function display_artists_meta_box($post)
{
  $selected_artists = get_post_meta($post->ID, 'artists', true);
  $all_artists = get_posts(array(
    'post_type' => 'artist',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC'
  ));

  wp_nonce_field(basename(__FILE__), 'artists_nonce');
?>
  <select name="artists[]" multiple="multiple" style="width: 100%;">
    <?php foreach ($all_artists as $artist) : ?>
      <option value="<?php echo esc_attr($artist->ID); ?>" <?php echo in_array($artist->ID, (array) $selected_artists) ? 'selected="selected"' : ''; ?>>
        <?php echo esc_html($artist->post_title); ?>
      </option>
    <?php endforeach; ?>
  </select>
<?php
}

function save_artists_meta_box($post_id)
{
  if (!isset($_POST['artists_nonce']) || !wp_verify_nonce($_POST['artists_nonce'], basename(__FILE__))) {
    return $post_id;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return $post_id;
  }

  if ('event' !== $_POST['post_type'] || !current_user_can('edit_post', $post_id)) {
    return $post_id;
  }

  $artists = isset($_POST['artists']) ? array_map('sanitize_text_field', $_POST['artists']) : array();
  update_post_meta($post_id, 'artists', $artists);
}
add_action('save_post', 'save_artists_meta_box');

$artists = get_post_meta(get_the_ID(), 'artists', true);
if (!empty($artists)) {
  foreach ($artists as $artist_id) {
    $artist = get_userdata($artist_id);
    echo esc_html($artist->display_name) . '<br>';
  }
}
