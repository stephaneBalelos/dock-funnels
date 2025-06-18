<?php


/**
 * DockFunnels_DB class for handling database operations related to Dock Funnels.
 *
 * This class provides methods to interact with the database, including creating,
 * updating, and retrieving forms and their responses.
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class DockFunnels_DB {

    public static function get_forms() {
        global $wpdb;
        return $wpdb->get_results("SELECT * FROM {$wpdb->prefix}dock_funnels", ARRAY_A);
    }

    public static function get_form_by_id($id) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}dock_funnels WHERE id = %d", $id));
    }

    public static function create_form($title, $description, $form_data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dock_funnels';

        $wpdb->insert(
            $table_name,
            [
                'name' => $title,
                'description' => $description,
                'form_data' => wp_json_encode($form_data),
            ]
        );

        return $wpdb->insert_id;
    }

    public static function update_form($id, $title, $description, $form_data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dock_funnels';

        $wpdb->update(
            $table_name,
            [
                'name' => $title,
                'description' => $description,
                'form_data' => wp_json_encode($form_data),
            ],
            ['id' => $id]
        );

        return $wpdb->rows_affected > 0;
    }

    public static function delete_form($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dock_funnels';

        $result = $wpdb->delete($table_name, ['id' => $id]);

        if ($result) {
            // Also delete associated responses
            $wpdb->delete($wpdb->prefix . 'dock_funnel_responses', ['form_id' => $id]);
        }

        return $result;
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
