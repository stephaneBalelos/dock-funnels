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

        // Run Form On Submit Actions
        $form_settings = json_decode($form->form_settings, true);
        $on_submit_actions = isset($form_settings['onSubmitAction']) ? $form_settings['onSubmitAction'] : [];
        $redirect_url = '';
        if (!empty($on_submit_actions)) {
            foreach ($on_submit_actions as $action) {
                if ($action['type'] === 'redirect') {
                    // Redirect to the specified URL
                    $redirect_url = isset($action['url']) ? esc_url_raw($action['url']) : '';
                } elseif ($action['type'] === 'mail') {
                    // Send email notification
                    DockFunnels_Mailing::handleOnSubmitActionMail($form, $fields, $action);
                } else {
                    continue; // Skip unsupported action types
                }
            }
        }

        $response = [
            'message' => 'success',
            'submission_id' => $submission_id,
        ];

        if (!empty($redirect_url)) {
            $response['redirect_url'] = $redirect_url;
        }

        wp_send_json_success($response);
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
        $form_settings = json_decode($form->form_settings, true);
        if (!$form_data) {
            return wp_send_json_error(['message' => 'Form data is invalid.']);
        }
        $form_data = [
            'id' => $form->id,
            'title' => $form->name,
            'description' => $form->description,
            'form_steps' => $form_data['form_steps'],
            'form_fields' => $form_data['form_fields'],
            'form_settings' => [
                'design_settings' => $form_settings['design_settings'] ?? [],
            ],
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

        // Format the data
        $form_state = json_decode($data['form_state'], true); // see @/types.index.ts for Form type
        $validator = new DockFunnels_FormStateValidator($form_state);
        $validation_result = $validator->validate();
        if (!$validation_result['valid']) {
            wp_send_json_error(['message' => 'Form validation failed.', 'errors' => $validation_result['errors']]);
        }

        $results = $validation_result['data'];

        $form_data = ['form_steps' => $results['form_steps'], 'form_fields' => $results['form_fields']];
        $form_settings = $results['form_settings'];
        $updated = DockFunnels_DB::update_form($form_id, $results['title'], $results['description'], $form_data, $form_settings);
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
}


class DockFunnels_FormStateValidator
{
    private $form_title = '';
    private $form_description = '';
    private $form_steps = [];
    private $form_fields = [];
    private $form_settings = [];

    private $sanitized_form_state = [];
    private $errors = [];



    public function __construct($form_state)
    {
        $this->form_title = $form_state['title'] ?? '';
        $this->form_description = $form_state['description'] ?? '';
        $this->form_steps = $form_state['form_steps'] ?? [];
        $this->form_fields = $form_state['form_fields'] ?? [];
        $this->form_settings = $form_state['form_settings'] ?? [];
    }

