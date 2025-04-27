<?php

/**
 * Plugin Name:       SixOneSix
 * Description:       Registers custom shortcodes and helper functions.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           1.3.12
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

// the menu single post type should be manually registered in the theme root folder

require_once plugin_dir_path(__FILE__) . 'shortcodes/index.php';
require_once plugin_dir_path(__FILE__) . 'dashboard/index.php';
require_once plugin_dir_path(__FILE__) . 'injections/index.php';

if (!defined('ABSPATH')) {
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

register_activation_hook(__FILE__, 'create_event_list_page');

function create_event_list_page()
{
  $page_title = 'Event List';
  $page_content = '<!-- wp:shortcode -->[eventsl]<!-- /wp:shortcode -->';

  // Use WP_Query to check if the page exists
  $args = array(
    'post_type' => 'page',
    'title' => $page_title,
    'post_status' => 'publish',
    'posts_per_page' => 1,
    'name' => sanitize_title($page_title),
  );

  $query = new WP_Query($args);

  if (!$query->have_posts()) {
    // Page does not exist, create it
    $new_page_id = wp_insert_post(array(
      'post_title'    => $page_title,
      'post_content'  => $page_content,
      'post_status'   => 'publish',
      'post_type'     => 'page',
    ));
  }
}
