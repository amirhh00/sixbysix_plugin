<?php
function sixonesix_add_clubxq_setting()
{
  add_submenu_page(
    'sixonesix-settings', // Parent slug
    'ClubXq', // Menu title
    '🌐 ClubXq', // Page title
    'edit_posts', // Capability
    'sixonesix-clubxq', // Menu slug
    'sixonesix_clubxq_page' // Function to display the page content
  );
}
add_action('admin_menu', 'sixonesix_add_clubxq_setting');

// add <script> tag to the head of the admin page
function sixonesix_clubxq_admin_head()
{
  // check if the current page is the ClubXQ settings page
  if (isset($_GET['page']) && $_GET['page'] === 'sixonesix-clubxq') {
    echo '<script src="https://unpkg.com/@tailwindcss/browser@4"></script>';
  }
}
add_action('admin_head', 'sixonesix_clubxq_admin_head');

// get all contact form 7 forms in a list to show them in the admin page
function sixonesix_get_contact_form_7_forms()
{
  $args = array(
    'post_type' => 'wpcf7_contact_form',
    'posts_per_page' => -1,
  );

  $forms = get_posts($args);
  // save id and title of each form in an array
  $form_list = array();
  foreach ($forms as $form) {
    $form_list[] = array(
      "id" => $form->ID,
      "title" => $form->post_title,
    );
  }

  return $form_list;
}


