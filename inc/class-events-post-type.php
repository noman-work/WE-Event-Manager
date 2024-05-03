<?php
require_once EVENT_SITES_DIR . 'inc/class-events-admin-columns.php';
class Events_Post_Type
{
    public function __construct()
    {
        add_action('init', array($this, 'register_custom_post_type'));

        if (is_admin()) {
            new Events_Admin_Columns();
        }

        add_action('add_meta_boxes', array($this, 'add_event_meta_boxes'));

        add_action('save_post', array($this, 'save_event_meta_data'));

        add_filter('template_include', array($this, 'custom_plugin_single_event_template'));
    }

    /**
     * Register the custom post type 'events'.
     */
    public function register_custom_post_type()
    {
        $labels = array(
            'name'               => __('Events', 'we-event-manager'),
            'singular_name'      => __('Event', 'we-event-manager'),
            'add_new'            => __('Add New', 'we-event-manager'),
            'add_new_item'       => __('Add New Event', 'we-event-manager'),
            'edit_item'          => __('Edit Event', 'we-event-manager'),
            'new_item'           => __('New Event', 'we-event-manager'),
            'view_item'          => __('View Event', 'we-event-manager'),
            'view_items'         => __('View Events', 'we-event-manager'),
            'search_items'       => __('Search Events', 'we-event-manager'),
            'not_found'          => __('No events found', 'we-event-manager'),
            'not_found_in_trash' => __('No events found in Trash', 'we-event-manager'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'supports'           => array('title', 'editor', 'thumbnail'),
            'menu_icon'          => 'dashicons-calendar-alt',
            'has_archive'        => true,
            'rewrite'            => array('slug' => 'events'),
            'show_in_rest'       => true,
            'register_meta_box_cb' => array($this, 'add_event_meta_boxes'),
            'show_in_search'     => true,
        );

        // Register the custom post type 'events'
        register_post_type('events', $args);
    }

    // Filter the single template to use a custom template file from the plugin directory
    public function custom_plugin_single_event_template($template)
    {
        if (is_singular('events')) {
            // Check if we're viewing a single event post
            $new_template = EVENT_SITES_DIR . 'templates/single-event.php';

            if ($new_template && file_exists($new_template)) {
                return $new_template;
            }
        }
        return $template;
    }


    /**
     * Add meta boxes to handle event meta data.
     */
    public function add_event_meta_boxes()
    {
        add_meta_box(
            'event_meta_box',
            __('Event Details', 'we-event-manager'),
            array($this, 'render_event_meta_box'),
            'events',
            'normal',
            'default'
        );
    }

    /**
     * Render meta box for event details.
     */
    public function render_event_meta_box($post)
    {
        // Retrieve existing meta data
        $event_date = get_post_meta($post->ID, 'event_date', true);
        $event_start_time = get_post_meta($post->ID, 'event_start_time', true);
        $event_end_time = get_post_meta($post->ID, 'event_end_time', true);
?>
        <label for="event_date"><?php _e('Date:', 'we-event-manager'); ?></label>
        <input type="date" id="event_date" class="regular-text" name="event_date" value="<?php echo esc_attr($event_date); ?>" /><br /><br />

        <label for="event_start_time"><?php _e('Start Time:', 'we-event-manager'); ?></label>
        <input type="time" id="event_start_time" class="regular-text" name="event_start_time" value="<?php echo esc_attr($event_start_time); ?>" /><br /><br />

        <label for="event_end_time"><?php _e('End Time:', 'we-event-manager'); ?></label>
        <input type="time" id="event_end_time" class="regular-text" name="event_end_time" value="<?php echo esc_attr($event_end_time); ?>" />

<?php
        // Add a nonce field for security
        wp_nonce_field('event_meta_box_nonce', 'event_meta_box_nonce');
    }

    /**
     * Save event meta data.
     */
    public function save_event_meta_data($post_id)
    {
        // Verify nonce to ensure data is coming from the correct form
        if (!isset($_POST['event_meta_box_nonce']) || !wp_verify_nonce($_POST['event_meta_box_nonce'], 'event_meta_box_nonce')) {
            return;
        }

        // Check if this is an autosave or the post is being updated
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check if user has permissions to save data
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Validate and sanitize input data
        if (isset($_POST['event_date'])) {
            $event_date = sanitize_text_field($_POST['event_date']);
            update_post_meta($post_id, 'event_date', $event_date);
        }

        if (isset($_POST['event_start_time'])) {
            $event_start_time = sanitize_text_field($_POST['event_start_time']);
            update_post_meta($post_id, 'event_start_time', $event_start_time);
        }

        if (isset($_POST['event_end_time'])) {
            $event_end_time = sanitize_text_field($_POST['event_end_time']);
            update_post_meta($post_id, 'event_end_time', $event_end_time);
        }
    }
}
