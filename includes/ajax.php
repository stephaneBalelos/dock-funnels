<?php

class DockFunnels_Ajax {

    public static function handle_form_submission() {
        check_ajax_referer('dock_funnel_form_nonce', 'nonce');

        $form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
        $fields = isset($_POST['fields']) ? $_POST['fields'] : [];

        if (!$form_id || empty($fields)) {
            wp_send_json_error(['message' => 'Missing form data.']);
        }

        DockFunnels_DB::save_form_response($form_id, $fields);

        wp_send_json_success(['message' => 'Thank you! Your response has been recorded.']);
    }

    public static function create_form() {
        $body = file_get_contents('php://input');
        if (empty($body)) {
            wp_send_json_error(['message' => 'No data received.']);
        }
        $data = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(['message' => 'Invalid JSON data.']);
        }
        wp_verify_nonce($data['nonce'], 'dock_funnel_admin_nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'You do not have permission to create forms.']);
        }
        $form_data = json_decode($data['form_data'], true); // see @/types.index.ts for Form type

        // Validate form data
        if (!self::validate_form_data($form_data)) {
            wp_send_json_error(['message' => 'Invalid form data.']);
        }

        // Save form data
        $form_id = DockFunnels_DB::create_form($form_data['title'], $form_data['description'], $form_data);
        if (!$form_id) {
            wp_send_json_error(['message' => 'Failed to create form.']);
        }
        wp_send_json_success(['message' => 'Form created successfully.', 'form_id' => $form_id]);
    }

    public static function get_form_by_id() {
        $body = file_get_contents('php://input');
        if (empty($body)) {
            wp_send_json_error(['message' => 'No data received.']);
        }
        $data = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(['message' => 'Invalid JSON data.']);
        }
        wp_verify_nonce($data['nonce'], 'dock_funnel_form_nonce');

        $form_id = isset($data['form_id']) ? intval($data['form_id']) : 0;
        if (!$form_id) {
            wp_send_json_error(['message' => 'Invalid form ID.']);
        }
        $form = DockFunnels_DB::get_form_by_id($form_id);
        if (!$form) {
            return wp_send_json_error(['message' => 'Form not found.']);
        }
        wp_send_json_success($form);
    }

    private static function validate_form_data($data) {
        $f_data = [];
        // Check Title
        if (empty($data['title']) || !is_string($data['title'])) {
            return false;
        }
        $f_data['title'] = sanitize_text_field($data['title']);

        // Check Description
        if (!isset($data['description']) || !is_string($data['description'])) {
            return false;
        }
        $f_data['description'] = isset($data['description']) ? sanitize_textarea_field($data['description']) : '';

        // Check Steps
        if (!isset($data['form_steps']) || !is_array($data['form_steps']) || empty($data['form_steps'])) {
            return false;
        }
        $steps_data = self::validate_steps_data($data['form_steps']);
        if (!$steps_data) {
            return false;
        }
        $f_data['form_steps'] = $steps_data;

        // Check Fields
        if (!isset($data['fields']) || !is_array($data['fields']) || empty($data['fields'])) {
            return false;
        }
        
        return $f_data;
    }

    private static function validate_steps_data($steps) {
        $f_steps = [];

        foreach ($steps as $step) {
            $title = self::validate_and_sanitize($step['title']);
            if (!$title) {
                return false;
            }
            $description = isset($step['description']) ? self::validate_and_sanitize($step['description']) : '';
            $f_step = [
                'title' => sanitize_text_field($step['title']),
                'description' => $description,
            ];
            // Add $f_step to $f_steps
            $f_steps[] = $f_step; 
        }
        if (empty($f_steps)) {
            return false;
        }
        return $f_steps;
    }

    public static function validate_fields_data($fields) {
        $f_fields = [];
        foreach ($fields as $field) {
            if (!isset($field['name']) || !is_string($field['name']) || empty($field['name'])) {
                return false;
            }
            $name = sanitize_text_field($field['name']);

            if (!isset($field['type']) || !is_string($field['type']) || empty($field['type'])) {
                return false;
            }
            $type = sanitize_text_field($field['type']);

            if (!isset($field['required']) || !is_bool($field['required'])) {
                return false;
            }
            $required = (bool)$field['required'];

            $f_fields[] = [
                'name' => $name,
                'type' => $type,
                'required' => $required,
            ];
        }
        return $f_fields;
    }

    private static function validate_and_sanitize($string_data) {
        if (!is_string($string_data) || empty($string_data)) {
            return false;
        }
        return sanitize_text_field($string_data);
    }
}
