<?php

/**
 * DockFunnels_Mailing class for handling mailing operations related to Dock Funnels.
 * * This class provides methods to send emails, manage mailing lists, and handle
 * subscriptions.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class DockFunnels_Mailing
{
    /**
     * Sends an email to a specified recipient.
     *
     * @param string $to The recipient's email address.
     * @param string $subject The subject of the email.
     * @param string $message The body of the email.
     * @param array $headers Optional. Additional headers for the email.
     * @return bool True on success, false on failure.
     */
    public static function send_email($to, $subject, $message, $headers = [])
    {
        $result = false;
        $options = get_option('dock_funnels_options');
        if (!empty($options['smtp_host']) && !empty($options['smtp_username']) && !empty($options['smtp_password'])) {
            // Backup current PHPMailer init
            remove_action('phpmailer_init', 'wp_phpmailer_init'); // Prevent WordPress from overriding again

            add_action('phpmailer_init', [__CLASS__, 'dock_funnels_configure_phpmailer']);

            $result = wp_mail($to, $subject, $message, $headers);

            // Remove your hook to prevent affecting other plugins
            remove_action('phpmailer_init', [__CLASS__, 'dock_funnels_configure_phpmailer']);

            // Restore default PHPMailer config
            add_action('phpmailer_init', 'wp_phpmailer_init');
        } else {
            // Fallback to default WordPress mail function
            $result = wp_mail($to, $subject, $message, $headers);
        }

        return $result;
    }

    // Additional mailing methods can be added here
    public static function send_notifications_emails($form, $submission)
    {
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

        $subject = self::parse_email_subject($subject, $form, $submission);
        $body = self::parse_email_body($body, $form, $submission);

        return self::send_email($to, $subject, $body, [
            'Content-Type: text/html; charset=UTF-8',
        ]);
    }

    public static function handleOnSubmitActionMail($form, $submission, $action)
    {
        $email_field = $action['email_field'] ?? '';
        if (empty($email_field)) {
            return false; // No email field specified
        }
        $email_to = isset($submission[$email_field]) ? $submission[$email_field]['value'] : '';
        if (empty($email_to)) {
            return false; // No email value found in submission
        }
        $subject = isset($action['subject']) ? $action['subject'] : 'Danke für Ihre Übermittlung';
        $body = isset($action['body']) ? $action['body'] : 'Wir haben Ihre Übermittlung erhalten. {submission_data} Vielen Dank!';

        $subject = self::parse_email_subject($subject, $form, $submission);
        $body = self::parse_email_body($body, $form, $submission);
        $headers = [
            'Content-Type: text/html; charset=UTF-8',
        ];
        // Send the email
        return self::send_email($email_to, $subject, $body, $headers);
    }

    private static function parse_email_subject($subject, $form, $submission)
    {
        // Replace placeholders in the email subject
        $placeholders = [
            '{form_name}' => $form->name,
        ];
        return strtr($subject, $placeholders);
    }

    private static function parse_email_body($body, $form, $submission)
    {
        // Replace placeholders in the email body
        $placeholders = [
            '{form_name}' => $form->name,
            '{form_description}' => $form->description,
            '{submission_data}' => self::get_submission_html($form, $submission),
        ];

        $dom = new DomDocument();
        // Use UTF-8 encoding to handle special characters
        $body = mb_convert_encoding($body, 'HTML-ENTITIES', 'UTF-8');
        // Load HTML content into DOMDocument
        // Suppress warnings from invalid HTML
        libxml_use_internal_errors(true);
        $dom->loadHTML($body, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        // Replace placeholders in the HTML content
        foreach ($placeholders as $key => $value) {
            $body = str_replace($key, $value, $body);
        }
        // Find all .mention elements and replace them with their text content
        $xpath = new DOMXPath($dom);
        $mentions = $xpath->query('//span[contains(@class, "mention")]');
        foreach ($mentions as $mention) {
            // get data-id attribute if exists
            $data_id = $mention->attributes->getNamedItem('data-id') ? $mention->attributes->getNamedItem('data-id')->nodeValue : '';
            if ($data_id) {
                // Replace the mention with data-id value
                $mention_value = self::get_submission_field_display_value($data_id, $submission);
                $mention->nodeValue = $mention_value;
            } else {
                // If no data-id, just use the text content
                $mention->nodeValue = $mention->textContent;
            }
        }
                
        // Convert DOMDocument back to HTML
        $body = $dom->saveHTML();
        libxml_clear_errors();
        return $body;
    }


    private static function get_submission_field_display_value($field_name, $submission_fields)
    {
        // Get the submitted field value from the submission data
        if (isset($submission_fields[$field_name])) {
            $field = $submission_fields[$field_name];
            // Handle Select fields with options
            $field_value = $field['value'];
            if ($field['type'] === 'select') {
                $field_value = $field['value_label'] ?? $field_value; // Use value_label if available
            }
            return $field_value; // Return the field value directly
        }
        return 'N/A'; // If field not found in submission
    }


    private static function get_submission_html($form, $submission_fields)
    {
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

    public static function dock_funnels_configure_phpmailer($phpmailer)
    {
        $options = get_option('dock_funnels_options');
        if (empty($options['smtp_host']) || empty($options['smtp_username']) || empty($options['smtp_password'])) {
            return; // No SMTP settings configured, use default WordPress mail function
        }
        // Custom SMTP config
        $phpmailer->isSMTP();
        $phpmailer->Host       = $options['smtp_host'];
        $phpmailer->SMTPAuth   = true;
        $phpmailer->Port       = !empty($options['smtp_port']) ? $options['smtp_port'] : 587;
        $phpmailer->Username   = $options['smtp_username'];
        $phpmailer->Password   = DockFunnels_Main::dock_funnels_decrypt($options['smtp_password']);
        $phpmailer->SMTPSecure = !empty($options['smtp_secure']) ? $options['smtp_secure'] : 'tls'; // or 'ssl'
        $phpmailer->From       = !empty($options['from_email']) ? $options['from_email'] : 'your@email.com';
        $phpmailer->FromName   = !empty($options['from_name']) ? $options['from_name'] : 'Your Name or Plugin Name';
    }
}
