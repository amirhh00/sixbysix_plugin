<?php

function artists_shortcode($atts, $content = null)
{
  $attributes = shortcode_atts(array(
    'class' => '',
    'id' => '',
  ), $atts);
  $handle = 'sixonesix_artists';
  wp_enqueue_style($handle, plugin_dir_url(__FILE__) . 'styles/artists.css');
  $image_url = plugin_dir_url(__FILE__) . 'images/bg_artists.png';
  $custom_css = "
        .artist {
            background-image: url('{$image_url}');
        }
    ";
  wp_add_inline_style($handle, $custom_css);
  $class = $attributes['class'] ? ' class="' . esc_attr($attributes['class']) . '"' : '';
  $id = $attributes['id'] ? ' id="' . esc_attr($attributes['id']) . '"' : '';
  $artists = get_option('sixonesix_artist_options')['artists'] ?? [];


  $artists_by_year_month = [];
  // Sort artists by date in ascending order
  usort($artists, function ($a, $b) {
    return strtotime($a['date']) - strtotime($b['date']);
  });

  foreach ($artists as $artist) {
    $year = date('Y', strtotime($artist['date']));
    $month = date('F', strtotime($artist['date']));
    if (!isset($artists_by_year_month[$year])) {
      $artists_by_year_month[$year] = [];
    }
    if (!isset($artists_by_year_month[$year][$month])) {
      $artists_by_year_month[$year][$month] = [];
    }
    $artists_by_year_month[$year][$month][] = $artist;
  }

  $output = '';
  $output .= '<div' . $id . $class . '>';
  // i.e. ?open=2024-July or null
  $yearMonthQueryParams = $_GET['open'] ?? null;
  $firstItem = true;
  foreach ($artists_by_year_month as $year => $months) {
    foreach ($months as $month => $artists) {
      $open = '';
      if ($yearMonthQueryParams === "{$year}-{$month}" || ($yearMonthQueryParams === null && $firstItem)) {
        $open = ' open';
      }
      $firstItem = false; // Set to false after the first iteration

      $output .= <<<HTML
      <details class="accordion-item" name="artists-accordion" id="{$year}-{$month}" $open>
          <summary class="accordion-trigger">
            <span class="accordion-title">
              $year - $month
            </span>
            <span class="accordion-icon" aria-hidden="true">
              &plus;
            </span>
          </summary>
          <div class="accordion-content">
      HTML;
      foreach ($artists as $artist) {
        $instagram_link = '';
        if (!empty($artist['instagram'])) {
          $instagram_handle = preg_replace('/https:\/\/www.instagram.com\/([a-zA-Z0-9_]+)\/?/', '@$1', $artist['instagram']);
          $plugin_dir_url = plugin_dir_url(__FILE__);
          $instagram_link = <<<HTML
         <a target="_blank" href="{$artist['instagram']}">
             <img width="40" height="40" style="scale:0.7" src="{$plugin_dir_url}images/icon-instagram.svg" alt="Instagram icon">
             {$instagram_handle}
         </a>
        HTML;
        }

        $spotify_link = '';
        if (!empty($artist['spotify'])) {
          $spotify_link = <<<HTML
         <a target="_blank" href="{$artist['spotify']}">
             <img width="40" height="40" src="{$plugin_dir_url}images/icon-spotify.svg" alt="Spotify icon">
             Listen Now
         </a>
        HTML;
        }
        $date = new DateTime($artist['date']);
        $formattedDate = $date->format('l jS F Y');
        $output .= <<<HTML
          <div class="artistWrapper">
              <li class="artist">
                  <img class="artistPicture" src="{$artist['image']}" alt="{$artist['name']}">
                  <div class="artistInfo">
                      <div>
                          <p style="text-transform:uppercase">{$formattedDate}</p>
                          <h3 class="artistName">{$artist['name']}</h3>
                      </div>
                      <div class="artistLinks">
                          $instagram_link
                          $spotify_link
                      </div>
                  </div>
              </li>
            </div>
      HTML;
      }
      $output .= '</div>';
      $output .= '</details>';
    }
  }

  // if browser is not chrome add javascript to close all details except the one that is open

  $polyfillForDetails  = strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') ? '' : 'if (detail.open) setTargetDetail(detail);';
  $output .= <<<HTML
          <script>
            const details = document.querySelectorAll("details");
            // Add the onclick listeners.
            details.forEach((detail) => {
              detail.addEventListener("toggle", () => {
                // add query parameter to the URL to keep the state of the accordion
                if (detail.open) {
                  window.history.pushState(null, null, "?open=" + detail.id);
                } else {
                  // if none of the details are open remove the query parameter
                  if (!Array.from(details).some((detail) => detail.open)) {
                    window.history.pushState(null, null, window.location.pathname);
                  }
                }
                $polyfillForDetails
              });
            });
            // Close all the details that are not targetDetail.
            function setTargetDetail(targetDetail) {
              details.forEach((detail) => {
                if (detail !== targetDetail) {
                  detail.open = false;
                }
              });
            }
          </script>
HTML;

  $output .= '</div>';
  // // $output .= '<pre>' . var_export($artists, true) . '</pre>';
  return $output;
}

add_shortcode('artists', 'artists_shortcode');


// function pp($arr)
// {
//   $retStr = '<ul>';
//   if (is_array($arr)) {
//     foreach ($arr as $key => $val) {
//       if (is_array($val)) {
//         $retStr .= '<li>' . $key . ' => ' . pp($val) . '</li>';
//       } else {
//         $retStr .= '<li>' . $key . ' => ' . $val . '</li>';
//       }
//     }
//   }
//   $retStr .= '</ul>';
//   return $retStr;
// }
