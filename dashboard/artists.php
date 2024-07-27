<?php
function sixonesix_add_artist_menu()
{
  add_submenu_page(
    'sixonesix-settings',
    'Artists Settings',
    'Artists',
    'manage_options',
    'sixonesix-artist-settings',
    'sixonesix_artist_settings_page'
  );
}
add_action('admin_menu', 'sixonesix_add_artist_menu');

// Callback for the settings page content
function sixonesix_artist_settings_page()
{
  // Check user capabilities
  if (!current_user_can('manage_options')) {
    return;
  }

  // Handle file upload logic here (ensure to verify and sanitize file inputs)

  // Settings form
?>
  <div class="wrap">
    <h1>Artist Settings</h1>
    <form method="post" action="options.php" enctype="multipart/form-data">
      <?php
      settings_fields('sixonesix_artist_options_group');
      do_settings_sections('sixonesix-artist-settings');
      submit_button('Save Artists');
      ?>
    </form>
  </div>
<?php
}

// Step 1: Register a custom post type for artists
function sixonesix_register_artist_post_type()
{
  $args = array(
    'public' => true,
    'label'  => 'Artists',
    'supports' => array('title', 'editor', 'thumbnail', 'page-attributes'), // Enable support for page attributes including order
    'show_in_menu' => true,
    'show_in_rest' => true, // Enable Gutenberg editor
    'hierarchical' => false,
    'has_archive' => true,
    'rewrite' => array('slug' => 'artists'), // Customize the permalink structure
  );
  register_post_type('sixonesix_artist', $args);
}
add_action('init', 'sixonesix_register_artist_post_type');

// Step 2: The 'page-attributes' support added in the custom post type registration enables the order functionality in the admin UI automatically.

// Step 3: Adjust the query for displaying artists according to their 'menu_order'
function display_artists_ordered()
{
  $args = array(
    'post_type' => 'sixonesix_artist',
    'orderby' => 'menu_order',
    'order' => 'ASC',
    'posts_per_page' => -1, // Retrieve all artists
  );
  $artists_query = new WP_Query($args);

  if ($artists_query->have_posts()) {
    while ($artists_query->have_posts()) {
      $artists_query->the_post();
      // Output the artist's title, thumbnail, etc.
      echo '<div class="artist">';
      the_title('<h2>', '</h2>');
      if (has_post_thumbnail()) {
        the_post_thumbnail('thumbnail');
      }
      the_content();
      echo '</div>';
    }
  } else {
    echo '<p>No artists found.</p>';
  }

  // Reset Post Data
  wp_reset_postdata();
}

// Register settings, sections, and fields
function sixonesix_artist_settings_init()
{
  register_setting('sixonesix_artist_options_group', 'sixonesix_artist_options', 'sanitize_artist_options');

  add_settings_section(
    'sixonesix_artist_settings_section',
    'Artist Details',
    'sixonesix_artist_settings_section_cb',
    'sixonesix-artist-settings'
  );

  add_settings_field(
    'sixonesix_artist_name',
    'Artist Name',
    'sixonesix_artist_name_cb',
    'sixonesix-artist-settings',
    'sixonesix_artist_settings_section'
  );
}
add_action('admin_init', 'sixonesix_artist_settings_init');

function sixonesix_artist_settings_section_cb()
{
  echo '<p>Enter the details of the artists here.</p>';
}

function sixonesix_enqueue_media_uploader()
{
  wp_enqueue_media();
  wp_enqueue_script(
    'sixonesix-media-uploader',
    plugin_dir_url(__FILE__) . './js/media-uploader.js',
    array('jquery'),
    null,
    true
  );
}
add_action('admin_enqueue_scripts', 'sixonesix_enqueue_media_uploader');

