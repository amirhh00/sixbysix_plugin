<?php

// Hook into the 'wp_footer' action
add_action('wp_footer', 'add_button_to_footer_on_homepage');

function add_button_to_footer_on_homepage()
{
  // Check if it's the homepage
  if (is_front_page()) {
    // load css file
    $version = get_active_plugin_version();
    wp_enqueue_style('floating_booking', plugin_dir_url(__FILE__) . 'styles/floating_booking.css', [], $version);

    // Get the options for button text and link
    $button_text = get_option('sixonesix_button_text', 'Book from here'); // 'Default Text' is a fallback if the option is not set
    $current_Domain = $_SERVER['HTTP_HOST'];
    $fallback_link = 'https://' . $current_Domain . '/reservations';
    $button_link = get_option('sixonesix_button_link', $fallback_link); // 'https://domain/reservations' is a fallback if the option is not set

    // Echo the button HTML with dynamic text and link
    echo '<a id="floating_booking" href="' . esc_url($button_link) . '">' . esc_html($button_text) . '</a>';
  }
}
