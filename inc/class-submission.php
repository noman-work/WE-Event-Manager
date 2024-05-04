<?php
class EventSubmissionForm
{
    public function __construct()
    {
        add_shortcode('event_submission_form', array($this, 'render_form_shortcode'));
    }

    // Shortcode handler function to render the event submission form
    public function render_form_shortcode()
    {
        ob_start();

        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_event'])) {
            $this->process_event_submission();
        }

        // Output the event submission form
?>
        <form method="post" action="" id="event_sub" enctype="multipart/form-data">
            <h1>Submit Your Event</h1>
            <label for="event_title">Event Title:</label>
            <input type="text" id="event_title" name="event_title" required><br>

            <label for="event_content">Event Description:</label>
            <textarea id="event_content" name="event_content" required></textarea><br>

            <div class="wrap_time">
                <div class="form-control"><label for="event_date">Event Date:</label>
                    <input type="date" id="event_date" name="event_date" required>
                </div>


                <div class="form-control"><label for="event_start_time">Event Start Time:</label>
                    <input type="time" id="event_start_time" name="event_start_time" required>
                </div>


                <div class="form-control"><label for="event_end_time">Event End Time:</label>
                    <input type="time" id="event_end_time" name="event_end_time" required>
                </div>

            </div>

            <label for="event_image">Event Image:</label>
            <input type="file" id="event_image" name="event_image"><br>

            <input type="submit" name="submit_event" value="Submit Event">
        </form>
<?php

        return ob_get_clean();
    }

    // Method to process event submission
    private function process_event_submission()
    {
        if (isset($_POST['submit_event'])) {
            $event_title = sanitize_text_field($_POST['event_title']);
            $event_content = sanitize_textarea_field($_POST['event_content']);
            $event_date = sanitize_text_field($_POST['event_date']);
            $event_start_time = sanitize_text_field($_POST['event_start_time']);
            $event_end_time = sanitize_text_field($_POST['event_end_time']);


            // Handle file upload
            if (!empty($_FILES['event_image']['name'])) {
                // Include WordPress core files for media handling
                require_once ABSPATH . '/wp-admin/includes/file.php';
                require_once ABSPATH . '/wp-admin/includes/image.php';
                require_once ABSPATH . '/wp-admin/includes/media.php';

                // Upload event image using media_handle_upload()
                $uploaded_image = media_handle_upload('event_image', 0);
                if (is_wp_error($uploaded_image)) {
                    // Handle upload error
                    echo 'Image upload failed. Please try again.';
                } else {
                    // Create new event post
                    $new_event_id = wp_insert_post(array(
                        'post_title'    => wp_strip_all_tags($event_title),
                        'post_content'  => $event_content,
                        'post_status'   => 'pending', // Set post status (pending for admin review)
                        'post_type'     => 'events', // Adjust post type based on your setup
                    ));

                    // Add custom fields (meta data)
                    if ($new_event_id) {
                        update_post_meta($new_event_id, 'event_date', $event_date);
                        update_post_meta($new_event_id, 'event_start_time', $event_start_time);
                        update_post_meta($new_event_id, 'event_end_time', $event_end_time);

                        set_post_thumbnail($new_event_id, $uploaded_image);

                        echo '<p class="message-event">' . esc_html__('Event submitted successfully. It will be reviewed by an admin.', 'text-domain') . '</p>';
                    } else {
                        echo '<p class="message-event">' . esc_html__('Failed to submit event. Please try again.', 'text-domain') . '</p>';
                    }
                }
            }
        }
    }

    // Method to handle file upload and return attachment ID
    private function upload_event_image($file_input_name)
    {
        $uploaded_file = $_FILES[$file_input_name];

        // Check if file upload error exists
        if ($uploaded_file['error'] !== UPLOAD_ERR_OK) {
            return new WP_Error('upload_error', esc_html__('Failed to upload image.', 'text-domain'), array('status' => 500));
        }

        // Handle file upload and return attachment ID
        $upload_overrides = array('test_form' => false);
        $attachment_id = media_handle_upload($file_input_name, 0, array(), $upload_overrides);

        if (is_wp_error($attachment_id)) {
            return $attachment_id;
        }

        return $attachment_id;
    }
}
