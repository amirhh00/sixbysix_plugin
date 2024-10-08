<?php
// Add menu item to the dashboard
function sixonesix_add_admin_menu()
{
  $svg_icon = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents(plugin_dir_path(__FILE__) . '../assets/images/logo.svg'));

  add_menu_page(
    'SixOneSix Settings',
    'SixOneSix',
    'edit_posts',
    'sixonesix-settings',
    'sixonesix_settings_page',
    $svg_icon,
    20
  );
}
add_action('admin_menu', 'sixonesix_add_admin_menu');

function sixonesix_enqueue_media_uploader()
{
  wp_enqueue_media();
  wp_enqueue_script('sixonesix-media-uploader', plugin_dir_url(__FILE__) . 'js/media-uploader.js', array('jquery'), null, true);
}
add_action('admin_enqueue_scripts', 'sixonesix_enqueue_media_uploader');

function sixonesix_settings_page()
{
  $isAdmin = current_user_can('manage_options');
  if (!$isAdmin) {
    echo '<h1>Sorry, you do not have permission to access this page.</h1>';
    return;
  }
?>
  <div class="wrap">
    <h1>SixOneSix Settings</h1>
    <form method="post" action="options.php">
      <?php settings_fields('sixonesix_options_group'); ?>
      <table class="form-table">
        <tr valign="top">
          <th scope="row">Floating reservation Text</th>
          <td><input placeholder="Book from here" type="text" name="sixonesix_button_text" value="<?php echo esc_attr(get_option('sixonesix_button_text')); ?>" /></td>
        </tr>

        <tr valign="top">
          <th scope="row">Floating reservation Link</th>
          <td><input placeholder="https://domain/reservations" type="text" name="sixonesix_button_link" value="<?php echo esc_attr(get_option('sixonesix_button_link')); ?>" /></td>
        </tr>
        <!-- another row for newsLetter button text -->
        <tr valign="top">
          <th scope="row">NewsLetter Button Text</th>
          <td><input placeholder="NewsLetter Signup" type="text" name="sixonesix_newsletter_btn_text" value="<?php echo esc_attr(get_option('sixonesix_newsletter_btn_text')); ?>" /></td>
        </tr>
        <tr valign="top">
          <th scope="row">NewsLetter Text</th>
          <td><textarea cols="100" rows="5" placeholder="NewsLetter Signup" type="text" name="sixonesix_newsletter_text"><?php echo esc_attr(get_option('sixonesix_newsletter_text')); ?></textarea>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">popup NewsLetter background</th>
          <td>
            <input type="text" id="sixonesix_newsletter_bg" name="sixonesix_newsletter_bg" value="<?php echo esc_attr(get_option('sixonesix_newsletter_bg')); ?>" />
            <button type="button" class="button" id="sixonesix_newsletter_bg_button">Select Image</button>
          </td>
        </tr>
      </table>
      <?php submit_button(); ?>
    </form>
  </div>
<?php
}

function sixonesix_settings_init()
{
  register_setting('sixonesix_options_group', 'sixonesix_button_text');
  register_setting('sixonesix_options_group', 'sixonesix_button_link');
  register_setting('sixonesix_options_group', 'sixonesix_newsletter_btn_text');
  register_setting('sixonesix_options_group', 'sixonesix_newsletter_text');
  register_setting('sixonesix_options_group', 'sixonesix_newsletter_bg');
}
add_action('admin_init', 'sixonesix_settings_init');
