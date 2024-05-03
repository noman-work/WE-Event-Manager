<?php
/*
Plugin Name: WE Event Manager
Plugin URI: https://nomanwc.com/
Description: Manage events using custom post type.
Version: 1.0
Author: Abdullah Al Noman
Author URI: https://nomanwc.com/
License: GPLv2 or later
Text Domain: we-event-manager
*/

/**
 * Set constants for file and directory paths.
 */
if (!defined('EVENT_SITES_FILE')) {
    define('EVENT_SITES_FILE', __FILE__);
}

if (!defined('EVENT_SITES_DIR')) {
    define('EVENT_SITES_DIR', plugin_dir_path(EVENT_SITES_FILE));
}

// Include the main class file for the custom post type
require_once EVENT_SITES_DIR . 'inc/class-events-post-type.php';
require_once EVENT_SITES_DIR . 'inc/class-event-metadata-handler.php';
require_once EVENT_SITES_DIR . 'inc/class-event-calendar-admin.php';
require_once EVENT_SITES_DIR . 'inc/class-submission.php';


//Style for the Plugin
function wwm_enqueue_calendar_style()
{
    $screen = get_current_screen();

    // Check if the current screen is the events calendar page
    if ($screen && $screen->id === 'events_page_event-calendar-page') {
        wp_enqueue_style('wwm_admin_calendar_style', plugin_dir_url(__FILE__) . 'admin/css/calendar.css');

        wp_enqueue_script('custom-calendar-script', plugin_dir_url(__FILE__) . 'admin/js/calendar.js', array('jquery'), '1.0', true);

        // Pass PHP variables to JavaScript
        wp_localize_script('custom-calendar-script', 'calendar_ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'current_month' => date('n'),
            'current_year' => date('Y')
        ));
    }
}
add_action('admin_enqueue_scripts', 'wwm_enqueue_calendar_style');

// Enqueue CSS conditionally based on the template file
function enqueue_custom_css_for_event_template()
{
    if (is_singular('events')) {
        wp_enqueue_style('custom-event-template-css', plugin_dir_url(__FILE__) . 'front/css/event-template.css');
    }
}
add_action('wp_enqueue_scripts', 'enqueue_custom_css_for_event_template');


// Instantiate the Events_Post_Type class
if (class_exists('Events_Post_Type')) {
    new Events_Post_Type();
}

// Instantiate the Event_Metadata_Handler class
if (class_exists('Event_Metadata_Handler')) {
    new Event_Metadata_Handler();
}

// Instantiate the eventCalendarAdmin class
if (class_exists('eventCalendarAdmin')) {
    new EventCalendarAdmin();
}

// Instantiate the new EventSubmissionForm(); class
if (class_exists('EventSubmissionForm')) {
    new EventSubmissionForm();
}
