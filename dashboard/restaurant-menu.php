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
    'supports' => array('title', 'editor', 'thumbnail', 'page-attributes'), // Added editor support
    'show_in_rest' => true,
    'menu_icon' => 'dashicons-food',
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => false, // Do not show in main menu
    'query_var'          => true,
    'capability_type'    => 'post',
    'hierarchical'       => true, // Enable ordering
    'menu_position'      => null,
    'taxonomies'         => array('category', 'post_tag'),
  );

  register_post_type('menu_item', $args);
}
add_action('init', 'register_menu_post_type');

function add_menu_submenu()
{
  add_submenu_page(
    'sixonesix-settings', // Parent slug
    'All Menu Items',     // Page title
    '🍽️All Menu Items',     // Menu title
    'manage_options',     // Capability
    'edit.php?post_type=menu_item' // Menu slug
  );

  add_submenu_page(
    'sixonesix-settings', // Parent slug
    'Add New Menu Item',  // Page title
    '   +Add New Menu',  // Menu title
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

  // Enqueue WordPress media scripts
  wp_enqueue_media();

?>
  <style>
    .menu-item-meta-field {
      margin-bottom: 15px;
    }

    .menu-item-meta-field label {
      display: block;
      font-weight: bold;
      margin-bottom: 5px;
    }

    .menu-item-meta-field input[type="text"] {
      width: 100%;
      padding: 8px;
      border-radius: 4px;
      border: 1px solid #ddd;
    }

    .media-preview {
      margin-top: 10px;
      max-width: 200px;
    }

    .media-preview img {
      max-width: 100%;
      height: auto;
      border: 1px solid #eee;
      padding: 3px;
    }

    .pdf-preview {
      display: inline-block;
      padding: 10px;
      background: #f5f5f5;
      border: 1px solid #ddd;
      border-radius: 4px;
    }

    .media-buttons {
      margin-top: 8px;
    }

    .checkbox-field label {
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }
  </style>

  <div class="menu-item-meta-field checkbox-field">
    <label for="exclusive_menu">
      <input type="checkbox" id="exclusive_menu" name="exclusive_menu" value="1" <?php checked(get_post_meta($post->ID, 'exclusive_menu', true), '1'); ?> />
      Mark as Exclusive Menu
    </label>
  </div>

  <div class="menu-item-meta-field">
    <label for="days_available">Days Available:</label>
    <input type="text" id="days_available" name="days_available" value="<?php echo esc_attr($days_available); ?>" />
  </div>

  <div class="menu-item-meta-field">
    <label for="time_available">Time Available:</label>
    <input type="text" id="time_available" name="time_available" value="<?php echo esc_attr($time_available); ?>" />
  </div>

  <div class="menu-item-meta-field">
    <label for="link">Menu File (Image or PDF):</label>
    <input type="text" id="link" name="link" value="<?php echo esc_attr($link); ?>" readonly />
    <div class="media-buttons">
      <button type="button" class="button" id="upload_file_button">Upload File</button>
      <button type="button" class="button" id="remove_file_button" <?php echo empty($link) ? 'style="display:none;"' : ''; ?>>Remove File</button>
    </div>
    <div id="media_preview" class="media-preview">
      <?php if (!empty($link)):
        $file_type = wp_check_filetype($link);
        if (strpos($file_type['type'], 'image') !== false): ?>
          <img src="<?php echo esc_url($link); ?>" alt="Menu preview" />
        <?php elseif ($file_type['ext'] == 'pdf'): ?>
          <div class="pdf-preview">
            <span class="dashicons dashicons-pdf"></span> PDF File Selected
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>

  <div class="menu-item-meta-field">
    <label for="button_text">Button Text:</label>
    <input type="text" id="button_text" name="button_text" value="<?php echo esc_attr($button_text); ?>" />
  </div>

  <script>
    jQuery(document).ready(function($) {
      $('#upload_file_button').click(function() {
        var mediaUploader = wp.media({
          title: 'Select Menu File',
          button: {
            text: 'Use this file'
          },
          library: {
            type: ['image', 'application/pdf']
          },
          multiple: false
        });

        mediaUploader.on('select', function() {
          var attachment = mediaUploader.state().get('selection').first().toJSON();
          $('#link').val(attachment.url);

          // Show preview based on file type
          var preview = '';
          if (attachment.type === 'image') {
            preview = '<img src="' + attachment.url + '" alt="Menu preview" />';
          } else if (attachment.subtype === 'pdf') {
            preview = '<div class="pdf-preview"><span class="dashicons dashicons-pdf"></span> PDF File Selected</div>';
          }

          $('#media_preview').html(preview);
          $('#remove_file_button').show();
        });

        mediaUploader.open();
      });

      $('#remove_file_button').click(function() {
        $('#link').val('');
        $('#media_preview').empty();
        $(this).hide();
      });
    });
  </script>
<?php
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
  update_post_meta($post_id, 'exclusive_menu', isset($_POST['exclusive_menu']) ? '1' : '0');
}
add_action('save_post', 'save_menu_item_meta_box_data');