    public function validate()
    {
        // Check if title is set and is a string
        if (!isset($this->form_title) || !is_string($this->form_title) || empty($this->form_title)) {
            $this->errors['title'] = 'Title is required and must be a string.';
        } else {
            $this->sanitized_form_state['title'] = sanitize_text_field($this->form_title);
        }

        // Check if description is set. If not, set it to an empty string
        if (!isset($this->form_description) || !is_string($this->form_description)) {
            $this->sanitized_form_state['description'] = '';
        } else {
            $this->sanitized_form_state['description'] = sanitize_textarea_field($this->form_description);
        }

        // Validate form steps
        if (!isset($this->form_steps) || !is_array($this->form_steps) || empty($this->form_steps)) {
            $this->errors['form_steps'] = 'Form steps are required and must be a non-empty array.';
        } else {
            $this->sanitized_form_state['form_steps'] = [];
            foreach ($this->form_steps as $idx => $step) {
                $fields_for_step = array_filter($this->form_fields, function ($field) use ($idx) {
                    return isset($field['step_index']) && $field['step_index'] === $idx;
                });
                $validation_result = self::validate_form_step($idx, $step, $fields_for_step);
                if ($validation_result['valid']) {
                    $this->sanitized_form_state['form_steps'][] = [
                        'title' => $validation_result['data']['title'],
                        'description' => $validation_result['data']['description']
                    ];
                } else {
                    $this->errors['form_steps'][$idx] = $validation_result['errors'];
                }
            }
        }

        // Validate form fields
        if (!isset($this->form_fields) || !is_array($this->form_fields) || empty($this->form_fields)) {
            $this->errors['form_fields'] = 'Form fields are required and must be a non-empty array.';
        } else {
            $this->sanitized_form_state['form_fields'] = [];
            foreach ($this->form_fields as $field) {
                // Check if Step Index is set and is an integer
                if (!isset($field['step_index']) || !is_int($field['step_index']) || $field['step_index'] < 0) {
                    $this->errors['form_fields'][] = 'Field step index is required and must be a non-negative integer.';
                } else {
                    $step = $this->form_steps[$field['step_index']] ?? null;
                    if (!$step) {
                        // If the step does not exist, ignore the field
                        continue;
                    } else {
                        $validation_result = self::validate_form_field($field);
                        if ($validation_result['valid']) {
                            $sanitized_field = $validation_result['data'];
                            $sanitized_field['step_index'] = $field['step_index']; // Add step index to the sanitized field
                            $this->sanitized_form_state['form_fields'][] = $sanitized_field;
                        } else {
                            $this->errors['form_fields'][$field['field_name']] = $validation_result['errors'];
                        }
                    }
                }
            }
        }

        // Validate field dependencies
        $fields = $this->sanitized_form_state['form_fields'] ?? [];
        $validate_fields_dependencies_result = self::validate_fields_dependencies($fields);
        if (!$validate_fields_dependencies_result['valid']) {
            $this->errors['form_fields_dependencies'] = $validate_fields_dependencies_result['errors'];
        }

        // Validate form settings
        // Validate Notifications Settings
        $notifications_settings_validation_result = self::validate_notifications_settings($this->form_settings['notifications_settings'] ?? []);
        if (!$notifications_settings_validation_result['valid']) {
            $this->errors['notifications_settings'] = $notifications_settings_validation_result['errors'];
        } else {
            $this->sanitized_form_state['form_settings']['notifications_settings'] = $notifications_settings_validation_result['data'];
        }

        // Validate onSubmit Actions
        foreach ($this->form_settings['onSubmitAction'] ?? [] as $action) {
            $action_validation_result = self::validate_onSubmitAction_settings($action, $this->sanitized_form_state['form_fields']);
            if (!$action_validation_result['valid']) {
                $this->errors['onSubmitAction'][] = $action_validation_result['errors'];
            } else {
                $this->sanitized_form_state['form_settings']['onSubmitAction'][] = $action_validation_result['data'];
            }
        }

        // Validate Design Settings
        $design_settings_validation_result = self::validate_design_settings($this->form_settings['design_settings'] ?? []);
        if (!$design_settings_validation_result['valid']) {
            $this->errors['design_settings'] = $design_settings_validation_result['errors'];
        } else {
            $this->sanitized_form_state['form_settings']['design_settings'] = $design_settings_validation_result['data'];
        }


        return empty($this->errors) ?
            ['valid' => true, 'data' => $this->sanitized_form_state] :
            ['valid' => false, 'errors' => $this->errors];
    }

    private static function validate_form_step($idx, $step, $fields)
    {
        $errors = [];
        $sanitized_step = [];
        $sanitized_step['fields'] = [];

        // check if $fields is empty
        if (empty($fields)) {
            $errors['fields'] = 'Step must have at least one field.';
        }

        // Check if title is set and is a string
        if (!isset($step['title']) || !is_string($step['title']) || empty($step['title'])) {
            $errors['title'] = 'Step title is required and must be a string.';
        } else {
            $sanitized_step['title'] = sanitize_text_field($step['title']);
        }

        // Check if description is set. If not, set it to an empty string
        if (!isset($step['description']) || !is_string($step['description'])) {
            $sanitized_step['description'] = '';
        } else {
            $sanitized_step['description'] = sanitize_textarea_field($step['description']);
        }

        return empty($errors) ? ['valid' => true, 'data' => $sanitized_step] : ['valid' => false, 'errors' => $errors];
    }

