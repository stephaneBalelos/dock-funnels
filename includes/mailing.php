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
}