<?php

function menus_shortcode($atts, $content = null)
{
  $attributes = shortcode_atts(array(
    'class' => '',
    'id' => '',
    'exclusive' => 'no', // Default to showing only non-exclusive menus
  ), $atts);

  $class = $attributes['class'] ? ' class="' . esc_attr($attributes['class']) . '"' : '';
  $id = $attributes['id'] ? ' id="' . esc_attr($attributes['id']) . '"' : '';

  // Query menu items
  $args = array(
    'post_type' => 'menu_item',
    'posts_per_page' => -1,
    'orderby' => 'menu_order',
    'order' => 'ASC'
  );

  // Add meta query based on exclusive attribute
  if ($attributes['exclusive'] === 'yes') {
    $args['meta_query'] = array(
      array(
        'key' => 'exclusive_menu',
        'value' => '1',
        'compare' => '='
      )
    );
  } elseif ($attributes['exclusive'] !== 'all') {
    // Default behavior - show only non-exclusive menus
    $args['meta_query'] = array(
      array(
        'relation' => 'OR',
        array(
          'key' => 'exclusive_menu',
          'value' => '1',
          'compare' => '!='
        ),
        array(
          'key' => 'exclusive_menu',
          'compare' => 'NOT EXISTS'
        )
      )
    );
  }

  // load background image from /images/ folder
  $menu_bg_url = plugin_dir_url(__FILE__) . 'images/menus-bg-pattern.png';

  $query = new WP_Query($args);
  $menu_items = [];

  if ($query->have_posts()) {
    while ($query->have_posts()) {
      $query->the_post();
      $post_id = get_the_ID();
      $menu_items[] = [
        'name' => get_the_title(),
        'days' => get_post_meta($post_id, 'days_available', true),
        'time' => get_post_meta($post_id, 'time_available', true),
        'link' => get_post_meta($post_id, 'link', true),
        'button_text' => get_post_meta($post_id, 'button_text', true) ?: 'View Menu',
        'exclusive' => get_post_meta($post_id, 'exclusive_menu', true)
      ];
    }
    wp_reset_postdata();
  }

  // Return early if no menu items
  if (empty($menu_items)) {
    return '';
  }

  // Include inline CSS
  $css = <<<CSS
<style>
.u-content-background {
    background-color: black;
}

.u-content-background .entry-title {
    text-align: center;
    color: var(--ast-global-color-5, white);
}

#sixbysixMenus {
    padding: 2rem 1rem 2rem 1rem;
    background-color: inherit;
    margin: 0 auto;
}

#sixbysixMenus * {
    margin: 0;
    margin-bottom: 0 !important;
    padding: 0;
    box-sizing: border-box;
    outline: none;
    color: var(--ast-global-color-5, white);
    transition: all 0.2s ease-in;
}

#sixbysixMenus ul {
    display: grid;
    grid-template-columns: repeat(3, minmax(250px, 1fr));
    gap: 1rem;
    list-style: none;
}

@media screen and (max-width: 860px) {
    #sixbysixMenus ul {
        grid-template-columns: 1fr;
        place-items: center;
    }

    #sixbysixMenus ul .menu-item {
        width: 100%;
    }
}

#sixbysixMenus ul .menu-item {
    display: flex;
    max-width: 350px;
    flex-direction: column;
    justify-content: space-between;
    border: 2px solid var(--ast-global-color-0, #c2b276);
    padding: 0.5rem 1rem;
    gap: 0.5rem;
    text-align: center;
    background-color: var(--ast-global-color-0, #c2b276);
}

#sixbysixMenus ul .menu-item div {
    font-size: 10px;
}

#sixbysixMenus ul .menu-item h3,
#sixbysixMenus ul .menu-item a {
    font-size: 1rem;
    font-weight: 700;
    white-space: nowrap;
    text-decoration: none;
    margin: 0 auto;
    border-bottom: 2px solid transparent;
}

#sixbysixMenus ul li.menu-item:hover {
    background-color: var(--ast-global-color-5, white);
    cursor: pointer;
}
#sixbysixMenus ul li.menu-item:hover * {
    color: var(--ast-global-color-0, black);
}
</style>
CSS;


  // Build HTML output using heredoc syntax
  $html = <<<HTML
<div id="sixbysixMenus">
    <ul{$id}{$class}>
HTML;

  foreach ($menu_items as $item) {
    $onclick = (!empty($item['link']) && $item['link'] !== '#') ?
      ' onclick="window.open(\'' . esc_url($item['link']) . '\', \'_blank\')"' : '';
    $target = (!empty($item['link']) && $item['link'] !== '#') ? ' target="_blank"' : '';

    $html .= <<<HTML
        <li class="menu-item relative"{$onclick}>
            <h3>{$item['name']}</h3>
            <div>
                <p>{$item['days']}</p>
                <p>{$item['time']}</p>
            </div>
            <a href="{$item['link']}"{$target}>{$item['button_text']}</a>
            <div class="absolute top-0 left-0 w-full h-full bg-black" style="opacity:0.07; background-size: 290px; background-image: url('{$menu_bg_url}');"></div>
        </li>
HTML;
  }

  $html .= <<<HTML
    </ul>
</div>
HTML;

  return $css . $html;
}


add_shortcode('rmenu', 'menus_shortcode');
