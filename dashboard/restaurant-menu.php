<?php
function restaurant_menu_add_admin_menu()
{
  add_submenu_page(
    'sixonesix-settings', // Parent slug
    'Restaurant Menu Settings', // Page title
    'Restaurant Menu', // Menu title
    'manage_options', // Capability
    'restaurant-menu-settings', // Menu slug
    'restaurant_menu_settings_page' // Function to display the page
  );
}
add_action('admin_menu', 'restaurant_menu_add_admin_menu');

// Callback function to render the settings page
function restaurant_menu_settings_page()
{
?>
  <div class="wrap">
    <h1>Restaurant Menu Settings</h1>
    <form method="post" action="options.php">
      <?php
      settings_fields('restaurant_menu_settings');
      do_settings_sections('restaurant-menu-settings');
      submit_button();
      ?>
    </form>
  </div>
<?php
}

// Register settings
function restaurant_menu_register_settings()
{
  register_setting('restaurant_menu_settings', 'restaurant_menu_items', 'restaurant_menu_sanitize');

  add_settings_section(
    'restaurant_menu_section',
    'Menu Items',
    'restaurant_menu_section_callback',
    'restaurant-menu-settings'
  );

  add_settings_field(
    'restaurant_menu_items_field',
    'Menu Items',
    'restaurant_menu_items_field_callback',
    'restaurant-menu-settings',
    'restaurant_menu_section'
  );
}
add_action('admin_init', 'restaurant_menu_register_settings');

// Section callback
function restaurant_menu_section_callback()
{
  echo 'Enter your menu items below:';
}

// Field callback
function restaurant_menu_items_field_callback()
{
  $menu_items = get_option('restaurant_menu_items', array());
?>
  <div id="menu-items">
    <?php foreach ($menu_items as $index => $item) : ?>
      <div class="menu-item">
        <input type="text" name="restaurant_menu_items[<?php echo $index; ?>][name]" value="<?php echo esc_attr($item['name']); ?>" placeholder="Item Name">
        <input type="text" name="restaurant_menu_items[<?php echo $index; ?>][days]" value="<?php echo esc_attr($item['days']); ?>" placeholder="Days Available">
        <input type="text" name="restaurant_menu_items[<?php echo $index; ?>][time]" value="<?php echo esc_attr($item['time']); ?>" placeholder="Time Available">
        <input type="text" name="restaurant_menu_items[<?php echo $index; ?>][link]" value="<?php echo esc_attr($item['link']); ?>" placeholder="Link">
        <button type="button" class="button remove-item">Remove</button>
      </div>
    <?php endforeach; ?>
  </div>
  <button type="button" class="button" id="add-item">Add Item</button>

  <script>
    jQuery(document).ready(function($) {
      var index = <?php echo count($menu_items); ?>;

      $('#add-item').on('click', function() {
        var newItem = '<div class="menu-item">' +
          '<input type="text" name="restaurant_menu_items[' + index + '][name]" placeholder="Item Name">' +
          '<input type="text" name="restaurant_menu_items[' + index + '][days]" placeholder="Days Available">' +
          '<input type="text" name="restaurant_menu_items[' + index + '][time]" placeholder="Time Available">' +
          '<input type="text" name="restaurant_menu_items[' + index + '][link]" placeholder="Link">' +
          '<button type="button" class="button remove-item">Remove</button>' +
          '</div>';
        $('#menu-items').append(newItem);
        index++;
      });

      $(document).on('click', '.remove-item', function() {
        $(this).parent().remove();
      });
    });
  </script>
<?php
}

// Sanitize and save the input
function restaurant_menu_sanitize($input)
{
  $sanitized_input = array();
  foreach ($input as $item) {
    if (
      !empty($item['name']) && !empty($item['days']) && !empty($item['time']) && !empty($item['link'])
    ) {
      $sanitized_input[] = array(
        'name' => sanitize_text_field($item['name']),
        'days' => sanitize_text_field($item['days']),
        'time' => sanitize_text_field($item['time']),
        'link' => esc_url($item['link']),
      );
    }
  }
  return $sanitized_input;
}