    private static function validate_form_field($field)
    {
        $errors = [];
        $sanitized_field = [];

        // Check if "field_name" is set and is a string
        if (!isset($field['field_name']) || !is_string($field['field_name']) || empty($field['field_name'])) {
            $errors['field_name'] = 'Field name is required and must be a string.';
        } else {
            $sanitized_field['field_name'] = sanitize_text_field($field['field_name']);
        }

        // Validate field type
        if (!isset($field['type']) || !is_string($field['type']) || empty($field['type'])) {
            $errors['type'] = 'Field type is required and must be a string.';
        } else {
            $sanitized_field['type'] = sanitize_text_field($field['type']);
        }

        // Check if "label" is set and is a string
        if (!isset($field['label']) || !is_string($field['label']) || empty($field['label'])) {
            $errors['label'] = 'Field label is required and must be a string.';
        } else {
            $sanitized_field['label'] = sanitize_text_field($field['label']);
        }

        // Check if "description" is set and is a string. If not, set it to an empty string
        if (isset($field['description']) && !is_string($field['description'])) {
            $errors['description'] = 'Field description must be a string.';
        } else {
            $sanitized_field['description'] = isset($field['description']) ? sanitize_textarea_field($field['description']) : '';
        }

        // Check if "required" is set and is a boolean
        if (isset($field['required']) && !is_bool($field['required'])) {
            $errors['required'] = 'Field required status must be a boolean.';
        } else {
            $sanitized_field['required'] = isset($field['required']) ? (bool)$field['required'] : false;
        }

        // Validate field by type
        $validation_result = self::validate_field_by_type($field);
        if (!$validation_result['valid']) {
            $errors = array_merge($errors, $validation_result['errors']);
        } else {
            $sanitized_field = array_merge($sanitized_field, $validation_result['data']);
        }

        return empty($errors) ? ['valid' => true, 'data' => $sanitized_field] : ['valid' => false, 'errors' => $errors];
    }

    public static function validate_field_by_type($field)
    {
        $field_types = ['text', 'select', 'checkboxList', 'textarea', 'submissionSummary'];
        $errors = [];
        $sanitized_field = [
            'field_name' => $field['field_name'],
            'type' => $field['type'],
            'label' => $field['label'],
            'description' => isset($field['description']) ? sanitize_textarea_field($field['description']) : '',
            'required' => isset($field['required']) ? (bool)$field['required'] : false,
        ];

        // Check if "type" is set and is a string
        if (!in_array($field['type'], $field_types, true)) {
            $errors['type'] = 'Field type is required and must be one of: ' . implode(', ', $field_types) . '.';
        }
        switch ($field['type']) {
            case 'text':
                // Validate text field
                $text_validation_result = self::validate_text_field($field);
                if (!$text_validation_result['valid']) {
                    $errors = array_merge($errors, $text_validation_result['errors']);
                } else {
                    $sanitized_field = array_merge($sanitized_field, $text_validation_result['data']);
                }
                break;
            case 'select':
                // Additional validation for select fields can be added here
                $select_validation_result = self::validate_select_field($field);
                if (!$select_validation_result['valid']) {
                    $errors = array_merge($errors, $select_validation_result['errors']);
                } else {
                    $sanitized_field = array_merge($sanitized_field, $select_validation_result['data']);
                }
                break;
            case 'checkboxList':
                // Additional validation for checkbox list fields can be added here
                $checkboxList_validation_result = self::validate_checkboxList_field($field);
                if (!$checkboxList_validation_result['valid']) {
                    $errors = array_merge($errors, $checkboxList_validation_result['errors']);
                } else {
                    $sanitized_field = array_merge($sanitized_field, $checkboxList_validation_result['data']);
                }
                break;
            case 'textarea':
                // Additional validation for textarea fields can be added here
                $textarea_validation_result = self::validate_textarea_field($field);
                if (!$textarea_validation_result['valid']) {
                    $errors = array_merge($errors, $textarea_validation_result['errors']);
                } else {
                    $sanitized_field = array_merge($sanitized_field, $textarea_validation_result['data']);
                }
                break;
            case 'submissionSummary':
                // Additional validation for submission summary fields can be added here
                $submission_summary_validation_result = self::validate_submission_summary_field($field);
                if (!$submission_summary_validation_result['valid']) {
                    $errors = array_merge($errors, $submission_summary_validation_result['errors']);
                } else {
                    $sanitized_field = array_merge($sanitized_field, $submission_summary_validation_result['data']);
                }
                break;
            default:
                $errors['type'] = 'Invalid field type.';
        }

        return empty($errors) ?
            ['valid' => true, 'data' => $sanitized_field] :
            ['valid' => false, 'errors' => $errors];
    }

