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
require_once EVENT_SITES_DIR . 'inc/class-event-calendar.php';
require_once EVENT_SITES_DIR . 'inc/class-event-submission.php';


//Style for the Plugin Admin
function wem_enqueue_calendar_style_admin()
{
    $screen = get_current_screen();

    // Check if the current screen is the events calendar page
    if ($screen && $screen->id === 'events_page_event-calendar-page') {
        wp_enqueue_style('wwm_admin_calendar_style', plugin_dir_url(__FILE__) . 'admin/css/calendar.css');

        wp_enqueue_script('admin-calendar-script', plugin_dir_url(__FILE__) . 'admin/js/calendar.js', array('jquery'), '1.0', true);

        // Pass PHP variables to JavaScript
        wp_localize_script('admin-calendar-script', 'calendar_ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'current_month' => date('n'),
            'current_year' => date('Y')
        ));
    }
}
add_action('admin_enqueue_scripts', 'wem_enqueue_calendar_style_admin');


// Enqueue CSS conditionally based on the template file
function wem_enqueue_calendar_style_front()
{
    if (is_singular('events')) {
        wp_enqueue_style('wem-event-template-css', plugin_dir_url(__FILE__) . 'front/css/event-template.css');
    }

    wp_enqueue_style('wem_front_calendar_style', plugin_dir_url(__FILE__) . 'front/css/calendar.css');

    wp_enqueue_script('admin-calendar-script', plugin_dir_url(__FILE__) . 'admin/js/calendar.js', array('jquery'), '1.0', true);

    // Pass PHP variables to JavaScript
    wp_localize_script('admin-calendar-script', 'calendar_ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'current_month' => date('n'),
        'current_year' => date('Y')
    ));
}
add_action('wp_enqueue_scripts', 'wem_enqueue_calendar_style_front');


// Instantiate the EventsPostType class
if (class_exists('EventsPostType')) {
    new EventsPostType();
}

// Instantiate the EventMetadataHandler class
if (class_exists('EventMetadataHandler')) {
    new EventMetadataHandler();
}

// Instantiate the EventCalendar class
if (class_exists('eventCalendar')) {
    new EventCalendar();
}

// Instantiate the new EventSubmissionForm(); class
if (class_exists('EventSubmissionForm')) {
    new EventSubmissionForm();
}
