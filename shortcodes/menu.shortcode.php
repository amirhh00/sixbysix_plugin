<?php

function menus_shortcode($atts, $content = null)
{
  $attributes = shortcode_atts(array(
    'class' => '',
    'id' => '',
  ), $atts);
  // load css
  wp_enqueue_style('restaurant-menu', plugin_dir_url(__FILE__) . './styles/menu.css');
  $class = $attributes['class'] ? ' class="' . esc_attr($attributes['class']) . '"' : '';
  $id = $attributes['id'] ? ' id="' . esc_attr($attributes['id']) . '"' : '';
  $args = array(
    'post_type' => 'menu_item',
    'posts_per_page' => -1, // Retrieve all menu items
    'orderby' => 'menu_order', // Order by the custom order set in the admin
    'order' => 'ASC' // Ascending order
  );

  ob_start();
  $query = new WP_Query($args);
  if ($query->have_posts()) {
    $menu_items = [];
    while ($query->have_posts()) {
      $query->the_post();
      $post_id = get_the_ID();
      $menu_name = get_the_title();
      $menu_days = get_post_meta($post_id, 'days_available', true);
      $menu_time = get_post_meta($post_id, 'time_available', true);
      $menu_link = get_post_meta($post_id, 'link', true);
      $button_text = get_post_meta($post_id, 'button_text', true);
      $menu_item = [
        'name' => $menu_name,
        'days' => $menu_days,
        'time' => $menu_time,
        'link' => $menu_link,
        'button_text' => $button_text ? $button_text : 'View Menu'
      ];
      $menu_items[] = $menu_item;
    }
    wp_reset_postdata();
  }

  $output = '';
  if (empty($menu_items)) {
    return ob_get_clean() . $output;
  }

  $output .= '<div id="sixbysixMenus">';
  $output .= '<ul' . $id . $class . '>';
  foreach ($menu_items as $item) {
    $output .= '<li' . (!empty($item['link']) && $item['link'] !== '#' ? ' onclick="window.open(\'' . esc_url($item['link']) . '\', \'_blank\')"' : '') . ' class="menu-item">';
    $output .= '<h3>' . esc_html($item['name']) . '</h3>';
    $output .= '<div>';
    $output .= '<p>' . esc_html($item['days']) . '</p>';
    $output .= '<p>' . esc_html($item['time']) . '</p>';
    $output .= '</div>';
    $output .= '<a href="' . esc_url($item['link']) . '"' . (!empty($item['link']) && $item['link'] !== '#' ? ' target="_blank"' : '') . '>' . esc_html($item['button_text']) . '</a>';
    $output .= '</li>';
  }
  $output .= '</ul>';
  $output .= '</div>';
  return ob_get_clean() . $output;
}

add_shortcode('rmenu', 'menus_shortcode');
