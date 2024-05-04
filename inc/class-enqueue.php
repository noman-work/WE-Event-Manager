<?php

class EventEnqueue
{

    public function __construct()
    {
        // Admin enqueue
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));

        // Frontend enqueue
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
    }

    // Enqueue assets for the admin area
    public function enqueue_admin_assets()
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

    // Enqueue assets for the frontend
    public function enqueue_frontend_assets()
    {
        if (is_singular('events')) {
            wp_enqueue_style('custom-event-template-css', plugin_dir_url(__FILE__) . 'front/css/event-template.css');
        }

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
