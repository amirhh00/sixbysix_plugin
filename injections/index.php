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
  // wp_enqueue_script('seven_rooms', 'https://www.sevenrooms.com/widget/embed.js');

  $sevenRoomsScript = <<<HTML
    <div id="sr-res-root" class="sr-lg sr-dark sr-#142417 floating_booking btn">
      $button_text
    </div>
    <script src="https://www.sevenrooms.com/widget/embed.js"></script>
    <script>
    SevenroomsWidget.init({
        venueId: "sixonesix",
        triggerId: "sr-res-root", // id of the dom element that will trigger this widget
        type: "reservations", // either 'reservations' or 'waitlist' or 'events'
        styleButton: true, // true if you are using the SevenRooms button
        clientToken: "" //(Optional) Pass the api generated clientTokenId here
    })
    </script>
  HTML;

  echo $sevenRoomsScript;

  $newsLetterBtnText = get_option('sixonesix_newsletter_btn_text', 'NewsLetter Signup');
  $newsLetterText = get_option('sixonesix_newsletter_text', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Nulla, obcaecati laboriosam magnam reiciendis blanditiis iure aliquid modi officiis deleniti');
  $newsLetterElement = <<<HTML
  <div id="newsletter_wrapper">
    <div id="newsletter">
      <p>$newsLetterText</p>
      <a style="color:black" href="/newsletter" class="btn btn-reverted">
        $newsLetterBtnText
      </a>
    </div>
  </div>
  HTML;

  echo <<<HTML
    <script type="text/javascript">
      // add element to the end of the main
      const element = `$newsLetterElement`;
      let main = document.querySelectorAll('article.page');
      if (!main || main.length === 0) {
        main = document.getElementsByTagName('main')[0];
      } else {
        main = main[main.length - 1];
      }
      main?.insertAdjacentHTML('beforeend', element);

      const wpadminbar = document.getElementById('wpadminbar');
      if (wpadminbar) {
        const callUsBtnElement = document.querySelector('#call_us_wrapper');
        if (callUsBtnElement) {
          const clientHeight = wpadminbar.clientHeight;
          const initialTop = 10;
          callUsBtnElement.style.top = `\${clientHeight + initialTop}px`;
        }
      }
    </script>
  HTML;
}

function addCallUsFloatingButtonOnHead()
{
  $version = get_active_plugin_version();
  wp_enqueue_style('floating_call_us', plugin_dir_url(__FILE__) . 'styles/floating_call_us.css', [], $version);
  $callUsBtnElement = <<<HTML
  <div id="call_us_wrapper">
    <div id="call_us">
      <a href="tel:+1234567890" class="btn btn-reverted btn-icon">
        <svg style="width:30px;height:30px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
          <path d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z"/>
        </svg>
      </a>
    </div>
  </div>
  HTML;

  echo $callUsBtnElement;
}

add_action('wp_head', 'addCallUsFloatingButtonOnHead');
