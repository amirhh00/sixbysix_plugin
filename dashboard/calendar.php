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

function sixonesix_calendar_page()
{
  $is_admin_dashboardPage = strpos($_SERVER['REQUEST_URI'], 'sixonesix-calendar') !== false && current_user_can('edit_posts');
  global $REST_API_BASE, $HOSTNAME;
  // Check if the current page is the calendar page and user is an admin
  // current month and year mm-yyyy
  $currentMonth = date('m');
  $currentYear = date('Y');
  $background_image = get_option("sixonesix_calendar_background_$currentMonth-$currentYear");
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
      <button class="button" style="margin-bottom:16px;padding: 5px;aspect-ratio: 16/9; background-image: url('<?php echo $background_image; ?>'); background-size: cover;"
        id="upload-background">
        <p style="color: white; background-color: rgba(0, 0, 0, 0.5);">
          <?php echo $background_image ? 'Change' : 'Upload'; ?> Calendar Background
        </p>
      </button>
      <!-- delete background for this month -->
      <script>
        var $ = jQuery;
        /**
         * @typedef {Object} FilterMultiSelect
         * @property {(value: string) => boolean} hasOption
         * @property {(value: string) => void} selectOption
         * @property {(value: string) => void} deselectOption
         * @property {(value: string) => boolean} isOptionSelected
         * @property {() => void} selectAll
         * @property {() => void} deselectAll
         * @property {(value: string) => void} enableOption
         * @property {(value: string) => void} disableOption
         * @property {(value: string) => boolean} isOptionDisabled
         * @property {() => void} enable  
         * @property {() => void} disable
         * @property {(includeDisabled: boolean) => string} getSelectedOptionsAsJson
         */
        /** @type {FilterMultiSelect} */
        var select;
      </script>
      <script src="<?php echo plugin_dir_url(__FILE__) . 'js/filter-multi-select-bundle.min.js'; ?>"></script>
      <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__) . 'css/filter_multi_select.css'; ?>">
      <style>
        :root {
          --primary: #0073aa;
        }
      </style>
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
        onclose="this.returnValue==-1"
        onclick="if(event.target===this) this.close(-1)"
        onkeydown="if(event.key==='Escape') this.close(-1)">
        <form
          action="<?php echo rest_url($REST_API_BASE . '/calendar'); ?>"
          method="POST">
          <button type="button" id="closemodal" style="position: absolute; top: 0; right: 0; padding: 4px; background: none; border: 0;cursor: pointer;" onclick="this.closest('dialog').close(-1)">
            <svg style="width: 24px; height: 24px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
              <path d="M464 32H48C21.5 32 0 53.5 0 80v352c0 26.5 21.5 48 48 48h416c26.5 0 48-21.5 48-48V80c0-26.5-21.5-48-48-48zm-83.6 290.5c4.8 4.8 4.8 12.6 0 17.4l-40.5 40.5c-4.8 4.8-12.6 4.8-17.4 0L256 313.3l-66.5 67.1c-4.8 4.8-12.6 4.8-17.4 0l-40.5-40.5c-4.8-4.8-4.8-12.6 0-17.4l67.1-66.5-67.1-66.5c-4.8-4.8-4.8-12.6 0-17.4l40.5-40.5c4.8-4.8 12.6-4.8 17.4 0l66.5 67.1 66.5-67.1c4.8-4.8 12.6-4.8 17.4 0l40.5 40.5c4.8 4.8 4.8 12.6 0 17.4L313.3 256l67.1 66.5z" />
            </svg>
          </button>
          <h2 style="text-align: center;margin-bottom: 0;"></h2>
          <label for="artist">Select Events:</label>
          <div style="display: flex; gap: 6px; width: 100%;">
            <select style="flex:1" name="events[]" id="event" multiple>
              <!-- <option selected value> select an Event </option> -->
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
          </div>
          <label for="dayBackground">Select Day Background:</label>
          <div style="display: flex;align-items: center;gap: 6px;">
            <input style="width: 0;padding: 0;border: 0;" type="text" name="daybg" id="dayBackground" readonly />
            <img src="" alt="thumbnail" id="dayBackgroundThumbnail" style="width: 100px; height: 100px;outline: 1px solid black;">
            <div style="display: flex; flex-direction: column; gap: 6px;">
              <button id="selectDayBackground" type="button" class="button">Select Background</button>
              <button id="removeDayBackground" type="button" class="button" style="border-color: red; color: red">Remove Background</button>
            </div>
          </div>
          <button class="button" type="submit">Set Event</button>
        </form>
        <script>
          $(document).ready(function() {
            select = $('#event').filterMultiSelect({
              placeholderText: "nothing selected",
              filterText: "Filter",
              selectAllText: "Select All",
              labelText: "",
              selectionLimit: 0,
              caseSensitive: false,
              allowEnablingAndDisabling: true,
              allowFiltering: true,
              allowSearching: true,
              allowSelectAll: true,
              allowDeselectAll: true,
              allowStyling: true,
              enableStyling: true,
              enableFiltering: true,
              enableSearching: true,
            });
          });

          document.getElementById('removeDayBackground').addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('dayBackground').value = '';
            document.getElementById('dayBackgroundThumbnail').src = '';
          });

          document.getElementById('selectDayBackground').addEventListener('click', function(event) {
            event.preventDefault();
            var mediaUploader;
            if (mediaUploader) {
              mediaUploader.open();
              return;
            }
            mediaUploader = wp.media({
              title: 'Select Day Background',
              button: {
                text: 'Use this media'
              },
              multiple: false
            });
            mediaUploader.on('select', function() {
              var attachment = mediaUploader.state().get('selection').first().toJSON();
              document.getElementById('dayBackground').value = attachment.url;
              document.getElementById('dayBackgroundThumbnail').src = attachment.url;
            });
            mediaUploader.open();
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
      /** @type {Array<string> | undefined} */
      const eventId = event.target.dataset.eventid;
      const thumbnail = event.target.dataset.bgurl;
      <?php if ($is_admin_dashboardPage) : ?>
        const modal = document.querySelector('dialog#setArtistForTheDay');
        const eventSelect = modal.querySelector('select#event');
        const dayBackgroundImg = modal.querySelector('img#dayBackgroundThumbnail');
        select.deselectAll();
        dayBackgroundImg.src = '';
        if (thumbnail) dayBackgroundImg.src = thumbnail;
        if (eventId && eventId !== 'undefined') {
          const enabledEvents = JSON.parse(eventId);
          enabledEvents.forEach(eId => {
            select.selectOption(eId.toString());
          });
        }
        modal.querySelector('h2').textContent = date;
        // show without blocking the rest of the page
        modal.show();
        // add the date to the body of the post request form
        const modalForm = modal.querySelector('form');

        function handleModalSubmit(event) {
          event.preventDefault();
          const formData = new FormData(event.target);
          formData.append('date', date);
          const thumbnail = modal.querySelector('input#dayBackground').value;
          if (thumbnail) formData.set('daybg', thumbnail);
          // select.getSelectedOptionsAsJson() returns a stringified array of selected options: '{\n  "events": [\n    "149",\n    "148"\n  ]\n}'
          let selectedEvents = select.getSelectedOptionsAsJson();
          if (selectedEvents) selectedEvents = JSON.parse(selectedEvents);
          formData.append('events', JSON.stringify(selectedEvents['events[]'].map(Number)));
          formData.delete('events[]');
          fetch(event.target.action, {
              method: event.target.method,
              body: formData,
              headers: {
                'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
              }
            }).then(response => response.text())
            .then(data => {
              const parsedData = JSON.parse(data);
              const date = Object.keys(parsedData)[0];
              const dayButton = document.querySelector(`.day[data-date="${date}"]`);
              const events = parsedData[date].events;
              if (events === '') {
                // remove the background image
                dayButton.dataset.bgurl = '';
                dayButton.dataset.eventid = '';
              } else if (events.length > 0) {
                const dayBg = parsedData[date].daybg;
                dayButton.dataset.bgurl = dayBg;
                if (events) dayButton.dataset.eventid = events;
                if (!!dayBg) {
                  dayButton.innerHTML = `<img src="${dayBg}" alt="thumbnail">`;
                } else {
                  dayButton.innerHTML = date.split('-')[2];
                  // remove daybg data attribute
                  dayButton.removeAttribute('data-bgurl');
                }
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
        // alert with button to choose the events for the day and open selected events in new tab
        if (eventId && eventId !== 'undefined') {
          const events = JSON.parse(eventId);
          const allEvents = <?php echo json_encode($allEvents); ?>;
          alert(`Events for ${date}: ${events.map(eId => allEvents.find(e => e.ID === eId).post_title).join(', ')}`);
        } else {
          alert(`No events for ${date}`);
        }
      <?php endif; ?>
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
        // const totalNumberOfGrids = 7 * Math.ceil((firstDay + lastDate) / 7);
        const Month = `${date.getMonth() + 1}`.padStart(2, '0');
        fetch(`${REST_API_BASE}/calendar?month=${Month}&year=${year}`)
          .then(response => response.json())
          .then(
            /**
             * @typedef {{ monthBg: string | false; days: { [date: string]: { events: string, daybg: string } } }} EventsForMonth
             * 
             * @param {EventsForMonth} allEventsForMonth
             */
            (allEventsForMonth) => {
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

                const date = `${year}-${(Month).toString().padStart(2, '0')}-${i.toString().padStart(2, '0')}`;
                const todayEvent = allEventsForMonth.days[date];
                const todayThumbnail = todayEvent?.daybg;
                calendar.innerHTML += `<button 
                onclick="handleDayClick(event)" 
                ${todayThumbnail ? `data-bgurl="${todayThumbnail}"` : ''} 
                data-date="${date}" 
                data-eventId="${todayEvent?.events}"
                class="day ${isCurrentDay ? 'current' : ''}"
              >
                ${todayThumbnail ? `<img src="${todayThumbnail}" alt="thumbnail">`: i}
              </button>`;
              }
              // Add empty divs to complete the grid, if necessary each row should have 7 days
              const totalDays = firstDay + lastDate;
              const remainingDays = totalDays % 7;
              if (remainingDays !== 0) {
                for (let i = 0; i < 7 - remainingDays; i++) {
                  calendar.innerHTML += '<div class="day empty"></div>';
                }
              }
              // if the background image is set, change elements
              calendar.style.backgroundImage = allEventsForMonth.monthBg ? `url(${allEventsForMonth.monthBg})` : '';
              // set the background image to the selected image
              uploadButton.style.backgroundImage = `url(${allEventsForMonth.monthBg})`;
              if (allEventsForMonth.monthBg) {
                uploadButton.querySelector('p').textContent = 'Change Calendar Background';
                document.querySelectorAll('.day:not(.empty)').forEach(day => {
                  if (!day.dataset.bgurl) {
                    day.style.mixBlendMode = 'lighten';
                  }
                });
              } else {
                uploadButton.querySelector('p').textContent = 'Upload Calendar Background';
                document.querySelectorAll('.day:not(.empty)').forEach(day => {
                  if (!day.dataset.bgurl) {
                    day.style.mixBlendMode = 'unset';
                  }
                });
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
        const frame = wp.media({
          title: 'Select Calendar Background',
          multiple: false,
          // 
        });
        const onselectListener = frame.on('select', function() {
          const attachment = frame.state().get('selection').first().toJSON();
          const imageUrl = attachment.url;
          calendar.style.backgroundImage = `url(${imageUrl})`;
          // Save the selected image URL in the database
          jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
              action: 'save_calendar_background',
              image_url: imageUrl,
              month: `${currentDate.getMonth() + 1}`.padStart(2, '0'),
              year: currentDate.getFullYear()
            },
            headers: {
              'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
            },
            /**
             * @typedef {{ data: {image_url: string, month: string, year: string} }} response
             * @param {response} response
             */
            success: function(response) {
              // set the background image to the selected image
              calendar.style.backgroundImage = `url(${response.data.image_url})`;
              // set the background image to the selected image
              uploadButton.style.backgroundImage = `url(${response.data.image_url})`;
              uploadButton.querySelector('p').textContent = 'Change Calendar Background';
              document.querySelectorAll('.day:not(.empty)').forEach(day => {
                if (!day.dataset.bgurl) {
                  day.style.mixBlendMode = 'lighten';
                }
              });
            }
          });
        })
        frame.on('close', function() {
          onselectListener.off();
          console.log('closed');
        });
        // frame.views.view.modal.__proto__.once("open", () => {
        //   // select default background image
        //   const defaultBackgroundUrl = document.querySelector('#upload-background').style.backgroundImage;
        //   if (defaultBackgroundUrl) {
        //     const defaultBackground = defaultBackgroundUrl.replace('url("', '').replace('")', '');
        //     console.log('bg: ', defaultBackground);
        //     const selection = frame.state().get('selection');
        //     const attachment = new wp.media.model.Attachment({
        //       id: 0,
        //       url: defaultBackground
        //     });
        //     selection.add(attachment);
        //   }
        // });
        frame.open();
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

    dialog#setArtistForTheDay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(0, 0, 0, 0.5);
    }

    dialog#setArtistForTheDay form {
      display: flex;
      flex-direction: column;
      padding: 8px 20px;
      gap: 12px;
      align-items: center;
      position: absolute;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
      background-color: white;
    }

    #event {
      flex: 1;
      box-shadow: 0 0 4px rgba(0, 0, 0, 0.2);
      padding: 4px;
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
  // Check if the user is allowed to update the background image
  if (!current_user_can('edit_posts')) {
    wp_send_json_error('You are not allowed to update the background image');
  }
  // Check if the image URL is provided and month and year are provided as well
  if (isset($_POST['image_url']) && isset($_POST['month']) && isset($_POST['year'])) {
    update_option('sixonesix_calendar_background_' . esc_attr($_POST['month']) . '-' . esc_attr($_POST['year']), esc_url_raw($_POST['image_url']));
    $responseData = array(
      'image_url' => esc_url_raw($_POST['image_url']),
      'month' => esc_attr($_POST['month']),
      'year' => esc_attr($_POST['year'])
    );
    wp_send_json_success($responseData);
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
      'events' => array(
        'required' => true,
        'validate_callback' => function ($param, $request, $key) {
          // its either an empty string or an array of integers
          // echo typeof $param 
          if ($param === '') return true;

          $events = json_decode($param);
          return is_array($events) && array_reduce($events, function ($carry, $item) {
            return $carry && is_numeric($item);
          }, true);
        }
      ),
      'daybg' => array(
        'required' => false,
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
 * @param events: the IDs of the events to set for the day
 * @return string
 */
function sixonesix_set_event_for_day(WP_REST_Request $request)
{
  $date = $request->get_param('date');
  $events = $request->get_param('events');
  $events = json_decode($events);
  $dayBg = $request->get_param('daybg');
  $formattedDate = date('Y-m-d', strtotime($date));
  $dayobject = array(
    'events' => json_encode($events),
    'daybg' => $dayBg
  );
  if ($events === '' || count($events) === 0) {
    delete_option('sixonesix_calendar_' . $formattedDate);
  } else {
    update_option('sixonesix_calendar_' . $formattedDate, $dayobject);
  }
  return rest_ensure_response(array("$formattedDate" => $dayobject));
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
  $calendayData = array();
  $calendarDays = array();
  for ($i = 1; $i <= $daysInMonth; $i++) {
    $currentDay = new DateTime("$year-$month-$i");
    $dayobjectValue = get_option('sixonesix_calendar_' . $currentDay->format('Y-m-d'));
    $calendarDays[$currentDay->format('Y-m-d')] = $dayobjectValue ? $dayobjectValue : null;
  }
  // calendarDays in in calendarData object
  $calendarData = array(
    'monthBg' => get_option('sixonesix_calendar_background_' . $month . '-' . $year),
    'days' => $calendarDays
  );
  return rest_ensure_response($calendarData);
}

/**
 * gets all the calendar data for the events that are on Sundays from now until the end of the year
 */
function getAllCalendarDataOnSundays()
{
  $today = date('Y-m-d');
  $endOfYear = date('Y-12-31');
  $calendarData = array();
  $currentDate = new DateTime($today);
  $endOfYearDate = new DateTime($endOfYear);
  while ($currentDate <= $endOfYearDate) {
    if ($currentDate->format('D') === 'Sun') {
      $event_id = get_option('sixonesix_calendar_' . $currentDate->format('Y-m-d'));
      $calendarData[$currentDate->format('Y-m-d')] = $event_id ? $event_id : null;
    }
    $currentDate->modify('+1 day');
  }
  return $calendarData;
}
