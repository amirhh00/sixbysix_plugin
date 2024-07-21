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
    <p>Welcome to the SixOneSix settings page.</p>
  </div>
<?php
}
