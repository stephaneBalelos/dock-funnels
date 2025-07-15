<?php

/**
 * DockFunnels_Mailing class for handling mailing operations related to Dock Funnels.
 * * This class provides methods to send emails, manage mailing lists, and handle
 * subscriptions.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class DockFunnels_Mailing {

    /**
     * Sends an email to a specified recipient.
     *
     * @param string $to The recipient's email address.
     * @param string $subject The subject of the email.
     * @param string $message The body of the email.
     * @param array $headers Optional. Additional headers for the email.
     * @return bool True on success, false on failure.
     */
    public static function send_email($to, $subject, $message, $headers = []) {
        return wp_mail($to, $subject, $message, $headers);
    }

    // Additional mailing methods can be added here
    public static function send_notifications_emails($form, $submission) {
        // Example implementation for sending notification emails
        $form_settings = json_decode($form->form_settings, true);
        $form_notification_settings = isset($form_settings['notifications_settings']) ? $form_settings['notifications_settings'] : [];
        $emails = isset($form_notification_settings['emails']) ? $form_notification_settings['emails'] : "";
        $subject = isset($form_notification_settings['subject']) ? $form_notification_settings['subject'] : 'Neue Formularübermittlung';
        $body = isset($form_notification_settings['body']) ? $form_notification_settings['body'] : 'Sie haben eine neue Formularübermittlung erhalten.';
        $to = get_option('admin_email');
        if (!empty($emails)) {
            // Split emails by comma and trim whitespace and join with admin email
            $emails_array = array_map('trim', explode(',', $emails));
            $emails_array = array_filter($emails_array); // Remove empty values
            if (!empty($emails_array)) {
                $to = array_merge([$to], $emails_array); // Merge admin email with other emails
            }
        }

        // Look for placeholders in the subject and body
        $placeholders = [
            '{form_name}' => $form->name,
            '{form_description}' => $form->description,
            '{submission_data}' => self::get_submission_html($form, $submission),
        ];
        $subject = strtr($subject, $placeholders);
        $body = strtr($body, $placeholders);

        return self::send_email($to, $subject, $body, [
            'Content-Type: text/html; charset=UTF-8',
        ]);
    }

    public static function handleOnSubmitActionMail($form, $submission, $action) {
        $email_field = $action['email_field'] ?? '';
        if (empty($email_field)) {
            return false; // No email field specified
        }
        $email_to = isset($submission[$email_field]) ? $submission[$email_field]['value'] : '';
        if (empty($email_to)) {
            return false; // No email value found in submission
        }
        $subject = isset($action['subject']) ? $action['subject'] : '';
        $body = isset($action['body']) ? $action['body'] : '';
        $placeholders = [
            '{form_name}' => $form->name,
            '{form_description}' => $form->description,
            '{submission_data}' => self::get_submission_html($form, $submission),
        ];
        $subject = strtr($subject, $placeholders);
        $body = strtr($body, $placeholders);
        $headers = [
            'Content-Type: text/html; charset=UTF-8',
        ];
        // Send the email
        return self::send_email($email_to, $subject, $body, $headers);
    }


    private static function get_submission_html($form, $submission_fields) {
        // Generate HTML for the submission data
        $form_data = json_decode($form->form_data, true);
        $form_steps = isset($form_data['form_steps']) ? $form_data['form_steps'] : [];
        $form_fields = isset($form_data['form_fields']) ? $form_data['form_fields'] : [];
        $html = '<h3>Formulardaten</h3>';
        for ($i = 0; $i < count($form_steps); $i++) {
            $step = $form_steps[$i];
            $fields = array_filter($form_fields, function ($field) use ($step, $i) {
                return $field['step_index'] === $i;
            });
            if (empty($fields)) {
                continue; // Skip empty steps
            }
            $html .= '<h4> ' . ($i + 1) . ': ' . esc_html($step['title']) . '</h4>';
            foreach ($fields as $field) {
                $submitted_field = isset($submission_fields[$field['field_name']]) ? $submission_fields[$field['field_name']] : null;
                if ($submitted_field === null) {
                    continue; // Skip fields that were not submitted
                }
                $field_value = $submitted_field['value'];
                // Handle Select fields with options
                if ($field['type'] === 'select' && isset($field['options']) && is_array($field['options'])) {
                    $selected_option_label = array_find($field['options'], function ($option) use ($field_value) {
                        return $option['value'] === $field_value;
                    });
                    if ($selected_option_label) {
                        $field_value = $selected_option_label['label'];
                    } else {
                        $field_value = 'N/A'; // If no matching option found
                    }
                }
                // Handle Checkbox fields with multiple values
                if ($field['type'] === 'checkboxList' && is_array($field_value)) {
                    $field_options = isset($field['options']) ? $field['options'] : [];
                    $field_value = array_map(function ($value) use ($field_options) {
                        $option = array_find($field_options, function ($option) use ($value) {
                            return $option['value'] === $value;
                        });
                        return $option ? $option['label'] : $value; // Use label if available, otherwise use value
                    }, $field_value);
                    $field_value = implode(', ', $field_value); // Join multiple values with a comma
                }

                if (is_array($field_value)) {
                    $field_value = implode(', ', $field_value); // Handle multi-select fields
                }

                
                $html .= '<p><strong>' . esc_html($field['label']) . ':</strong> ' . esc_html($field_value) . '</p>';
            }
        }
        return $html;
    }
}


