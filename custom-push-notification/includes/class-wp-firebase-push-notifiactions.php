<?php
/**
 * WP_Firebase_Push_Notifications Class
 *
 * Handles the integration with Firebase for push notifications.
 */

if (!class_exists('WP_Firebase_Push_Notifications')) {
    class WP_Firebase_Push_Notifications {
        /**
         * Constructor
         *
         * Initializes the class by setting up hooks and actions.
         */
        public function __construct() {
            // Log constructor call
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('WP_Firebase_Push_Notifications: Constructor called.');
            }

            // Hook into WordPress actions
            add_action('init', array($this, 'register_project_post_type'));
            add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
            add_action('wp_ajax_save_fcm_token', array($this, 'ajax_save_fcm_token'));
            add_action('wp_ajax_nopriv_save_fcm_token', array($this, 'ajax_save_fcm_token'));
            add_action('comment_post', array($this, 'handle_new_comment'), 10, 3);
            add_action('save_post_project', array($this, 'handle_new_project'), 10, 3);
        }

        /**
         * Registers a custom post type 'Project'.
         */
        public function register_project_post_type() {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('WP_Firebase_Push_Notifications: Registering custom post type "project".');
            }

            $labels = array(
                'name'                  => _x('Projects', 'Post Type General Name', 'wp-firebase-push-notifications'),
                'singular_name'         => _x('Project', 'Post Type Singular Name', 'wp-firebase-push-notifications'),
                'menu_name'             => __('Projects', 'wp-firebase-push-notifications'),
                'name_admin_bar'        => __('Project', 'wp-firebase-push-notifications'),
                'archives'              => __('Project Archives', 'wp-firebase-push-notifications'),
                'attributes'            => __('Project Attributes', 'wp-firebase-push-notifications'),
                'parent_item_colon'     => __('Parent Project:', 'wp-firebase-push-notifications'),
                'all_items'             => __('All Projects', 'wp-firebase-push-notifications'),
                'add_new_item'          => __('Add New Project', 'wp-firebase-push-notifications'),
                'add_new'               => __('Add New', 'wp-firebase-push-notifications'),
                'new_item'              => __('New Project', 'wp-firebase-push-notifications'),
                'edit_item'             => __('Edit Project', 'wp-firebase-push-notifications'),
                'update_item'           => __('Update Project', 'wp-firebase-push-notifications'),
                'view_item'             => __('View Project', 'wp-firebase-push-notifications'),
                'view_items'            => __('View Projects', 'wp-firebase-push-notifications'),
                'search_items'          => __('Search Project', 'wp-firebase-push-notifications'),
                'not_found'             => __('Not found', 'wp-firebase-push-notifications'),
                'not_found_in_trash'    => __('Not found in Trash', 'wp-firebase-push-notifications'),
                'featured_image'        => __('Featured Image', 'wp-firebase-push-notifications'),
                'set_featured_image'    => __('Set featured image', 'wp-firebase-push-notifications'),
                'remove_featured_image' => __('Remove featured image', 'wp-firebase-push-notifications'),
                'use_featured_image'    => __('Use as featured image', 'wp-firebase-push-notifications'),
                'insert_into_item'      => __('Insert into project', 'wp-firebase-push-notifications'),
                'uploaded_to_this_item' => __('Uploaded to this project', 'wp-firebase-push-notifications'),
                'items_list'            => __('Projects list', 'wp-firebase-push-notifications'),
                'items_list_navigation' => __('Projects list navigation', 'wp-firebase-push-notifications'),
                'filter_items_list'     => __('Filter projects list', 'wp-firebase-push-notifications'),
            );

            $args = array(
                'label'                 => __('Project', 'wp-firebase-push-notifications'),
                'description'           => __('Post Type Description', 'wp-firebase-push-notifications'),
                'labels'                => $labels,
                'supports'              => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
                'taxonomies'            => array('category', 'post_tag'),
                'hierarchical'          => false,
                'public'                => true,
                'show_ui'               => true,
                'show_in_menu'          => true,
                'menu_position'         => 5,
                'show_in_admin_bar'     => true,
                'show_in_nav_menus'     => true,
                'can_export'            => true,
                'has_archive'           => true,        
                'exclude_from_search'   => false,
                'publicly_queryable'    => true,
                'capability_type'       => 'post',
                'show_in_rest'          => true,
            );

            register_post_type('project', $args);
        }

        /**
         * Enqueues Firebase SDKs and custom JavaScript.
         */
        public function enqueue_frontend_scripts() {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('WP_Firebase_Push_Notifications: Enqueuing frontend scripts.');
            }

            // Enqueue Firebase SDKs
            wp_enqueue_script('firebase-app', 'https://www.gstatic.com/firebasejs/9.22.2/firebase-app-compat.js', array(), null, true);
            wp_enqueue_script('firebase-messaging', 'https://www.gstatic.com/firebasejs/9.22.2/firebase-messaging-compat.js', array('firebase-app'), null, true);

            // Enqueue custom JavaScript
            wp_enqueue_script(
                'wp-firebase-push-notifications',
                plugin_dir_url(__FILE__) . '../public/js/firebase-initiliaze.js',
                array('firebase-app', 'firebase-messaging'),
                '1.0',
                true
            );

            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('WP_Firebase_Push_Notifications: Enqueued firebase-init.js script.');
            }

            // Localize script with Firebase config, VAPID key, AJAX URL, and nonce
            wp_localize_script('wp-firebase-push-notifications', 'wpFirebasePush', array(
                'firebaseConfig' => array(
                    'apiKey' => 'AIzaSyBvbWQU7MVYJybgBgeDVIIJsWQ8TG88Nfw', // Replace with your Firebase API Key
                    'authDomain' => 'custom-push-notification-2c2d7.firebaseapp.com', // Replace with your Firebase Auth Domain
                    'projectId' => 'custom-push-notification-2c2d7', // Replace with your Firebase Project ID
                    'storageBucket' => 'custom-push-notification-2c2d7.appspot.com', // Replace with your Firebase Storage Bucket
                    'messagingSenderId' => '172660199967', // Replace with your Firebase Messaging Sender ID
                    'appId' => '1:172660199967:web:178b9c8a8c5a43088bfb2a', // Replace with your Firebase App ID
                ),
                'vapidKey' => WP_FIREBASE_PUSH_VAPID_PUBLIC_KEY, // Pass the VAPID Public Key
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wp_firebase_push_notifications_nonce'),
            ));

            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('WP_Firebase_Push_Notifications: Localized script with Firebase config, VAPID key, and AJAX URL.');
            }

            // Add Service Worker registration script to footer
            add_action('wp_footer', array($this, 'add_service_worker_registration'));
        }

        /**
         * Adds the Service Worker registration script to the footer.
         */
        public function add_service_worker_registration() {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('WP_Firebase_Push_Notifications: Adding Service Worker registration script to footer.');
            }
            ?>
            <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('<?php echo plugin_dir_url(__FILE__) . '../public/firebase-messaging-sw.js'; ?>')
                    .then(function(registration) {
                        console.log('Service Worker registered with scope:', registration.scope);
                        <?php if (defined('WP_DEBUG') && WP_DEBUG) { ?>
                            console.log('WP_Firebase_Push_Notifications: Service Worker registered successfully.');
                        <?php } ?>
                    }).catch(function(err) {
                        console.error('Service Worker registration failed:', err);
                        <?php if (defined('WP_DEBUG') && WP_DEBUG) { ?>
                            console.error('WP_Firebase_Push_Notifications Error: Service Worker registration failed.', err);
                        <?php } ?>
                    });
                });
            }
            </script>
            <?php
        }

        /**
         * Handles AJAX requests to save the FCM token.
         */
        public function ajax_save_fcm_token() {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('WP_Firebase_Push_Notifications: AJAX request received to save FCM token.');
            }

            // Verify nonce
            check_ajax_referer('wp_firebase_push_notifications_nonce', 'nonce');

            // Check if user is logged in
            if (!is_user_logged_in()) {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('WP_Firebase_Push_Notifications Error: Unauthorized AJAX request.');
                }
                wp_send_json_error(array('message' => 'Unauthorized'), 401);
            }

            // Get user ID and FCM token from POST data
            $user_id = get_current_user_id();
            $fcm_token = isset($_POST['fcmToken']) ? sanitize_text_field($_POST['fcmToken']) : '';

            if ($fcm_token) {
                update_user_meta($user_id, 'fcm_token', $fcm_token);
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('WP_Firebase_Push_Notifications: FCM token saved for user ID ' . $user_id);
                }
                wp_send_json_success(array('message' => 'FCM Token saved'));
            } else {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('WP_Firebase_Push_Notifications Error: No FCM Token provided.');
                }
                wp_send_json_error(array('message' => 'No FCM Token provided'), 400);
            }
        }

        /**
         * Handles new approved comments and sends notifications.
         *
         * @param int $comment_ID The comment ID.
         * @param int $comment_approved Whether the comment is approved.
         * @param array $commentdata Comment data.
         */
        public function handle_new_comment($comment_ID, $comment_approved, $commentdata) {
            if ($comment_approved) {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('WP_Firebase_Push_Notifications: Approved new comment ID ' . $comment_ID . '. Sending notification.');
                }

                $title = "New Comment";
                $body = "A new comment has been added to your post.";

                $this->send_notification_to_all($title, $body);
            }
        }

        /**
         * Handles new project creation and sends notifications.
         *
         * @param int $post_id The post ID.
         * @param WP_Post $post The post object.
         * @param bool $update Whether this is an update.
         */
        public function handle_new_project($post_id, $post, $update) {
            // Only send notification on project creation and when published
            if (!$update && $post->post_status === 'publish') {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('WP_Firebase_Push_Notifications: New project created with ID ' . $post_id . '. Sending notification.');
                }

                $title = "New Project";
                $body = "A new project has been created.";

                $this->send_notification_to_all($title, $body);
            }
        }

        /**
         * Sends a notification to all users with saved FCM tokens.
         *
         * @param string $title The notification title.
         * @param string $body The notification body.
         */
        private function send_notification_to_all($title, $body) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('WP_Firebase_Push_Notifications: Sending notification to all users.');
            }

            // Retrieve all users with a non-empty 'fcm_token' meta key
            $users = get_users(array(
                'meta_key'     => 'fcm_token',
                'meta_value'   => '',
                'meta_compare' => '!=',
            ));

            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('WP_Firebase_Push_Notifications: Found ' . count($users) . ' users with FCM tokens.');
            }

            foreach ($users as $user) {
                $fcm_token = get_user_meta($user->ID, 'fcm_token', true);
                if ($fcm_token) {
                    $this->send_notification($fcm_token, $title, $body);
                }
            }
        }

        /**
         * Sends a notification to a specific FCM token via the Node.js backend.
         *
         * @param string $fcm_token The FCM token of the recipient.
         * @param string $title The notification title.
         * @param string $body The notification body.
         */
        private function send_notification($fcm_token, $title, $body) {
            // Replace with your Node.js backend URL
            $backend_url = 'http://localhost:3000/api/send-notification';

            // Replace with your actual API key for authentication
            $api_key = 'http://localhost:3000/api/send-notification';

            $args = array(
                'body'        => wp_json_encode(array(
                    'fcmToken' => $fcm_token,
                    'title'    => $title,
                    'body'     => $body,
                )),
                'headers'     => array(
                    'Content-Type' => 'application/json',
                    'x-api-key'    => $api_key,
                ),
                'timeout'     => 60,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking'    => true,
                'sslverify'   => true, // Ensure SSL verification for security
            );

            // Send POST request to the backend
            $response = wp_remote_post($backend_url, $args);

            if (is_wp_error($response)) {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('WP_Firebase_Push_Notifications Error: Failed to send notification to ' . $fcm_token . '. Error: ' . $response->get_error_message());
                }
            } else {
                $status_code   = wp_remote_retrieve_response_code($response);
                $body_response = wp_remote_retrieve_body($response);

                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('WP_Firebase_Push_Notifications: Notification sent to ' . $fcm_token . '. Response Code: ' . $status_code . '. Response Body: ' . $body_response);
                }
            }
        }
    }
}
?>