function sixonesix_artist_name_cb()
{
  $options = get_option('sixonesix_artist_options');
?>
  <div id="artists-wrapper">
    <?php
    if (!empty($options['artists'])) {
      foreach ($options['artists'] as $index => $artist) {
    ?>
        <div style="margin: 30px 0;" class="artist-entry">
          <input type="text" placeholder="Artist Name" name="sixonesix_artist_options[artists][<?php echo $index; ?>][name]" value="<?php echo esc_attr($artist['name'] ?? ''); ?>" />
          <input type="text" placeholder="Instagram Link" name="sixonesix_artist_options[artists][<?php echo $index; ?>][instagram]" value="<?php echo esc_attr($artist['instagram'] ?? ''); ?>" />
          <input type="text" placeholder="Spotify Link" name="sixonesix_artist_options[artists][<?php echo $index; ?>][spotify]" value="<?php echo esc_attr($artist['spotify'] ?? ''); ?>" />
          <input type="date" placeholder="Date" name="sixonesix_artist_options[artists][<?php echo $index; ?>][date]" value="<?php echo esc_attr($artist['date'] ?? ''); ?>" />
          <button type="button" class="button select-image">Select Image</button>
          <input type="hidden" name="sixonesix_artist_options[artists][<?php echo $index; ?>][image]" class="image-data" value="<?php echo esc_attr($artist['image'] ?? ''); ?>" />
          <div class="image-preview"><?php if (!empty($artist['image'])) {
                                        echo '<img src="' . esc_url($artist['image']) . '" style="max-width: 100px; max-height: 100px;">';
                                      } ?></div>
          <button class="button action" type="button" onclick="removeArtistEntry(this)">Remove</button>
        </div>
    <?php
      }
    }
    ?>
  </div>
  <button class="button action" style="margin: 10px 0;" type="button" onclick="addArtistEntry()">Add Another Artist</button>
  <script>
    function addArtistEntry() {
      var wrapper = document.getElementById('artists-wrapper');
      var index = document.querySelectorAll('.artist-entry').length;
      var div = document.createElement("div");
      div.className = "artist-entry";
      div.innerHTML = `
                <input type="text" placeholder="Artist Name" name="sixonesix_artist_options[artists][` + index + `][name]" />
                <input type="text" placeholder="Instagram Link" name="sixonesix_artist_options[artists][` + index + `][instagram]" />
                <input type="text" placeholder="Spotify Link" name="sixonesix_artist_options[artists][` + index + `][spotify]" />
                <input type="date" placeholder="Date" name="sixonesix_artist_options[artists][` + index + `][date]" />
                <button type="button" class="button select-image">Select Image</button>
                <input type="hidden" class="image-data" name="sixonesix_artist_options[artists][` + index + `][image]" />
                <div class="image-preview"></div>
                <button class="button action" type="button" onclick="removeArtistEntry(this)">Remove</button>
            `;
      wrapper.appendChild(div);
      // Re-bind click event to new select-image buttons
      bindSelectImageButtons();
    }

    function removeArtistEntry(button) {
      button.parentElement.remove();
    }

    function bindSelectImageButtons() {
      jQuery('.select-image').off('click').on('click', function(e) {
        e.preventDefault();
        var button = jQuery(this);
        var custom_uploader = wp.media({
          title: 'Select Image',
          library: {
            type: 'image'
          },
          button: {
            text: 'Use this image'
          },
          multiple: false
        }).on('select', function() {
          var attachment = custom_uploader.state().get('selection').first().toJSON();
          button.next('.image-data').val(attachment.url);
          button.siblings('.image-preview').html('<img src="' + attachment.url + '" style="max-width: 100px; max-height: 100px;">');
        }).open();
      });
    }

    jQuery(document).ready(function() {
      bindSelectImageButtons();
    });
  </script>
<?php
}

// Update the sanitize_artist_options function to handle an array of names
function sanitize_artist_options($input)
{
  $new_input = array();
  if (isset($input['artists']) && is_array($input['artists'])) {
    foreach ($input['artists'] as $index => $artist) {
      // Ensure the array for each artist is properly initialized.
      $new_input['artists'][$index] = array(
        'name' => '',
        'instagram' => '',
        'spotify' => '',
        'date' => '',
        'image' => ''
      );

      // Sanitize the artist name.
      if (isset($artist['name'])) {
        $new_input['artists'][$index]['name'] = sanitize_text_field($artist['name']);
      }

      // Sanitize the Instagram URL.
      if (isset($artist['instagram'])) {
        $new_input['artists'][$index]['instagram'] = esc_url_raw($artist['instagram']);
      }

      // Sanitize the Spotify URL.
      if (isset($artist['spotify'])) {
        $new_input['artists'][$index]['spotify'] = esc_url_raw($artist['spotify']);
      }

      // Sanitize the date.
      if (isset($artist['date'])) {
        $new_input['artists'][$index]['date'] = sanitize_text_field($artist['date']);
      }

      // Sanitize the image URL.
      if (isset($artist['image'])) {
        $new_input['artists'][$index]['image'] = esc_url_raw($artist['image']);
      }
    }
  }

  return $new_input;
}
