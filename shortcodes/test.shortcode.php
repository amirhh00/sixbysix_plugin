<?php

function custom_button_shortcode($atts, $content = null)
{
  $attributes = shortcode_atts(array(
    'class' => '',
    'id' => '',
  ), $atts);

  $class = $attributes['class'] ? ' class="' . esc_attr($attributes['class']) . '"' : '';
  $id = $attributes['id'] ? ' id="' . esc_attr($attributes['id']) . '"' : '';

  return '<button' . $class . $id . '>' . do_shortcode($content) . "test" . '</button>';
}
add_shortcode('button', 'custom_button_shortcode');
