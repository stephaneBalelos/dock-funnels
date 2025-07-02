<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class DockFunnels_Ajax
{

    public static function handle_form_submission()
    {
        // Handle public form submissions
        $body = file_get_contents('php://input');
        if (empty($body)) {
            wp_send_json_error(['message' => 'No data received.']);
        }
        $data = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(['message' => 'Invalid JSON data.']);
        }
        $nonce = wp_verify_nonce($data['nonce'], 'dock_funnel_form_nonce');
        if (!$nonce) {
            wp_send_json_error(['message' => 'Nicht autorisierte Anfrage.']);
        }

        $submission = isset($data['form_submission']) ? $data['form_submission'] : [];
        if (empty($submission)) {
            wp_send_json_error(['message' => 'No form submission data provided.']);
        }

        $form_id = isset($submission['form_id']) ? intval($submission['form_id']) : 0;
        if (!$form_id) {
            wp_send_json_error(['message' => 'Invalid form ID.']);
        }

        $form = DockFunnels_DB::get_form_by_id($form_id);
        if (!$form) {
            wp_send_json_error(['message' => 'Form not found.']);
        }

        $fields = isset($submission['fields']) ? $submission['fields'] : [];
        if (empty($fields)) {
            wp_send_json_error(['message' => 'No fields data provided.']);
        }

        $submission_id = DockFunnels_DB::save_form_response($form_id, $fields);
        if (!$submission_id) {
            wp_send_json_error(['message' => 'Failed to save form response.']);
        }

        // Send Response Per Mail
        DockFunnels_Mailing::send_notifications_emails($form, $fields);

        wp_send_json_success(['message' => 'Thank you! Your response has been recorded.']);
    }

    public static function create_form()
    {
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
        $form_state = json_decode($data['form_state'], true); // see @/types.index.ts for Form type

        $form_data = ['form_steps' => $form_state['form_steps'], 'form_fields' => $form_state['form_fields']];
        $form_settings = $form_state['form_settings'];

        // TODO: Validate form data

        // Save form data
        $form_id = DockFunnels_DB::create_form($form_state['title'], $form_state['description'], $form_data, $form_settings);
        // Check if form was created successfully
        if (!$form_id) {
            wp_send_json_error(['message' => 'Failed to create form.']);
        }
        wp_send_json_success(['message' => 'Form created successfully.', 'form_id' => $form_id]);
    }

    /**
     * Get form by ID
     */
    public static function get_form_by_id()
    {
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
        $form_data = json_decode($form->form_data, true);
        $form_settings = json_decode($form->form_settings, true);

        $form_state = [
            'id' => $form->id,
            'title' => $form->name,
            'description' => $form->description,
            'form_steps' => $form_data['form_steps'],
            'form_fields' => $form_data['form_fields'],
            'form_settings' => $form_settings,
            'status' => $form->status,
        ];
        wp_send_json_success($form_state);
    }

    /**
     * Get form data by ID
     */
    public static function get_form_data_by_id()
    {
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
        $form_data = json_decode($form->form_data, true);
        if (!$form_data) {
            return wp_send_json_error(['message' => 'Form data is invalid.']);
        }
        $form_data = [
            'id' => $form->id,
            'title' => $form->name,
            'description' => $form->description,
            'form_steps' => $form_data['form_steps'],
            'form_fields' => $form_data['form_fields'],
        ];

        return wp_send_json_success($form_data);
    }

    /**
     * Update form by ID
     */
    public static function update_form()
    {
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
        $form_id = isset($data['form_id']) ? intval($data['form_id']) : 0;
        if (!$form_id) {
            wp_send_json_error(['message' => 'Invalid form ID.']);
        }

        $saved_form = DockFunnels_DB::get_form_by_id($form_id);
        if (!$saved_form) {
            wp_send_json_error(['message' => 'Form not found.']);
        }
        // Check If the saved form status is published
        if ($saved_form->status === 'published') {
            wp_send_json_error(['message' => 'Cannot update a published form. Please create a new version instead.']);
        }

        // TODO: Validate form data

        // Format the data
        $form_state = json_decode($data['form_state'], true); // see @/types.index.ts for Form type
        $form_data = ['form_steps' => $form_state['form_steps'], 'form_fields' => $form_state['form_fields']];
        $form_settings = $form_state['form_settings'];
        $updated = DockFunnels_DB::update_form($form_id, $form_state['title'], $form_state['description'], $form_data, $form_settings);
        if (!$updated) {
            wp_send_json_error(['message' => 'Failed to update form.']);
        }
        wp_send_json_success(['message' => 'Form updated successfully.', 'form_id' => $form_id]);
    }

    /**
     * Delete form by ID
     */
    public static function delete_form()
    {
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
            wp_send_json_error(['message' => 'You do not have permission to delete forms.']);
        }
        $form_id = isset($data['form_id']) ? intval($data['form_id']) : 0;
        if (!$form_id) {
            wp_send_json_error(['message' => 'Invalid form ID.']);
        }
        $form = DockFunnels_DB::get_form_by_id($form_id);
        if (!$form) {
            return wp_send_json_error(['message' => 'Form not found.']);
        }

        $deleted = DockFunnels_DB::delete_form($form_id);
        if (!$deleted) {
            wp_send_json_error(['message' => 'Failed to delete form.']);
        }
        wp_send_json_success(['message' => 'Form deleted successfully.']);
    }

    /**
     * Validate and sanitize form data
     *
     * @param array $data
     * @return array|false
     */

    private static function validate_form_data($data)
    {
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
        if (!isset($data['form_fields']) || !is_array($data['form_fields']) || empty($data['form_fields'])) {
            return false;
        }

        return $f_data;
    }

    private static function validate_steps_data($steps)
    {
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

    public static function validate_fields_data($fields)
    {
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

    private static function validate_and_sanitize($string_data)
    {
        if (!is_string($string_data) || empty($string_data)) {
            return false;
        }
        return sanitize_text_field($string_data);
    }
}
