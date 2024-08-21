<?php
// Add submenu to the WordPress admin panel
function sixonesix_add_calendar_submenu()
{
  add_submenu_page(
    'sixonesix-settings', // Parent slug
    'Events Calendar', // Menu title
    '📅 Events Calendar', // Page title
    'edit_posts', // Capability
    'sixonesix-calendar', // Menu slug
    'sixonesix_calendar_page' // Function to display the page content
  );
}
add_action('admin_menu', 'sixonesix_add_calendar_submenu');

$PLUGIN_NAME = strtolower(get_plugin_info()['Name']);
$HOSTNAME = get_home_url();
$REST_API_BASE = $PLUGIN_NAME . '/v1';
$is_admin_dashboardPage = strpos($_SERVER['REQUEST_URI'], 'sixonesix-calendar') !== false && current_user_can('edit_posts');

function sixonesix_calendar_page()
{
  global $REST_API_BASE, $HOSTNAME, $is_admin_dashboardPage;
  // Check if the current page is the calendar page and user is an admin
  $background_image = get_option('sixonesix_calendar_background', '');
  $allEvents = get_posts(array(
    'post_type' => 'event',
    'numberposts' => -1
  ));
  foreach ($allEvents as &$event) {
    $event->featured_image = get_the_post_thumbnail_url($event->ID, 'thumbnail');
  }
  unset($event);

?>
  <div class="wrap">
    <h1>Calendar</h1>
    <?php if ($is_admin_dashboardPage) : ?>
      <button class="button" style="padding: 5px;margin: 10px auto;" id="upload-background">Upload Calendar Background</button>
    <?php endif; ?>
    <div style="display: flex; gap: 16px; justify-content: space-between; width: 100%;">
      <button class="btn btn-reverted" id="prevMonth">Previous</button>
      <h3 class="currentMonth">
        <?php
        $monthNames = [
          'January',
          'February',
          'March',
          'April',
          'May',
          'June',
          'July',
          'August',
          'September',
          'October',
          'November',
          'December'
        ];
        echo $monthNames[date('n') - 1] . ' ' . date('Y');
        ?>
      </h3>
      <button class="btn btn-reverted" id="nextMonth">Next</button>
    </div>
    <br>
    <div id="daysinweek" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px;">
      <div style="text-align: center;">Sunday</div>
      <div style="text-align: center;">Monday</div>
      <div style="text-align: center;">Tuesday</div>
      <div style="text-align: center;">Wednesday</div>
      <div style="text-align: center;">Thursday</div>
      <div style="text-align: center;">Friday</div>
      <div style="text-align: center;">Saturday</div>
    </div>
    <!-- calendar -->
    <div id="calendar">
      <?php
      // Render the calendar days totalNumberOfGrids
      $totalNumberOfGrids = 7 * ceil((date('w', strtotime('last day of this month')) + date('d')) / 7);
      for ($i = 0; $i < $totalNumberOfGrids; $i++) {
        echo '<div class="day empty php"></div>';
      }
      ?>
    </div>
    <!-- END of calendar -->
    <?php if ($is_admin_dashboardPage) : ?>
      <dialog style="min-width: 250px; padding:0;" id="setArtistForTheDay"
        onmousedown="event.target==this && this.close(-1)"
        onclose="this.returnValue==-1">
        <form
          style="display: flex;flex-direction: column; padding: 20px; gap: 12px;align-items: center;"
          action="<?php echo rest_url($REST_API_BASE . '/calendar'); ?>"
          method="POST">
          <span>
            <label for="artist">Select Artist:</label> <br />
            <select name="event" id="event">
              <option selected value> select an Event </option>
              <?php foreach ($allEvents as $event) : ?>
                <option value="<?php echo $event->ID; ?>"><?php echo $event->post_title; ?></option>
              <?php endforeach; ?>
            </select>
            <button id="previewSelectedEvent" title="preview selected event" type="button" class="button" onclick="goToPreviewEvent()" style="display: inline-flex; align-items: center; padding: 0px;">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false">
                <path d="M19.5 4.5h-7V6h4.44l-5.97 5.97 1.06 1.06L18 7.06v4.44h1.5v-7Zm-13 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3H17v3a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h3V5.5h-3Z"></path>
              </svg>
            </button>
            <button id="editSelectedEvent" title="edit selected event" type="button" class="button" onclick="goToEditEvent()" style="display: inline-flex; align-items: center; padding: 4px;">
              <svg style="width: 18px; height: 18px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                <path d="M441 58.9L453.1 71c9.4 9.4 9.4 24.6 0 33.9L424 134.1 377.9 88 407 58.9c9.4-9.4 24.6-9.4 33.9 0zM209.8 256.2L344 121.9 390.1 168 255.8 302.2c-2.9 2.9-6.5 5-10.4 6.1l-58.5 16.7 16.7-58.5c1.1-3.9 3.2-7.5 6.1-10.4zM373.1 25L175.8 222.2c-8.7 8.7-15 19.4-18.3 31.1l-28.6 100c-2.4 8.4-.1 17.4 6.1 23.6s15.2 8.5 23.6 6.1l100-28.6c11.8-3.4 22.5-9.7 31.1-18.3L487 138.9c28.1-28.1 28.1-73.7 0-101.8L474.9 25C446.8-3.1 401.2-3.1 373.1 25zM88 64C39.4 64 0 103.4 0 152L0 424c0 48.6 39.4 88 88 88l272 0c48.6 0 88-39.4 88-88l0-112c0-13.3-10.7-24-24-24s-24 10.7-24 24l0 112c0 22.1-17.9 40-40 40L88 464c-22.1 0-40-17.9-40-40l0-272c0-22.1 17.9-40 40-40l112 0c13.3 0 24-10.7 24-24s-10.7-24-24-24L88 64z" />
              </svg>
            </button>

          </span>
          <button class="button" type="submit">Set Event</button>
        </form>
        <script>
          document.getElementById('event').addEventListener('change', function(event) {
            const previewButton = document.querySelector('button#previewSelectedEvent');
            const editButton = document.querySelector('button#editSelectedEvent');
            const submitButton = document.querySelector('button[type="submit"]');
            const defaultOption = document.querySelector('option[value=""]');
            if (document.getElementById('event').value) {
              submitButton.textContent = 'Set Event';
              previewButton.style.display = 'inline-flex';
              editButton.style.display = 'inline-flex';
            } else {
              submitButton.textContent = 'Remove Event';
              previewButton.style.display = 'none';
              editButton.style.display = 'none';

            }
          });

          function goToPreviewEvent() {
            const selectedEvent = document.getElementById('event').value;
            const previewUrl = '<?php echo $HOSTNAME; ?>/?p=' + selectedEvent;
            window.open(previewUrl, '_blank');
          }

          function goToEditEvent() {
            const selectedEvent = document.getElementById('event').value;
            const editUrl = '<?php echo $HOSTNAME; ?>/wp-admin/post.php?post=' + selectedEvent + '&action=edit';
            window.open(editUrl, '_blank');
          }
        </script>
      </dialog>
    <?php endif; ?>
  </div>
  <script>
    function handleDayClick(event) {
      const date = event.target.dataset.date;
      const eventId = event.target.dataset.eventid;
      <?php if ($is_admin_dashboardPage) : ?>
        const modal = document.querySelector('dialog#setArtistForTheDay');
        const eventSelect = modal.querySelector('select');
        if (!!eventId) eventSelect.value = eventId;

        modal.showModal();
        // add the date to the body of the post request form
        const modalForm = modal.querySelector('form');

        function handleModalSubmit(event) {
          event.preventDefault();
          const formData = new FormData(event.target);
          formData.append('date', date);
          fetch(event.target.action, {
              method: event.target.method,
              body: formData,
              headers: {
                'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
              }
            }).then(response => response.text())
            .then(data => {
              const parsedData = JSON.parse(data);
              const eventId = parsedData[date];
              const dayButton = document.querySelector(`button[data-date="${date}"]`);
              dayButton.dataset.eventid = eventId;
              if (!!eventId) {
                const thumbnail = getTodayFeaturedImage(Number(eventId));
                dayButton.dataset.bgurl = !!thumbnail;
                dayButton.innerHTML = thumbnail ? `<img src="${thumbnail}" alt="thumbnail">` : date.split('-')[2];
              } else {
                delete dayButton.dataset.bgurl;
                dayButton.innerHTML = date.split('-')[2];
              }
            }).finally(() => {
              modal.close(-1);
            });
        }
        modal.addEventListener('close', function(event) {
          modalForm.removeEventListener('submit', handleModalSubmit);
        });
        modalForm.addEventListener('submit', handleModalSubmit);
      <?php else : ?>
        if (!!eventId) {
          const eventUrl = '<?php echo $HOSTNAME; ?>/?p=' + eventId;
          window.open(eventUrl, '_blank');
        }
      <?php endif; ?>
    }

    function getTodayFeaturedImage(eventId) {
      const events = <?php echo json_encode($allEvents); ?>;
      const event = events.find(event => event.ID === eventId);
      return event ? event.featured_image : false;
    }

    document.addEventListener('DOMContentLoaded', function() {
      const calendar = document.getElementById('calendar');
      const prevMonth = document.getElementById('prevMonth');
      const nextMonth = document.getElementById('nextMonth');
      const uploadButton = document.getElementById('upload-background');
      let currentDate = new Date();

      function renderCalendar(date) {
        const year = date.getFullYear();
        const month = date.getMonth();
        const firstDay = new Date(year, month, 1).getDay();
        const lastDate = new Date(year, month + 1, 0).getDate();
        const monthNames = <?php echo json_encode($monthNames); ?>;

        document.querySelector('.currentMonth').textContent = `${monthNames[month]} ${year}`;
        const REST_API_BASE = '<?php echo $HOSTNAME . '/wp-json/' . $REST_API_BASE; ?>';
        // calculate the total number of grids in the calendar
        const totalNumberOfGrids = 7 * Math.ceil((firstDay + lastDate) / 7);
        console.log('totalGridNumOfthisMonth :', totalNumberOfGrids);
        fetch(`${REST_API_BASE}/calendar?month=${month + 1}&year=${year}`)
          .then(response => response.json())
          .then(allEventsForMonth => {
            calendar.innerHTML = '';
            for (let i = 0; i < firstDay; i++) {
              calendar.innerHTML += '<div class="day empty"></div>';
            }
            for (let i = 1; i <= lastDate; i++) {
              // const isCurrentDay = i === new Date().getDate() && month === new Date().getMonth() && year === new Date().getFullYear();
              // month and day should be zero-padded
              let isCurrentDay = false;
              if (i === new Date().getDate())
                if (month === new Date().getMonth())
                  if (year === new Date().getFullYear())
                    isCurrentDay = true;

              const date = `${year}-${(month + 1).toString().padStart(2, '0')}-${i.toString().padStart(2, '0')}`;
              const todayEvent = allEventsForMonth[date];
              const todayThumbnail = getTodayFeaturedImage(Number(todayEvent));
              calendar.innerHTML += `<button onclick="handleDayClick(event)" ${todayThumbnail ? `data-bgurl="${!!todayThumbnail}"` : ''} data-date="${date}" data-eventId="${todayEvent}" class="day ${isCurrentDay ? 'current' : ''}">${todayThumbnail ? `<img src="${todayThumbnail}" alt="thumbnail">`: i}</button>`;
            }
            // Add empty divs to complete the grid, if necessary each row should have 7 days
            const totalDays = firstDay + lastDate;
            const remainingDays = totalDays % 7;
            if (remainingDays !== 0) {
              for (let i = 0; i < 7 - remainingDays; i++) {
                calendar.innerHTML += '<div class="day empty"></div>';
              }
            }
          });

      }

      prevMonth.addEventListener('click', function() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar(currentDate);
      });

      nextMonth.addEventListener('click', function() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar(currentDate);
      });

      renderCalendar(currentDate);

      uploadButton?.addEventListener('click', function() {
        const customUploader = wp.media({
          title: 'Select Calendar Background',
          button: {
            text: 'Use this image'
          },
          multiple: false
        }).on('select', function() {
          const attachment = customUploader.state().get('selection').first().toJSON();
          const imageUrl = attachment.url;
          calendar.style.backgroundImage = `url(${imageUrl})`;

          // Save the selected image URL in the database
          jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
              action: 'save_calendar_background',
              image_url: imageUrl
            },
            success: function(response) {
              console.log('Background image saved:', response);
            }
          });
        }).open();
      });
    });
  </script>
  <style>
    .wrap {
      max-width: 940px;
      margin: 0 auto;
    }

    .btn {
      min-width: 100px;
      cursor: pointer;
    }

    #calendar {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      background-image: url('<?php echo esc_url($background_image); ?>');
      background-size: cover;
      background-clip: padding-box;
      background-color: white;
      border: 4px solid white;
    }

    #calendar * {
      outline: none !important;
    }

    /* .day that has not string data-bgurl */
    .day:not([data-bgurl]) {
      background-color: black;
      font-size: xx-large;
      color: white;
    }


    .day,
    .empty {
      padding: 10px;
      border: 4px solid white;
      text-align: center;
      aspect-ratio: 1;
    }

    .day:not(.empty):hover {
      border-color: #2bff00 !important;
    }

    .day.current:not(.empty) {
      border-color: #2bff00 !important;
    }

    .day.empty {
      background-color: white !important;
    }

    .day:not(.empty) {
      cursor: pointer;

      /* if background exists in php have mix blend mode */
      <?php if ($background_image) : ?>mix-blend-mode: lighten;
      <?php endif; ?>
    }

    /*  .day that has data-bgurl */
    .day[data-bgurl] {
      mix-blend-mode: unset !important;
      position: relative;
    }

    .day[data-bgurl] img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      position: absolute;
      top: 0;
      left: 0;
      pointer-events: none;
    }
  </style>
