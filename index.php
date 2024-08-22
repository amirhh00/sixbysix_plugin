<?php

/**
 * Plugin Name:       SixOneSix
 * Description:       Registers custom shortcodes and helper functions.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           1.1.46
 * Author:            Amirhossein
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
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
  // header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
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

function get_plugin_info()
{
  if (!function_exists('get_plugin_data')) {
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
  }
  $pluginPath = plugin_dir_path(__FILE__) . 'index.php';
  $plugin_data = get_plugin_data($pluginPath);
  return $plugin_data;
}
define('GUTENBERG_BLOCKS_NAME', get_plugin_info()['Name']);
define('GUTENBERG_BLOCKS_VERSION', get_active_plugin_version());
define('GUTENBERG_BLOCKS_URL', plugin_dir_url(__FILE__));
define('GUTENBERG_BLOCKS_INC_URL', GUTENBERG_BLOCKS_URL . 'assets/');

// /**
//  * Loads PSR-4-style plugin classes.
//  */
// function classloader($class)
// {
//   static $ns_offset;
//   if (strpos($class, __NAMESPACE__ . '\\') === 0) {
//     if ($ns_offset === NULL) {
//       $ns_offset = strlen(__NAMESPACE__) + 1;
//     }
//     include __DIR__ . strtr(substr($class, $ns_offset), '\\', '/') . '.php';
//   }
// }
// spl_autoload_register(__NAMESPACE__ . '\classloader');

// add_action('plugins_loaded', __NAMESPACE__ . '\Plugin::loadTextDomain');
// add_action('init', __NAMESPACE__ . '\Plugin::perInit', 0);
// add_action('init', __NAMESPACE__ . '\Plugin::init', 20);
// //add_action('admin_init', __NAMESPACE__ . '\Admin::init');