function sixonesix_clubxq_page()
{
  $form7_forms = sixonesix_get_contact_form_7_forms();
  // $cover_options has a video url, title, and description, logo
  $cover_options = get_option('sixonesix_clubxq_video_cover_options');
  if (!$cover_options) {
    $cover_options = array(
      'video_url' => '',
      'title' => '',
      'description' => '',
      'logo' => '',
    );
  }
  // $clubCard_options has 3 club card options, and each option has a title, image and rich text
  $clubCard_options = get_option('sixonesix_clubxq_clubCard_options');
  if (!$clubCard_options) {
    $clubCard_options = array(
      'option1' => array(
        'title' => '',
        'image' => '',
        'rich_text' => '',
      ),
      'option2' => array(
        'title' => '',
        'image' => '',
        'rich_text' => '',
      ),
      'option3' => array(
        'title' => '',
        'image' => '',
        'rich_text' => '',
      ),
    );
  }
?>
  <h1>ClubXQ Integration</h1>

  <?php
  // show form7 forms in a list
  if (count($form7_forms) > 0) {
  ?>
    <h2>Choose a form to integrate with ClubXQ as the newsletter form</h2>
    <form action="" method="post">
      <select name="form7_form_id" id="form7_form_id">
        <?php
        foreach ($form7_forms as $form) {
          echo "<option value='{$form['id']}'";
          if ($form['id'] == get_option('sixonesix_clubxq_form7_form_id')) {
            echo " selected";
          }
          echo ">{$form['title']}</option>";
        }
        ?>
      </select>
      <button class="p-2 border cursor-pointer border-black hover:bg-black hover:text-white" type="submit">Save</button>
    </form>
  <?php
  } else {
    echo "<p>No Contact Form 7 forms found.</p>";
  }
  ?>
  <h2> choose the video to show in the clubxq page as the cover</h2>
  <form action="" method="post" class="flex flex-col container">
    <label for="video_url">Video URL</label>
    <input type="text" name="video_url" id="video_url" value="<?php echo $cover_options['video_url'] ?>">
    <button type="button" class="button" id="video_url_button">Select Video</button>
    <label for="title">Title</label>
    <input type="text" name="title" id="title" value="<?php echo $cover_options['title'] ?>">
    <label for="description">Description</label>
    <input type="text" name="description" id="description" value="<?php echo $cover_options['description'] ?>">
    <label for="logo">Logo</label>
    <input type="text" name="logo" id="logo" value="<?php echo $cover_options['logo'] ?>">
    <button type="button" class="button" id="logo_button">Select Logo</button>
    <button class="p-2 border cursor-pointer border-black hover:bg-black hover:text-white" type="submit">Save</button>
  </form>

  <script>
    jQuery(document).ready(function($) {
      $('#video_url_button').click(function(e) {
        e.preventDefault();
        var videoUploader = wp.media({
          title: 'Select Video',
          button: {
            text: 'Use this video'
          },
          multiple: false
        }).on('select', function() {
          var attachment = videoUploader.state().get('selection').first().toJSON();
          $('#video_url').val(attachment.url);
        }).open();
      });

      $('#logo_button').click(function(e) {
        e.preventDefault();
        var logoUploader = wp.media({
          title: 'Select Logo',
          button: {
            text: 'Use this logo'
          },
          multiple: false
        }).on('select', function() {
          var attachment = logoUploader.state().get('selection').first().toJSON();
          $('#logo').val(attachment.url);
        }).open();
      });
    });
  </script>
  <h2>Club Card</h2>
  <p> create the 3 club card options here </p>
  <form action="" method="post" class="flex flex-col container gap-2">
    <div class="flex items-baseline gap-4 border border-black p-2">
      <h3>Option1: </h3>
      <div class="flex flex-col gap-2 ">
        <div>
          <label for="option1_title" class="mr-[42px]">Title:
          </label>
          <input type="text" name="option1_title" id="option1_title" value="<?php echo $clubCard_options['option1']['title'] ?>">
        </div>
        <div>
          <label for="option1_image" class="mr-[31px]">Image: </label>
          <input type="text" name="option1_image" id="option1_image" value="<?php echo $clubCard_options['option1']['image'] ?>">
          <button type="button" class="button" id="option1_image_button">Select Image</button>
        </div>
        <div class="flex items-center gap-2">
          <label for="option1_rich_text">description:</label>
          <?php
          wp_editor($clubCard_options['option1']['rich_text'], 'option1_rich_text', array(
            'textarea_name' => 'option1_rich_text',
            'media_buttons' => false,
            'textarea_rows' => 5,
            'teeny' => false,
            'quicktags' => true,
          ));
          ?>
        </div>
      </div>
    </div>
    <div class="flex items-baseline gap-4 border border-black p-2">
      <h3>Option2: </h3>
      <div class="flex flex-col gap-2 ">
        <div>
          <label for="option2_title" class="mr-[42px]">Title:
          </label>
          <input type="text" name="option2_title" id="option2_title" value="<?php echo $clubCard_options['option2']['title'] ?>">
        </div>
        <div>
          <label for="option2_image" class="mr-[31px]">Image: </label>
          <input type="text" name="option2_image" id="option2_image" value="<?php echo $clubCard_options['option2']['image'] ?>">
          <button type="button" class="button" id="option2_image_button">Select Image</button>
        </div>
        <div class="flex items-center gap-2">
          <label for="option2_rich_text">description:</label>
          <?php
          wp_editor($clubCard_options['option2']['rich_text'], 'option2_rich_text', array(
            'textarea_name' => 'option2_rich_text',
            'media_buttons' => false,
            'textarea_rows' => 5,
            'teeny' => false,
            'quicktags' => true,
          ));
          ?>
        </div>
      </div>
    </div>
    <div class="flex items-baseline gap-4 border border-black p-2">
      <h3>Option3: </h3>
      <div class="flex flex-col gap-2 ">
        <div>
          <label for="option3_title" class="mr-[42px]">Title:
          </label>
          <input type="text" name="option3_title" id="option3_title" value="<?php echo $clubCard_options['option3']['title'] ?>">
        </div>
        <div>
          <label for="option3_image" class="mr-[31px]">Image: </label>
          <input type="text" name="option3_image" id="option3_image" value="<?php echo $clubCard_options['option3']['image'] ?>">
          <button type="button" class="button" id="option3_image_button">Select Image</button>
        </div>
        <div class="flex items-center gap-2">
          <label for="option3_rich_text">description:</label>
          <?php
          wp_editor($clubCard_options['option3']['rich_text'], 'option3_rich_text', array(
            'textarea_name' => 'option3_rich_text',
            'media_buttons' => false,
            'textarea_rows' => 5,
            'teeny' => false,
            'quicktags' => true,
          ));
          ?>
        </div>
      </div>
    </div>
    <button class="p-2 border cursor-pointer border-black hover:bg-black hover:text-white" type="submit">Save</button>
  </form>
  <script>
    jQuery(document).ready(function($) {
      $('#option1_image_button').click(function(e) {
        e.preventDefault();
        var option1_imageUploader = wp.media({
          title: 'Select Image',
          button: {
            text: 'Use this Image'
          },
          multiple: false
        }).on('select', function() {
          var attachment = option1_imageUploader.state().get('selection').first().toJSON();
          $('#option1_image').val(attachment.url);
        }).open();
      });

      $('#option2_image_button').click(function(e) {
        e.preventDefault();
        var option2_imageUploader = wp.media({
          title: 'Select Image',
          button: {
            text: 'Use this Image'
          },
          multiple: false
        }).on('select', function() {
          var attachment = option2_imageUploader.state().get('selection').first().toJSON();
          $('#option2_image').val(attachment.url);
        }).open();
      });

      $('#option3_image_button').click(function(e) {
        e.preventDefault();
        var option3_imageUploader = wp.media({
          title: 'Select Image',
          button: {
            text: 'Use this Image'
          },
          multiple: false
        }).on('select', function() {
          var attachment = option3_imageUploader.state().get('selection').first().toJSON();
          $('#option3_image').val(attachment.url);
        }).open();
      });
    });
  </script>
<?php
}