    private static function validate_text_field($field)
    {
        $errors = [];
        $sanitized_field = [
            'field_name' => $field['field_name'],
            'type' => $field['type'],
            'label' => $field['label'],
            'description' => isset($field['description']) ? sanitize_textarea_field($field['description']) : '',
            'required' => isset($field['required']) ? (bool)$field['required'] : false,
        ];

        $alllowed_input_types = ['text', 'email', 'number', 'tel', 'url', 'date'];
        // Check if "input_type" is set and is a string
        if (!isset($field['input_type']) || !is_string($field['input_type']) || empty($field['input_type'])) {
            $errors['input_type'] = 'Input type is required and must be a string.';
        } elseif (!in_array($field['input_type'], $alllowed_input_types, true)) {
            $errors['input_type'] = 'Input type is not valid. Allowed types are: ' . implode(', ', $alllowed_input_types) . '.';
        } else {
            $sanitized_field['input_type'] = sanitize_text_field($field['input_type']);
        }
        // Validate placeholder
        if (isset($field['placeholder']) && !is_string($field['placeholder'])) {
            $errors['placeholder'] = 'Placeholder must be a string.';
        } else {
            $sanitized_field['placeholder'] = isset($field['placeholder']) ? sanitize_text_field($field['placeholder']) : '';
        }
        // Validate default value
        if (isset($field['default_value']) && !is_string($field['default_value'])) {
            $errors['default_value'] = 'Default value must be a string.';
        } else {
            $sanitized_field['default_value'] = isset($field['default_value']) ? sanitize_text_field($field['default_value']) : '';
        }

        // Check if "depends_on" is set and is an array
        if (isset($field['depends_on']) && is_array($field['depends_on'])) {
            $sanitized_field['depends_on'] = self::sanitize_dependencies($field['depends_on']);
        } else {
            $field['depends_on'] = [];
        }

        return empty($errors) ? ['valid' => true, 'data' => $sanitized_field] : ['valid' => false, 'errors' => $errors];
    }

    private static function validate_select_field($field)
    {
        $errors = [];
        $sanitized_field = [
            'field_name' => $field['field_name'],
            'type' => $field['type'],
            'label' => $field['label'],
            'description' => isset($field['description']) ? sanitize_textarea_field($field['description']) : '',
            'required' => isset($field['required']) ? (bool)$field['required'] : false,
            'options' => [],
        ];

        // Validate options
        if (!isset($field['options']) || !is_array($field['options']) || empty($field['options'])) {
            $errors['options'] = 'Options are required and must be a non-empty array.';
        } else {
            foreach ($field['options'] as $option) {
                // Chech for Option Label
                if (!isset($option['label']) || !is_string($option['label']) || empty($option['label'])) {
                    $errors['options'][] = 'Option label is required and must be a string.';
                } else {
                    $option['label'] = sanitize_text_field($option['label']);
                }
                // Check for Option Value
                if (!isset($option['value']) || !is_string($option['value']) || empty($option['value'])) {
                    $errors['options'][] = 'Option value is required and must be a string.';
                } else {
                    $option['value'] = sanitize_text_field($option['value']);
                }
                // Check if the description is set and is a string
                if (isset($option['description']) && !is_string($option['description'])) {
                    $errors['options'][] = 'Option description must be a string.';
                } else {
                    $option['description'] = isset($option['description']) ? sanitize_textarea_field($option['description']) : '';
                }
                // Add the sanitized option to the field options
                $sanitized_field['options'][] = [
                    'label' => $option['label'],
                    'value' => $option['value'],
                    'description' => $option['description'],
                    'depends_on' => isset($option['depends_on']) ? $option['depends_on'] : []
                ];
            }
        }
        // Check if "depends_on" is set and is an array
        if (isset($field['depends_on']) && is_array($field['depends_on'])) {
            $sanitized_field['depends_on'] = self::sanitize_dependencies($field['depends_on']);
        } else {
            $field['depends_on'] = [];
        }

        return empty($errors) ? ['valid' => true, 'data' => $sanitized_field] : ['valid' => false, 'errors' => $errors];
    }

