<?php
function register_events_post_type()
{
  $labels = array(
    'name'               => _x('Events', 'post type general name', 'textdomain'),
    'singular_name'      => _x('Event', 'post type singular name', 'textdomain'),
    'menu_name'          => _x('Events', 'admin menu', 'textdomain'),
    'name_admin_bar'     => _x('Event', 'add new on admin bar', 'textdomain'),
    'add_new'            => _x('Add New', 'event', 'textdomain'),
    'add_new_item'       => __('Add New Event', 'textdomain'),
    'new_item'           => __('New Event', 'textdomain'),
    'edit_item'          => __('Edit Event', 'textdomain'),
    'view_item'          => __('View Event', 'textdomain'),
    'all_items'          => __('All Events', 'textdomain'),
    'search_items'       => __('Search Events', 'textdomain'),
    'parent_item_colon'  => __('Parent Events:', 'textdomain'),
    'not_found'          => __('No Events found.', 'textdomain'),
    'not_found_in_trash' => __('No Events found in Trash.', 'textdomain')
  );

  $args = array(
    'labels'             => $labels,
    'public'             => true,
    'show_in_rest'       => true,
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => false,
    'query_var'          => true,
    'rewrite'            => array('slug' => 'event'),
    'capability_type'    => 'post',
    'capabilities' => array(
      'create_posts' => 'edit_posts',
      'edit_posts' => 'edit_posts',
      'edit_post' => 'edit_post',
      'edit_others_posts' => 'edit_posts',
      'publish_posts' => 'edit_posts',
      'delete_others_posts' => 'edit_posts',
    ),
    'map_meta_cap' => true,
    'has_archive'        => true,
    'hierarchical'       => false,
    'menu_position'      => null,
    'supports'           => array('title', 'editor', 'thumbnail')
  );

  register_post_type('event', $args);
}

add_action('init', 'register_events_post_type');
function add_event_submenu()
{
  add_submenu_page(
    'sixonesix-settings', // Parent slug
    'All Events',        // Page title
    '🗒️All Events',        // Menu title
    'edit_posts',     // Capability
    'edit.php?post_type=event' // Menu slug
  );

  add_submenu_page(
    'sixonesix-settings', // Parent slug
    'Add New Event',     // Page title
    '   +Add New Eventt',     // Menu title
    'edit_posts',     // Capability
    'post-new.php?post_type=event' // Menu slug
  );
}

add_action('admin_menu', 'add_event_submenu');

$PLUGIN_NAME = strtolower(get_plugin_info()['Name']);
$HOSTNAME = get_home_url();
$REST_API_BASE = $PLUGIN_NAME . '/v1';

// add new event rest api if user is admin
function add_event_rest_api()
{
  global $REST_API_BASE;
  register_rest_route($REST_API_BASE, '/event', array(
    'methods' => 'POST',
    'callback' => 'create_event',
    'permission_callback' => function () {
      return current_user_can('edit_posts');
    },
    'args' => array(
      'title' => array(
        'required' => true,
        'validate_callback' => function ($param, $request, $key) {
          return is_string($param);
        }
      ),
      'content' => array(
        'required' => false,
        'validate_callback' => function ($param, $request, $key) {
          return is_string($param);
        }
      ),
      'status' => array(
        'required' => false,
        'validate_callback' => function ($param, $request, $key) {
          return is_string($param);
        }
      ),
      'date' => array(
        'required' => false,
        'validate_callback' => function ($param, $request, $key) {
          return is_string($param);
        }
      )
    )
  ));
}

add_action('rest_api_init', 'add_event_rest_api');

function create_event(WP_REST_Request $request)
{
  $title = $request->get_param('title');
  $content = $request->get_param('content') ? $request->get_param('content') : '';
  $status = $request->get_param('status') ? $request->get_param('status') : 'publish';
  $date = $request->get_param('date') ? $request->get_param('date') : date('Y-m-d H:i:s');

  $post_id = wp_insert_post(array(
    'post_title' => $title,
    'post_content' => $content,
    'post_status' => $status,
    'post_date' => $date,
    'post_type' => 'event'
  ));

  return new WP_REST_Response($post_id, 123);
}