<?php
}

// make shortcode to display the calendar
function sixonesix_calendar_shortcode()
{
  ob_start();
  sixonesix_calendar_page();
  return ob_get_clean();
}
add_shortcode('eventcal', 'sixonesix_calendar_shortcode');

// Handle AJAX request to save the background image URL
function sixonesix_save_calendar_background()
{
  if (isset($_POST['image_url'])) {
    update_option('sixonesix_calendar_background', esc_url_raw($_POST['image_url']));
    wp_send_json_success('Image URL saved');
  } else {
    wp_send_json_error('No image URL provided');
  }
}
add_action('wp_ajax_save_calendar_background', 'sixonesix_save_calendar_background');


add_action('rest_api_init', function () use ($REST_API_BASE) {
  register_rest_route($REST_API_BASE, '/calendar', array(
    'methods' => 'POST',
    'callback' => 'sixonesix_set_event_for_day',
    'permission_callback' => function () {
      return current_user_can('edit_posts');
    },
    'args' => array(
      'date' => array(
        'required' => true,
        'validate_callback' => function ($param, $request, $key) {
          return preg_match('/^\d{4}-\d{2}-\d{2}$/', $param);
        }
      ),
      'event' => array(
        'required' => true,
        'validate_callback' => function ($param, $request, $key) {
          return is_numeric($param) || $param === '';
        }
      )
    )
  ));

  register_rest_route($REST_API_BASE, '/calendar', array(
    'methods' => 'GET',
    'callback' => 'sixonesix_get_calendar_data',
    // everyone can view the calendar
    'permission_callback' => '__return_true',
    'args' => array(
      'month' => array(
        'required' => true,
        'validate_callback' => function ($param, $request, $key) {
          return is_numeric($param) && $param >= 1 && $param <= 12;
        }
      ),
      'year' => array(
        'required' => true,
        'validate_callback' => function ($param, $request, $key) {
          return is_numeric($param) && $param >= 1970;
        }
      )
    )
  ));
});

