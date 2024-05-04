<?php
class EventCalendar
{
    public function __construct()
    {
        // Add custom admin menu item for the event calendar
        add_action('admin_menu', array($this, 'register_event_calendar_admin_page'));

        // AJAX handler to load event calendar
        add_action('wp_ajax_load_event_calendar', array($this, 'load_event_calendar'));
        add_action('wp_ajax_nopriv_load_event_calendar', array($this, 'load_event_calendar'));

        // Shortcode to display event calendar on the frontend
        add_shortcode('event_calendar', array($this, 'event_calendar_shortcode'));

        add_action('wp_enqueue_scripts', array($this, 'style_script_event_calendar'));
    }

    /**
     * Add a custom admin menu item for the event calendar.
     */
    public function register_event_calendar_admin_page()
    {
        add_submenu_page(
            'edit.php?post_type=events',
            __('Event Calendar', 'event-manager'),  // Page title
            __('Event Calendar', 'event-manager'),  // Menu title
            'manage_options',
            'event-calendar-page',
            array($this, 'render_event_calendar_page')
        );
    }

    /**
     * Render custom admin page content for event calendar.
     */
    public function render_event_calendar_page()
    {
?>
        <div class="wrap">
            <h1><?php _e('Event Calendar', 'event-manager'); ?></h1>
            <?php
            // Display calendar and events for the current month
            $this->display_event_calendar();
            ?>
        </div>
<?php
    }

    /**
     * Shortcode callback to display event calendar on the frontend.
     */
    public function event_calendar_shortcode($atts)
    {
        ob_start();
        $this->display_event_calendar();
        return ob_get_clean();
    }

    /**
     * Style for the Front end Shortcode
     */
    public function style_script_event_calendar()
    {
        wp_enqueue_style('wwm_front_calendar_style', plugin_dir_url(__FILE__) . 'front/css/calendar.css');

        wp_enqueue_script('admin-calendar-script', plugin_dir_url(__FILE__) . 'front/js/calendar.js', array('jquery'), '1.0', true);

        // Pass PHP variables to JavaScript
        wp_localize_script('admin-calendar-script', 'calendar_ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'current_month' => date('n'),
            'current_year' => date('Y')
        ));
    }

    /**
     * Display event calendar for the current month.
     */
    public function display_event_calendar()
    {
        $current_month = date('n');  // Current month (numeric without leading zero)
        $current_year = date('Y');   // Current year

        // Get all events for the current month
        $events = $this->get_events_for_month($current_month, $current_year);

        // Generate calendar markup
        echo '<div id="event-calendar">';
        echo $this->generate_calendar($current_month, $current_year, $events);
        echo '</div>';
    }

    /**
     * Retrieve events for a specific month and year.
     */
    public function get_events_for_month($month, $year)
    {
        $start_date = date('Y-m-01', strtotime("$year-$month-01"));
        $end_date = date('Y-m-t', strtotime("$year-$month-01"));

        // Query events using meta_query to filter by event_date within the month
        $events_query = new WP_Query(array(
            'post_type' => 'events',
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'event_date',
                    'value' => array($start_date, $end_date),
                    'compare' => 'BETWEEN',
                    'type' => 'DATE',
                ),
            ),
        ));

        return $events_query->posts;
    }

    /**
     * Generate calendar markup for a specific month and year.
     */
    public function generate_calendar($month, $year, $events)
    {

        // Use WordPress functions to generate calendar HTML
        $calendar_html = '<table id="calendar" class="event-calendar">';
        $calendar_html .= '<caption>' . date('F Y', strtotime("$year-$month-01")) . '</caption>';
        $calendar_html .= '<div class="calendar-nav">
        <button id="prev-month"><span class="dashicons dashicons-arrow-left-alt2"></span> Previous Month</button>
        <button id="next-month">Next Month <span class="dashicons dashicons-arrow-right-alt2"></span></button>
    </div>';

        // Define weekdays row
        $calendar_html .= '<tr class="weekdays">
       
        <th scope="col">Monday</th>
        <th scope="col">Tuesday</th>
        <th scope="col">Wednesday</th>
        <th scope="col">Thursday</th>
        <th scope="col">Friday</th>
        <th scope="col">Saturday</th>
        <th scope="col">Sunday</th>
    </tr>';
        $calendar_html .= '<tbody>';

        // Get the first day of the month
        $first_day = date('N', strtotime("$year-$month-01"));

        // Get the number of days in the month
        $days_in_month = date('t', strtotime("$year-$month-01"));

        // Initialize day counter
        $day_counter = 1;

        // Loop through each week
        for ($i = 0; $i < 6; $i++) {
            $calendar_html .= '<tr class="days">';

            // Loop through each day of the week
            for ($j = 0; $j < 7; $j++) {
                if (($i === 0 && $j < $first_day - 1) || $day_counter > $days_in_month) {
                    $calendar_html .= '<td class="day other-month"></td>';
                } else {
                    $current_date = sprintf('%04d-%02d-%02d', $year, $month, $day_counter);
                    $calendar_html .= '<td class="day"><div class="date">';

                    // Display day number
                    $calendar_html .= $day_counter;
                    $calendar_html .= '</div>';

                    // Display events for the day
                    $events_for_day = $this->get_events_for_day($current_date, $events);
                    if (!empty($events_for_day)) {
                        $calendar_html .= ' <div class="event">';
                        foreach ($events_for_day as $event) {
                            $event_start_time = get_post_meta($event->ID, 'event_start_time', true);
                            $event_end_time = get_post_meta($event->ID, 'event_end_time', true);

                            $calendar_html .= '<div class="event-desc"><a href="' . get_the_permalink($event) . '">';
                            $calendar_html .= '<strong>' . get_the_title($event) . '</strong><br>';
                            $calendar_html .= 'Start Time: ' . $event_start_time . '<br>';
                            $calendar_html .= 'End Time: ' . $event_end_time;
                            $calendar_html .= '</a></div>';
                        }
                        $calendar_html .= '</div>';
                    }

                    $calendar_html .= '</td>';
                    $day_counter++;
                }
            }

            $calendar_html .= '</tr>';
        }

        $calendar_html .= '</tbody></table>';

        return $calendar_html;
    }


    /**
     * Retrieve events for a specific day.
     */
    public function get_events_for_day($day, $events)
    {
        $events_for_day = array();

        foreach ($events as $event) {
            $event_date = get_post_meta($event->ID, 'event_date', true);
            if ($event_date === $day) {
                $events_for_day[] = $event;
            }
        }

        return $events_for_day;
    }

    public function load_event_calendar()
    {
        // Retrieve month and year from AJAX request
        $month = isset($_POST['month']) ? intval($_POST['month']) : date('n');
        $year = isset($_POST['year']) ? intval($_POST['year']) : date('Y');

        // Generate calendar HTML
        $event_calendar = new EventCalendar();
        $calendar_html = $event_calendar->generate_calendar($month, $year, $event_calendar->get_events_for_month($month, $year));

        // Output calendar HTML
        echo $calendar_html;

        // Always exit to avoid further processing
        wp_die();
    }
}
