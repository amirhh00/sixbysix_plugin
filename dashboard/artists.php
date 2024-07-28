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
    'show_in_menu'       => true,
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

function add_artist_settings_page()
{
  add_submenu_page(
    'sixonesix-settings',
    'Artists Settings',
    'Artists',
    'manage_options',
    'sixonesix-artist-settings',
    'render_artist_settings_page'
  );
}
add_action('admin_menu', 'add_artist_settings_page');

function enqueue_media_uploader()
{
  wp_enqueue_media();
  wp_enqueue_script('artist-media-uploader', plugin_dir_url(__FILE__) . 'js/artist-media-uploader.js', array('jquery'), null, true);
}
add_action('admin_enqueue_scripts', 'enqueue_media_uploader');

function render_artist_settings_page()
{
  // Handle form submission for adding/editing an artist
  if (isset($_POST['submit_artist'])) {
    $artist_name = sanitize_text_field($_POST['artist_name']);
    $instagram_link = esc_url_raw($_POST['instagram_link']);
    $spotify_link = esc_url_raw($_POST['spotify_link']);
    $date = sanitize_text_field($_POST['date']);
    $artist_image = esc_url_raw($_POST['artist_image']);
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

    // Insert or update artist post
    $post_data = array(
      'post_title' => $artist_name,
      'post_type' => 'artist',
      'post_status' => 'publish',
    );

    if ($post_id) {
      $post_data['ID'] = $post_id;
      wp_update_post($post_data);
    } else {
      $post_id = wp_insert_post($post_data);
    }

    if ($post_id) {
      // Save custom fields
      update_post_meta($post_id, 'instagram_link', $instagram_link);
      update_post_meta($post_id, 'spotify_link', $spotify_link);
      update_post_meta($post_id, 'date', $date);
      update_post_meta($post_id, 'artist_image', $artist_image);
    }
  }

  // Handle delete action
  if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['post_id'])) {
    $post_id = intval($_GET['post_id']);
    wp_delete_post($post_id, true);
  }

  // Fetch artist data for editing
  $edit_artist = null;
  if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['post_id'])) {
    $post_id = intval($_GET['post_id']);
    $edit_artist = get_post($post_id);
    $artist_name = $edit_artist->post_title;
    $instagram_link = get_post_meta($post_id, 'instagram_link', true);
    $spotify_link = get_post_meta($post_id, 'spotify_link', true);
    $date = get_post_meta($post_id, 'date', true);
    $artist_image = get_post_meta($post_id, 'artist_image', true);
  }

?>
  <div class="wrap">
    <?php
    if (isset($edit_artist)) {
      $heading = __('Edit Artist', 'textdomain');
    } else {
      $heading = __('Add Artist', 'textdomain');
      if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['post_id'])) {
        $heading = __('Artist Deleted', 'textdomain');
        // remove action and post_id query parameters
        $redirect_url = remove_query_arg(array('action', 'post_id'));
        echo '<script>window.location.href = "' . $redirect_url . '";</script>';
      }
    }
    ?>
    <h1><?php echo $heading; ?></h1>
    <form method="post" action="">
      <input type="hidden" name="post_id" value="<?php echo isset($post_id) ? esc_attr($post_id) : ''; ?>" />
      <table class="form-table">
        <tr>
          <th scope="row"><label for="artist_name"><?php _e('Artist Name', 'textdomain'); ?></label></th>
          <td><input name="artist_name" type="text" id="artist_name" value="<?php echo isset($artist_name) ? esc_attr($artist_name) : ''; ?>" class="regular-text" required /></td>
        </tr>
        <tr>
          <th scope="row"><label for="instagram_link"><?php _e('Instagram Link', 'textdomain'); ?></label></th>
          <td><input name="instagram_link" type="url" id="instagram_link" value="<?php echo isset($instagram_link) ? esc_attr($instagram_link) : ''; ?>" class="regular-text" /></td>
        </tr>
        <tr>
          <th scope="row"><label for="spotify_link"><?php _e('Spotify Link', 'textdomain'); ?></label></th>
          <td><input name="spotify_link" type="url" id="spotify_link" value="<?php echo isset($spotify_link) ? esc_attr($spotify_link) : ''; ?>" class="regular-text" /></td>
        </tr>
        <tr>
          <th scope="row"><label for="date"><?php _e('Date', 'textdomain'); ?></label></th>
          <td><input name="date" type="date" id="date" value="<?php echo isset($date) ? esc_attr($date) : ''; ?>" class="regular-text" required /></td>
        </tr>
        <tr>
          <th scope="row"><label for="artist_image"><?php _e('Artist Image', 'textdomain'); ?></label></th>
          <td>
            <input type="hidden" name="artist_image" id="artist_image" value="<?php echo isset($artist_image) ? esc_attr($artist_image) : ''; ?>" />
            <button type="button" class="button" id="upload_image_button"><?php _e('Upload Image', 'textdomain'); ?></button>
            <img id="artist_image_preview" src="<?php echo isset($artist_image) ? esc_url($artist_image) : ''; ?>" style="max-width: 150px; <?php echo isset($artist_image) ? '' : 'display: none;'; ?>" />
          </td>
        </tr>
      </table>
      <p class="submit">
        <input type="submit" name="submit_artist" id="submit" class="button button-primary" value="<?php echo isset($edit_artist) ? __('Save Changes', 'textdomain') : __('Save Artist', 'textdomain'); ?>">
      </p>
    </form>
    <h2><?php _e('Existing Artists', 'textdomain'); ?></h2>
    <table class="wp-list-table widefat fixed striped">
      <thead>
        <tr>
          <th><?php _e('Artist Name', 'textdomain'); ?></th>
          <th><?php _e('Instagram Link', 'textdomain'); ?></th>
          <th><?php _e('Spotify Link', 'textdomain'); ?></th>
          <th><?php _e('Date', 'textdomain'); ?></th>
          <th><?php _e('Artist Image', 'textdomain'); ?></th>
          <th><?php _e('Actions', 'textdomain'); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php
        $artists = get_posts(array('post_type' => 'artist', 'numberposts' => -1));
        foreach ($artists as $artist) {
          $instagram_link = get_post_meta($artist->ID, 'instagram_link', true);
          $spotify_link = get_post_meta($artist->ID, 'spotify_link', true);
          $date = get_post_meta($artist->ID, 'date', true);
          $artist_image = get_post_meta($artist->ID, 'artist_image', true);
        ?>
          <tr>
            <td><?php echo esc_html($artist->post_title); ?></td>
            <td><a href="<?php echo esc_url($instagram_link); ?>" target="_blank"><?php echo esc_html($instagram_link); ?></a></td>
            <td><a href="<?php echo esc_url($spotify_link); ?>" target="_blank"><?php echo esc_html($spotify_link); ?></a></td>
            <td><?php echo esc_html($date); ?></td>
            <td><?php if ($artist_image) { ?><img src="<?php echo esc_url($artist_image); ?>" style="max-width: 100px;" /><?php } ?></td>
            <td>
              <a href="?page=sixonesix-artist-settings&action=edit&post_id=<?php echo $artist->ID; ?>" class="button"><?php _e('Edit', 'textdomain'); ?></a>
              <a href="?page=sixonesix-artist-settings&action=delete&post_id=<?php echo $artist->ID; ?>" class="button btn-danger" onclick="return confirmDelete();"><?php _e('Delete', 'textdomain'); ?></a>
            </td>
          </tr>
        <?php
        }
        ?>
      </tbody>
    </table>
  </div>
  <script>
    function confirmDelete() {
      if (confirm('<?php _e('Are you sure you want to delete this artist?', 'textdomain'); ?>')) {
        // Perform the delete action
        // After the delete action, remove query parameters
        return true;
      }
      return false;
    }
  </script>