/** 
 * sets the event for the day in the calendar
 * @param date: the date to set the event for in the format 'YYYY-mm-dd'
 * @param event: the ID of the event to set for the day
 * @return string
 */
function sixonesix_set_event_for_day(WP_REST_Request $request)
{
  $date = $request->get_param('date');
  $day = date('d', strtotime($date));
  $month = date('m', strtotime($date));
  $year = date('Y', strtotime($date));
  $event = $request->get_param('event');
  $formattedDate = $year . '-' . $month . '-' . $day;
  if ($event === '') {
    delete_option('sixonesix_calendar_' . $formattedDate);
  } else {
    update_option('sixonesix_calendar_' . $formattedDate, $event);
  }
  // send json as response to the client
  $jsonResponse = array($formattedDate => $event);
  return rest_ensure_response($jsonResponse);
}

/**
 * removes the event for the day in the calendar
 * @param date: the date to remove the event for in the format 'YYYY-mm-dd'
 * @return string
 */
function sixonesix_remove_event_for_day(WP_REST_Request $request)
{
  $date = $request->get_param('date');
  $formattedDate = date('Y-m-d', strtotime($date));
  delete_option('sixonesix_calendar_' . $formattedDate);
  return rest_ensure_response(array("$formattedDate" => ''));
}

/**
 * gets month and year from the request query params and returns the calendar data for that month
 */
function sixonesix_get_calendar_data(WP_REST_Request $request)
{
  $month = $request->get_param('month');
  $year = $request->get_param('year');
  $firstDay = new DateTime("$year-$month-01");
  $lastDay = new DateTime("$year-$month-01");
  $lastDay->modify('last day of this month');
  $daysInMonth = $lastDay->format('d');
  $calendarData = array();
  for ($i = 1; $i <= $daysInMonth; $i++) {
    $currentDay = new DateTime("$year-$month-$i");
    $event_id = get_option('sixonesix_calendar_' . $currentDay->format('Y-m-d'));
    $calendarData[$currentDay->format('Y-m-d')] = $event_id ? (int)$event_id : '';
  }
  return rest_ensure_response($calendarData);
}
