<?php

/**
 * Plugin Name:       SixOneSix
 * Description:       Registers custom shortcodes and helper functions.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           1.1.43
 * Author:            Amirhossein
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       copyright-date-block
 * Plugin URI:        https://github.com/amirhh00/sixbysix_plugin
 * Author URI:        https://github.com/amirhh00
 * GitHub Plugin URI: https://github.com/amirhh00/sixbysix_plugin
 * GitHub Branch:     main
 * 
 * @package CreateBlock
 */

require_once plugin_dir_path(__FILE__) . 'shortcodes/index.php';
require_once plugin_dir_path(__FILE__) . 'dashboard/index.php';
require_once plugin_dir_path(__FILE__) . 'injections/index.php';

if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

function get_active_plugin_version()
{
  if (!function_exists('get_plugin_data')) {
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
  }
  $pluginPath = plugin_dir_path(__FILE__) . 'index.php';
  $plugin_data = get_plugin_data($pluginPath);
  return $plugin_data['Version'];
}
