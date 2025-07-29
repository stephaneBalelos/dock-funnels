<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class DockFunnels_Main
{

    public static function install()
    {

        add_action('init', ['DockFunnels_Main', 'register_shortcode']);
        add_action('admin_menu', ['DockFunnels_Admin', 'register_admin_menu']);
        add_action('admin_init', ['DockFunnels_Admin', 'register_plugin_settings']);
        add_action('admin_notices', ['DockFunnels_Admin', 'dock_funnels_admin_notice_key']);

        add_action('wp_ajax_dock_funnel_ajax_create_form', ['DockFunnels_Ajax', 'create_form']);
        add_action('wp_ajax_dock_funnel_ajax_get_form', ['DockFunnels_Ajax', 'get_form_by_id']);
        add_action('wp_ajax_dock_funnel_ajax_get_responses', ['DockFunnels_Ajax', 'get_form_responses_by_id']);
        add_action('wp_ajax_dock_funnel_ajax_update_form', ['DockFunnels_Ajax', 'update_form']);
        add_action('wp_ajax_dock_funnel_ajax_delete_form', ['DockFunnels_Ajax', 'delete_form']);
        add_action('wp_ajax_dock_funnel_ajax_submit_form', ['DockFunnels_Ajax', 'handle_form_submission']);
        add_action('wp_ajax_dock_funnel_ajax_delete_form_response', ['DockFunnels_Ajax', 'delete_form_response']);
        add_action('wp_ajax_nopriv_dock_funnel_ajax_get_form', ['DockFunnels_Ajax', 'get_form_data_by_id']);
        add_action('wp_ajax_nopriv_dock_funnel_ajax_submit_form', ['DockFunnels_Ajax', 'handle_form_submission']);

        add_action('admin_enqueue_scripts', ['DockFunnels_Admin', 'enqueue_admin_assets']);
    }


    public static function register_shortcode()
    {
        add_shortcode('dock_funnel', ['DockFunnels_Shortcode', 'render']);
    }

    public static function activate()
    {
        // Create necessary database tables
        self::create_tables();
        // Setup encryption key if not already defined
        self::setup_encryption_key();
    }

    public static function deactivate()
    {
        global $wpdb;

        // $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}dock_funnel_responses");
        // $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}dock_funnel_steps");
        // $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}dock_funnel_fields");
        // $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}dock_funnels");
    }
    public static function uninstall()
    {
        global $wpdb;

        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}dock_funnel_responses");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}dock_funnel_steps");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}dock_funnel_fields");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}dock_funnels");
    }

    public static function create_tables()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $forms_table = $wpdb->prefix . 'dock_funnels';
        $responses_table = $wpdb->prefix . 'dock_funnel_responses';

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sql_forms = "CREATE TABLE $forms_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name text NOT NULL,
            description text NOT NULL,
            form_data longtext NOT NULL,
            form_settings longtext NOT NULL,
            status enum('draft', 'published') DEFAULT 'draft' NOT NULL,
            response_count mediumint(9) DEFAULT 0 NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql_responses = "CREATE TABLE $responses_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            form_id mediumint(9) NOT NULL,
            response longtext NOT NULL,
            submitted_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        dbDelta($sql_forms);
        dbDelta($sql_responses);

        // Check if the foreign key form_id already exists in the responses table
        $fk_exists = $wpdb->get_var("SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_NAME = '$responses_table' AND CONSTRAINT_NAME = 'fk_form_id'") > 0;
        if (!$fk_exists) {
            $wpdb->query("ALTER TABLE $responses_table ADD CONSTRAINT fk_form_id FOREIGN KEY (form_id) REFERENCES $forms_table(id) ON DELETE CASCADE;");
        }

    }

    public static function setup_encryption_key()
    {
        // Generate and save encryption key in wp-config.php
        if (!defined('DOCK_FUNNELS_ENCRYPTION_KEY')) {
            $encryption_key = wp_generate_password(32, false);
            $config_path = ABSPATH . 'wp-config.php';

            if (is_writable($config_path)) {
                $config_contents = file_get_contents($config_path);

                // Insert before the line that says "That's all, stop editing!"
                $insert_line = "define( 'DOCK_FUNNELS_ENCRYPTION_KEY', '$encryption_key' );\n";
                $pattern = "/(\/\* That's all, stop editing! Happy publishing. \*\/)/";

                if (preg_match($pattern, $config_contents)) {
                    $updated_contents = preg_replace($pattern, $insert_line . "\n$1", $config_contents);
                    file_put_contents($config_path, $updated_contents);
                }
            }
        }
    }

    public static function dock_funnels_encrypt($data)
    {
        if (defined('DOCK_FUNNELS_ENCRYPTION_KEY')) {
            $key = hash('sha256', constant("DOCK_FUNNELS_ENCRYPTION_KEY"));
            $iv = openssl_random_pseudo_bytes(16);
            $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
            return base64_encode($iv . $encrypted);
        } else {
            return $data; // Return unencrypted data if key is not defined
        }
    }

    public static function dock_funnels_decrypt($data)
    {
        if (defined('DOCK_FUNNELS_ENCRYPTION_KEY')) {
            $key = hash('sha256', constant("DOCK_FUNNELS_ENCRYPTION_KEY"));
            $data = base64_decode($data);
            $iv = substr($data, 0, 16);
            $encrypted = substr($data, 16);
            return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
        } else {
            return $data; // Return unencrypted data if key is not defined
        }
    }
}
