<?php

class DockFunnels_DB {

    public static function activate() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $forms_table = $wpdb->prefix . 'dock_funnels';
        $responses_table = $wpdb->prefix . 'dock_funnel_responses';

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta("CREATE TABLE $forms_table (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            fields longtext NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) $charset_collate;");

        dbDelta("CREATE TABLE $responses_table (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            form_id BIGINT UNSIGNED NOT NULL,
            response TEXT NOT NULL,
            submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (form_id) REFERENCES $forms_table(id) ON DELETE CASCADE
        ) $charset_collate;");
    }

    public static function deactivate() {
        // Drop everything related to the plugin
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}dock_funnel_responses");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}dock_funnels");
    }

    public static function uninstall() {
        global $wpdb;

        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}dock_funnel_responses");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}dock_funnels");
    }

    public static function get_forms() {
        global $wpdb;
        return $wpdb->get_results("SELECT * FROM {$wpdb->prefix}dock_funnels", ARRAY_A);
    }

    public static function get_form_by_id($id) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}dock_funnels WHERE id = %d", $id));
    }

    public static function create_form($name, $description, $fields) {
        global $wpdb;

        $wpdb->insert(
            $wpdb->prefix . 'dock_funnels',
            [
                'name' => $name,
                'description' => $description,
                'fields' => wp_json_encode($fields),
            ]
        );

        return $wpdb->insert_id;
    }

    public static function get_form_responses($form_id) {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}dock_funnel_responses WHERE form_id = %d", $form_id), ARRAY_A);
    }

    public static function save_form_response($form_id, $response) {
        global $wpdb;
        $wpdb->insert(
            $wpdb->prefix . 'dock_funnel_responses',
            [
                'form_id' => $form_id,
                'response' => wp_json_encode($response),
            ]
        );
    }
}
