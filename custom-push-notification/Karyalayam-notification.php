<?php
/**
 * Plugin Name: Custom Push Notifications
 * Description: Sends push notifications via Firebase when a new comment or project is added.
 * Version: 1.0
 * Author: Aashish
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Enable error logging for this file
if (defined('WP_DEBUG') && WP_DEBUG) {
    error_log('WP Firebase Push Notifications Plugin: Initializing plugin.');
}

// Define VAPID Public Key
define('WP_FIREBASE_PUSH_VAPID_PUBLIC_KEY', 'BGQpLDc1eUH4P8rj6szZl0F4IasKhUDNdjUnNt8nwOg05k4yvurcif6UqhsuzdTV1qgmBs8SEOdhwCSPlXTjkSA'); // Replace with your actual VAPID public key

// Include necessary files
$includes_path = plugin_dir_path(__FILE__) . 'includes/class-wp-firebase-push-notifications.php';
if (file_exists($includes_path)) {
    require_once $includes_path;
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('WP Firebase Push Notifications Plugin: Included class file successfully.');
    }
} else {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('WP Firebase Push Notifications Plugin Error: Class file not found at ' . $includes_path);
    }
}

// Initialize the plugin
function wp_firebase_push_notifications_init() {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('WP Firebase Push Notifications Plugin: Initializing WP_Firebase_Push_Notifications class.');
    }
    $plugin = new WP_Firebase_Push_Notifications();
}
add_action('plugins_loaded', 'wp_firebase_push_notifications_init');

?>
