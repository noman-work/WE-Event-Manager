<?php
class Events_Admin_Columns
{
    public function __construct()
    {
        // Hook into WordPress admin initialization to setup admin columns
        add_action('admin_init', array($this, 'setup_admin_columns'));
    }

    /**
     * Setup admin columns to display event date.
     */
    public function setup_admin_columns()
    {
        add_filter('manage_events_posts_columns', array($this, 'add_event_date_column'));
        add_action('manage_events_posts_custom_column', array($this, 'render_event_date_column_content'), 10, 2);
    }

    /**
     * Add event date column.
     */
    public function add_event_date_column($columns)
    {
        $columns['event_date'] = __('Event Date', 'we-event-manager');
        return $columns;
    }

    /**
     * Render event date column content.
     */
    public function render_event_date_column_content($column, $post_id)
    {
        if ($column === 'event_date') {
            $event_date = get_post_meta($post_id, 'event_date', true);
            if (!empty($event_date)) {
                echo date('F j, Y', strtotime($event_date));
            } else {
                echo __('Not set', 'we-event-manager');
            }
        }
    }
}
