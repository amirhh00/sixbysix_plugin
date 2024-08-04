<?php
// Add menu item to the dashboard
function sixonesix_add_admin_menu()
{
  add_menu_page(
    'SixOneSix Settings',
    'SixOneSix',
    'manage_options',
    'sixonesix-settings',
    'sixonesix_settings_page',
    'dashicons-admin-site',
    20
  );
}
add_action('admin_menu', 'sixonesix_add_admin_menu');

function sixonesix_settings_page()
{
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
}
add_action('admin_init', 'sixonesix_settings_init');
