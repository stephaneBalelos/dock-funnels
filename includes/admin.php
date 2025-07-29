<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class DockFunnels_Admin
{

    public static function enqueue_admin_assets($hook)
    {
        if (strpos($hook, 'dock-funnels') === false) return;

        wp_enqueue_style('dock-funnels-dashboard', plugin_dir_url(__FILE__) . '../front-end/dist/assets/dashboard/dock-funnels.css');
        wp_enqueue_script('dock-funnels-vue', 'https://unpkg.com/vue@3/dist/vue.global.js"', [], null, true);
        wp_enqueue_script('dock-funnels-dashboard', plugin_dir_url(__FILE__) . '../front-end/dist/assets/dashboard/index.iife.js', [], null, true);

        $data = [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('dock_funnels_admin_nonce')
        ];
        wp_localize_script('dock-funnels-dashboard', 'DockFunnelsAdmin', $data);
    }

    public static function register_admin_menu()
    {
        add_menu_page(
            'Dock Funnels',
            'Dock Funnels',
            'manage_options',
            'dock-funnels',
            [__CLASS__, 'render_forms_page'],
            'dashicons-feedback'
        );

        add_submenu_page(
            'dock-funnels',
            'Form Responses',
            'Responses',
            'manage_options',
            'dock-funnels-responses',
            [__CLASS__, 'render_responses_page']
        );

        add_submenu_page(
            'dock-funnels',
            'Form Editor',
            'Form Editor',
            'manage_options',
            'dock-funnels-editor',
            [__CLASS__, 'render_form_editor_page']
        );

        // Register settings page
        add_options_page(
            'Dock Funnels Settings',
            'Dock Funnels Settings',
            'manage_options',
            'dock-funnels-settings',
            [__CLASS__, 'render_settings_page']
        );
    }

    public static function register_plugin_settings()
    {
        register_setting('dock_funnels_options_group', 'dock_funnels_options');

        add_settings_section('dock_funnels_smtp_section', 'SMTP Settings', null, 'dock-funnels');
        add_settings_field('dock_funnels_smtp_host', 'SMTP Host', [__CLASS__, 'render_smtp_host_field'], 'dock-funnels', 'dock_funnels_smtp_section');
        add_settings_field('dock_funnels_smtp_port', 'SMTP Port', [__CLASS__, 'render_smtp_port_field'], 'dock-funnels', 'dock_funnels_smtp_section');
        add_settings_field('dock_funnels_smtp_username', 'SMTP Username', [__CLASS__, 'render_smtp_username_field'], 'dock-funnels', 'dock_funnels_smtp_section');
        add_settings_field('dock_funnels_smtp_password', 'SMTP Password', [__CLASS__, 'render_smtp_password_field'], 'dock-funnels', 'dock_funnels_smtp_section');
        add_settings_field('dock_funnels_smtp_secure', 'SMTP Secure', [__CLASS__, 'render_smtp_secure_field'], 'dock-funnels', 'dock_funnels_smtp_section');
        add_settings_field('dock_funnels_from_email', 'From Email', [__CLASS__, 'render_from_email_field'], 'dock-funnels', 'dock_funnels_smtp_section');
        add_settings_field('dock_funnels_from_name', 'From Name', [__CLASS__, 'render_from_name_field'], 'dock-funnels', 'dock_funnels_smtp_section');
    }

    public static function render_forms_page()
    {
        if (!current_user_can('manage_options')) {
            echo '<div class="wrap"><h1>Access Denied</h1><p>You do not have permission to view this page.</p></div>';
            return;
        }
        if (isset($_GET['form_id']) && is_numeric($_GET['form_id'])) {
            $form_id = intval($_GET['form_id']);
            $form = DockFunnels_DB::get_form_by_id($form_id);
            print_r($form);
            if ($form) {
                echo '<div id="dock-funnels-editor" class="dock-funnels-root"></div></div>';
                return;
            } else {
                echo '<div class="wrap"><h1>Form Not Found</h1><p>The requested form does not exist.</p></div>';
                return;
            }
        } else {
            $forms = DockFunnels_DB::get_forms();
            echo '<div class="wrap"><h1>Dock Funnels</h1>';
            echo '<p>Verwalten Sie Ihre Formulare und deren Antworten.</p>';
            if (empty($forms)) {
                echo '<br><br><h2>Keine Formulare gefunden</h2><p>Bitte erstellen Sie ein neues Formular.</p>';
                echo '<a href="' . admin_url('admin.php?page=dock-funnels-editor') . '" class="button button-primary">Neues Formular erstellen</a>';
                return;
            }
            self::render_forms_table($forms);
            echo '</div>'; // Close the wrap div
            return;
        }
    }

    public static function render_responses_page()
    {
        if (!current_user_can('manage_options')) {
            echo '<div class="wrap"><h1>Access Denied</h1><p>You do not have permission to view this page.</p></div>';
            return;
        }
        if (!isset($_GET['form_id']) || !is_numeric($_GET['form_id'])) {
            $forms = DockFunnels_DB::get_forms();
            echo '<div class="wrap"><h1>Formular Einreichungen</h1>
                <table class="widefat">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                <tbody>';
            foreach ($forms as $form) {
                echo "<tr>
                <td>{$form['id']}</td>
                <td>{$form['name']}</td>
                <td>
                    <a href='" . admin_url("admin.php?page=dock-funnels-responses&form_id={$form['id']}") . "' class='button'>
                    Antworten anzeigen ({$form['response_count']})</a>
                </td>
                </tr>";
            }
            echo '</tbody></table></div>';
            return;
        } else {
            $form_id = intval($_GET['form_id']);
            $form = DockFunnels_DB::get_form_by_id($form_id);
            if (!$form) {
                echo '<div class="wrap"><h1>Form Not Found</h1><p>The requested form does not exist.</p></div>';
                return;
            } else {
                echo '<div id="dock-funnels-responses" class="dock-funnels-root"></div>';
            }
        }
    }

    public static function render_form_editor_page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        if (isset($_GET['form_id']) && is_numeric($_GET['form_id'])) {
            $form_id = intval($_GET['form_id']);
            $form = DockFunnels_DB::get_form_by_id($form_id);
            if (!$form) {
                echo '<div class="wrap"><h1>Formular nicht gefunden</h1><p>
                Das Formular mit der ID <b>"' . $form_id . '"</b> wurde nicht gefunden. Bitte erstellen Sie ein neues Formular.
                <br><br>
                <a href="' . admin_url('admin.php?page=dock-funnels-editor') . '" class="button">Neues Formular erstellen</a>
                </p></div>';
                return;
            }
        } else {
            $form = null; // No form selected, create a new one
        }
        echo '<div id="dock-funnels-editor" class="dock-funnels-root"></div>';
    }

    public static function render_forms_table($forms)
    {
        echo '<table class="widefat">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Shortcode</th>
                    <th>Actions</th>
                </tr>
                </thead>
            <tbody>';
        foreach ($forms as $form) {
            echo "<tr>
            <td>{$form['id']}</td>
            <td>{$form['name']}</td>
            <td><code>[dock_funnel id='{$form['id']}']</code></td>
            <td>
                <a href='" . admin_url("admin.php?page=dock-funnels-editor&form_id={$form['id']}") . "' class='button'>Edit</a>
                <a href='" . admin_url("admin.php?page=dock-funnels-responses&form_id={$form['id']}") . "' class='button'>Responses</a>
            </tr>";
        }
        echo '</tbody></table>';
    }

    public static function render_settings_page()
    {
        echo '<div class="wrap">';
        echo '<h1>Dock Funnels Settings</h1>';
        echo '<form method="post" action="options.php">';
        settings_fields('dock_funnels_options_group');
        do_settings_sections('dock-funnels');
        submit_button();
        echo '</form>';
        echo '</div>';
    }



    // Option Fields
    public static function render_smtp_host_field()
    {
        $options = get_option('dock_funnels_options');
        echo '<input type="text" name="dock_funnels_options[smtp_host]" value="' . esc_attr($options['smtp_host'] ?? '') . '" />';
        echo '<p class="description">Enter your SMTP host for sending emails.</p>';
    }
    public static function render_smtp_port_field()
    {
        $options = get_option('dock_funnels_options');
        echo '<input type="number" name="dock_funnels_options[smtp_port]" value="' . esc_attr($options['smtp_port'] ?? '') . '" />';
        echo '<p class="description">Enter your SMTP port (usually 587 for TLS or 465 for SSL).</p>';
    }
    public static function render_smtp_username_field()
    {
        $options = get_option('dock_funnels_options');
        echo '<input type="text" name="dock_funnels_options[smtp_username]" value="' . esc_attr($options['smtp_username'] ?? '') . '" />';
        echo '<p class="description">Enter your SMTP username.</p>';
    }
    public static function render_smtp_password_field()
    {
        $options = get_option('dock_funnels_options');
        echo '<input type="password" name="dock_funnels_options[smtp_password]" value="' . esc_attr($options['smtp_password'] ?? '') . '" />';
        echo '<p class="description">Enter your SMTP password.</p>';
    }

    public static function render_smtp_secure_field()
    {
        $options = get_option('dock_funnels_options');
        $secure_options = ['tls' => 'TLS', 'ssl' => 'SSL'];
        echo '<select name="dock_funnels_options[smtp_secure]">';
        foreach ($secure_options as $value => $label) {
            $selected = (isset($options['smtp_secure']) && $options['smtp_secure'] === $value) ? 'selected' : '';
            echo "<option value='$value' $selected>$label</option>";
        }
        echo '</select>';
        echo '<p class="description">Select the SMTP secure protocol.</p>';
    }

    public static function render_from_email_field()
    {
        $options = get_option('dock_funnels_options');
        echo '<input type="email" name="dock_funnels_options[from_email]" value="' . esc_attr($options['from_email'] ?? '') . '" />';
        echo '<p class="description">Enter the email address that will be used as the sender.</p>';
    }

    public static function render_from_name_field()
    {
        $options = get_option('dock_funnels_options');
        echo '<input type="text" name="dock_funnels_options[from_name]" value="' . esc_attr($options['from_name'] ?? '') . '" />';
        echo '<p class="description">Enter the name that will be used as the sender.</p>';
    }
}
