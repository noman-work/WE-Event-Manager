<?php
// Template Name: Single Event Template
get_header();

do_action('before_events_single_post');

if (have_posts()) {
    while (have_posts()) {
        the_post();
        $event_date = sanitize_text_field(get_post_meta(get_the_ID(), 'event_date', true));
        $event_start_time = sanitize_text_field(get_post_meta(get_the_ID(), 'event_start_time', true));
        $event_end_time = sanitize_text_field(get_post_meta(get_the_ID(), 'event_end_time', true));

        $formatted_start_time = date('g:i A', strtotime($event_start_time));
        $formatted_end_time = date('g:i A', strtotime($event_end_time));
?>
        <div id="primary" class="content-area">
            <main id="main" class="site-main" role="main">
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                    </header>
                    <div class="meta_event">
                        <div class="date">
                            <p><strong>Event Date: </strong> <?php echo $event_date; ?> <b>@</b> <?php echo $formatted_start_time; ?> - <?php echo $formatted_end_time; ?></p>
                        </div>
                    </div>
                    <div class="preview">
                        <img src="<?php echo esc_url(get_the_post_thumbnail_url()); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                    </div>
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>

                    <?php
                    // You can add more details specific to your event post type here
                    ?>
                </article>
            </main>
        </div>
<?php
    }
}

do_action('after_events_single_post');

get_footer();