    private static function validate_checkboxList_field($field)
    {
        $errors = [];
        $sanitized_field = [
            'field_name' => $field['field_name'],
            'type' => $field['type'],
            'label' => $field['label'],
            'description' => isset($field['description']) ? sanitize_textarea_field($field['description']) : '',
            'required' => isset($field['required']) ? (bool)$field['required'] : false,
            'options' => [],
        ];

        // Validate options
        if (!isset($field['options']) || !is_array($field['options']) || empty($field['options'])) {
            $errors['options'] = 'Options are required and must be a non-empty array.';
        } else {
            foreach ($field['options'] as $option) {
                // Check for Option Label
                if (!isset($option['label']) || !is_string($option['label']) || empty($option['label'])) {
                    $errors['options'][] = 'Option label is required and must be a string.';
                } else {
                    $option['label'] = sanitize_text_field($option['label']);
                }
                // Check for Option Value
                if (!isset($option['value']) || !is_string($option['value']) || empty($option['value'])) {
                    $errors['options'][] = 'Option value is required and must be a string.';
                } else {
                    $option['value'] = sanitize_text_field($option['value']);
                }
                // Check if the description is set and is a string
                if (isset($option['description']) && !is_string($option['description'])) {
                    $errors['options'][] = 'Option description must be a string.';
                } else {
                    $option['description'] = isset($option['description']) ? sanitize_textarea_field($option['description']) : '';
                }
                // Check if "min" is set and is an integer
                if (isset($option['min']) && !is_int($option['min'])) {
                    $errors['options'][] = 'Option min value must be an integer.';
                } else {
                    $option['min'] = isset($option['min']) ? intval($option['min']) : null;
                }

                // Check if "max" is set and is an integer
                if (isset($option['max']) && !is_int($option['max'])) {
                    $errors['options'][] = 'Option max value must be an integer.';
                } else {
                    $option['max'] = isset($option['max']) ? intval($option['max']) : null;
                }

                // Add the sanitized option to the field options
                $sanitized_field['options'][] = [
                    'label' => $option['label'],
                    'value' => $option['value'],
                    'description' => $option['description'],
                    'depends_on' => isset($option['depends_on']) ? $option['depends_on'] : []
                ];
            }
        }

        // Check if "depends_on" is set and is an array
        if (isset($field['depends_on']) && is_array($field['depends_on'])) {
            $sanitized_field['depends_on'] = self::sanitize_dependencies($field['depends_on']);
        } else {
            $field['depends_on'] = [];
        }

        return empty($errors) ? ['valid' => true, 'data' => $sanitized_field] : ['valid' => false, 'errors' => $errors];
    }

    public static function validate_textarea_field($field)
    {
        $errors = [];
        $sanitized_field = [
            'field_name' => $field['field_name'],
            'type' => $field['type'],
            'label' => $field['label'],
            'description' => isset($field['description']) ? sanitize_textarea_field($field['description']) : '',
            'required' => isset($field['required']) ? (bool)$field['required'] : false,
        ];

        // Validate placeholder
        if (isset($field['placeholder']) && !is_string($field['placeholder'])) {
            $errors['placeholder'] = 'Placeholder must be a string.';
        } else {
            $sanitized_field['placeholder'] = isset($field['placeholder']) ? sanitize_text_field($field['placeholder']) : '';
        }
        // Validate default value
        if (isset($field['default_value']) && !is_string($field['default_value'])) {
            $errors['default_value'] = 'Default value must be a string.';
        } else {
            $sanitized_field['default_value'] = isset($field['default_value']) ? sanitize_textarea_field($field['default_value']) : '';
        }

        // Validate rows
        if (isset($field['rows']) && !is_int($field['rows']) && $field['rows'] <= 0) {
            $errors['rows'] = 'Rows must be an integer.';
        } else {
            $sanitized_field['rows'] = isset($field['rows']) ? intval($field['rows']) : 3; // Default to 3 rows if not set
        }
        // Validate cols
        if (isset($field['cols']) && !is_int($field['cols']) && $field['cols'] <= 0) {
            $errors['cols'] = 'Cols must be an integer.';
        } else {
            $sanitized_field['cols'] = isset($field['cols']) ? intval($field['cols']) : 20; // Default to 20 cols if not set
        }

        // Check if "depends_on" is set and is an array
        if (isset($field['depends_on']) && is_array($field['depends_on'])) {
            $sanitized_field['depends_on'] = self::sanitize_dependencies($field['depends_on']);
        } else {
            $field['depends_on'] = [];
        }

        return empty($errors) ? ['valid' => true, 'data' => $sanitized_field] : ['valid' => false, 'errors' => $errors];
    }

    public static function validate_submission_summary_field($field)
    {
        $errors = [];
        $sanitized_field = [
            'field_name' => $field['field_name'],
            'type' => $field['type'],
            'label' => $field['label'],
            'description' => isset($field['description']) ? sanitize_textarea_field($field['description']) : '',
            'required' => false, // Submission summary fields are not required
        ];

        // Check if "show_full_summary" is set and is a boolean
        if (isset($field['show_full_summary']) && !is_bool($field['show_full_summary'])) {
            $errors['show_full_summary'] = 'Show full summary must be a boolean.';
        } else {
            $sanitized_field['show_full_summary'] = isset($field['show_full_summary']) ? (bool)$field['show_full_summary'] : false;
        }

        // Check if "depends_on" is set and is an array
        if (isset($field['depends_on']) && is_array($field['depends_on'])) {
            $sanitized_field['depends_on'] = self::sanitize_dependencies($field['depends_on']);
        } else {
            $field['depends_on'] = [];
        }

        return empty($errors) ? ['valid' => true, 'data' => $sanitized_field] : ['valid' => false, 'errors' => $errors];
    }


