<?php
/*
Plugin Name: WP Push Notifications
Description: Sends push notifications for new comments and projects.
Version: 1.0
Author: PIYUSH-MISHRA-00
*/

// Enqueue JavaScript for push notifications
function enqueue_push_notification_script() {
    wp_enqueue_script('push-notifications', plugin_dir_url(__FILE__) . 'push-notifications.js', [], null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_push_notification_script');

// Send data to JavaScript
function send_data_to_js() {
    ?>
    <script>
        const NOTIFICATION_API_URL = '<?php echo site_url('/send-notification'); ?>';
    </script>
    <?php
}
add_action('wp_footer', 'send_data_to_js');
