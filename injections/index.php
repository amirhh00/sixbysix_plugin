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
  if (is_front_page()) {
    $popupContentBg = get_option('sixonesix_newsletter_bg', '');
    $popupContentElement = <<<HTML
    <dialog id="popup_newsletter" style="color:white;background: transparent;padding: 10px;  min-width: 300px;max-width: 600px;width: 50%;border-color:#c4b483">
      <div style="display: flex;padding: 20px;flex-direction: column;gap: 12px;background: url('$popupContentBg') black;">
        <p style="color:currentColor;margin:0; font-size: calc(2rem + (56 - 32) * ((100vw - 23.4375rem) / (1920 - 375)));">Sign up to our newsletter.</p>
        <p style="margin:0;">As a thank you for subscribing, you'll get early access to events and exclusive information, as well as entry into our monthly prize draw to win a £100 Six One Six gift card.</p>
        <a href="/newsletter" class="btn btn-hover">
          ENTER NOW
        </a>
      </div>
      <button onclick="this.parentElement.close()" class="close" style="padding:10px;cursor:pointer; position: absolute;right: 10px;top: 10px;background: transparent;border: none;">
        <svg width="20" height="20" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M11.3475 0.505008C11.3861 0.485156 11.43 0.478353 11.4727 0.485622C11.5154 0.492891 11.5546 0.513839 11.5844 0.545313C11.616 0.598394 11.6265 0.661378 11.614 0.72185C11.6014 0.782321 11.5666 0.835891 11.5165 0.871997C10.7473 1.5685 10.0147 2.30441 9.32168 3.07676C9.00348 3.419 8.67468 3.77326 8.34092 4.11832C7.94211 4.53127 7.51856 4.94069 7.1155 5.34374C6.45365 5.99145 5.77129 6.66249 5.14338 7.36677C4.74316 7.81508 4.34152 8.26197 3.9413 8.71028C3.33814 9.37991 2.71094 10.0821 2.10212 10.7687C1.7975 11.1367 1.45895 11.4752 1.09095 11.7798C1.05205 11.8262 0.996362 11.8553 0.936071 11.8607C0.875781 11.8661 0.815792 11.8475 0.769221 11.8088C0.734534 11.7585 0.720409 11.6969 0.729746 11.6365C0.739082 11.5762 0.771172 11.5217 0.819426 11.4843C1.17338 11.1939 1.49786 10.8695 1.78816 10.5155C2.39769 9.82255 3.0256 9.12535 3.6323 8.45077C4.0344 8.00435 4.4351 7.55651 4.83438 7.10726C5.47572 6.38955 6.16657 5.71144 6.83408 5.05524C7.23783 4.65855 7.65573 4.25338 8.04818 3.84255C8.37769 3.50173 8.70508 3.15029 9.02116 2.81018C9.73012 2.02167 10.479 1.26997 11.2648 0.558041C11.2895 0.536184 11.3174 0.518292 11.3475 0.505008Z" fill="#c4b483" data-darkreader-inline-fill="" style="--darkreader-inline-fill: #e2d2bf;"></path>
          <path d="M11.8281 11.3484C11.8479 11.387 11.8547 11.4309 11.8475 11.4736C11.8402 11.5163 11.8192 11.5555 11.7878 11.5853C11.7347 11.6169 11.6717 11.6274 11.6112 11.6149C11.5508 11.6023 11.4972 11.5675 11.4611 11.5174C10.7646 10.7482 10.0287 10.0156 9.25632 9.32257C8.91408 9.00437 8.55982 8.67557 8.21475 8.34182C7.8018 7.94301 7.39239 7.51945 6.98934 7.1164C6.34163 6.45455 5.67058 5.77219 4.9663 5.14428C4.518 4.74406 4.07111 4.34242 3.6228 3.9422C2.95317 3.33903 2.25101 2.71183 1.56441 2.10301C1.19642 1.79839 0.857873 1.45985 0.553251 1.09185C0.506876 1.05294 0.477793 0.997256 0.47236 0.936966C0.466928 0.876676 0.485587 0.816686 0.52426 0.770116C0.57453 0.735429 0.636172 0.721304 0.69653 0.730641C0.756888 0.739977 0.811381 0.772066 0.848822 0.82032C1.13913 1.17427 1.46361 1.49875 1.81756 1.78906C2.51052 2.39858 3.20773 3.02649 3.88231 3.63319C4.32873 4.0353 4.77656 4.43599 5.22581 4.83527C5.94353 5.47662 6.62164 6.16746 7.27784 6.83497C7.67452 7.23873 8.0797 7.65663 8.49053 8.04907C8.83135 8.37859 9.18278 8.70598 9.5229 9.02205C10.3114 9.73101 11.0631 10.4799 11.775 11.2657C11.7969 11.2904 11.8148 11.3183 11.8281 11.3484Z" fill="#c4b483" data-darkreader-inline-fill="" style="--darkreader-inline-fill: #e2d2bf;"></path>
          <path d="M11.3475 0.505008C11.3861 0.485156 11.43 0.478353 11.4727 0.485622C11.5154 0.492891 11.5546 0.513839 11.5844 0.545313C11.616 0.598394 11.6265 0.661378 11.614 0.72185C11.6014 0.782321 11.5666 0.835891 11.5165 0.871997C10.7473 1.5685 10.0147 2.30441 9.32168 3.07676C9.00348 3.419 8.67468 3.77326 8.34092 4.11832C7.94211 4.53127 7.51856 4.94069 7.1155 5.34374C6.45365 5.99145 5.77129 6.66249 5.14338 7.36677C4.74316 7.81508 4.34152 8.26197 3.9413 8.71028C3.33814 9.37991 2.71094 10.0821 2.10212 10.7687C1.7975 11.1367 1.45895 11.4752 1.09095 11.7798C1.05205 11.8262 0.996362 11.8553 0.936071 11.8607C0.875781 11.8661 0.815792 11.8475 0.769221 11.8088C0.734534 11.7585 0.720409 11.6969 0.729746 11.6365C0.739082 11.5762 0.771172 11.5217 0.819426 11.4843C1.17338 11.1939 1.49786 10.8695 1.78816 10.5155C2.39769 9.82255 3.0256 9.12535 3.6323 8.45077C4.0344 8.00435 4.4351 7.55651 4.83438 7.10726C5.47572 6.38955 6.16657 5.71144 6.83408 5.05524C7.23783 4.65855 7.65573 4.25338 8.04818 3.84255C8.37769 3.50173 8.70508 3.15029 9.02116 2.81018C9.73012 2.02167 10.479 1.26997 11.2648 0.558041C11.2895 0.536184 11.3174 0.518292 11.3475 0.505008Z" stroke="#c4b483" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round" data-darkreader-inline-stroke="" style="--darkreader-inline-stroke: #e2d2bf;"></path>
          <path d="M11.8281 11.3484C11.8479 11.387 11.8547 11.4309 11.8475 11.4736C11.8402 11.5163 11.8192 11.5555 11.7878 11.5853C11.7347 11.6169 11.6717 11.6274 11.6112 11.6149C11.5508 11.6023 11.4972 11.5675 11.4611 11.5174C10.7646 10.7482 10.0287 10.0156 9.25632 9.32257C8.91408 9.00437 8.55982 8.67557 8.21475 8.34182C7.8018 7.94301 7.39239 7.51945 6.98934 7.1164C6.34163 6.45455 5.67058 5.77219 4.9663 5.14428C4.518 4.74406 4.07111 4.34242 3.6228 3.9422C2.95317 3.33903 2.25101 2.71183 1.56441 2.10301C1.19642 1.79839 0.857873 1.45985 0.553251 1.09185C0.506876 1.05294 0.477793 0.997256 0.47236 0.936966C0.466928 0.876676 0.485587 0.816686 0.52426 0.770116C0.57453 0.735429 0.636172 0.721304 0.69653 0.730641C0.756888 0.739977 0.811381 0.772066 0.848822 0.82032C1.13913 1.17427 1.46361 1.49875 1.81756 1.78906C2.51052 2.39858 3.20773 3.02649 3.88231 3.63319C4.32873 4.0353 4.77656 4.43599 5.22581 4.83527C5.94353 5.47662 6.62164 6.16746 7.27784 6.83497C7.67452 7.23873 8.0797 7.65663 8.49053 8.04907C8.83135 8.37859 9.18278 8.70598 9.5229 9.02205C10.3114 9.73101 11.0631 10.4799 11.775 11.2657C11.7969 11.2904 11.8148 11.3183 11.8281 11.3484Z" stroke="#c4b483" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round" data-darkreader-inline-stroke="" style="--darkreader-inline-stroke: #e2d2bf;"></path>
        </svg>
      </button>
    </dialog>
    <script type="text/javascript">
      const dialog = document.getElementById('popup_newsletter');
      dialog.showModal();
      // close the dialog when the user clicks on the background
      dialog.addEventListener('click', (e) => {
        if (e.target === dialog) {
          dialog.close();
        }
      });
    </script>
    HTML;

    echo $popupContentElement;
  }
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
