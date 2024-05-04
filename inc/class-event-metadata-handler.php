<?php
class EventMetadataHandler
{
    public function __construct()
    {
        // Hook into WordPress admin initialization to add meta box saving functionality
        add_action('save_post', array($this, 'save_event_date_meta_data'));
    }

    /**
     * Save event date meta data.
     */
    public function save_event_date_meta_data($post_id)
    {
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
    }

    /**
     * Render meta box for event date.
     */
    public function render_event_date_meta_box($post)
    {
        $event_date = get_post_meta($post->ID, 'event_date', true);
?>
        <label for="event_date"><?php _e('Date:', 'we-event-manager'); ?></label>
        <input type="date" id="event_date" name="event_date" value="<?php echo esc_attr($event_date); ?>" />
<?php
    }
}
