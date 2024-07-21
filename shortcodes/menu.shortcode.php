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
  $output = '';
  $output .= '<div id="sixbysixMenus">';
  $output .= '<ul' . $id . $class . '>';
  foreach ($menu_items as $item) {
    $output .= '<li' . (!empty($item['link']) && $item['link'] !== '#' ? ' onclick="window.open(\'' . esc_url($item['link']) . '\', \'_blank\')"' : '') . ' class="menu-item">';
    $output .= '<h3>' . esc_html($item['name']) . '</h3>';
    $output .= '<div>';
    $output .= '<p>' . esc_html($item['days']) . '</p>';
    $output .= '<p>' . esc_html($item['time']) . '</p>';
    $output .= '</div>';
    $output .= '<a href="' . esc_url($item['link']) . '"' . (!empty($item['link']) && $item['link'] !== '#' ? ' target="_blank"' : '') . '>View Menu</a>';
    $output .= '</li>';
  }
  $output .= '</ul>';
  $output .= '</div>';
  return $output;
}

add_shortcode('restaurantmenu', 'menus_shortcode');

// load css file
function enqueue_shortcode_styles()
{
  wp_enqueue_style('shortcode-styles', plugin_dir_url(__FILE__) . './styles/menu.css');
}

add_action('wp_enqueue_scripts', 'enqueue_shortcode_styles');
