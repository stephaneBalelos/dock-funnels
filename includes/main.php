<?php

class DockFunnels_Main
{

    public static function install()
    {

        add_action('init', ['DockFunnels_Main', 'register_shortcode']);
        add_action('admin_menu', ['DockFunnels_Admin', 'register_admin_menu']);

        add_action('wp_ajax_dock_funnel_ajax_create_form', ['DockFunnels_Ajax', 'create_form']);
        add_action('wp_ajax_dock_funnel_ajax_get_form', ['DockFunnels_Ajax', 'get_form_by_id']);
        add_action('wp_ajax_nopriv_dock_funnel_ajax_get_form', ['DockFunnels_Ajax', 'get_form_by_id']);
        add_action('wp_ajax_nopriv_dock_funnel_ajax_submit', ['DockFunnels_Ajax', 'handle_form_submission']);

        add_action('admin_enqueue_scripts', ['DockFunnels_Admin', 'enqueue_admin_assets']);
    }


    public static function register_shortcode()
    {
        add_shortcode('dock_funnel', ['DockFunnels_Shortcode', 'render']);
    }

    public static function activate()
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
            status enum('draft', 'published') DEFAULT 'draft' NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql_responses = "CREATE TABLE $responses_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            form_id mediumint(9) NOT NULL,
            response longtext NOT NULL,
            submitted_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            FOREIGN KEY (form_id) REFERENCES $forms_table(id) ON DELETE CASCADE
        ) $charset_collate;";

        dbDelta($sql_forms);
        dbDelta($sql_responses);

    }

    public static function deactivate()
    {
        global $wpdb;

        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}dock_funnel_responses");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}dock_funnel_steps");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}dock_funnel_fields");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}dock_funnels");
    }
    public static function uninstall()
    {
        global $wpdb;

        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}dock_funnel_responses");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}dock_funnel_steps");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}dock_funnel_fields");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}dock_funnels");
    }
}
