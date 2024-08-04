<?php
/*
Plugin Name: SixOneSix
Description: Registers custom shortcodes and helper functions.
Version: 1.1.23
Author: Amirhossein
Plugin URI: https://github.com/amirhh00/sixbysix_plugin
Author URI: https://github.com/amirhh00
GitHub Plugin URI: https://github.com/amirhh00/sixbysix_plugin
GitHub Branch: main
*/

require_once plugin_dir_path(__FILE__) . 'shortcodes/index.php';
require_once plugin_dir_path(__FILE__) . 'dashboard/index.php';
require_once plugin_dir_path(__FILE__) . 'injections/index.php';

function get_active_plugin_version()
{
  if (!function_exists('get_plugin_data')) {
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
  }
  $pluginPath = plugin_dir_path(__FILE__) . 'index.php';
  $plugin_data = get_plugin_data($pluginPath);
  return $plugin_data['Version'];
}
