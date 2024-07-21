<?php
/*
Plugin Name: SixOneSix
Description: Registers custom shortcodes and helper functions.
Version: 1.0
Author: Amirhossein
Plugin URI: https://github.com/amirhh00/sixbysix_plugin
Author URI: https://github.com/amirhh00
GitHub Plugin URI: https://github.com/amirhh00/sixbysix_plugin
GitHub Branch: main
*/

// Register custom shortcodes
require_once plugin_dir_path(__FILE__) . 'shortcodes/test.php';
require_once plugin_dir_path(__FILE__) . 'shortcodes/menu.shortcode.php';
require_once plugin_dir_path(__FILE__) . 'dashboard/restaurant-menu.php';
