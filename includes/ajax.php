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
        $data = $data['data'] ?? [];
        $name = isset($data['name']) ? sanitize_text_field($data['name']) : '';
        $description = isset($data['description']) ? sanitize_textarea_field($data['description']) : '';
        $fields = isset($data['fields']) ? $data['fields'] : [];
        if (empty($name) || empty($fields)) {
            wp_send_json_error(['message' => 'Name and fields are required.']);
        }

        $fields_encoded = wp_json_encode($fields);

        // wp_send_json_success($fields_encoded);

        $form_id = DockFunnels_DB::create_form($name, $description, $fields_encoded);
        if (!$form_id) {
            wp_send_json_error(['message' => 'Failed to create form.']);
        }
        wp_send_json_success(['message' => 'Form created successfully.', 'form_id' => $form_id]);


    }
}
