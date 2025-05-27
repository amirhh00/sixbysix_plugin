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
    <style>
      .form-grid {
        display: grid;
        grid-template-columns: 200px 1fr;
        gap: 15px 20px;
        align-items: start;
        max-width: 800px;
      }

      .form-grid label {
        font-weight: 600;
        padding-top: 5px;
      }

      .form-grid input,
      .form-grid textarea {
        width: 100%;
        max-width: 400px;
      }

      .form-grid .button-group {
        display: flex;
        gap: 10px;
        align-items: center;
      }

      .form-grid .button-group input {
        flex: 1;
      }
    </style>
    <form method="post" action="options.php">
      <?php settings_fields('sixonesix_options_group'); ?>
      <div class="form-grid">
        <div style="width: 100%; margin-top: 20px; grid-column-start: 1; grid-column-end: 3;">
          Floating Reservation Settings
        </div>
        <label for="sixonesix_button_text">Floating reservation Text</label>
        <input placeholder="Book from here" type="text" id="sixonesix_button_text" name="sixonesix_button_text" value="<?php echo esc_attr(get_option('sixonesix_button_text')); ?>" />

        <!-- <label for="sixonesix_button_link">Floating reservation Link</label>
        <input placeholder="https://domain/reservations" type="text" id="sixonesix_button_link" name="sixonesix_button_link" value="<?php echo esc_attr(get_option('sixonesix_button_link')); ?>" /> -->

        <div style="width: 100%;margin-top: 20px; grid-column-start: 1; grid-column-end: 3;">
          NewsLetter Settings
        </div>
        <label for="sixonesix_newsletter_btn_text">NewsLetter Button Text</label>
        <input placeholder="NewsLetter Signup" type="text" id="sixonesix_newsletter_btn_text" name="sixonesix_newsletter_btn_text" value="<?php echo esc_attr(get_option('sixonesix_newsletter_btn_text')); ?>" />

        <label for="sixonesix_newsletter_text">NewsLetter Text</label>
        <textarea cols="50" rows="5" placeholder="NewsLetter Signup" id="sixonesix_newsletter_text" name="sixonesix_newsletter_text"><?php echo esc_attr(get_option('sixonesix_newsletter_text')); ?></textarea>

        <div style="width: 100%; margin-top: 20px; grid-column-start: 1; grid-column-end: 3;">
          Popup Settings
        </div>

        <label for="sixonesix_popup_title">Popup Title</label>
        <input placeholder="Enter popup title here" type="text" id="sixonesix_popup_title" name="sixonesix_popup_title" value="<?php echo esc_attr(get_option('sixonesix_popup_title')); ?>" />

        <label for="sixonesix_popup_text">Popup Text</label>
        <textarea cols="50" rows="3" placeholder="Enter popup text here" id="sixonesix_popup_text" name="sixonesix_popup_text"><?php echo esc_attr(get_option('sixonesix_popup_text')); ?></textarea>


        <label for="sixonesix_popup_button_text">Popup Button Text</label>
        <input placeholder="Click Here" type="text" id="sixonesix_popup_button_text" name="sixonesix_popup_button_text" value="<?php echo esc_attr(get_option('sixonesix_popup_button_text')); ?>" />

        <label for="sixonesix_popup_button_link">Popup Button Link</label>
        <input placeholder="Enter popup button link here" type="text" id="sixonesix_popup_button_link" name="sixonesix_popup_button_link" value="<?php echo esc_attr(get_option('sixonesix_popup_button_link')); ?>" />

        <label for="sixonesix_newsletter_bg">popup NewsLetter background</label>
        <div class="button-group">
          <input type="text" id="sixonesix_newsletter_bg" name="sixonesix_newsletter_bg" value="<?php echo esc_attr(get_option('sixonesix_newsletter_bg')); ?>" />
          <button type="button" class="button" id="sixonesix_newsletter_bg_button">Select Image</button>
        </div>
      </div>
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
  register_setting('sixonesix_options_group', 'sixonesix_popup_title');
  register_setting('sixonesix_options_group', 'sixonesix_popup_text');
  register_setting('sixonesix_options_group', 'sixonesix_popup_button_text');
  register_setting('sixonesix_options_group', 'sixonesix_popup_button_link');
  register_setting('sixonesix_options_group', 'sixonesix_newsletter_bg');
}
add_action('admin_init', 'sixonesix_settings_init');
