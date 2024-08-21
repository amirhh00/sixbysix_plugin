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
    'exclude_from_search' => true,
    'show_ui'            => true,
    'show_in_menu'       => false, // Do not show in main menu
    'query_var'          => false,
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
    '🎹All Artists',        // Menu title
    'manage_options',     // Capability
    'edit.php?post_type=artist' // Menu slug
  );

  add_submenu_page(
    'sixonesix-settings', // Parent slug
    'Add New Artist',     // Page title
    '   +Add New Artist',     // Menu title
    'manage_options',     // Capability
    'post-new.php?post_type=artist' // Menu slug
  );
}

add_action('admin_menu', 'add_artist_submenu');

function register_artist_meta()
{
  register_post_meta('artist', 'artist_instagram', array(
    'show_in_rest' => true,
    'single' => true,
    'type' => 'string',
  ));
  register_post_meta('artist', 'artist_spotify', array(
    'show_in_rest' => true,
    'single' => true,
    'type' => 'string',
  ));
}

add_action('init', 'register_artist_meta');

function add_artist_meta_boxes()
{
  add_meta_box('artist_meta_box', __('Artist Meta', 'textdomain'), 'render_artist_meta_box', 'artist', 'side', 'default');
}
add_action('add_meta_boxes', 'add_artist_meta_boxes');

function render_artist_meta_box($post)
{
  $instagram = get_post_meta($post->ID, 'artist_instagram', true);
  $spotify = get_post_meta($post->ID, 'artist_spotify', true);
?>
  <label for="artist_instagram"><?php _e('Instagram', 'textdomain'); ?></label>
  <input type="text" name="artist_instagram" id="artist_instagram" value="<?php echo esc_attr($instagram); ?>" />
  <br />
  <label for="artist_spotify"><?php _e('Spotify', 'textdomain'); ?></label>
  <input type="text" name="artist_spotify" id="artist_spotify" value="<?php echo esc_attr($spotify); ?>" />
<?php
}

function save_artist_meta($post_id)
{
  if (array_key_exists('artist_instagram', $_POST)) {
    update_post_meta($post_id, 'artist_instagram', sanitize_text_field($_POST['artist_instagram']));
  }
  if (array_key_exists('artist_spotify', $_POST)) {
    update_post_meta($post_id, 'artist_spotify', sanitize_text_field($_POST['artist_spotify']));
  }
}
add_action('save_post', 'save_artist_meta');

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


function enqueue_quick_edit_script()
{
  wp_enqueue_media();
  wp_enqueue_script('quick-edit-thumbnail', plugin_dir_url(__FILE__) . '/js/artist-media-uploader.js', array('jquery', 'inline-edit-post'), '', true);
}
add_action('admin_enqueue_scripts', 'enqueue_quick_edit_script');

function add_quick_edit_thumbnail($column_name, $post_type)
{
  if ($column_name != 'thumbnail') return;
?>
  <fieldset class="inline-edit-col-right">
    <div class="inline-edit-col">
      <label>
        <span class="title"><?php _e('Thumbnail', 'textdomain'); ?></span>
        <span class="input-text-wrap">
          <img id="artist_thumbnail_preview" src="" style="max-width: 100px; display: none;" />
          <input type="hidden" name="artist_thumbnail" value="">
          <input type="button" class="button" id="artist_thumbnail_button" value="<?php _e('Select Thumbnail', 'textdomain'); ?>">
        </span>
      </label>
    </div>
  </fieldset>
<?php
}
add_action('quick_edit_custom_box', 'add_quick_edit_thumbnail', 10, 2);

function save_quick_edit_thumbnail($post_id)
{
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
  if (!current_user_can('edit_post', $post_id)) return $post_id;

  if (isset($_POST['artist_thumbnail'])) {
    $thumbnail_id = attachment_url_to_postid($_POST['artist_thumbnail']);
    if ($thumbnail_id) {
      set_post_thumbnail($post_id, $thumbnail_id);
    }
  }
}
add_action('save_post', 'save_quick_edit_thumbnail');
