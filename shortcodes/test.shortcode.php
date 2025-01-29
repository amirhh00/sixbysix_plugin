<?php

function custom_shortcode($atts, $content = null)
{
  $attributes = shortcode_atts(array(
    'class' => '',
    'id' => '',
  ), $atts);

  $class = $attributes['class'] ? ' class="' . esc_attr($attributes['class']) . '"' : '';
  $id = $attributes['id'] ? ' id="' . esc_attr($attributes['id']) . '"' : '';

  return '<div' . $class . $id . '>' . do_shortcode($content);
}
add_shortcode('div', 'custom_shortcode');

function custom_shortcode_end($atts, $content = null)
{
  return '</div>';
}
add_shortcode('/div', 'custom_shortcode_end');