<?php
}

function save_artist_meta_boxes($post_id)
{
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if (!isset($_POST['instagram']) || !isset($_POST['spotify']) || !isset($_POST['date']) || !isset($_POST['image'])) return;
  if (!current_user_can('edit_post', $post_id)) return;

  update_post_meta($post_id, 'instagram', sanitize_text_field($_POST['instagram']));
  update_post_meta($post_id, 'spotify', sanitize_text_field($_POST['spotify']));
  update_post_meta($post_id, 'date', sanitize_text_field($_POST['date']));
  update_post_meta($post_id, 'image', esc_url_raw($_POST['image']));
}
add_action('save_post', 'save_artist_meta_boxes');

function add_artist_meta_boxes()
{
  add_meta_box('artist_meta_box', __('Artist Details', 'textdomain'), 'render_artist_meta_box', 'artist', 'normal', 'high');
}
add_action('add_meta_boxes', 'add_artist_meta_boxes');

function render_artist_meta_box($post)
{
  $instagram = get_post_meta($post->ID, 'instagram', true);
  $spotify = get_post_meta($post->ID, 'spotify', true);
  $date = get_post_meta($post->ID, 'date', true);
  $image = get_post_meta($post->ID, 'image', true);
?>
  <p>
    <label for=" instagram"><?php _e('Instagram Link', 'textdomain'); ?></label>
    <input type="text" name="instagram" id="instagram" value="<?php echo esc_attr($instagram); ?>" class="widefat">
  </p>
  <p>
    <label for="spotify"><?php _e('Spotify Link', 'textdomain'); ?></label>
    <input type="text" name="spotify" id="spotify" value="<?php echo esc_attr($spotify); ?>" class="widefat">
  </p>
  <p>
    <label for="date"><?php _e('Date', 'textdomain'); ?></label>
    <input type="date" name="date" id="date" value="<?php echo esc_attr($date); ?>" class="widefat">
  </p>
  <p>
    <label for="image"><?php _e('Image', 'textdomain'); ?></label>
    <input type="text" name="image" id="image" value="<?php echo esc_url($image); ?>" class="widefat">
    <button type="button" class="button select-image"><?php _e('Select Image', 'textdomain'); ?></button>
  <div class="image-preview">
    <?php if ($image) : ?>
      <img src="<?php echo esc_url($image); ?>" style="max-width: 100px; max-height: 100px;">
    <?php endif; ?>
  </div>
  </p>
  <script>
    jQuery(document).ready(function($) {
      $('.select-image').on('click', function(e) {
        e.preventDefault();
        var button = $(this);
        var custom_uploader = wp.media({
          title: '<?php _e('Select Image', 'textdomain'); ?>',
          library: {
            type: 'image'
          },
          button: {
            text: '<?php _e('Use this image', 'textdomain'); ?>'
          },
          multiple: false
        }).on('select', function() {
          var attachment = custom_uploader.state().get('selection').first().toJSON();
          button.prev('input').val(attachment.url);
          button.next('.image-preview').html('<img src="' + attachment.url + '" style="max-width: 100px; max-height: 100px;">');
        }).open();
      });
    });
  </script>
<?php
}