// add wordpress media selector scripts to this page
function sixonesix_clubxq_admin_scripts()
{
  // check if the current page is the ClubXQ settings page
  if (isset($_GET['page']) && $_GET['page'] === 'sixonesix-clubxq') {
    wp_enqueue_media();
  }
}
add_action('admin_enqueue_scripts', 'sixonesix_clubxq_admin_scripts');

// save the selected form7 form id as an option
if (isset($_POST['form7_form_id'])) {
  update_option('sixonesix_clubxq_form7_form_id', $_POST['form7_form_id']);
}

// save the cover options as an option
if (isset($_POST['video_url']) && isset($_POST['title']) && isset($_POST['description']) && isset($_POST['logo'])) {
  update_option('sixonesix_clubxq_video_cover_options', array(
    'video_url' => $_POST['video_url'],
    'title' => $_POST['title'],
    'description' => $_POST['description'],
    'logo' => $_POST['logo'],
  ));
}

// save the club card options as an option
if (isset($_POST['option1_title']) && isset($_POST['option1_image']) && isset($_POST['option1_rich_text']) && isset($_POST['option2_title']) && isset($_POST['option2_image']) && isset($_POST['option2_rich_text']) && isset($_POST['option3_title']) && isset($_POST['option3_image']) && isset($_POST['option3_rich_text'])) {
  update_option('sixonesix_clubxq_clubCard_options', array(
    'option1' => array(
      'title' => $_POST['option1_title'],
      'image' => $_POST['option1_image'],
      'rich_text' => $_POST['option1_rich_text'],
    ),
    'option2' => array(
      'title' => $_POST['option2_title'],
      'image' => $_POST['option2_image'],
      'rich_text' => $_POST['option2_rich_text'],
    ),
    'option3' => array(
      'title' => $_POST['option3_title'],
      'image' => $_POST['option3_image'],
      'rich_text' => $_POST['option3_rich_text'],
    ),
  ));
}

// set a rest api endpoint to get the clubxq options
add_action('rest_api_init', function () {
  register_rest_route('sixonesix/v1', '/clubxq', array(
    'methods' => 'GET',
    'callback' => 'sixonesix_get_clubxq_options',
  ));
});

function sixonesix_get_clubxq_options()
{
  $form7_form_id = get_option('sixonesix_clubxq_form7_form_id');
  $cover_options = get_option('sixonesix_clubxq_video_cover_options');
  $clubCard_options = get_option('sixonesix_clubxq_clubCard_options');
  return array(
    'form7_form_id' => $form7_form_id,
    'cover_options' => $cover_options,
    'clubCard_options' => $clubCard_options,
  );
}
