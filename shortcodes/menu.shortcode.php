<?php

function menus_shortcode($atts, $content = null)
{
  $attributes = shortcode_atts(array(
    'class' => '',
    'id' => '',
  ), $atts);
  $class = $attributes['class'] ? ' class="' . esc_attr($attributes['class']) . '"' : '';
  $id = $attributes['id'] ? ' id="' . esc_attr($attributes['id']) . '"' : '';
  $menu_items = get_option('restaurant_menu_items', array());
  $output = '<div' . $id . $class . '>';
  foreach ($menu_items as $item) {
    $output .= '<div class="menu-item">';
    $output .= '<h3>' . esc_html($item['name']) . '</h3>';
    $output .= '<p><strong>Days Available:</strong> ' . esc_html($item['days']) . '</p>';
    $output .= '<p><strong>Time Available:</strong> ' . esc_html($item['time']) . '</p>';
    $output .= '<p><a href="' . esc_url($item['link']) . '" target="_blank">More Info</a></p>';
    $output .= '</div>';
  }
  $output .= '</div>';
  return $output;
}

add_shortcode('restaurantmenu', 'menus_shortcode');

// load css file
function enqueue_shortcode_styles()
{
  wp_enqueue_style('shortcode-styles', plugin_dir_url(__FILE__) . './styles/menu.css');
}
