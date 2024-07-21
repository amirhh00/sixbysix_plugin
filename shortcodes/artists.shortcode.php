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
        #artists .artist {
            background-image: url('{$image_url}');
        }
    ";
  wp_add_inline_style($handle, $custom_css);
  $class = $attributes['class'] ? ' class="' . esc_attr($attributes['class']) . '"' : '';
  $id = $attributes['id'] ? ' id="' . esc_attr($attributes['id']) . '"' : '';
  $artists = get_option('sixonesix_artist_options')['artists'] ?? [];

  $output = '';
  $output .= '<div' . $id . $class . '>';
  $output .= '<ul id="artists">';
  foreach ($artists as $artist) {
    $output .= '<div class="artistWrapper">';
    $output .= '<li class="artist">';
    $output .= '<img class="artistPicture" src="' . esc_url($artist['image']) . '" alt="' . esc_attr($artist['name']) . '">';
    $output .= '<div class="artistInfo">';
    $output .= '<div>';
    $output .= '<p>' . esc_html($artist['date']) . '</p>';
    $output .= '<h3 class="artistName">' . esc_html($artist['name']) . '</h3>';
    $output .= '</div>';
    $output .= '<div class="artistLinks">';

    if ($artist['instagram']) {
      $output .= '<a target="_blank" href="' . esc_url($artist['instagram']) . '">' . '<img width="40" height="40" style="scale:0.7" src="' . plugin_dir_url(__FILE__) . 'images/icon-instagram.svg" alt="Instagram icon">' .
        // external user tag from link using regex and put @ behind it e.g. https://www.instagram.com/jakeoneillvocal/
        preg_replace('/https:\/\/www.instagram.com\/([a-zA-Z0-9_]+)\/?/', '@$1', $artist['instagram'])
        . '</a>';
    }
    if ($artist['spotify']) {
      $output .= '<a target="_blank" href="' . esc_url($artist['spotify']) . '">' . '<img width="40" height="40" src="' . plugin_dir_url(__FILE__) . 'images/icon-spotify.svg" alt="Spotify icon">' . 'Listen Now' . '</a>';
    }
    $output .= '</div>';
    $output .= '</div>';
    $output .= '</li>';
    $output .= '</div>';
  }
  $output .= '</ul>';
  $output .= '</div>';

  // $output .= '<pre>' . var_export($artists, true) . '</pre>';

  return $output;
}

add_shortcode('artists', 'artists_shortcode');