    public static function sanitize_dependencies($dependencies)
    {
        $sanitized_dependencies = [];
        foreach ($dependencies as $dependency) {
            // Check if "field_name" is set and is a string
            if (!isset($dependency['field_name']) || !is_string($dependency['field_name']) || empty($dependency['field_name'])) {
                continue; // Skip invalid dependencies
            }
            // Check if "value" is set and is a string
            if (isset($dependency['value']) && !is_string($dependency['value'])) {
                continue; // Skip invalid dependencies
            }
            $sanitized_dependencies[] = [
                'field_name' => sanitize_text_field($dependency['field_name']),
                'value' => sanitize_text_field($dependency['value'])
            ];
        }
        return $sanitized_dependencies;
    }

    public static function validate_fields_dependencies($fields)
    {
        $fields = is_array($fields) ? $fields : [];
        $errors = [];

        foreach ($fields as $field) {
            $dependencies = $field['depends_on'] ?? [];
            if (is_array($dependencies) && !empty($dependencies)) {
                foreach ($dependencies as $dependency) {
                    // Validate each dependency
                    $validation_result = self::validate_dependency($dependency, $fields, $field['step_index']);
                    if (!$validation_result['valid']) {
                        $errors['form_fields'][$field['field_name']]['depends_on'][] = $validation_result['errors'];
                    } else {
                        // If valid, add the dependency to the field
                        $field['depends_on'][] = $validation_result['data'];
                    }
                }
            }
            // If field is of type 'select' or 'checkboxList', validate the options dependencies
            if (in_array($field['type'], ['select', 'checkboxList'], true))
            {
                foreach ($field['options'] as $option) {
                    $option_dependencies = $option['depends_on'] ?? [];
                    if (is_array($option_dependencies) && !empty($option_dependencies)) {
                        foreach ($option_dependencies as $dependency) {
                            // Validate each option dependency
                            $validation_result = self::validate_dependency($dependency, $fields, $field['step_index']);
                            if (!$validation_result['valid']) {
                                $errors['form_fields'][$field['field_name']]['options'][$option['value']]['depends_on'][] = $validation_result['errors'];
                            } else {
                                // If valid, add the dependency to the option
                                $option['depends_on'][] = $validation_result['data'];
                            }
                        }
                    }
                }
            }
        }
        return empty($errors) ? ['valid' => true] : ['valid' => false, 'errors' => $errors];
    }

    private static function validate_dependency($dependency, $fields, $field_step_index)
    {
        $fields = is_array($fields) ? $fields : [];
        $dependent_field = array_filter($fields, function ($field) use ($dependency) {
            return isset($field['field_name']) && $field['field_name'] === $dependency['field_name'];
        });
        $dependent_field = reset($dependent_field); // Get the first matching field
        $dependent_value = $dependency['value'] ?? null;
        $errors = [];

        // Check if the dependent field exists
        if (!$dependent_field) {
            $errors[] = 'Dependent field "' . $dependency['field_name'] . '" does not exist.';
        } else {
            // Check if the dependent field step_index is lower than the current field step index
            if ($dependent_field['step_index'] >= $field_step_index) {
                $errors[] = 'Dependency should be on a field with a lower step index than the current field.';
            }
            // Check if the dependent value is valid for the field type
            if (!isset($dependent_value)) {
                $errors[] = 'Dependent value is required for field "' . $dependency['field_name'] . '".';
            }
            if (!is_string($dependent_value)) {
                $errors[] = 'Dependent value must be a string for field "' . $dependency['field_name'] . '".';
            }
            switch ($dependent_field['type']) {
                case 'text':
                    // No specific validation for text fields
                    break;
                case 'select':
                    $option_values = array_column($dependent_field['options'], 'value');
                    if (!in_array($dependent_value, $option_values, true)) {
                        $errors[] = 'Dependent value "' . $dependent_value . '" does not exist in the options for field "' . $dependency['field_name'] . '".';
                    }
                    break;
                case 'checkboxList':
                    $option_values = array_column($dependent_field['options'], 'value');
                    if (!in_array($dependent_value, $option_values, true)) {
                        $errors[] = 'Dependent value "' . $dependent_value . '" does not exist in the options for field "' . $dependency['field_name'] . '".';
                    }
                    break;
                case 'textarea':
                    // No specific validation for textarea fields
                    break;
                case 'submissionSummary':
                    // No specific validation for submission summary fields
                    break;
                default:
                    $errors[] = 'Invalid field type for dependency: ' . $dependent_field['type'];
            }
        }

        return empty($errors) ?
            ['valid' => true, 'data' => $dependency] :
            ['valid' => false, 'errors' => $errors];
    }

