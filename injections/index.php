<?php

// Hook into the 'wp_footer' action
add_action('wp_footer', 'add_button_to_footer_on_homepage');

function add_button_to_footer_on_homepage()
{
  $version = get_active_plugin_version();
  wp_enqueue_style('injected_footer_style', plugin_dir_url(__FILE__) . 'styles/global.css', [], $version);
  // wp_enqueue_script('injected_footer_script1', 'https://unpkg.com/jspdf@latest/dist/jspdf.umd.min.js');
  // wp_enqueue_script('injected_footer_script2', 'https://html2canvas.hertzen.com/dist/html2canvas.js');
  // Check if it's the homepage
  // if (is_front_page()) {
  // load css file
  wp_enqueue_style('floating_booking', plugin_dir_url(__FILE__) . 'styles/floating_booking.css', [], $version);

  // Get the options for button text and link
  $button_text = get_option('sixonesix_button_text', 'Book from here'); // 'Default Text' is a fallback if the option is not set
  $current_Domain = $_SERVER['HTTP_HOST'];
  $fallback_link = 'https://' . $current_Domain . '/reservations';
  $button_link = get_option('sixonesix_button_link', $fallback_link); // 'https://domain/reservations' is a fallback if the option is not set

  echo '<a class="btn" id="floating_booking" href="' . esc_url($button_link) . '">' . esc_html($button_text) . '</a>';
  $newsLetterBtnText = get_option('sixonesix_newsletter_btn_text', 'NewsLetter Signup');
  $newsLetterText = get_option('sixonesix_newsletter_text', 'NewsLetter Signup');
  $newsLetterElement = <<<HTML
  <div id="newsletter_wrapper">
    <div id="newsletter">
      <p>$newsLetterText</p>
      <a style="color:black" class="btn btn-reverted">
        $newsLetterBtnText
      </a>
    </div>
  </div>
  HTML;

  echo <<<HTML
    <script type="text/javascript">
      // add element to the end of the main
      const element = `$newsLetterElement`;
      let main = document.getElementsByTagName('main')[0];
      if (!main) {
        main = document.getElementsByTagName('article')[0];
      }
      main.insertAdjacentHTML('beforeend', element)
    </script>
  HTML;
}