    private static function validate_notifications_settings($notifications_settings)
    {
        $errors = [];
        $sanitized_settings = [];

        // Verify the "emails" contains comma separated email addresses
        if (!isset($notifications_settings['emails']) || !is_string($notifications_settings['emails']) || empty($notifications_settings['emails'])) {
            $errors['emails'] = 'Emails are required and must be a comma-separated string.';
        } else {
            $emails = explode(',', $notifications_settings['emails']);
            $emails = array_map('trim', $emails); // Trim whitespace from each email
            $emails = array_filter($emails, function ($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            }); // Filter out invalid emails
            if (empty($emails)) {
                $errors['emails'] = 'At least one valid email address is required.';
            } else {
                $sanitized_settings['emails'] = implode(',', $emails); // Join valid emails back
            }
        }

        // Verify the subject is set and is a string
        if (!isset($notifications_settings['subject']) || !is_string($notifications_settings['subject']) || empty($notifications_settings['subject'])) {
            $errors['subject'] = 'Subject is required and must be a string.';
        } else {
            $sanitized_settings['subject'] = sanitize_text_field($notifications_settings['subject']);
        }

        // Verify the body is set and is a string
        if (!isset($notifications_settings['body']) || !is_string($notifications_settings['body']) || empty($notifications_settings['body'])) {
            $errors['body'] = 'body is required and must be a string.';
        } else {
            $sanitized_settings['body'] = $notifications_settings['body']; // HTML content can be left as is, but you might want to sanitize it further if needed
        }

        return empty($errors) ? ['valid' => true, 'data' => $sanitized_settings] : ['valid' => false, 'errors' => $errors];
    }

    private static function validate_onSubmitAction_settings($action, $fields)
    {
        $errors = [];
        $sanitized_action = [];
        $allowed_actions = ['redirect', 'mail'];
        $action_type = isset($action['type']) ? $action['type'] : null;
        if (in_array($action_type, $allowed_actions, true)) {
            $sanitized_action['type'] = $action_type; // Sanitize action type
            if ($action_type === 'redirect') {
                // Validate redirect URL
                if (!isset($action['url']) || !is_string($action['url']) || empty($action['url'])) {
                    $errors[] = 'Redirect URL is required and must be a string.';
                } else {
                    $sanitized_action['url'] = esc_url_raw($action['url']);
                }
                // Validate bool open_in_new_tab
                if (isset($action['open_in_new_tab']) && !is_bool($action['open_in_new_tab'])) {
                    $errors[] = 'Open in new tab must be a boolean.';
                } else {
                    $sanitized_action['open_in_new_tab'] = isset($action['open_in_new_tab']) ? (bool)$action['open_in_new_tab'] : false; // Default to false
                }
            } elseif ($action_type === 'mail') {
                // Validate email_field
                // Check if email_field is set in the form fields and has no dependencies
                $email_field = array_filter($fields, function ($field) use ($action) {
                    return isset($field['field_name']) && $field['field_name'] === $action['email_field'];
                });
                $email_field = reset($email_field); // Get the first matching field
                if (!$email_field || $email_field['type'] !== 'text' || $email_field['input_type'] !== 'email' || !empty($email_field['depends_on'])) {
                    $errors[] = 'Email field is required and must be an email text field with no dependencies.';
                } else {
                    $sanitized_action['email_field'] = sanitize_text_field($action['email_field']);
                }

                // Validate subject
                if (!isset($action['subject']) || !is_string($action['subject']) || empty($action['subject'])) {
                    $errors[] = 'Subject is required and must be a string.';
                } else {
                    $sanitized_action['subject'] = sanitize_text_field($action['subject']);
                }

                // Validate body
                if (!isset($action['body']) || !is_string($action['body']) || empty($action['body'])) {
                    $errors[] = 'Body is required and must be a string.';
                } else {
                    $sanitized_action['body'] = $action['body']; // HTML content can be left as is, but you might want to sanitize it further if needed
                }
            }
        } else {
            $errors[] = 'Invalid onSubmit action. Allowed actions are: ' . implode(', ', $allowed_actions) . '.';
        }
        return empty($errors) ? ['valid' => true, 'data' => $sanitized_action] : ['valid' => false, 'errors' => $errors];
    }

    private static function validate_design_settings($design_settings)
    {
        $errors = [];
        $sanitized_settings = [];

        // Validate Colors
        $colors = $design_settings['colors'] ?? [];
        if (!is_array($colors) || empty($colors)) {
            $errors['colors'] = 'Colors must be a non-empty array.';
        } else {
            foreach ($colors as $color_name => $color_value) {
                if (!is_string($color_name) || empty($color_name)) {
                    $errors['colors'][$color_name] = 'Color name must be a non-empty string.';
                } elseif (!is_string($color_value) || !self::validate_color_value_hex($color_value)) {
                    $errors['colors'][$color_name] = 'Color value must be a valid hex color code.';
                } else {
                    $sanitized_settings['colors'][$color_name] = self::validate_color_value_hex($color_value);
                }
            }
        }

        // Validate Header
        $header = $design_settings['header'] ?? [];
        if (isset($header['show']) && !is_bool($header['show'])) {
            $errors['header']['show'] = 'Header show must be a boolean.';
        } else {
            $sanitized_settings['header']['show'] = isset($header['show']) ? (bool)$header['show'] : true; // Default to true
        }
        $allowed_header_alignments = ['left', 'center', 'right'];
        if (isset($header['align']) && !is_string($header['align'])) {
            $errors['header']['align'] = 'Header alignment must be a string.';
        } elseif (isset($header['align']) && !self::validate_enum_value($header['align'], $allowed_header_alignments)) {
            $errors['header']['align'] = 'Header alignment must be one of: ' . implode(', ', $allowed_header_alignments) . '.';
        } else {
            $sanitized_settings['header']['align'] = isset($header['align']) ? sanitize_text_field($header['align']) : 'left'; // Default to left
        }

        // Validate Steps
        $steps = $design_settings['steps'] ?? [];
        if (!isset($steps['hide_step_header']) || !is_bool($steps['hide_step_header'])) {
            $errors['steps']['hide_step_header'] = 'Steps hide step header must be a boolean.';
        } else {
            $sanitized_settings['steps']['hide_step_header'] = (bool)$steps['hide_step_header']; // Default to false
        }
        if (!isset($steps['text_align']) || !is_string($steps['text_align'])) {
            $errors['steps']['text_align'] = 'Steps text alignment must be a string.';
        } elseif (!self::validate_enum_value($steps['text_align'], ['text-left', 'text-center', 'text-right'])) {
            $errors['steps']['text_align'] = 'Steps text alignment must be one of: text-left, text-center, text-right.';
        } else {
            $sanitized_settings['steps']['text_align'] = sanitize_text_field($steps['text_align']);
        }
        if (!isset($steps['items_align']) || !is_string($steps['items_align'])) {
            $errors['steps']['items_align'] = 'Steps items alignment must be a string.';
        } elseif (!self::validate_enum_value($steps['items_align'], ['items-start', 'items-center', 'items-end'])) {
            $errors['steps']['items_align'] = 'Steps items alignment must be one of: items-start, items-center, items-end.';
        } else {
            $sanitized_settings['steps']['items_align'] = sanitize_text_field($steps['items_align']);
        }
        if (!isset($steps['step_transition']) || !is_string($steps['step_transition'])) {
            $errors['steps']['step_transition'] = 'Steps step transition must be a string.';
        } elseif (!self::validate_enum_value($steps['step_transition'], ['default', 'slide'])) {
            $errors['steps']['step_transition'] = 'Steps step transition must be one of: default, slide.';
        } else {
            $sanitized_settings['steps']['step_transition'] = sanitize_text_field($steps['step_transition']);
        }

        // Validate Footer
        $footer = $design_settings['footer'] ?? [];
        if (isset($footer['show_progress_bar']) && !is_bool($footer['show_progress_bar'])) {
            $errors['footer']['show_progress_bar'] = 'Footer show progress bar must be a boolean.';
        } else {
            $sanitized_settings['footer']['show_progress_bar'] = isset($footer['show_progress_bar']) ? (bool)$footer['show_progress_bar'] : true; // Default to true
        }

        return empty($errors) ? ['valid' => true, 'data' => $sanitized_settings] : ['valid' => false, 'errors' => $errors];
    }

    public static function validate_color_value_hex($color) {
        // Check if the color is a valid hex color code
        if (preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color)) {
            return $color; // Valid hex color code
        } else {
            return false; // Invalid hex color code
        }
    }

    public static function validate_enum_value($value, $allowed_values)
    {
        // Check if the value is in the allowed values array
        if (in_array($value, $allowed_values, true)) {
            return $value; // Valid enum value
        } else {
            return false; // Invalid enum value
        }
    }


}
